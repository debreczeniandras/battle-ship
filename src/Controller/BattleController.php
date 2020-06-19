<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Form\Type\BattleType;
use App\Form\Type\GameOptionsType;
use App\Repository\BattleRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Route("/battles")
 */
class BattleController extends AbstractFOSRestController
{
    /**
     * Set up and prepare for ships.
     *
     * @param GameOptions      $options
     * @param BattleRepository $repository
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("options", converter="fos_rest.request_body")
     * @Rest\Post("/")
     * @SWG\Parameter(name="options",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="object",
     *          ref=@Model(type=GameOptions::class)
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="The Battle is set.",
     *     @Model(type=Battle::class, groups={"init"})
     * )
     */
    public function setOptions(GameOptions $options, BattleRepository $repository): Response
    {
        $form = $this->createForm(GameOptionsType::class, $options);
        $form->submit(null, false);
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $battle = $repository->createNewFromOptions($form->getData());
        $view   = $this->view($battle, 201, ['Location', '/battles/' . $battle->getId()]);
        
        return $this->handleView($view);
    }
    
    /**
     * Set up ships and start the game
     *
     * @param Battle $battle
     *
     * @Rest\Post("/{id}")
     *
     * @SWG\Parameter(name="battle",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="array",
     *          @Model(type=BattleType::class, groups={"place"})
     *     )
     * )
     * @SWG\Response(
     *     response=204,
     *     description="The Battle is set."
     * )
     */
    public function placeShips(Battle $battle)
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
