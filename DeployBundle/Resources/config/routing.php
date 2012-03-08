<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('PwxDeployBundle_homepage', new Route('/hello/{name}', array(
    '_controller' => 'PwxDeployBundle:Default:index',
)));

return $collection;
