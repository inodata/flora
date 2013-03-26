<?php

namespace Inodata\FloraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('InodataFloraBundle:Default:index.html.twig', array('name' => $name));
    }
}
