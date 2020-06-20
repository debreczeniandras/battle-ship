<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Entity\Player;
use App\Form\Type\BattleType;
use App\Form\Type\GameOptionsType;
use App\Form\Type\PlayerType;
use App\Helper\BattleWorkflowHelper;
use App\Repository\BattleRepository;
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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Rest\Route("/battles")
 */
class BattleController extends AbstractFOSRestController
{
    /**
     * Set up and prepare for ships.
     *
     * @param GameOptions          $options
     * @param Request              $request
     * @param BattleWorkflowHelper $workflow
     * @param BattleRepository     $repository
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
     *     @Model(type=Battle::class)
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
        BattleWorkflowHelper $workflow,
        BattleRepository $repository
    ): Response {
        $form = $this->createForm(GameOptionsType::class, $options);
        $form->submit($request->request->all(), false);
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $battle = Battle::createNewFromOptions($options);
        $workflow->apply($battle, 'set_options');
        
        $repository->store($battle, 'Init');
        
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
     *     @Model(type=Battle::class, groups={"Default"})
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
        $view->setContext((new Context())->setGroups(['Default']))->setFormat('json');
        
        return $this->handleView($view);
    }
    
    /**
     * Set up ships for a player
     *
     * @param Battle               $battle
     * @param Request              $request
     * @param BattleWorkflowHelper $workflow
     * @param BattleRepository     $repository
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("battle", options={"requestParam": "id"})
     * @Rest\Post("/{id}")
     * @SWG\Parameter(name="players",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Player::class, groups={"place"}))
     *     )
     * )
     * @SWG\Response(
     *     response=204,
     *     description="The grid has been set for the players.",
     *     headers={@SWG\Header(header="Location", description="Link to created resource", type="string")}
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When a validation error has occured."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function setShips(
        Battle $battle,
        Request $request,
        BattleWorkflowHelper $workflow,
        BattleRepository $repository
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
        $repository->store($battle, 'Default');
        
        return $this->handleView($this->view(null, 204)->setFormat('json'));
    }
    
    /**
     * Get player data
     *
     * @param Battle           $battle
     * @param Player           $player
     *
     * @param Request          $request
     * @param BattleRepository $repository
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("battle", options={"requestParam": "battleId"})
     * @Rest\GET("/{battleId}/players/{id}", name="get_player")
     * @SWG\Response(
     *     response=200,
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
    public function getPlayer(Battle $battle, Player $player, Request $request, BattleRepository $repository): Response
    {
        $form = $this->createForm(PlayerType::class, $player, ['battle' => $battle]);
        $form->submit($request->request->all(), false);
        
        if ($battle->hasPlayer($player)) {
            $form->addError(new FormError('This user has already been added.'));
        }
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $battle->addPlayer($player);
        $repository->store($battle, 'Default');
        
        $view = $this->view($battle, 201)
                     ->setHeader('Location', $this->generateUrl('get_battle', ['id' => $battle->getId()]))
                     ->setFormat('json');
        
        return $this->handleView($view);
    }
}
