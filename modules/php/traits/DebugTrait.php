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
        $cards = $this->dogCards->getCardsOfTypeInLocation(EXP_WORLD, $dogTypeId, LOCATION_DECK);
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

        $playerResourcesFlat = [];
        foreach ($playerResources as $resource => $count) {
            for ($i = 0; $i < intval($count); $i++) {
                $playerResourcesFlat[] = $resource;
            }
        }

        $resources = [];
        for ($i = 0; $i < $nrOfResourcesToAdd; $i++) {
            $resources[] = array_pop($playerResourcesFlat);
        }

        var_dump(json_encode($resources));
        $this->playerManager->payResources($this->getCurrentPlayerId(), $resources);

    }

    function test2()
    {
        $i = 'NULL';
        var_dump(strcasecmp('NULL', 'null')) === 0;
    }
}
