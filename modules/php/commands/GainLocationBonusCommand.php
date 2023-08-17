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
        $locationId = DogPark::$instance->playerManager->getWalker($this->playerId)->locationArg;

        if ($action->additionalArgs->extraBonus) {
            DogPark::$instance->dogWalkPark->removeExtraLocationBonus($locationId, $bonusType);
        }

        if (in_array($bonusType, [RESOURCE_STICK, RESOURCE_BALL, RESOURCE_TREAT, RESOURCE_TOY])) {
            DogPark::$instance->playerManager->gainResources($this->playerId, [$bonusType]);
        } else if ($bonusType == REPUTATION) {
            $playerScore = DogPark::$instance->getPlayerScore($this->playerId);
            DogPark::$instance->updatePlayerScore($this->playerId, $playerScore + 1);
        }

        DogPark::$instance->dogManager->createWalkingAdditionalActionsForDogsOnLead($this->playerId, $bonusType, $this->originActionId);

        DogPark::$instance->notifyAllPlayers('playerGainsLocationBonusResource', clienttranslate('Location Bonus: ${player_name} gains ${resource}'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'resource' => $bonusType,
            'locationId' => $locationId,
            'extraBonus' => $action->additionalArgs->extraBonus,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);
        DogPark::$instance->actionManager->markActionPerformed($this->playerId, $this->actionId);
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $bonusType = $action->additionalArgs->bonusType;
        $locationId = DogPark::$instance->playerManager->getWalker($this->playerId)->locationArg;

        if ($action->additionalArgs->extraBonus) {
            DogPark::$instance->dogWalkPark->addExtraLocationBonus($locationId, $bonusType);
        }

        if (in_array($bonusType, [RESOURCE_STICK, RESOURCE_BALL, RESOURCE_TREAT, RESOURCE_TOY])) {
            DogPark::$instance->playerManager->payResources($this->playerId, [$bonusType]);
        } else if ($bonusType == REPUTATION) {
            $playerScore = DogPark::$instance->getPlayerScore($this->playerId);
            DogPark::$instance->updatePlayerScore($this->playerId, $playerScore - 1);
        }

        DogPark::$instance->dogManager->undoWalkingAdditionalActionForDogsOnLead($this->playerId, $this->originActionId);

        DogPark::$instance->notifyAllPlayers('undoPlayerGainsLocationBonusResource', clienttranslate('Undo: <s>Location Bonus: ${player_name} gains ${resource}</s>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'resource' => $bonusType,
            'locationId' => $locationId,
            'extraBonus' => $action->additionalArgs->extraBonus,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);
    }
}