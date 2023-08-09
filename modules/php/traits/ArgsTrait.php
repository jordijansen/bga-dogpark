<?php

namespace traits;
use DogPark;
use objects\DogCard;
use objects\ObjectiveCard;

trait ArgsTrait
{

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    //////////////////////////////////////////////////////////////////////////////
    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    function argRecruitmentOffer(): array
    {
        return [
            'recruitmentRound' => $this->getRecruitmentRoundArg(),
            'maxOfferValue' => min($this->getPlayerScore($this->getActivePlayerId()), 5)
        ];
    }

    function argRecruitmentResolveOffers():  array
    {
        return [
            'recruitmentRound' => $this->getRecruitmentRoundArg(),
        ];
    }

    function argRecruitmentTakeDog():  array
    {
        return [
            'recruitmentRound' => $this->getRecruitmentRoundArg(),
        ];
    }

    function argSelectionPlaceDogOnLead($playerId): array
    {
        return [
            "canCancelMoves" => DogPark::$instance->commandManager->hasCommands($playerId),
            "playerId" => $playerId,
            "maxNumberOfDogs" => DogPark::$instance->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection(),
            "numberOfDogsOnlead" => sizeof(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId)),
            "dogs" => DogPark::$instance->dogManager->getDogsForSelection($playerId)
        ];
    }

    function argSelectionPlaceDogOnLeadSelectResources($playerId): array
    {
        $dogId = $this->getGlobalVariable(SELECTION_DOG_ID_ . $playerId);
        $dog = DogCard::from($this->dogCards->getCard($dogId));

        return [
            "playerId" => $playerId,
            "dog" => $dog,
            "dogName" => $dog->name,
            "maxNumberOfDogs" => DogPark::$instance->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection(),
            "numberOfDogsOnlead" => sizeof(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId)),
            "resources" => DogPark::$instance->playerManager->getResources($playerId)
        ];
    }

    function argSelectionPlaceDogOnLeadAfter($playerId): array
    {
        return [
            "canCancelMoves" => DogPark::$instance->commandManager->hasCommands($playerId),
            "playerId" => $playerId,
            "maxNumberOfDogs" => DogPark::$instance->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection(),
            "numberOfDogsOnlead" => sizeof(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId)),
        ];
    }

    function argWalkingMoveWalker(): array
    {
        return [
            "possibleParkLocationIds" => $this->dogWalkPark->getPossibleParkLocationIds($this->getActivePlayerId())
        ];
    }

    function argWalkingMoveWalkerAfter(): array
    {
        return [
            "locationId" => $this->playerManager->getWalker($this->getActivePlayerId())->locationArg,
            "canCancelMoves" => $this->commandManager->hasCommands($this->getActivePlayerId()),
            "additionalActions" => $this->actionManager->getActions($this->getActivePlayerId(), true)
        ];
    }

    function argActionSwap(): array
    {
        return [
            "dogsInKennel" => DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_PLAYER, $this->getActivePlayerId())),
            "dogsInField" => $this->dogField->getDogCards()
        ];
    }

    function argActionScout(): array
    {
        return [
            "canCancelMoves" => DogPark::$instance->commandManager->hasCommands($this->getActivePlayerId()),
            "scoutedDogCards" => DogCard::fromArray($this->dogCards->getCards($this->getGlobalVariable(SCOUTED_CARDS)))
        ];
    }

    function getRecruitmentRoundArg() {
        return $this->getGlobalVariable(CURRENT_PHASE) == PHASE_RECRUITMENT_2 ? 2 : 1;
    }
    
}