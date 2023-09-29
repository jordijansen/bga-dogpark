<?php

namespace objects;

use DogPark;

class AutoWalker
{
    public int $id;
    public string $name;
    public string $color;
    public ?int $lastDieRoll;

    public function __construct($id, $color, $lastDieRoll)
    {
        $this->id = $id;
        $this->name = "Autowalker #$id";
        $this->color = $color;
        $this->lastDieRoll = $lastDieRoll;
    }

    public function takeRecruitmentTurn()
    {
        $breedExpertCards = DogPark::$instance->breedExpertAwardManager->getExpertAwards();
        $dogsInField = DogPark::$instance->dogField->getDogCards();

        foreach ($breedExpertCards as $breedExpertCard) {
            $suitableDogs = array_filter($dogsInField, function($dog) use($breedExpertCard){return in_array($breedExpertCard->type, $dog->breeds);});
            foreach ($suitableDogs as $suitableDog) {
                $walkersField = $suitableDog->location. '_' .$suitableDog->locationArg;
                $walkersInField = DogPark::$instance->dogField->getWalkersInField($walkersField);
                $autoWalkersInField = array_filter($walkersInField, function($walker) {return $walker->typeArg <= 2;});
                if (sizeof($autoWalkersInField) == 0) {
                    $locationArgForWalker = sizeof(DogPark::$instance->dogWalkers->getCardsInLocation('field_'.$suitableDog->locationArg)) + 1;
                    DogPark::$instance->dogWalkers->moveAllCardsInLocation(LOCATION_PLAYER, 'field_'.$suitableDog->locationArg, $this->id, $locationArgForWalker);
                    DogPark::$instance->playerManager->updatePlayerOfferValue($this->id, DogPark::$instance->getNextAutoWalkerDiceValue($this));

                    DogPark::$instance->notifyAllPlayers('dogOfferPlaced', clienttranslate('${name} places an offer on <b>${dogName}</b>'),[
                        'i18n' => ['dogName'],
                        'playerId' => $this->id,
                        'name' => $this->name,
                        'dogName' => $suitableDog->name,
                        'walker' => DogPark::$instance->playerManager->getWalker($this->id)
                    ]);

                    return;
                }
            }
        }
    }

    public function takeWalkingTurn()
    {
        $walker = DogPark::$instance->playerManager->getWalker($this->id);
        $steps = DogPark::$instance->getNextAutoWalkerDiceValue($this);
        $possibleLocations = DogPark::$instance->dogWalkPark->getNextLocations($walker->locationArg, 0, $steps, [], true);
        if (sizeof($possibleLocations) == 1) {
            $this->moveWalkerToLocation($walker->id, current($possibleLocations), $steps);
        } else {
            $possibleLocationsLeavingThePark = array_filter($possibleLocations, function ($locationId) { return $locationId >= 90;});
            if (sizeof($possibleLocations) == 0 || sizeof($possibleLocationsLeavingThePark) > 0) {
                // We've moved past the last spot, find the top most leaving the park spot that is open
                $leavingTheParkLocations = array_filter(array_keys(DogPark::$instance->PARK_LOCATIONS), function ($locationId) { return $locationId >= 90;});
                $availableLeavingTheParkLocations = array_filter($leavingTheParkLocations, function ($locationId) { return DogPark::$instance->dogWalkPark->isLocationAccessible($locationId);});
                $topMostSpot = min($availableLeavingTheParkLocations);
                $this->moveWalkerToLocation($walker->id, $topMostSpot, $steps);
            } else {
                DogPark::$instance->setGlobalVariable(LAST_WALKED_WALKER_ID, $walker->id);
                DogPark::$instance->setGlobalVariable(MOVE_AUTO_WALKER_STEPS, $steps);
                DogPark::$instance->setGlobalVariable(MOVE_AUTO_WALKER_LOCATIONS, $possibleLocations);

                $playersInOrder = DogPark::$instance->playerManager->getPlayerIdsInTurnOrder();
                DogPark::$instance->gamestate->changeActivePlayer(intval(current($playersInOrder)['player_id']));
                DogPark::$instance->gamestate->jumpToState(ST_ACTION_MOVE_AUTO_WALKER);
            }
        }
    }

    public function moveWalkerToLocation($walkerId, $locationId, $steps)
    {
        DogPark::$instance->dogWalkers->moveCard($walkerId, LOCATION_PARK, $locationId);
        DogPark::$instance->setGlobalVariable(LAST_WALKED_WALKER_ID, $walkerId);

        DogPark::$instance->notifyAllPlayers('moveWalkers', clienttranslate('${name} moves ${steps}'), [
            'name' => $this->name,
            'steps' => $steps,
            'walkers' => [DogWalker::from(DogPark::$instance->dogWalkers->getCard($walkerId))]
        ]);
        DogPark::$instance->gamestate->jumpToState(ST_WALKING_NEXT);
    }

}