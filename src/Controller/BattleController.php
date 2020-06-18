<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BattleController extends AbstractController
{
    /**
     * @Rest\Get("start")
     */
    public function place()
    {
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
