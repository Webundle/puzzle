<?php

namespace Puzzle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\CalendarBundle\Entity\Moment;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author qwincy <qwincypercy@fermentuse.com>
 */
class CalendarController extends Controller
{
    /**
     * Show moments
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showMomentsAction(Request $request){
        $em = $this->getDoctrine();
    	$moments = $em->getRepository("CalendarBundle:Moment")->findAll();
    	
    	if($request->getMethod() == "POST"){
    	    $data = $request->request->all();
    	    $criteria = $response = [];
    	    
    	    if (isset($data['agenda_slug']) === true){
    	        $agenda = $em->getRepository("CalendarBundle:Agenda")->findOneBy(['slug' => $data['agenda_slug']]);
    	        if ($agenda !== null){
    	           $criteria['agenda'] = $agenda->getId();
    	        }
    	    }
    	    
    	    $moments = $em->getRepository("CalendarBundle:Moment")->findBy($criteria);
    	    
    	    foreach ($moments as $moment){
    	        $response[] = [
    	            'id' => $moment->getId(),
    	            'name' => $moment->getTitle(),
    	            'startdate' => $moment->getStartedAt()->format("Y-m-d"),
    	            'enddate' => $moment->getEndedAt()->format("Y-m-d"),
    	            'starttime' => $moment->getStartedAt()->format("H:i"),
    	            'endtime' => $moment->getEndedAt()->format("H:i"),
    	            'color' => $moment->getColor(),
    	            'url' => $this->generateUrl('admin_calendar_moment', array('id' => $moment->getId()))
    	        ];
    	    }
    	    
    	    return new JsonResponse($response);
    	}
    	
    	return $this->render("AppBundle:Calendar:moments.html.twig", array(
    	    'moments' => $moments
    	));
    }
    
    /**
     * Show moment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showMomentAction(Request $request, Moment $moment){
        return $this->render("AppBundle:Calendar:moment.html.twig", array(
            'moment' => $moment
        ));
    }
    
    /**
     * Helper for upcoming moments
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function helperUpcomingMomentsAction(Request $request){
        $now = new \DateTime();
        $moments = $this->getDoctrine()->getRepository("CalendarBundle:Moment")->customFindBy(
            null, 
            null, 
            [['startedAt', $now->format("Y-m-d H:i:s"), '>=']],
            ['isOnFront' => 'DESC', 'startedAt' => 'ASC'],
            5
        );
        
        return $this->render("AppBundle:Calendar:helper_upcoming_moments.html.twig", array(
            'moments' => $moments
        ));
    }
}
