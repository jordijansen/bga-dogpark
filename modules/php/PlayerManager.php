<?php

namespace managers;

use APP_DbObject;
use DogPark;

class PlayerManager extends APP_DbObject
{
    public function setInitialPlayerOder() {
        self::DbQuery("UPDATE player SET player_custom_order = player_no");
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

}