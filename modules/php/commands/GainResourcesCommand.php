<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;
use ForecastManager;
use objects\DogCard;

class GainResourcesCommand extends BaseCommand
{
    private int $playerId;
    private string $actionId;
    private array $resources;
    private string $doLogMessage;
    private string $undoLogMessage;

    public function __construct(int $playerId, string $actionId, array $resources, string $doLogMessage, string $undoLogMessage)
    {
        $this->playerId = $playerId;
        $this->actionId = $actionId;
        $this->resources = $resources;
        $this->doLogMessage = $doLogMessage;
        $this->undoLogMessage = $undoLogMessage;
    }

    public function do()
    {
        DogPark::$instance->actionManager->markActionPerformed($this->playerId, $this->actionId);

        DogPark::$instance->playerManager->gainResources($this->playerId, $this->resources);

        DogPark::$instance->notifyAllPlayers('activateForecastCard', $this->doLogMessage, [
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'forecastCard' => DogPark::$instance->forecastManager->getCurrentForecastCard(),
            'gainedResources' => $this->resources
        ]);

        DogPark::$instance->gamestate->setPrivateState($this->playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }

    public function undo()
    {
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);

        DogPark::$instance->playerManager->payResources($this->playerId, $this->resources);

        DogPark::$instance->notifyAllPlayers('activateForecastCard', $this->undoLogMessage, [
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'forecastCard' => DogPark::$instance->forecastManager->getCurrentForecastCard(),
            'lostResources' => $this->resources
        ]);

        DogPark::$instance->gamestate->setPrivateState($this->playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }
}