<?php

namespace managers;

use APP_DbObject;
use DogPark;
use objects\DogCard;
use objects\DogWalker;

class DogManager extends APP_DbObject
{
    function recruitDog($playerId, $dogId, $reputationCost, $dogWalkerId) {
        $playerScore = DogPark::$instance->getPlayerScore($playerId);
        $newScore = $playerScore - $reputationCost;
        DogPark::$instance->updatePlayerScore($playerId, $newScore);

        $this->moveDogToKennel($playerId, $dogId);

        $dogCard = DogCard::from(DogPark::$instance->dogCards->getCard($dogId));
        DogPark::$instance->notifyAllPlayers('dogRecruited', clienttranslate('${player_name} pays ${reputationCost} and recruits <b>${dogName}</b>'),[
            'i18n' => ['dogName'],
            'playerId' => $playerId,
            'player_name' => DogPark::$instance->getPlayerName($playerId),
            'reputationCost' => $reputationCost,
            'dog' => $dogCard,
            'dogName' => $dogCard->name,
            'score' => $newScore,
            'walker' => DogWalker::from(DogPark::$instance->dogWalkers->getCard($dogWalkerId))
        ]);
    }

    public function moveDogToKennel($playerId, $dogId)
    {
        DogPark::$instance->dogCards->moveCard($dogId, LOCATION_PLAYER, $playerId);
    }

    public function getDogsForSelection($playerId)
    {
        $allDogs = DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
        $dogsForSelection = [];
        $resources = DogPark::$instance->playerManager->getResources($playerId);
        foreach ($allDogs as $dog) {
            $resourcesForDog = $resources;
            foreach ($dog->costs as $costType => $cost) {
                $resourcesForDog[$costType] = $resourcesForDog[$costType] - $cost;
            }
            $missingResources = abs(array_sum(array_filter($resourcesForDog, function ($r) { return $r < 0;})));
            $remainingResources = array_sum(array_filter($resourcesForDog, function ($r) { return $r > 0;}));
            if ($missingResources == 0 || floor($remainingResources / 2) >= $missingResources) {
                $dogsForSelection[$dog->id] = $dog;
            }
        }
        return $dogsForSelection;
    }

    public function getDogResources($dogId) {
        return current($this->getCollectionFromDB("SELECT dog_walked as walked, dog_stick as stick, dog_ball as ball, dog_treat as treat, dog_toy as toy FROM dog WHERE card_id = ". $dogId));
    }

    public function addResource(int $dogId, string $resource)
    {
        $columnName = 'dog_' .$resource;
        self::DbQuery("UPDATE dog SET $columnName = $columnName + 1 WHERE card_id = ".$dogId);
    }

    public function removeResource(int $dogId, string $resource)
    {
        $columnName = 'dog_' .$resource;
        self::DbQuery("UPDATE dog SET $columnName = $columnName - 1 WHERE card_id = ".$dogId);
    }

}