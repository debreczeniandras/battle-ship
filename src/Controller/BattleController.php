<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Entity\Player;
use App\Entity\Shot;
use App\Form\Type\BattleType;
use App\Form\Type\GameOptionsType;
use App\Form\Type\ShotType;
use App\Service\BattleWorkflowService;
use App\Manager\BattleManager;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\FormError;
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
     * @param GameOptions           $options
     * @param Request               $request
     * @param BattleWorkflowService $workflow
     * @param BattleManager         $manager
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("options", converter="fos_rest.request_body")
     * @Rest\Post()
     * @SWG\Parameter(name="options",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="object",
     *          ref=@Model(type=GameOptions::class, groups={"Init"})
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="The Battle is set.",
     *     headers={@SWG\Header(header="Location", description="Link to created battle", type="string")},
     *     @Model(type=Battle::class, groups={"Init"})
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When a validation error has occured."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function setOptions(
        GameOptions $options,
        Request $request,
        BattleWorkflowService $workflow,
        BattleManager $manager
    ): Response {
        $form = $this->createForm(GameOptionsType::class, $options);
        $form->submit($request->request->all(), false);
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $battle = Battle::createNewFromOptions($options);
        $workflow->apply($battle, 'set_options');
        
        $manager->store($battle, 'Init');
        
        $view = $this->view($battle, 201)
                     ->setContext((new Context())->setGroups(['Init']))
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
     * @ParamConverter(name="battle", options={"requestParam": "id"})
     * @Rest\Get("/{id}", name="get_battle")
     * @SWG\Response(
     *     response=200,
     *     description="Get current infos about the battle.",
     *     @Model(type=Battle::class, groups={"Status"})
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
        $view->setContext((new Context())->setGroups(['Status']))->setFormat('json');
        
        return $this->handleView($view);
    }
    
    /**
     * Delete Battle
     *
     * @param Battle        $battle
     *
     * @param BattleManager $manager
     *
     * @return Response
     *
     * @ParamConverter(name="battle", options={"requestParam": "id"})
     * @Rest\Delete("/{id}", name="delete_battle")
     * @SWG\Response(
     *     response=204,
     *     description="The battle has been deleted."
     * )
     * @SWG\Response(
     *     response=404,
     *     description="When a battle with this id can not be found."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function deleteBattle(Battle $battle, BattleManager $manager): Response
    {
        $manager->remove($battle);
        
        return $this->handleView($this->view(null, 204)->setFormat('json'));
    }
    
    /**
     * Set up players for this Battle.
     *
     * @param Battle                $battle
     * @param Request               $request
     * @param BattleWorkflowService $workflow
     * @param BattleManager         $manager
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("battle", options={"requestParam": "id"})
     * @Rest\Put("/{id}")
     * @SWG\Parameter(name="players",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Player::class, groups={"Set"}))
     *     )
     * )
     * @SWG\Response(
     *     response=204,
     *     description="The grid has been set for the players.",
     *     headers={@SWG\Header(header="Location", description="Link to player shots", type="string")}
     * )
     * @SWG\Response(
     *     response=400,
     *     description="When a validation error has occured."
     * )
     * @SWG\Response(
     *     response=404,
     *     description="When a battle with this id can not be found."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function setShips(
        Battle $battle,
        Request $request,
        BattleWorkflowService $workflow,
        BattleManager $manager
    ): Response {
        $form = $this->createForm(BattleType::class, $battle);
        $form->submit(['players' => $request->request->all()], false);
        
        // check current workflow place
        if (!$workflow->can($battle, 'set_players')) {
            $form->addError(new FormError(sprintf('Setting the players/grid is not allowed at this state [%s].',
                                                  $battle->getState())));
        }
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $workflow->apply($battle, 'set_players');
        $manager->store($battle, 'Default');
        
        $linkToShoot = $this->generateUrl('player_shoot', ['battleId' => $battle->getId(), 'playerId' => 'A']);
        $view        = $this->view(null, 204)
                            ->setFormat('json')
                            ->setHeader('Location', $linkToShoot);
        
        return $this->handleView($view);
    }
    
    /**
     * Shoot.
     *
     * @param Battle        $battle
     * @param Shot          $shot
     * @param string        $playerId
     * @param BattleManager $manager
     *
     * @return Response
     *
     * @ParamConverter("battle", options={"requestParam": "battleId", "contextGroups": "Default"})
     * @ParamConverter("shot", converter="fos_rest.request_body")
     * @Rest\Post("/{battleId}/players/{playerId}/shots", name="player_shoot", requirements={"playerId": "(A|B)"})
     * @SWG\Parameter(name="shot",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="object",
     *          ref=@Model(type=Shot::class, groups={"Shoot"})
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Shot has been fired.",
     *     @Model(type=Shot::class, groups={"Default"}),
     *     headers={@SWG\Header(header="Location", description="Link to the shots of this player.", type="string")}
     * )
     * @SWG\Response(
     *     response=400,
     *     description="When a validation error has occured."
     * )
     * @SWG\Response(
     *     response=404,
     *     description="When a battle with this id can not be found."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function shoot(Battle $battle, string $playerId, BattleManager $manager, Shot $shot = null): Response
    {
        $form = $this->createForm(ShotType::class, $shot, ['battle' => $battle, 'playerId' => $playerId]);
        $form->submit(null, false);
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $manager->shoot($battle, $playerId, $shot);
        
        $linkToShots = $this->generateUrl('get_shots', ['battleId' => $battle->getId(), 'playerId' => $playerId]);
        $view        = $this->view($shot, 201)
                            ->setHeader('Location', $linkToShots)
                            ->setFormat('json');
        
        return $this->handleView($view);
    }
    
    /**
     * Get shots of a user.
     *
     * @param Battle $battle
     * @param string $playerId
     *
     * @return Response
     *
     * @ParamConverter("battle", options={"requestParam": "battleId"})
     * @Rest\Get("/{battleId}/players/{playerId}/shots", name="get_shots", requirements={"playerId": "(A|B)"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Shot::class, groups={"Default"}))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="When a battle with this id can not be found."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function getShots(Battle $battle, string $playerId): Response
    {
        $shots = $battle->getPlayer($playerId)->getGrid()->getShots();
        $view  = $this->view($shots, 200)
                      ->setFormat('json')
                      ->setContext((new Context())->setGroups(['Default']))->setFormat('json');
        
        return $this->handleView($view);
    }
}
