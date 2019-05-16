<?php

namespace Puzzle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\AdminBundle\Entity\MenuItem;
use Puzzle\StaticBundle\Entity\Page;
use Doctrine\Common\Cache\MemcachedCache;

class HelperController extends Controller
{
    public function menuAction(Request $request, $slug, $position){
        $em = $this->getDoctrine()->getManager();
        $menu = $em->getRepository("AdminBundle:Menu")->customfindBy(
            null, null, [['slug', $slug]], ['position' => 'ASC'], null, null, true
        );
        $criteria = [['parent', null, "IS NULL"], ["menu", $menu->getId()]];
        $menuItems = $em->getRepository("AdminBundle:MenuItem")->customfindBy(
            null, null, $criteria, ['position' => 'ASC'], null, null, true
        );
        return $this->render('AppBundle:Helper:menu_'.$position.'.html.twig',[
            'menu' => $menu,
            'menuItems' => $menuItems
        ]);
    }
    
    public function menuItemAction(Request $request, MenuItem $menuItem, $position){
        $em = $this->getDoctrine()->getManager();
        $translationsMenuItem = $translationsPage = null;
        $translationsMenuItem = $this->get('translation.translator')->findTranslations($menuItem, $request->getLocale(), true);
        if ($menuItem->getPage() !== null){
            $translationsPage = $this->get('translation.translator')->findTranslations($menuItem->getPage(), $request->getLocale(), true);
        }
        
        $childs = $em->getRepository("AdminBundle:MenuItem")->customfindBy(
            null, null, [['parent', $menuItem->getId()]], ['position' => 'ASC'], null, null, true
            );
        return $this->render('AppBundle:Helper:menu_'.$position.'_items.html.twig',[
            'menuItem' => $menuItem,
            'childs' => $childs,
            'page' => $menuItem->getPage(),
            'translationMenuItem' => $translationsMenuItem !== null && isset($translationsMenuItem[$request->getLocale()]) ? $translationsMenuItem[$request->getLocale()] : null,
            'translationPage' => $translationsPage !== null && isset($translationsPage[$request->getLocale()]) ? $translationsPage[$request->getLocale()] : null
        ]);
    } 
}
