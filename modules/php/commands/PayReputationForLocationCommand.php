<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;

class PayReputationForLocationCommand extends BaseCommand
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
        $accepted = $action->additionalArgs->accepted;
        $locationId = DogPark::$instance->playerManager->getWalker($this->playerId)->locationArg;

        if ($accepted) {
            $playerScore = DogPark::$instance->getPlayerScore($this->playerId);
            DogPark::$instance->updatePlayerScore($this->playerId, $playerScore - 1);

            DogPark::$instance->notifyAllPlayers('playerPaysReputationForLocation', clienttranslate('${player_name} pays ${icon_resource} to unlock location bonus(es)'),[
                'playerId' => $this->playerId,
                'player_name' => DogPark::$instance->getPlayerName($this->playerId),
                'icon_resource' => 'reputation',
                'score' => DogPark::$instance->getPlayerScore($this->playerId)
            ]);

            $locationBonuses = DogPark::$instance->dogWalkPark->getLocationBonuses($locationId);
            $extraLocationBonuses = DogPark::$instance->dogWalkPark->getExtraLocationBonuses($locationId);
            DogPark::$instance->actionManager->addActions($this->playerId, array_map(fn($bonus) => new AdditionalAction(WALKING_GAIN_LOCATION_BONUS, (object) ["bonusType" => $bonus, "extraBonus" => false]), $locationBonuses));
            DogPark::$instance->actionManager->addActions($this->playerId, array_map(fn($bonus) => new AdditionalAction(WALKING_GAIN_LOCATION_BONUS, (object) ["bonusType" => $bonus, "extraBonus" => true]), $extraLocationBonuses));
        } else {
            DogPark::$instance->notifyAllPlayers('gameLog', clienttranslate('${player_name} skips location bonus(es)'),[
                'playerId' => $this->playerId,
                'player_name' => DogPark::$instance->getPlayerName($this->playerId)
            ]);
        }

        $actions = DogPark::$instance->actionManager->getActions($this->playerId);
        $actionsToMark = array_filter($actions, function ($action) { return in_array($action->type, [WALKING_PAY_REPUTATION_ACCEPT, WALKING_PAY_REPUTATION_DENY]);});
        foreach ($actionsToMark as $action) {
            DogPark::$instance->actionManager->markActionPerformed($this->playerId, $action->id);
        }
    }

    public function undo()
    {
        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        $accepted = $action->additionalArgs->accepted;
        if ($accepted) {
            $playerScore = DogPark::$instance->getPlayerScore($this->playerId);
            DogPark::$instance->updatePlayerScore($this->playerId, $playerScore + 1);

            DogPark::$instance->notifyAllPlayers('playerPaysReputationForLocation', clienttranslate('Undo: ${player_name} pays ${icon_resource} to unlock location bonus(es)'),[
                'playerId' => $this->playerId,
                'player_name' => DogPark::$instance->getPlayerName($this->playerId),
                'icon_resource' => 'reputation',
                'score' => DogPark::$instance->getPlayerScore($this->playerId)
            ]);

            DogPark::$instance->actionManager->clear($this->playerId);
        } else {
            DogPark::$instance->notifyAllPlayers('gameLog', clienttranslate('Undo: ${player_name} skips location bonus(es)'),[
                'playerId' => $this->playerId,
                'player_name' => DogPark::$instance->getPlayerName($this->playerId)
            ]);
        }

        if (DogPark::$instance->getPlayerScore($this->playerId) > 0) {
            DogPark::$instance->actionManager->addAction($this->playerId, new AdditionalAction(WALKING_PAY_REPUTATION_ACCEPT, (object) ["accepted" => true]));
        }
        DogPark::$instance->actionManager->addAction($this->playerId, new AdditionalAction(WALKING_PAY_REPUTATION_DENY, (object) ["accepted" => false]));
    }
}