<?php

namespace Puzzle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class StaticController extends Controller
{
    /**
     * Show page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPageAction(Request $request, $slug){
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository("StaticBundle:Page")->findOneBy(['slug' => $slug]);
        $name = $page->getName();
        $content = $page->getContent();
        $slug = $slug;
        
        if ($page === null || $request->getLocale() != $this->getParameter('locale')) {
            $translation = $em->getRepository("TranslationBundle:Translation")->customFindBy(
                null, null, [['field', 'slug'], ['value', $slug], ['locale', 'ASC']], null, null, null, true
                );
            
            $translations = null;
            $translation = $em->getRepository("TranslationBundle:Translation")->customFindOneBy(
                null, null, [['field', 'slug'], ['value', $slug], ['locale', 'ASC']], null, null, null, true
                );
            
            $translations = null;
            if ($translation !== null){
                $page = $em->getRepository($translation->getTranslatableClass())->customFindOneBy(
                    null, null, [['id', $translation->getTranslatableId()]], null, null, null, true
                );
                $translations = $this->get('translation.translator')->findTranslations($page, $request->getLocale(), true);
                
                if ($translations !== null && isset($translations[0])){
                    if (isset($translations[0]['slug']) && $translations[0]['slug'] !== null){
                        $slug = $translations[0]['slug'];
                    }
                    
                    if (isset($translations[0]['name']) && $translations[0]['name'] !== null){
                        $name = $translations[0]['name'];
                    }
                    
                    if (isset($translations[0]['content']) && $translations[0]['content'] !== null){
                        $content = $translations[0]['content'];
                    }
                }
            }
        }
        
        $templateView = "default";
        if ($page->getTemplate() !== null){
            $templateView = $page->getTemplate()->getSlug();
        }
        
        // Default page view
    	return $this->render("AppBundle:Static:".$templateView.".html.twig", array(
    	    'page'         => $page,
    	    'name'         => $name,
    	    'content'      => $content,
    	    'slug'         => $slug
    	));
    }
}
