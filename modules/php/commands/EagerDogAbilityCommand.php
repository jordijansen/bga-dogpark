<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;
use objects\DogCard;

class EagerDogAbilityCommand extends BaseCommand
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

        $resources = [$dog->eagerResource];
        DogPark::$instance->playerManager->gainResources($this->playerId, $resources);

        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('${player_name} activates <b>${dogName}: ${abilityTitle}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle,
            'gainedResources' => $resources
        ]);

        DogPark::$instance->gamestate->setPrivateState($this->playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($action->additionalArgs->dogId));
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);

        $resources = [$dog->eagerResource];

        DogPark::$instance->playerManager->payResources($this->playerId, $resources);
        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('Undo: <s>${player_name} activates <b>${dogName}: ${abilityTitle}</b></s>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle,
            'lostResources' => $resources
        ]);

        DogPark::$instance->gamestate->setPrivateState($this->playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }
}