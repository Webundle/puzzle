<?php

namespace Puzzle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request){
        return $this->redirectToRoute('app_static_page', ['slug' => 'accueil']);
    }
}
