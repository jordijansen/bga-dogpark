<?php

namespace managers;

use APP_DbObject;
use DogPark;
use objects\DogWalker;
use objects\ObjectiveCard;

class PlayerManager extends APP_DbObject
{

    public function setInitialResources() {
        self::DbQuery("UPDATE player SET player_sticks = 2, player_balls = 2, player_toys = 1, player_treats = 1");
    }

    public function setInitialPlayerOder() {
        self::DbQuery("UPDATE player SET player_custom_order = player_no");
    }

    public function getResources($playerId) {
        $resources = current($this->getCollectionFromDB("SELECT 
                                                 player_sticks as stick, 
                                                 player_balls as ball,
                                                 player_toys as toy,
                                                 player_treats as treat
                                                 FROM player 
                                                 WHERE player_id = ".$playerId));
        $resources['stick'] = intval($resources['stick']);
        $resources['ball'] = intval($resources['ball']);
        $resources['toy'] = intval($resources['toy']);
        $resources['treat'] = intval($resources['treat']);
        return $resources;
    }

    public function getPlayerIdsInTurnOrder() {
        return $this->getCollectionFromDB("SELECT player_custom_order, player_id FROM player ORDER BY player_custom_order ASC");
    }

    public function getPlayerCustomOrderNo($playerId) {
        return $this->getUniqueValueFromDB("SELECT player_custom_order FROM player WHERE player_id = $playerId");
    }

    public function getWalkerId($playerId) {
        return $this->getUniqueValueFromDB("SELECT card_id FROM walker WHERE card_type_arg = " .$playerId);
    }

    public function getWalker($playerId) {
        $walkerId = $this->getWalkerId($playerId);
        return DogWalker::from(DogPark::$instance->dogWalkers->getCard($walkerId));
    }

    public function resetAllOfferValues() {
        DogPark::$instance->setGlobalVariable(OFFER_VALUE_REVEALED, false);
        self::DbQuery("UPDATE player SET player_offer_value = null");
    }

    public function dealObjectiveCardsToPlayers() {
        $experiencedObjectiveCards = DogPark::$instance->objectiveCards->getCardsOfTypeInLocation(OBJECTIVE_EXPERIENCED, null, LOCATION_DECK);
        $standardObjectiveCards = DogPark::$instance->objectiveCards->getCardsOfTypeInLocation(OBJECTIVE_STANDARD, null, LOCATION_DECK);

        $players = DogPark::$instance->loadPlayersBasicInfos();
        foreach ($players as $playerId => $player) {
            $experiencedObjective = ObjectiveCard::from(array_shift($experiencedObjectiveCards));
            if ($experiencedObjective->id == 1 && DogPark::$instance->getPlayersNumber() < 4) {
                // Experienced Objective card should only be used in a 4 player game (or 5 in expansion??)
                $experiencedObjective = ObjectiveCard::from(array_shift($experiencedObjectiveCards));
            }
            $standardObjective = ObjectiveCard::from(array_shift($standardObjectiveCards));
            $cardIds = [$experiencedObjective->id, $standardObjective->id];
            DogPark::$instance->objectiveCards->moveCards($cardIds, LOCATION_PLAYER, $playerId);
        }
    }

    public function payResources($playerId, $resources)
    {
        $this->updateResources($playerId, $resources, false);
    }

    public function gainResources($playerId, $resources)
    {
        $this->updateResources($playerId, $resources, true);
    }

    private function updateResources($playerId, $resources, $increment)
    {
        $resourcesCountByType = array_count_values($resources);
        $query = "UPDATE player SET ";
        $modifier = $increment ? '+' : '-';

        foreach ($resourcesCountByType as $resource => $count) {
            $columnName = "player_" . $resource . "s";
            $query .= "$columnName = $columnName $modifier $count,";
        }
        $query = rtrim($query, ',');
        $query .= " WHERE player_id = $playerId";
        self::DbQuery($query);
    }

    public function passFirstPlayerMarker(): int
    {
        $playerIdsInOrder = $this->getPlayerIdsInTurnOrder();
        $newFirstPlayerId = null;
        foreach ($playerIdsInOrder as $playerOrderNo => $player) {
            $playerId = intval($player['player_id']);
            $newTurnOrder = intval($playerOrderNo) - 1;
            if (intval($playerOrderNo) == 1) {
                $newTurnOrder = DogPark::$instance->getPlayersNumber();
            }
            if ($newTurnOrder == 1) {
                $newFirstPlayerId = $playerId;
            }
            self::DbQuery("UPDATE player SET player_custom_order = $newTurnOrder WHERE player_id = $playerId");
        }
        return $newFirstPlayerId;
    }
}