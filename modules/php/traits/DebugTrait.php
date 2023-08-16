<?php

namespace traits;
use DogPark;
use objects\DogCard;

trait DebugTrait
{

    function jumpState()
    {
        $this->setGlobalVariable(USED_WALK_ABILITIES, []);
        $this->gamestate->jumpToState(40);
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

    function finalScoring() {
        $result = DogPark::$instance->scoreManager->getScoreBreakDown();
        var_dump(json_encode($result));
    }
}
