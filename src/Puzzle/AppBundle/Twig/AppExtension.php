<?php
namespace Puzzle\AppBundle\Twig;

use Doctrine\ORM\EntityManager;
use Puzzle\AdminBundle\Entity\Menu;
use Puzzle\AdminBundle\Entity\MenuItem;
use Puzzle\TranslationBundle\Service\Translator;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var EntityManager $em
     */
    protected $em;
    
    /**
     * @var \Twig_Environment $twig
     */
    protected $twig;
    
    /**
     * @var Translator $translator
     */
    protected $translator;
    
    public function __construct(EntityManager $em, \Twig_Environment $twig, Translator $translator) {
        $this->em = $em;
        $this->twig = $twig;
        $this->translator = $translator;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('menu', [$this, 'getBlockMenu'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('menu_item', [$this, 'getMenuItem'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getBlockMenu($slug, $position, $locale = null, $useCache = false){
        $menu = $this->em->getRepository(Menu::class)->findOneBy(['slug' => $slug]);
        $criteria = [['parent', null, "IS NULL"], ["menu", $menu->getId()]];
        $menuItems = $this->em->getRepository(MenuItem::class)->customfindBy(
            null, null, $criteria, ['position' => 'ASC'], null, null, $useCache
        );
        return $this->twig->render('AppBundle:Helper:menu_'.$position.'.html.twig',[
            'menu' => $menu,
            'menuItems' => $menuItems
        ]);
    }
    
    public function getMenuItem(MenuItem $menuItem, $position, $locale = null, $useCache = false){
        $translationsMenuItem = $translationsPage = null;
        
        if ($locale !== null){
            $translationsMenuItem = $this->translator->findTranslations($menuItem, $locale, $useCache);
            if ($menuItem->getPage() !== null){
                $translationsPage = $this->translator->findTranslations($menuItem->getPage(), $locale, $useCache);
            }
        }
        
        $childs = $this->em->getRepository(MenuItem::class)->customfindBy(
            null, null, [['parent', $menuItem->getId()]], ['position' => 'ASC'], null, null, true
        );
        return $this->twig->render('AppBundle:Helper:menu_'.$position.'_items.html.twig',[
            'menuItem' => $menuItem,
            'childs' => $childs,
            'page' => $menuItem->getPage(),
            'translationMenuItem' => $translationsMenuItem !== null && isset($translationsMenuItem[$locale]) ? $translationsMenuItem[$locale] : null,
            'translationPage' => $translationsPage !== null && isset($translationsPage[$locale]) ? $translationsPage[$locale] : null
        ]);
    } 
}
