<?php

namespace Puzzle\ContactBundle\Controller;

use Puzzle\MediaBundle\Entity\File;
use Puzzle\ContactBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\AdminBundle\Util\DoctrineQueryParameterUtil;
use Puzzle\ContactBundle\Form\Type\ContactFormType;
use Doctrine\DBAL\Types\TextType;
use Puzzle\MediaBundle\Util\MediaUtil;
use Puzzle\ContactBundle\Entity\Group;

class ContactController extends Controller
{
    /***
     * Show Contacts
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showListAction(Request $request) {
        /** @var EntityRepository $er */
        $er = $this->getDoctrine()->getRepository("ContactBundle:Contact");
        
        if ($request->isMethod('POST') === true && $request->isXmlHttpRequest() === true) {
            $criteria = [];
            $contacts = null;
            
            if ($request->request->get('ids') !== null) {
                $list = DoctrineQueryParameterUtil::formatForInClause($request->request->get('ids'));
                $criteria[] = ['id', null, 'NOT IN '.$list];
            }
            
            $contacts = $er->customFindBy(null, null, $criteria);
            $array = [];
            foreach ($contacts as $contact) {
                $array[] = [
                    'id' => $contact->getId(),
                    'name' => $contact->getFullName(),
                    'email' => $contact->getEmail()
                ];
            }
            
            return new JsonResponse($array);
        }
        
    	return $this->render("ContactBundle:Contact:list.html.twig", array(
    	    'contacts' => $er->findAll()
    	));
    }
    
    
    /***
     * Show Contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Contact $contact){
        return $this->render("ContactBundle:Contact:view.html.twig", [
            'contact' => $contact
        ]);
    }
    
    
    /***
     * Create Contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request){
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_contact_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_contacts');
        }
        
        return $this->render("ContactBundle:Contact:create.html.twig", [
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Update Contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Contact $contact){
        $form = $this->createForm(ContactFormType::class, $contact, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_contact_update', ['id' => $contact->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_contacts');
        }
        
        return $this->render("ContactBundle:Contact:update.html.twig", [
            'contact' => $contact,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Export contacts
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function exportAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$contacts = null;
    	$date = new \DateTime();
    	$basename = $date->getTimestamp().'.csv';
    	
    	if ($groupId =  $request->query->get('group')) {
    	    $group = $em->getRepository("ContactBundle:Group")->find($groupId);
    	    
    	    if ($group !== null) {
    	        $basename = $group->getName().'.csv';
    	        if ($group->getContacts() !== null) {
    	            $list = DoctrineQueryParameterUtil::formatForInClause($group->getContacts());
    	            $contacts = $this->getDoctrine()->getRepository("ContactBundle:Contact")->customFindBy(
    	                null, null, [['id', null, 'IN '.$list]]
    	                );
    	        }
    	    }
    	}else {
    	    $contacts = $this->getDoctrine()->getRepository('ContactBundle:Contact')->findAll();
    	}
    	
    	$fs = new Filesystem();
    	$dirname = File::getBaseDir().File::getBasePath().'/contact/contacts';
    	
    	if(!$fs->exists($dirname)){
    	    $fs->mkdir($dirname);
    	}
    	
    	if ($contacts !== null ){
    	    $filename = $dirname.'/'.$basename;
    	    $fp = fopen($filename, 'w');
    	    fputcsv($fp, [
    	        $this->get('translator')->trans('contact.property.contact.full_name', [], 'messages'),
    	        $this->get('translator')->trans('contact.property.contact.email', [], 'messages'),
    	        $this->get('translator')->trans('contact.property.contact.phone', [], 'messages'),
    	        $this->get('translator')->trans('contact.property.contact.company', [], 'messages'),
    	        $this->get('translator')->trans('contact.property.contact.position', [], 'messages'),
    	        $this->get('translator')->trans('contact.property.contact.location', [], 'messages'),
    	        $this->get('translator')->trans('contact.property.contact.picture', [], 'messages'),
    	    ]);
    	    foreach ($contacts as $contact){
    	        fputcsv($fp, [
    	            $contact->getFullName(),
    	            $contact->getEmail(),
    	            $contact->getPhone(),
    	            $contact->getCompany(),
    	            $contact->getPosition(),
    	            $contact->getLocation(),
    	            $contact->getPicture()
    	        ]);
    	    }
    	    
    	    fclose($fp);
    	}
    	
    	$route = File::getBasePath().'/contact/contacts/'.$basename;
    	
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse([
    	        'status' => true,
    	        'href' => $route
    	    ]);
    	}
    	
    	return $this->redirect($route);
    }
    
    /**
     * Import contacts
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function importAction(Request $request) {
        if ($request->isMethod("POST") === true) {
            $folder = $this->get('media.file_manager')->createFolder(MediaUtil::extractContext(Contact::class), $this->getUser(), true);
            $media = $this->get('media.upload_manager')->prepareUpload($_FILES, $folder, $this->getUser());
            
            $filename = $media[0]->getAbsolutePath();
            $fp = fopen($filename, 'r');
            
            $count = 0;
            
            $em = $this->getDoctrine()->getManager();
            $group = null;
            
            while (feof($fp) === false) {
                $row = fgetcsv($fp);
                
                if (is_array($row) && (int)$row[0] !== false) {
                    $row[5] = trim($row[5]);
                    $contact = $em->getRepository('ContactBundle:Contact')->findOneBy(['email' => $row[5]]);
                    if ($row[5] === "" || $contact === null) {
                        $contact = new Contact();
                        $em->persist($contact);
                    }
                    
                    $contact->setFirstName(trim($row[1]));
                    $contact->setLastName(trim($row[2]));
                    $contact->setCompany(trim($row[4]));
                    $contact->setEmail(trim($row[5]));
                    $contact->setPhone(trim($row[6]));
                    $contact->setLocation(trim($row[7]));
                    
                    $em->flush();
                    
                    $group = $em->getRepository('ContactBundle:Group')->findOneBy(array('name' => $row[3]));
                    if ($group === null) {
                        $group = new Group();
                        $group->setName($row[3]);
                        
                        $em->persist($group);
                    }
                    
                    $group->addContact($contact->getId());
                    
                    $em->flush();
                    $count++;
                }
            }
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('contact_contacts');
        }
        
        return $this->render("ContactBundle:Contact:import.html.twig");
        
    }
    
    /**
     * Remove a contact
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, Contact $contact) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        return $this->redirectToRoute('admin_contacts');
    }
    
    /***
     * Remove contacts
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
    			$contact = $em->getRepository("ContactBundle:Contact")->find($id);
    			$em->remove($contact);
    		}
    	}
    	
    	$em->flush();
    	
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse(['status' => true]);
    	}
    	
    	return $this->redirectToRoute('admin_contacts');
    }
}
