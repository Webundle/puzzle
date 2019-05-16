<?php
namespace Puzzle\AppBundle\Twig;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class BlogExtension extends \Twig_Extension
{
    /**
     * @var EntityManager $em
     */
    protected $em;
    
    /**
     * @var Paginator $paginator
     */
    protected $paginator;
    
    public function __construct(EntityManager $em, Paginator $paginator) {
        $this->em = $em;
        $this->paginator = $paginator;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('blog_posts', [$this, 'getPosts'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getPosts($limit, $order, $page = 1) {
        $query = $this->em
                      ->createQueryBuilder()
                      ->select('p')
                      ->from("BlogBundle:Post", 'p')
                      ->orderBy('p.createdAt', 'DESC')
                      ->getQuery();
        
        return $this->paginator->paginate($query, $page,$limit);
    }
}
