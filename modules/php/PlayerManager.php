<?php

namespace managers;

use APP_DbObject;
use DogPark;

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

    public function getWalkerId($playerId) {
        return $this->getUniqueValueFromDB("SELECT card_id FROM walker WHERE card_type_arg = " .$playerId);
    }

    public function resetAllOfferValues() {
        DogPark::$instance->setGlobalVariable(OFFER_VALUE_REVEALED, false);
        self::DbQuery("UPDATE player SET player_offer_value = null");
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
        $query .= " WHERE player_id = ${playerId}";
        self::DbQuery($query);
    }
}