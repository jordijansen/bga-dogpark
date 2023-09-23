<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;
use ForecastManager;
use objects\DogCard;

class ActivateForecastCardCommand extends BaseCommand
{
    private int $playerId;
    private ?string $actionId;
    private array $resources;
    private int $reputation;
    private string $doLogMessage;
    private string $undoLogMessage;

    public function __construct(int $playerId, ?string $actionId, array $resources, int $reputation, string $doLogMessage, string $undoLogMessage)
    {
        $this->playerId = $playerId;
        $this->actionId = $actionId;
        $this->resources = $resources;
        $this->reputation = $reputation;
        $this->doLogMessage = $doLogMessage;
        $this->undoLogMessage = $undoLogMessage;
    }

    public function do()
    {
        if ($this->actionId != null) {
            DogPark::$instance->actionManager->markActionPerformed($this->playerId, $this->actionId);
        }

        if (sizeof($this->resources) > 0) {
            foreach ($this->resources as $resource) {
                if ($resource !== REPUTATION) {
                    DogPark::$instance->playerManager->gainResources($this->playerId, [$resource]);
                } else {
                    DogPark::$instance->updatePlayerScore($this->playerId, DogPark::$instance->getPlayerScore($this->playerId) + 1);
                }
            }
        }

        if ($this->reputation > 0) {
            DogPark::$instance->updatePlayerScore($this->playerId, DogPark::$instance->getPlayerScore($this->playerId) + $this->reputation);
        }

        DogPark::$instance->notifyAllPlayers('activateForecastCard', $this->doLogMessage, [
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'forecastCard' => DogPark::$instance->forecastManager->getCurrentForecastCard(),
            'gainedResources' => $this->resources,
            'gainedReputation' => $this->reputation,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);
    }

    public function undo()
    {
        if ($this->actionId != null) {
            DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);
        }

        if (sizeof($this->resources) > 0) {
            foreach ($this->resources as $resource) {
                if ($resource !== REPUTATION) {
                    DogPark::$instance->playerManager->payResources($this->playerId, [$resource]);
                } else {
                    DogPark::$instance->updatePlayerScore($this->playerId, DogPark::$instance->getPlayerScore($this->playerId) - 1);
                }
            }
            if ($this->actionId != null && DogPark::$instance->getGlobalVariable(CURRENT_PHASE) == PHASE_WALKING) {
                DogPark::$instance->dogManager->undoWalkingAdditionalActionForDogsOnLead($this->playerId, $this->actionId);
            }
        }

        if ($this->reputation > 0) {
            DogPark::$instance->updatePlayerScore($this->playerId, DogPark::$instance->getPlayerScore($this->playerId) - $this->reputation);
        }


        DogPark::$instance->notifyAllPlayers('activateForecastCard', $this->undoLogMessage, [
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'forecastCard' => DogPark::$instance->forecastManager->getCurrentForecastCard(),
            'lostResources' => $this->resources,
            'lostReputation' => $this->reputation,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);
    }
}