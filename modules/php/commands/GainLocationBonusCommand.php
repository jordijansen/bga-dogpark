<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;

class GainLocationBonusCommand extends BaseCommand
{
    private int $playerId;
    private string $actionId;
    private string $originActionId;

    public function __construct(int $playerId, string $actionId)
    {
        $this->playerId = $playerId;
        $this->actionId = $actionId;
        $this->originActionId = AdditionalAction::newId();
    }

    public function do()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $bonusType = $action->additionalArgs->bonusType;
        $amount = property_exists($action->additionalArgs, 'amount') ? $action->additionalArgs->amount : 1;
        $locationId = DogPark::$instance->playerManager->getWalker($this->playerId)->locationArg;
        $resources = [];
        for($i = 0; $i < $amount; $i++) {
            $resources[] = $bonusType;
        }

        if (in_array($bonusType, [RESOURCE_STICK, RESOURCE_BALL, RESOURCE_TREAT, RESOURCE_TOY])) {
            DogPark::$instance->playerManager->gainResources($this->playerId, $resources);
        } else if ($bonusType == REPUTATION) {
            $playerScore = DogPark::$instance->getPlayerScore($this->playerId);
            DogPark::$instance->updatePlayerScore($this->playerId, $playerScore + sizeof($resources));
        }

        DogPark::$instance->dogManager->createWalkingAdditionalActionsForDogsOnLead($this->playerId, $bonusType, $this->originActionId);

        DogPark::$instance->notifyAllPlayers('playerGainsLocationBonusResource', clienttranslate('Location Bonus: ${player_name} gains ${resources}'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'resources' => $resources,
            'locationId' => $locationId,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);
        DogPark::$instance->actionManager->markActionPerformed($this->playerId, $this->actionId);
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $bonusType = $action->additionalArgs->bonusType;
        $amount = property_exists($action->additionalArgs, 'amount') ? $action->additionalArgs->amount : 1;
        $locationId = DogPark::$instance->playerManager->getWalker($this->playerId)->locationArg;
        $resources = [];
        for($i = 0; $i < $amount; $i++) {
            $resources[] = $bonusType;
        }

        if (in_array($bonusType, [RESOURCE_STICK, RESOURCE_BALL, RESOURCE_TREAT, RESOURCE_TOY])) {
            DogPark::$instance->playerManager->payResources($this->playerId, [$bonusType]);
        } else if ($bonusType == REPUTATION) {
            $playerScore = DogPark::$instance->getPlayerScore($this->playerId);
            DogPark::$instance->updatePlayerScore($this->playerId, $playerScore - sizeof($resources));
        }

        DogPark::$instance->dogManager->undoWalkingAdditionalActionForDogsOnLead($this->playerId, $this->originActionId);

        DogPark::$instance->notifyAllPlayers('undoPlayerGainsLocationBonusResource', clienttranslate('Undo: <s>Location Bonus: ${player_name} gains ${resources}</s>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'resources' => $resources,
            'locationId' => $locationId,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);
    }
}