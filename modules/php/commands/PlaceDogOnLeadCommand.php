<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;
use objects\DogCard;

class PlaceDogOnLeadCommand extends BaseCommand
{
    private int $playerId;
    private int $dogId;
    /**
     * @var string[]
     */
    private $resources;
    private string $originActionId;
    private bool $isFreePlacement;
    private ?bool $isCostReducedTo1Resource = false;

    public function __construct(int $playerId, int $dogId, array $resources, bool $isFreePlacement, bool $isCostReducedTo1Resource)
    {
        $this->playerId = $playerId;
        $this->dogId = $dogId;
        $this->resources = $resources;
        $this->isFreePlacement = $isFreePlacement;
        $this->originActionId = AdditionalAction::newId();
        $this->isCostReducedTo1Resource = $isCostReducedTo1Resource;
    }

    public function do()
    {
        if (!$this->isFreePlacement) {
            DogPark::$instance->playerManager->payResources($this->playerId, $this->resources);
        } else {
            $freeDogsOnLead = DogPark::$instance->getGlobalVariable(FREE_DOG_ON_LEAD .$this->playerId);
            $freeDogsOnLead = $freeDogsOnLead != null ? $freeDogsOnLead - 1 : 1;
            DogPark::$instance->setGlobalVariable(FREE_DOG_ON_LEAD .$this->playerId, $freeDogsOnLead);
        }
        DogPark::$instance->dogCards->moveCard($this->dogId, LOCATION_LEAD, $this->playerId);
        DogPark::$instance->dogManager->addResource($this->dogId, WALKED);

        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($this->dogId));
        DogPark::$instance->notifyAllPlayers('dogPlacedOnLead', clienttranslate('${player_name} places <b>${dogName}</b> on lead'),[
            'i18n' => ['dogName'],
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dogName' => $dog->name,
            'dog' => $dog,
            'resources' => $this->resources
        ]);

        if (DogPark::$instance->forecastManager->getCurrentForecastCard()->typeArg == 6 && in_array(BREED_HOUND, $dog->breeds)) {
            // During SELECTION, gain 2 reputation for each HOUND you place on the Lead.
            DogPark::$instance->updatePlayerScore($this->playerId, DogPark::$instance->getPlayerScore($this->playerId) + 2);
            DogPark::$instance->notifyAllPlayers('activateForecastCard', clienttranslate('${player_name} activates the current round Forecast Card and gains 2 reputation'), [
                'playerId' => $this->playerId,
                'player_name' => DogPark::$instance->getPlayerName($this->playerId),
                'forecastCard' => DogPark::$instance->forecastManager->getCurrentForecastCard(),
                'score' => DogPark::$instance->getPlayerScore($this->playerId)
            ]);
        } else if (DogPark::$instance->forecastManager->getCurrentForecastCard()->typeArg == 3 && in_array(BREED_PASTORAL, $dog->breeds)) {
            $maxNumberOfDogs = DogPark::$instance->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection();
            $numberOfDogsOnlead = DogPark::$instance->dogCards->countCardInLocation(LOCATION_LEAD, $this->playerId);
            if ($numberOfDogsOnlead < $maxNumberOfDogs) {
                // During SELECTION, when you place a PASTORAL dog on the Lead, you may place another dog without paying the walking cost.
                $freeDogsOnLead = DogPark::$instance->getGlobalVariable(FREE_DOG_ON_LEAD .$this->playerId);
                $freeDogsOnLead = $freeDogsOnLead != null ? $freeDogsOnLead + 1 : 1;
                DogPark::$instance->setGlobalVariable(FREE_DOG_ON_LEAD .$this->playerId, $freeDogsOnLead);
                DogPark::$instance->notifyAllPlayers('activateForecastCard', clienttranslate('${player_name} activates the current round Forecast Card and may place a dog on the lead for free'), [
                    'playerId' => $this->playerId,
                    'player_name' => DogPark::$instance->getPlayerName($this->playerId),
                    'forecastCard' => DogPark::$instance->forecastManager->getCurrentForecastCard()
                ]);
            }
        }

        if (in_array($dog->ability, SELECTION_ABILITIES)) {
            DogPark::$instance->actionManager->addAction($this->playerId, new AdditionalAction(USE_DOG_ABILITY, (object) [
                "dogId" => $this->dogId,
                "dogName" => $dog->name,
                "abilityTitle" => $dog->abilityTitle
            ], $dog->isAbilityOptional(), true, $this->originActionId));
        }


