<?php

namespace App\Controller;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Swatch\Bundle\ApiBundle\Filter\RestPaginationParams;
use Swatch\Bundle\ApiBundle\Form\Type\RestPaginationFilterType;
use Swatch\Bundle\IntlBundle\Entity\Continent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BattleController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return new Response('asdfadsf');
        
//        $form = $this->createForm(RestPaginationFilterType::class);
//        $form->submit($request->query->all(), false);
//
//        if (!$form->isValid()) {
//            return $form;
//        }
//
//        /** @var RestPaginationParams $params */
//        $params = $form->getData();
//
//        $objectManager = $this->getDoctrine()->getManagerForClass(Continent::class);
//        $rep           = $objectManager->getRepository(Continent::class);
//        $continents    = $rep->findAll('order', $params->getLocale());
//
//        /** @var Pagerfanta $pagerfanta */
//        $pagerfanta = new Pagerfanta(new ArrayAdapter($continents));
//        $pagerfanta->setCurrentPage($params->getPage());
//        $pagerfanta->setMaxPerPage($params->getLimit());
//
//        $view = $this->view($pagerfanta->getCurrentPageResults(), 200, $this->getPaginationHeaders($pagerfanta));
//
//        return $this->handleView($view);
        
    }
}
