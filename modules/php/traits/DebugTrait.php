<?php

namespace traits;
use DogPark;
use objects\DogCard;

trait DebugTrait
{

    function jumpState()
    {
//        $this->setGlobalVariable(GAIN_RESOURCES_PLAYER_IDS, []);
        $this->gamestate->jumpToState(ST_FINAL_SCORING);
    }

    function placeInKennel($playerId, $dogTypeId) {
        $cards = $this->dogCards->getCardsOfTypeInLocation(BASE_GAME, $dogTypeId, LOCATION_DECK);
        if (sizeof($cards) == 1) {
            $dog = current(DogCard::fromArray($cards));
            $this->dogCards->moveCard($dog->id, LOCATION_PLAYER, $playerId);
        } else {
            throw new \BgaUserException("Card not found in deck");
        }
    }

    function test()
    {
        if ($this->getGameStateValue(VARIANT_GENTLE_WALK_OPTION) == VARIANT_GENTLE_WALK_OPTION_INCLUDED) {
            var_dump(LOCATION_BONUS_PLENTIFUL);
        }
        return null;
    }
}
