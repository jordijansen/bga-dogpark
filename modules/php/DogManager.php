<?php

namespace managers;

use actions\AdditionalAction;
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

            if (in_array(BREED_UTILITY, $dogCard->breeds)) {
                DogPark::$instance->setGlobalVariable(GAIN_RESOURCES_PLAYER_IDS, [...DogPark::$instance->getGlobalVariable(GAIN_RESOURCES_PLAYER_IDS), $playerId]);
            }

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

    public function getDogsForSelection($playerId, $hasFreeDogsOnLead = false, $nextDogCosts1Resource = false)
    {
        $allDogs = DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
        $dogsForSelection = [];
        $resources = DogPark::$instance->playerManager->getResources($playerId);
        foreach ($allDogs as $dog) {
            if ($hasFreeDogsOnLead) {
                $dogsForSelection[$dog->id] = $dog;
            } else if ($nextDogCosts1Resource && array_sum(array_values($resources)) > 0) {
                $dogsForSelection[$dog->id] = $dog;
            } else {
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
        }
        return $dogsForSelection;
    }

    public function createWalkingAdditionalActionsForDogsOnLead($playerId, $resourceGained, $originActionId) {
        $dogsOnLead = DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId));
        foreach ($dogsOnLead as $dog) {
            // walk abilities may only be used once per movement
            $usedDogIds = DogPark::$instance->getGlobalVariable(USED_WALK_ABILITIES);
            if (!in_array($dog->id, $usedDogIds)) {
                if (($dog->ability == GO_FETCH && $dog->goFetchResource == $resourceGained) ||
                    ($dog->ability == OBEDIENT && $dog->obedientResource == $resourceGained) ||
                    ($dog->ability == PLAYMATE && $dog->playmateResource == $resourceGained) ||
                    ($dog->ability == WELL_TRAINED && in_array($resourceGained, [SWAP, SCOUT]))) {
                    DogPark::$instance->actionManager->addAction($playerId, new AdditionalAction(USE_DOG_ABILITY, (object) [
                        "dogId" => $dog->id,
                        "dogName" => $dog->name,
                        "abilityTitle" => $dog->abilityTitle
                    ], $dog->isAbilityOptional(), true, $originActionId));

                    DogPark::$instance->setGlobalVariable(USED_WALK_ABILITIES, [...$usedDogIds, $dog->id]);
                }
            }
        }
    }

    public function undoWalkingAdditionalActionForDogsOnLead($playerId, $originActionId) {
        $usedDogIds = DogPark::$instance->getGlobalVariable(USED_WALK_ABILITIES);
        $originatedActions = DogPark::$instance->actionManager->getActionsForOriginActionId($playerId, $originActionId);
        foreach ($originatedActions as $originatedAction) {
            $usedDogIds = array_filter($usedDogIds, function($usedDogId) use($originatedAction){return $usedDogId != $originatedAction->additionalArgs->dogId;});
            DogPark::$instance->actionManager->removeAction($playerId, $originatedAction->id);
        }
        DogPark::$instance->setGlobalVariable(USED_WALK_ABILITIES, $usedDogIds);
    }

    public function getFirstDogOnLeadWithAbility($playerId, $ability) {
        $dogsOnLead = DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId));
        foreach ($dogsOnLead as $dog) {
            if ($dog->ability == $ability) {
                return $dog;
            }
        }
        return null;
    }

    public function getDogResources($dogId) {
        return current($this->getCollectionFromDB("SELECT dog_walked as walked, dog_stick as stick, dog_ball as ball, dog_treat as treat, dog_toy as toy FROM dog WHERE card_id = ". $dogId));
    }

    public function addResource(int $dogId, string $resource)
    {
        $this->addResources($dogId, $resource, 1);
    }

    public function addResources(int $dogId, string $resource, int $amount)
    {
        $columnName = 'dog_' .$resource;
        self::DbQuery("UPDATE dog SET $columnName = $columnName + $amount WHERE card_id = ".$dogId);
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