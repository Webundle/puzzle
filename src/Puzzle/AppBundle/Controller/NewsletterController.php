<?php

namespace Puzzle\AppBundle\Controller;

use Puzzle\NewsletterBundle\NewsletterEvents;
use Puzzle\NewsletterBundle\Entity\Subscriber;
use Puzzle\NewsletterBundle\Event\SubscriberEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends Controller
{
    /**
     * Add Subscriber
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSubscriberAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        
        if(! $subscriber = $em->getRepository("NewsletterBundle:Subscriber")->findOneBy(array('email' => $data['email']))){
            
            $subscriber = new Subscriber();
            $subscriber->setEmail($data['email']);
            
            if (isset($data['name']) === true){
                $subscriber->setName($data['name']);
            }
            
            $em->persist($subscriber);
            
            $em->flush();
            
            $event = new SubscriberEvent($subscriber);
            $this->get('event_dispatcher')->dispatch(NewsletterEvents::NEW_SUBSCRIBER, $event);
            
            return new JsonResponse([
                'status' => 200, 
                'content' => '<p>Vos informations ont bien été ajoutées à notre liste.</p>'
            ]);
        }
        
        return new JsonResponse([
            'status' => 400,
            'content' => '<p>Cet email existe déjà dans notre liste.</p>'
        ]);
    }
}
