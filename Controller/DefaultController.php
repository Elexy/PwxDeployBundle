<?php

namespace Pwx\DeployBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('PwxDeployBundle:Default:index.html.twig', array('name' => $name));
    }
}
