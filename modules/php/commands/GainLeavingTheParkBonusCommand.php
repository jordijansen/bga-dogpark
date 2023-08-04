<?php

namespace commands;

use BgaUserException;
use DogPark;

class GainLeavingTheParkBonusCommand extends BaseCommand
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
        $bonusType = $action->additionalArgs->bonusType;
        $amount = $action->additionalArgs->amount;
        $locationId = DogPark::$instance->playerManager->getWalker($this->playerId)->locationArg;

        if ($bonusType == REPUTATION) {
            $playerScore = DogPark::$instance->getPlayerScore($this->playerId);
            DogPark::$instance->updatePlayerScore($this->playerId, $playerScore + $amount);
        } else {
            throw new BgaUserException("Not supported yet");
        }

        DogPark::$instance->notifyAllPlayers('playerLeavesThePark', clienttranslate('${player_name} leaves the park and gains ${resource}'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'resource' => $bonusType,
            'locationId' => $locationId,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);

        $actions = DogPark::$instance->actionManager->getActions($this->playerId);
        $actionsToMark = array_filter($actions, function ($action) { return $action->type == WALKING_GAIN_LEAVING_THE_PARK_BONUS;});
        foreach ($actionsToMark as $action) {
            DogPark::$instance->actionManager->markActionPerformed($this->playerId, $action->id);
        }
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $bonusType = $action->additionalArgs->bonusType;
        $amount = $action->additionalArgs->amount;
        $locationId = DogPark::$instance->playerManager->getWalker($this->playerId)->locationArg;

        if ($bonusType == REPUTATION) {
            $playerScore = DogPark::$instance->getPlayerScore($this->playerId);
            DogPark::$instance->updatePlayerScore($this->playerId, $playerScore - $amount);
        } else {
            throw new BgaUserException("Not supported yet");
        }

        DogPark::$instance->notifyAllPlayers('playerLeavesThePark', clienttranslate('Undo: ${player_name} leaves the park and gains ${resource}'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'resource' => $bonusType,
            'locationId' => $locationId,
            'score' => DogPark::$instance->getPlayerScore($this->playerId)
        ]);

        $actions = DogPark::$instance->actionManager->getActions($this->playerId);
        $actionsToMark = array_filter($actions, function ($action) { return $action->type == WALKING_GAIN_LEAVING_THE_PARK_BONUS;});
        foreach ($actionsToMark as $action) {
            DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $action->id);
        }
    }
}