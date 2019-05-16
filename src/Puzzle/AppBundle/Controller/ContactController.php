<?php

namespace Puzzle\AppBundle\Controller;

use Puzzle\ContactBundle\Entity\Contact;
use Puzzle\ContactBundle\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
   
    /**
     * create contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createContactAction(Request $request){
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository("ContactBundle:Group")->findOneBy(array(
            'name' => $data['promotion']
        ));
        
        if ($group === null){
            $group = new Group();
            $group->setName($data['promotion']);
            $group->setDescription($data['promotion']);
            
            $em->persist($group);
        }
        
        $isAlreadyRegistered = true;
        
        if(! $contact = $em->getRepository("ContactBundle:Contact")->findOneBy(array('email' => $data['email']))){
            $contact = new Contact();
            
            $isAlreadyRegistered = false;
            $em->persist($contact);
        }
        
        $contact->setFirstName($data['first_name']);
        $contact->setLastName($data['last_name']);
        $contact->setEmail($data['email']);
        
        if (isset($data['phone']) && $data['phone']) {
            $contact->setPhone($data['phone']);
        }
        
        if (isset($data['company']) && $data['company']) {
            $contact->setCompany($data['company']);
        }
        
        if (isset($data['position']) && $data['position']) {
            $contact->setPosition($data['position']);
        }
        
        if (isset($data['location']) && $data['location']) {
            $contact->setLocation($data['location']);
        }
        
        $group->addContact($contact->getId());
        
        $em->flush();
        
        $templateSuffix = $isAlreadyRegistered === true ? "existing" : "new";
        
        if ($request->isXmlHttpRequest() === true){
            return new JsonResponse([
                'status' => true,
                'content' => $this->renderView("AppBundle:Contact:create_contact_message_".$templateSuffix.".html.twig", ['name' => $contact->getFullName()])
            ]);
        }
        
        return $this->redirectToRoute('app_homepage');
    }
}
