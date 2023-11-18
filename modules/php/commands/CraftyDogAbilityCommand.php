<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;
use objects\DogCard;

class CraftyDogAbilityCommand extends BaseCommand
{
    private int $playerId;
    private string $actionId;
    private string $resource;

    public function __construct(int $playerId, string $actionId, string $resource)
    {
        $this->playerId = $playerId;
        $this->actionId = $actionId;
        $this->resource = $resource;
    }

    public function do()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($action->additionalArgs->dogId));
        DogPark::$instance->actionManager->markActionPerformed($this->playerId, $this->actionId);

        $gainedResources = [$dog->craftyResource];
        $lostResources = [$this->resource];
        DogPark::$instance->playerManager->gainResources($this->playerId, $gainedResources);
        DogPark::$instance->playerManager->payResources($this->playerId, $lostResources);

        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('${player_name} activates <b>${dogName}: ${abilityTitle}</b>'),[
            'i18n' => ['dogName', 'abilityTitle'],
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle,
            'gainedResources' => $gainedResources,
            'lostResources' => $lostResources,
        ]);

        DogPark::$instance->gamestate->setPrivateState($this->playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($action->additionalArgs->dogId));
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);

        $gainedResources = [$this->resource];
        $lostResources = [$dog->craftyResource];
        DogPark::$instance->playerManager->gainResources($this->playerId, $gainedResources);
        DogPark::$instance->playerManager->payResources($this->playerId, $lostResources);

        DogPark::$instance->notifyAllPlayers('activateDogAbility', clienttranslate('Undo: <s>${player_name} activates <b>${dogName}: ${abilityTitle}</b></s>'),[
            'i18n' => ['dogName', 'abilityTitle'],
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'abilityTitle' => $dog->abilityTitle,
            'gainedResources' => $gainedResources,
            'lostResources' => $lostResources,
        ]);

        DogPark::$instance->gamestate->setPrivateState($this->playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }
}