        DogPark::$instance->deleteGlobalVariable(NEXT_DOG_COSTS_1_RESOURCE. $this->playerId);
        if ($dog->ability == FRIENDLY) {
            DogPark::$instance->setGlobalVariable(NEXT_DOG_COSTS_1_RESOURCE. $this->playerId, true);
        }
    }

    public function undo()
    {
        if (!$this->isFreePlacement) {
            DogPark::$instance->playerManager->gainResources($this->playerId, $this->resources);
        } else {
            $freeDogsOnLead = DogPark::$instance->getGlobalVariable(FREE_DOG_ON_LEAD .$this->playerId);
            $freeDogsOnLead = $freeDogsOnLead != null ? $freeDogsOnLead + 1 : 1;
            DogPark::$instance->setGlobalVariable(FREE_DOG_ON_LEAD .$this->playerId, $freeDogsOnLead);
        }
        DogPark::$instance->dogCards->moveCard($this->dogId, LOCATION_PLAYER, $this->playerId);
        DogPark::$instance->dogManager->removeResource($this->dogId, WALKED);

        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($this->dogId));
        if (DogPark::$instance->forecastManager->getCurrentForecastCard()->typeArg == 6 && in_array(BREED_HOUND, $dog->breeds)) {
            // During SELECTION, gain 2 reputation for each HOUND you place on the Lead.
            DogPark::$instance->updatePlayerScore($this->playerId, DogPark::$instance->getPlayerScore($this->playerId) - 2);
            DogPark::$instance->notifyAllPlayers('activateForecastCard', clienttranslate('Undo: <s>${player_name} activates the current round Forecast Card and gains 2 reputation</s>'), [
                'playerId' => $this->playerId,
                'player_name' => DogPark::$instance->getPlayerName($this->playerId),
                'forecastCard' => DogPark::$instance->forecastManager->getCurrentForecastCard(),
                'score' => DogPark::$instance->getPlayerScore($this->playerId)
            ]);
        } else if (DogPark::$instance->forecastManager->getCurrentForecastCard()->typeArg == 3 && in_array(BREED_PASTORAL, $dog->breeds)) {
            $maxNumberOfDogs = DogPark::$instance->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection();
            $numberOfDogsOnlead = DogPark::$instance->dogCards->countCardInLocation(LOCATION_LEAD, $this->playerId);
            if (($numberOfDogsOnlead + 1) < $maxNumberOfDogs) {
                // During SELECTION, when you place a PASTORAL dog on the Lead, you may place another dog without paying the walking cost.
                $freeDogsOnLead = DogPark::$instance->getGlobalVariable(FREE_DOG_ON_LEAD .$this->playerId);
                $freeDogsOnLead = $freeDogsOnLead != null ? $freeDogsOnLead - 1 : 0;
                DogPark::$instance->setGlobalVariable(FREE_DOG_ON_LEAD .$this->playerId, $freeDogsOnLead);
                DogPark::$instance->notifyAllPlayers('activateForecastCard', clienttranslate('Undo: <s>${player_name} activates the current round Forecast Card and may place a dog on the lead for free</s>'), [
                    'playerId' => $this->playerId,
                    'player_name' => DogPark::$instance->getPlayerName($this->playerId),
                    'forecastCard' => DogPark::$instance->forecastManager->getCurrentForecastCard()
                ]);
            }
        }

        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($this->dogId));
        DogPark::$instance->notifyAllPlayers('undoDogPlacedOnLead',clienttranslate('Undo: <s>${player_name} places <b>${dogName}</b> on lead</s>'),[
            'i18n' => ['dogName'],
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dogName' => $dog->name,
            'dog' => $dog,
            'resources' => $this->resources
        ]);

        DogPark::$instance->actionManager->removeActionsForOriginActionId($this->playerId, $this->originActionId);

        if ($dog->ability == FRIENDLY) {
            DogPark::$instance->deleteGlobalVariable(NEXT_DOG_COSTS_1_RESOURCE .$this->playerId);
        }
        if ($this->isCostReducedTo1Resource) {
            DogPark::$instance->setGlobalVariable(NEXT_DOG_COSTS_1_RESOURCE. $this->playerId, true);
        }
    }
}