<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Form\Type\BattleType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BattleController extends AbstractController
{
    /**
     * The first entry point to the game.
     *
     * @param Battle $battle
     *
     * @Rest\Post("start")
     *
     * @SWG\Parameter(name="battle",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="array",
     *          @Model(type=Battle::class, groups={"place"})
     *     )
     * )
     * @SWG\Response(
     *     response=204,
     *     description="The Battle is set."
     * )
     */
    public function start(Battle $battle)
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
