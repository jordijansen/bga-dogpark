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

    function placeInKennel($dogTypeId) {
        $cards = $this->dogCards->getCardsOfTypeInLocation(BASE_GAME, $dogTypeId, LOCATION_DECK);
        if (sizeof($cards) == 1) {
            $dog = current(DogCard::fromArray($cards));
            $this->dogCards->moveCard($dog->id, LOCATION_PLAYER, $this->getCurrentPlayerId());
        } else {
            throw new \BgaUserException("Card not found in deck");
        }
    }

    function test()
    {
        $playerResources = $this->playerManager->getResources($this->getCurrentPlayerId());
        $totalResourceSum = array_sum(array_values($playerResources));
        $nrOfResourcesToAdd = min($totalResourceSum, 12);
        if ($nrOfResourcesToAdd > 0 && ($nrOfResourcesToAdd % 2) > 0) {
            $nrOfResourcesToAdd = $nrOfResourcesToAdd - 1;
        }

        $result = array_map(fn($resourceType, $resourceCount) => array_map(fn($i) => $resourceType, range(1, $resourceCount)), array_keys($playerResources), array_values($playerResources));
        $playerResourcesFlat = [];
        array_walk_recursive($result, function($a) use (&$playerResourcesFlat) { $playerResourcesFlat[] = $a; });

        $resources = [];
        for ($i = 0; $i < $nrOfResourcesToAdd; $i++) {
            $resources[] = array_pop($playerResourcesFlat);
        }

        var_dump(json_encode($resources));


    }

    function test2()
    {
        $i = 'NULL';
        var_dump(strcasecmp('NULL', 'null')) === 0;
    }
}
