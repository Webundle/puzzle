<?php

namespace Puzzle\ContactBundle\Controller;

use Puzzle\ContactBundle\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Puzzle\ContactBundle\Form\Type\GroupFormType;

/**
 * @author AGNES Gnagne Cedric <cecenho55gmail.com>
 */
class GroupController extends Controller
{
	/***
	 * Show groups
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showListAction(Request $request) {
		$groups = $this->getDoctrine()->getRepository("ContactBundle:Group")->findBy([], ['name' => 'ASC']);
		return $this->render("ContactBundle:Group:list.html.twig", array(
			'groups' => $groups,
		));
	}
	
	/***
	 * Show group
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showAction(Request $request, Group $group) {
	    $contacts = $group->getContacts();
	    $list = null;
	    
	    if ($contacts !== null) {
	        foreach ($group->getContacts() as $key => $contact){
	            $list = $key <= 0 ? "'".$contact."'": $list.','."'".$contact."'";
	        }
	        
	        $contacts = $this->getDoctrine()->getRepository("ContactBundle:Contact")->customFindBy(
	            null, null, [['id', null, 'IN ('.$list.')']], ["firstName" => "ASC"]
	        );
	    }
	    
		return $this->render("ContactBundle:Group:view.html.twig", array(
			'group' => $group,
			'contacts' => $contacts,
		));
	}
	
	/***
	 * Create group
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createAction(Request $request){
	    $group = new Group();
	    $form = $this->createForm(GroupFormType::class, $group, [
	        'method' => 'POST',
	        'action' => $this->generateUrl('admin_contact_group_create')
	    ]);
	    $form->handleRequest($request);
	    
	    if ($form->isSubmitted() === true && $form->isValid() === true) {
	        $data = $request->request->all()['group_form'];
	        $group->setContacts($data['contacts']);
	        
	        $em = $this->getDoctrine()->getManager();
	        $em->persist($group);
	        $em->flush();
	        
	        if ($request->isXmlHttpRequest() === true) {
	            return new JsonResponse(['status' => true]);
	        }
	        
	        return $this->redirectToRoute('admin_contact_groups');
	    }
	    
		return $this->render("ContactBundle:Group:create.html.twig", [
		    'form' => $form->createView(),
		]);
	}
	
	/***
	 * Update group
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function updateAction(Request $request, Group $group){
	    $form = $this->createForm(GroupFormType::class, $group, [
	        'method' => 'POST',
	        'action' => $this->generateUrl('admin_contact_group_update', ['id' => $group->getId()])
	    ]);
	    $form->handleRequest($request);
	    
	    $contacts = $group->getContacts();
	    $list = null;
	    
	    if ($contacts !== null) {
	        foreach ($group->getContacts() as $key => $contact){
	            $list = $key <= 0 ? "'".$contact."'": $list.','."'".$contact."'";
	        }
	        
	        $contacts = $this->getDoctrine()->getRepository("ContactBundle:Contact")->customFindBy(
	            null, null, [['id', null, 'IN ('.$list.')']]
	            );
	    }
	    
	    if ($form->isSubmitted() === true && $form->isValid() === true) {
	        $data = $request->request->all();
	        $em = $this->getDoctrine()->getManager();
	        
	        if (isset($data['contacts']) && $data['contacts'] != ""){
	            $contactsId = explode(',', ($data['contacts']));
	            
	            foreach ($contactsId as $contactId) {
	                if ($contactId !== "" && $contactId !== null) {
	                    $group->addContact($contactId);
	                }
	            }
	        }
	        
	        $em->flush();
	        
	        if ($request->isXmlHttpRequest() === true) {
	            return new JsonResponse(['status' => true]);
	        }
	    }
	    
	    return $this->render("ContactBundle:Group:update.html.twig", [
	        'group' => $group,
	        'contacts' => $contacts,
	        'form' => $form->createView(),
	    ]);
	}
	
	
	/**
	 * Remove a group
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function removeAction(Request $request, Group $group) {
		$em = $this->getDoctrine()->getManager();
		$em->remove($group);
		$em->flush();
		
		if ($request->isXmlHttpRequest() === true) {
		    return new JsonResponse(['status' => true]);
		}
		
		return $this->redirectToRoute('admin_contact_groups');
	}
	
	/***
	 * Remove groups
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function removeListAction(Request $request)
	{
		$list = explode(',', $request->request->get('ids'));
		$em = $this->getDoctrine()->getManager();
		
		foreach ($list as $id){
			if($id != null){
				$group = $em->getRepository("ContactBundle:Group")->find($id);
				$em->remove($group);
			}
		}
		
		$em->flush();
		
		if ($request->isXmlHttpRequest() === true) {
		    return new JsonResponse(['status' => true]);
		}
		
		return $this->redirectToRoute('admin_contact_groups');
	}
	
}
