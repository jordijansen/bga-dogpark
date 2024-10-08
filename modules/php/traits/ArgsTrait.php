<?php

namespace traits;
use DogPark;
use objects\DogCard;
use objects\DogWalker;

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
        $freeDogsOnLead = $this->getGlobalVariable(FREE_DOG_ON_LEAD .$playerId);
        $freeDogsOnLead = $freeDogsOnLead != null ? $freeDogsOnLead : 0;

        $nextDogCosts1Resource = $this->getGlobalVariable(NEXT_DOG_COSTS_1_RESOURCE .$playerId);
        $nextDogCosts1Resource = $nextDogCosts1Resource != null && boolval($nextDogCosts1Resource);

        return [
            "canCancelMoves" => DogPark::$instance->commandManager->hasCommands($playerId),
            "playerId" => $playerId,
            "maxNumberOfDogs" => DogPark::$instance->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection(),
            "numberOfDogsOnlead" => intval(DogPark::$instance->dogCards->countCardInLocation(LOCATION_LEAD, $playerId)),
            "dogs" => DogPark::$instance->dogManager->getDogsForSelection($playerId, $freeDogsOnLead > 0, $nextDogCosts1Resource),
            "additionalActions" => $this->actionManager->getActions($playerId, true)
        ];
    }

    function argSelectionPlaceDogOnLeadSelectResources($playerId): array
    {
        $dogId = $this->getGlobalVariable(SELECTION_DOG_ID_ . $playerId);
        $dog = DogCard::from($this->dogCards->getCard($dogId));
        $freeDogsOnLead = $this->getGlobalVariable(FREE_DOG_ON_LEAD .$playerId);
        $freeDogsOnLead = $freeDogsOnLead != null ? $freeDogsOnLead : 0;
        $nextDogCosts1Resource = $this->getGlobalVariable(NEXT_DOG_COSTS_1_RESOURCE .$playerId);
        $nextDogCosts1Resource = $nextDogCosts1Resource != null && boolval($nextDogCosts1Resource);

        return [
            'i18n' => ['dogName'],
            "playerId" => $playerId,
            "dog" => $dog,
            "dogName" => $dog->name,
            "maxNumberOfDogs" => DogPark::$instance->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection(),
            "numberOfDogsOnlead" => intval(DogPark::$instance->dogCards->countCardInLocation(LOCATION_LEAD, $playerId)),
            "resources" => DogPark::$instance->playerManager->getResources($playerId),
            "freeDogsOnLead" => $freeDogsOnLead,
            "nextDogCosts1Resource" => $nextDogCosts1Resource
        ];
    }

    function argWalkingMoveWalker(): array
    {
        return [
            "possibleParkLocationIds" => $this->dogWalkPark->getPossibleParkLocationIds($this->playerManager->getWalkerId($this->getActivePlayerId()))
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

    function argActionCrafty($playerId): array
    {
        $actionId = $this->getGlobalVariable(CURRENT_ACTION_ID .$playerId);
        $action = $this->actionManager->getAction($playerId, $actionId);
        $dog = DogCard::from($this->dogCards->getCard($action->additionalArgs->dogId));
        return [
            "action" => $action,
            "dog" => $dog,
            "resources" => DogPark::$instance->playerManager->getResources($playerId)
        ];
    }

    function getRecruitmentRoundArg() {
        return $this->getGlobalVariable(CURRENT_PHASE) == PHASE_RECRUITMENT_2 ? 2 : 1;
    }

    function argActionMoveAutoWalker() {
        $walker = DogWalker::from($this->dogWalkers->getCard(intval($this->getGlobalVariable(LAST_WALKED_WALKER_ID))));
        $autoWalker = $this->getAutoWalkers()[$walker->typeArg];
        $nrOfPlaces = intval($this->getGlobalVariable(MOVE_AUTO_WALKER_STEPS));
        $possibleLocations = $this->getGlobalVariable(MOVE_AUTO_WALKER_LOCATIONS);
        return [
            "autoWalkerName" => $autoWalker->name,
            "autoWalkerColor" => $autoWalker->color,
            "possibleParkLocationIds" => $possibleLocations,
            "nrOfPlaces" => $nrOfPlaces
        ];
    }

    function argActionGainResourcesPrivate($playerId): array
    {
        $actionId = $this->getGlobalVariable(CURRENT_ACTION_ID .$playerId);
        $action = $this->actionManager->getAction($playerId, $actionId);
        $forecastCardType = $action->additionalArgs->forecastCardTypeArg;

        $nrOfResourcesToGain = intval($this->getGlobalVariable(GAIN_RESOURCES_NR_OF_RESOURCES .$playerId));
        $resourceOptions = $this->getGlobalVariable(GAIN_RESOURCES_RESOURCE_OPTIONS .$playerId);

        return [
            "forecastCardType" => $forecastCardType,
            "nrOfResourcesToGain" => $nrOfResourcesToGain,
            "resourceOptions" => $resourceOptions,
            'canCancel' => true
        ];
    }

    function argActionGainResources(): array
    {

        $result = [];
        $playerIds = $this->getPlayerIds();

        foreach ($playerIds as $playerId) {
            $result[$playerId] = [
                'playerId' => $playerId,
                'nrOfResourcesToGain' => intval($this->getGlobalVariable(GAIN_RESOURCES_NR_OF_RESOURCES .$playerId)),
                'resourceOptions' => $this->getGlobalVariable(GAIN_RESOURCES_RESOURCE_OPTIONS .$playerId),
                'canCancel' => false,
            ];
        }

        return $result;
    }

    function argActionGlobetrotter(): array
    {
        return [
            "possibleParkLocationIds" => array_values($this->dogWalkPark->getPossibleParkLocationsWithWalkers($this->playerManager->getWalkerId($this->getActivePlayerId()), 3))
        ];
    }
    
}