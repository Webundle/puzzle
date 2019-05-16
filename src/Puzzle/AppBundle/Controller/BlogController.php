<?php

namespace Puzzle\AppBundle\Controller;

use Puzzle\BlogBundle\Entity\Category;
use Puzzle\BlogBundle\Entity\Comment;
use Puzzle\BlogBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class BlogController extends Controller
{
    /**
     * Show Posts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTimelineAction(Request $request){
        $category = $this->getDoctrine()->getRepository("BlogBundle:Category")->findOneBy(['parent' => null], ['createdAt' => 'DESC']);
        $posts = null;
        
        if ($category !== null) {
            $posts = $this->getDoctrine()->getRepository("BlogBundle:Post")->findBy(['category' => $category->getId()], ['createdAt' => 'DESC']);
        }
        return $this->render("AppBundle:Blog:timeline.html.twig", array(
            'category' => $category,
    	    'posts' => $posts
    	));
    }
    
    /**
     * Show Posts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPostsAction(Request $request){
        $categories = $this->getDoctrine()->getRepository("BlogBundle:Category")->findBy([], ['createdAt' => 'DESC']);
        $posts = $this->getDoctrine()->getRepository("BlogBundle:Post")->findBy([], ['createdAt' => 'DESC']);
        return $this->render("AppBundle:Blog:posts.html.twig", array(
            'categories' => $categories,
            'posts' => $posts
        ));
    }
    
    /**
     * Show Post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPostAction(Request $request, $pageSlug, $postSlug){
        $em = $this->getDoctrine()->getManager();
        
        $localeName = $request->getLocale();
        $locale = $em->getRepository("TranslationBundle:Locale")->findOneBy(['name' => $localeName]);
        
        $pageTranslation = $em->getRepository("TranslationBundle:Translation")->findOneBy([
            'field' => 'slug',
            'value' => $pageSlug,
            'locale' => $locale->getId()
        ]);
        
        if ($pageTranslation !== null){
            $translatable = $pageTranslation->getTranslatable();
            $page = $em->getRepository($translatable->getClass())->find($translatable->getIdentifier());
        }else {
            $page = $em->getRepository("StaticBundle:Page")->findOneBy(['slug' => $pageSlug]);
        }
        
        $postTranslation = $em->getRepository("TranslationBundle:Translation")->findOneBy([
            'field' => 'slug',
            'value' => $postSlug,
            'locale' => $locale->getId()
        ]);
        
        if ($postTranslation !== null){
            $translatable = $pageTranslation->getTranslatable();
            $post = $em->getRepository($translatable->getClass())->find($translatable->getIdentifier());
        }else {
            $post = $em->getRepository("BlogBundle:Post")->findOneBy(['slug' => $postSlug]);
        }
        
        $posts = $em->getRepository("BlogBundle:Post")->customFindBy(
            null, null, [['id', $post->getId(), '!=']],  ['createdAt' => 'DESC'], 5
        );
        
    	return $this->render("AppBundle:Blog:post.html.twig", array(
    	    'page' => $page,
    	    'post' => $post,
    	    'posts' => $posts,
    	    'categories' => $em->getRepository("BlogBundle:Category")->findAll(),
    	    'archives' => $em->getRepository("BlogBundle:Archive")->findAll()
    	));
    }
    
    
    /***
     * Show Categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoriesAction(Request $request){
        $categories = $this->getDoctrine()->getRepository("BlogBundle:Category")->findBy(['parent' => null], ['createdAt' => 'DESC']);
        return $this->render("AppBundle:Blog:categories.html.twig", array('categories' => $categories));
    }
    
    
    /***
     * Show Category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryAction(Request $request, Category $category){
        $em    = $this->getDoctrine()->getManager();
        $dql   = "SELECT p FROM BlogBundle:Post p WHERE p.category = :category";
        $query = $em->createQuery($dql)->setParameter('category', $category->getId());
        
        $paginator  = $this->get('knp_paginator');
        $posts = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
        
    	return $this->render("AppBundle:Blog:category.html.twig", array(
    	    'category' => $category,
    	    'posts' => $posts,
    	));
    }
    
    /**
     * Show comments for posts
     * 
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCommentsAction(Request $request, Post $post) {
        $comments = $this->getDoctrine()->getRepository("BlogBundle:Comment")->findBy(['post' => $post->getId()]);
        
        if ($request->isXmlHttpRequest() === true) {
            $array = [];
            
            foreach ($comments as $comment) {
                $item = [
                    'id' => $comment->getId(),
//                     'created' => $comment->getCreatedAt()->format("Y-m-d H:i"),
                    'content' => $comment->getContent(),
                    'fullname' => $comment->getAuthor()->getFirstName(),
                    'upvote_count' => 1,
                    'user_has_upvoted' => false
                ];
                
                if ($comment->getParent() !== null) {
                    $item['parent'] = $comment->getParent()->getId();
                }
                
                $array[] = $item;
            }
            
            return new JsonResponse($array);
        }
       
            
        return $this->render("AppBundle:Blog:comments.html.twig", array('comments' => $comments));
    }
    
    /**
     * Add comment to post
     * 
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addCommentAction(Request $request, Post $post) {
        $data = $request->request->all();
        $comment = new Comment();
        if ($this->getUser() !== null) {
            $comment->setAuthor($this->getUser());
            $name = $this->getUser()->getFirstName().' '.$this->getUser()->getLastName();
            $email = $this->getUser()->getEmail();
        }else {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
        }
        
        $comment->setName($name);
        $comment->setEmail($email);
        $comment->setIsVisible(true);
        $comment->setPost($post);
        $comment->setContent($data['content']);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse([
                'status' => true,
                'id' => $comment->getId(),
                'created' => $comment->getCreatedAt()->format("Y-m-d"),
                'parent' => $comment->getParent() !== null ? $comment->getParent()->getId() : "",
                'content' => $comment->getContent(),
                'fullname' => $comment->getAuthor()->getFullName(),
                'upvote_count' => 1,
                'user_has_upvoted' => false
            ]);
        }
        
        return $this->redirectToRoute('app_blog_post', ['id' => $post->getId()]);
    }
    
    /**
     * Update comment
     *
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateCommentAction(Request $request, Comment $comment) {
        $data = $request->request->all();
        $comment->setIsVisible(true);
        $comment->setContent($data['content']);
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse([
                'status' => true,
                'id' => $comment->getId(),
                'created' => $comment->getCreatedAt()->format("Y-m-d"),
                'parent' => $comment->getParent() !== null ? $comment->getParent()->getId() : "",
                'content' => $comment->getContent(),
                'fullname' => $comment->getAuthor()->getFullName(),
                'upvote_count' => 1,
                'user_has_upvoted' => false
            ]);
        }
        
        return $this->redirectToRoute('app_blog_post', ['id' => $comment->getPost()->getId()]);
    }
    
    /**
     * Remove comment
     *
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentAction(Request $request, Comment $comment) {
        $data = $request->request->all();
        $comment->setIsVisible(true);
        $comment->setContent($data['content']);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        return $this->redirectToRoute('app_blog_post', ['id' => $comment->getPost()->getId()]);
    }
    
    
    /**
     * Helper sidebar recents posts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function helperSidebarRecentPostsAction(Request $request, Post $post){
        $posts = $this->getDoctrine()->getRepository("BlogBundle:Post")->customFindBy(
            null,
            null,
            [['category', $post->getCategory()->getId()], ['id', $post->getId(), '!=']],['createdAt' => 'DESC'],
            5  
        );
        
        return $this->render("AppBundle:Blog:helper_sidebar_recent_posts.html.twig", array(
            'posts' => $posts
        ));
    }
    
    /**
     * Helper sidebar categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function helperSidebarCategoriesAction(Request $request, Category $category){
        $criteria = [['id', $category->getId(), '!=']];
        if ($category->getParent() !== null){
            $criteria[] = ['parent', $category->getParent()->getId()];
        }else {
            $criteria[] = ['parent', null, 'IS NULL'];
        }
        
        $categories = $this->getDoctrine()->getRepository("BlogBundle:Category")->customFindBy(
            null, null, $criteria, ['name' => 'ASC']
        );
        
        return $this->render("AppBundle:Blog:helper_sidebar_categories.html.twig", array(
            'categories' => $categories
        ));
    }
    
    /**
     * Helper sidebar tags
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function helperSidebarTagsAction(Request $request){
        $results = $this->getDoctrine()->getRepository("BlogBundle:Post")->customFindBy(
            ['tags'], null,[['tags', null, 'IS NOT NULL']]);
        $tags = [];
        foreach ($results as $item) {
            $tags = $item['tags'] !== null ? array_unique(array_merge($tags, $item['tags'])) : $tags;
        }
        
        return $this->render("AppBundle:Blog:helper_sidebar_tags.html.twig", array(
            'tags' => $tags
        ));
    }
}
