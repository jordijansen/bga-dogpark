<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;
use objects\DogCard;

class ObedientDogAbilityCommand extends BaseCommand
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

        $score = DogPark::$instance->getPlayerScore($this->playerId);
        DogPark::$instance->updatePlayerScore($this->playerId, $score + 1);

        DogPark::$instance->dogManager->createWalkingAdditionalActionsForDogsOnLead($this->playerId, 'reputation', $this->actionId);

        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('${player_name} activates <b>${dogName}: ${abilityTitle}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);
        DogPark::$instance->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($action->additionalArgs->dogId));
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);

        $score = DogPark::$instance->getPlayerScore($this->playerId);
        DogPark::$instance->updatePlayerScore($this->playerId, $score - 1);

        DogPark::$instance->dogManager->undoWalkingAdditionalActioinForDogsOnLead($this->playerId, $this->actionId);

        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('Undo: ${player_name} activates <b>${dogName}: ${abilityTitle}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);

        DogPark::$instance->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
    }
}