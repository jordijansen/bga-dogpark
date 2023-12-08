<?php

namespace commands;

use DogPark;
use objects\DogCard;

class ShowOffDogAbilityCommand extends BaseCommand
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
            'i18n' => ['dogName', 'abilityTitle'],
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);

        DogPark::$instance->gamestate->setPrivateState($this->playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($action->additionalArgs->dogId));
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);

        $score = DogPark::$instance->getPlayerScore($this->playerId);
        DogPark::$instance->updatePlayerScore($this->playerId, $score - 1);

        DogPark::$instance->dogManager->undoWalkingAdditionalActionForDogsOnLead($this->playerId, $this->actionId);

        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('Undo: <s>${player_name} activates <b>${dogName}: ${abilityTitle}</b></s>'),[
            'i18n' => ['dogName', 'abilityTitle'],
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);

        DogPark::$instance->gamestate->setPrivateState($this->playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }
}