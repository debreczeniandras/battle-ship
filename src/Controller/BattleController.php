<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Entity\Player;
use App\Form\Type\GameOptionsType;
use App\Form\Type\PlayerType;
use App\Repository\BattleRepository;
use FOS\RestBundle\Context\Context;
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
     *     headers={@SWG\Header(header="Location", description="Link to created resource", type="string")},
     *     @Model(type=Battle::class, groups={"init"})
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When a validation error has occured."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function setOptions(GameOptions $options, BattleRepository $repository): Response
    {
        $form = $this->createForm(GameOptionsType::class, $options);
        $form->submit(null, false);
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $battle = $repository->createNewFromOptions($form->getData());
        $view   = $this->view($battle, 201)
                       ->setContext((new Context())->setGroups(['init']))
                       ->setHeader('Location', $this->generateUrl('get_battle', ['id' => $battle->getId()]))
                       ->setFormat('json');
        
        return $this->handleView($view);
    }
    
    /**
     * Get infos about the battle
     *
     * @param Battle $battle
     *
     * @return FormInterface|Response
     *
     * @ParamConverter(name="battle", options={"requestParam": "id", "contextGroups": {"init"}})
     * @Rest\Get("/{id}", name="get_battle")
     * @SWG\Response(
     *     response=200,
     *     description="Get current infos about the battle.",
     *     @Model(type=Battle::class, groups={"init"})
     * )
     * @SWG\Response(
     *     response=404,
     *     description="When a battle with this id can not be found."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function getBattle(Battle $battle): Response
    {
        $view = $this->view($battle, 200);
        $view->setContext((new Context())->setGroups(['init']))->setFormat('json');
        
        return $this->handleView($view);
    }
    
    /**
     * Set up ships for a player
     *
     * @param Battle $battle
     * @param Player $player
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("player", converter="fos_rest.request_body", options={"deserializationContext"={"groups"={"place"}}})
     * @ParamConverter("battle", options={"requestParam": "id"})
     * @Rest\Post("/{id}/players/")
     * @SWG\Parameter(name="player",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="object",
     *          ref=@Model(type=Player::class, groups={"place"})
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="The grid has been set for the player.",
     *     headers={@SWG\Header(header="Location", description="Link to created resource", type="string")},
     *     @Model(type=Battle::class, groups={"place"})
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When a validation error has occured."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function setShips(Battle $battle, Player $player, Request $request): Response
    {
        $form = $this->createForm(PlayerType::class, $player);
        $form->submit(null, false);
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $view   = $this->view($battle, 201)
                       ->setContext((new Context())->setGroups(['init']))
                       ->setHeader('Location', $this->generateUrl('get_battle', ['id' => $battle->getId()]))
                       ->setFormat('json');
        
        return $this->handleView($view);
    }
}
