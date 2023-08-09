<?php

namespace managers;

use APP_DbObject;
use DogPark;
use objects\DogCard;
use objects\DogWalker;

class DogManager extends APP_DbObject
{
    function recruitDog($playerId, $dogId, $reputationCost, $dogWalkerId) {
        $this->moveDogToKennel($playerId, $dogId);
        $dogCard = DogCard::from(DogPark::$instance->dogCards->getCard($dogId));

        $args = [
            'i18n' => ['dogName'],
            'playerId' => $playerId,
            'dog' => $dogCard,
            'dogName' => $dogCard->name,
            'walker' => DogWalker::from(DogPark::$instance->dogWalkers->getCard($dogWalkerId))
        ];

        if ($playerId > 2) {
            $playerScore = DogPark::$instance->getPlayerScore($playerId);
            $newScore = $playerScore - $reputationCost;
            DogPark::$instance->updatePlayerScore($playerId, $newScore);

            $args['player_name'] = DogPark::$instance->getPlayerName($playerId);
            $args['reputationCost'] = $reputationCost;
            $args['score'] = $newScore;
            DogPark::$instance->notifyAllPlayers('dogRecruited', clienttranslate('${player_name} pays ${reputationCost} and recruits <b>${dogName}</b>'), $args);
        } else {
            $args['name'] = DogPark::$instance->getPlayerName($playerId);
            DogPark::$instance->notifyAllPlayers('dogRecruited', clienttranslate('${name} recruits <b>${dogName}</b>'), $args);
        }
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

    public function removeAllResources(int $id)
    {
        self::DbQuery("UPDATE dog SET dog_walked = 0, dog_stick = 0, dog_ball = 0, dog_treat = 0, dog_toy = 0 WHERE card_id = ".$id);
    }

    public function setResource(int $id, int $walked, int $stick, int $ball, int $treat, int $toy)
    {
        self::DbQuery("UPDATE dog SET dog_walked = $walked, dog_stick = $stick, dog_ball = $ball, dog_treat = $treat, dog_toy = $toy WHERE card_id = ".$id);

    }

}