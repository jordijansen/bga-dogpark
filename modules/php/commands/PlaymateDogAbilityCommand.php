<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;
use objects\DogCard;

class PlaymateDogAbilityCommand extends BaseCommand
{
    private int $playerId;
    private string $actionId;

    public function __construct(int $playerId, string $actionId)
    {
        $this->playerId = $playerId;
        $this->actionId = $actionId;
    }

    public function do()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($action->additionalArgs->dogId));
        DogPark::$instance->actionManager->markActionPerformed($this->playerId, $this->actionId);

        DogPark::$instance->dogManager->createWalkingAdditionalActionsForDogsOnLead($this->playerId, 'swap', $this->actionId);

        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('${player_name} activates <b>${dogName}: ${abilityTitle}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle
        ]);

        DogPark::$instance->setGlobalVariable(STATE_AFTER_SWAP, ST_WALKING_MOVE_WALKER_AFTER);
        DogPark::$instance->setGlobalVariable(CURRENT_ACTION_ID, $this->actionId);
        DogPark::$instance->gamestate->jumpToState(ST_ACTION_SWAP);
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($action->additionalArgs->dogId));
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);

        DogPark::$instance->dogManager->undoWalkingAdditionalActioinForDogsOnLead($this->playerId, $this->actionId);

        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('Undo: ${player_name} activates <b>${dogName}: ${abilityTitle}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle
        ]);

        DogPark::$instance->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
    }
}