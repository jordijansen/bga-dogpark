<?php

namespace traits;
use actions\AdditionalAction;
use BgaUserException;
use commands\ActivateForecastCardCommand;
use commands\CraftyDogAbilityCommand;
use commands\EndScoutCommand;
use commands\GainLeavingTheParkBonusCommand;
use commands\GainLocationBonusCommand;
use commands\GoFetchDogAbilityCommand;
use commands\MoveWalkerCommand;
use commands\ObedientDogAbilityCommand;
use commands\PayReputationForLocationCommand;
use commands\PlaceDogOnLeadCommand;
use commands\PlaymateDogAbilityCommand;
use commands\ScoutCommand;
use commands\ShowOffDogAbilityCommand;
use commands\SocialButterflyDogAbilityCommand;
use commands\SwapCommand;
use commands\EagerDogAbilityCommand;
use commands\WellTrainedDogAbilityCommand;
use DogPark;
use objects\DogCard;
use objects\DogWalker;
use objects\ObjectiveCard;

trait ActionTrait
{

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in nicodemus.action.php)
    */

    function chooseObjective($cardId) {
        $playerId = $this->getCurrentPlayerId();

        $card = $this->objectiveCards->getCard($cardId);
        if (!isset($card)) {
            throw new BgaUserException('Unknown objective card');
        }
        $objectiveCard = ObjectiveCard::from($card);
        if ($objectiveCard->location != LOCATION_PLAYER || $objectiveCard->locationArg != $playerId) {
            throw new BgaUserException('Objective card not yours');
        }
        $this->setGlobalVariable(OBJECTIVE_ID_ .$playerId, $cardId);

        $this->gamestate->setPlayerNonMultiactive($this->getCurrentPlayerId(), "");
    }

    function changeObjective() {
        $playerId = $this->getCurrentPlayerId();

        $this->deleteGlobalVariable(OBJECTIVE_ID_ .$playerId);

        $this->gamestate->setPlayersMultiactive([$this->getCurrentPlayerId()], "");
    }

    function placeOfferOnDog($dogId, $offerValue) {
        $this->checkAction(ACT_PLACE_OFFER_ON_DOG);

        $activePlayerId = $this->getActivePlayerId();

        if ($this->getPlayerScore($activePlayerId) < $offerValue) {
            throw new BgaUserException("Offer too high");
        }

        if (!isset($dogId)) {
            throw new BgaUserException(DogPark::totranslate("You must select a Dog Card to take"));
        }
        $dog = DogCard::from($this->dogCards->getCard($dogId));
        if ($dog->location != LOCATION_FIELD) {
            throw new BgaUserException('Dog not in field');
        }

        $locationArgForWalker = sizeof($this->dogWalkers->getCardsInLocation('field_'.$dog->locationArg)) + 1;
        $this->dogWalkers->moveAllCardsInLocation(LOCATION_PLAYER, 'field_'.$dog->locationArg, $activePlayerId, $locationArgForWalker);
        $this->playerManager->updatePlayerOfferValue($activePlayerId, $offerValue);

        $this->notifyAllPlayers('dogOfferPlaced', clienttranslate('${player_name} places an offer on <b>${dogName}</b>'),[
            'i18n' => ['dogName'],
            'playerId' => $activePlayerId,
            'player_name' => $this->getPlayerName($activePlayerId),
            'dog' => $dog,
            'dogName' => $dog->name,
            'walker' => DogWalker::from(current($this->dogWalkers->getCardsInLocation('field_'.$dog->locationArg, $locationArgForWalker)))
        ]);
        $this->gamestate->nextState('');
    }

    /**
     * @throws BgaUserException if action is not allowed
     */
    function skipPlaceOfferOnDog() {
        $this->checkAction(ACT_SKIP_PLACE_OFFER_ON_DOG);

        $activePlayerId = $this->getActivePlayerId();

        if ($this->getPlayerScore($activePlayerId) > 0) {
            throw new BgaUserException("Can't skip");
        }

        $this->playerManager->updatePlayerOfferValue($activePlayerId, 0);

        $this->notifyAllPlayers('gameLog', clienttranslate('${player_name} can not place an offer (insufficient reputation)'),[
            'playerId' => $activePlayerId,
            'player_name' => $this->getPlayerName($activePlayerId),
        ]);

        $this->gamestate->nextState('');
    }

    function recruitDog($dogId) {
        $this->checkAction(ACT_RECRUIT_DOG);

        $activePlayerId = $this->getActivePlayerId();

        if (!isset($dogId)) {
            throw new BgaUserException(DogPark::totranslate("You must select a Dog Card to take"));
        }

        $dogCard = DogCard::from($this->dogCards->getCard($dogId));
        if ($dogCard->location != LOCATION_FIELD) {
            throw new BgaUserException('Dog not in field');
        }

        $reputationCost = $this->getPlayerScore($activePlayerId) > 0 ? 1 : 0;

        $walkerId = $this->playerManager->getWalkerId($activePlayerId);
        $this->dogWalkers->moveCard($walkerId, LOCATION_PLAYER, $activePlayerId);

        $this->dogManager->recruitDog($activePlayerId, $dogId, $reputationCost, $walkerId);

        $this->gamestate->nextState('');
    }

    function placeDogOnLead($dogId) {
        $this->checkAction(ACT_PLACE_DOG_ON_LEAD);

        $playerId = $this->getCurrentPlayerId();

        $freeDogsOnLead = $this->getGlobalVariable(FREE_DOG_ON_LEAD .$playerId);
        $freeDogsOnLead = $freeDogsOnLead != null ? $freeDogsOnLead : 0;

        $dogsForSelection = $this->dogManager->getDogsForSelection($playerId, $freeDogsOnLead > 0);
        if (!array_key_exists($dogId, $dogsForSelection)) {
            throw new BgaUserException("This dog is not available for selection");
        }

        $this->setGlobalVariable(SELECTION_DOG_ID_ . $playerId, $dogId);

        $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD_SELECT_RESOURCES);
    }

    function placeDogOnLeadCancel() {
        $playerId = $this->getCurrentPlayerId();
        $this->deleteGlobalVariable(SELECTION_DOG_ID_ . $playerId);

        $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }

    function placeDogOnLeadPayResources($dogId, $resources, $isFreePlacement) {
        $playerId = $this->getCurrentPlayerId();

        $freeDogsOnLead = $this->getGlobalVariable(FREE_DOG_ON_LEAD .$playerId);
        $freeDogsOnLead = $freeDogsOnLead != null ? $freeDogsOnLead : 0;
        if ($freeDogsOnLead <= 0 && $isFreePlacement) {
            throw new BgaUserException("You can't place for free");
        }

        $dogsForSelection = $this->dogManager->getDogsForSelection($playerId, $freeDogsOnLead > 0);

        if (!array_key_exists($dogId, $dogsForSelection)) {
            throw new BgaUserException("This dog is not available for selection");
        }
        $maxNumberOfDogsOnLead = $this->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection();
        $dogsAlreadyOnLead = sizeof($this->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId));
        if ($dogsAlreadyOnLead >= $maxNumberOfDogsOnLead) {
            throw new BgaUserException("You can't add any more dogs");
        }

        $nextDogCosts1Resource = $this->getGlobalVariable(NEXT_DOG_COSTS_1_RESOURCE .$playerId);
        $nextDogCosts1Resource = $nextDogCosts1Resource != null && boolval($nextDogCosts1Resource);

        $command = new PlaceDogOnLeadCommand($playerId, $dogId, $resources, $isFreePlacement, $nextDogCosts1Resource);
        $this->commandManager->addCommand($playerId, $command);

        $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }

    function confirmSelection() {
        $this->checkAction(ACT_CONFIRM_SELECTION);

        $playerId = $this->getCurrentPlayerId();

        if (sizeof($this->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId)) == 0) {
            if (sizeof($this->dogManager->getDogsForSelection($playerId)) > 1) {
                throw new BgaUserException("You must place at least place one dog on the lead if you can");
            }
        }

        $this->notifyAllPlayers('gameLog', clienttranslate('${player_name} confirms selection'),[
            'playerId' => $this->getCurrentPlayerId(),
            'player_name' => $this->getPlayerName($this->getCurrentPlayerId()),
        ]);

        $this->gamestate->unsetPrivateState($this->getCurrentPlayerId());
        $this->gamestate->setPlayerNonMultiactive($this->getCurrentPlayerId(), "");
    }

    function changeSelection() {
        $this->notifyAllPlayers('gameLog', clienttranslate('${player_name} wants to change the selection'),[
            'playerId' => $this->getCurrentPlayerId(),
            'player_name' => $this->getPlayerName($this->getCurrentPlayerId()),
        ]);

        $this->gamestate->setPlayersMultiactive([$this->getCurrentPlayerId()], "");
        $this->gamestate->initializePrivateState($this->getCurrentPlayerId());
    }

    function moveWalker($locationId, $isGlobetrotter = false) {

        $playerId = $this->getActivePlayerId();
        $walker = DogWalker::from($this->dogWalkers->getCard($this->playerManager->getWalkerId($playerId)));
        if ($walker->location != LOCATION_PARK) {
            throw new BgaUserException('Walker not in park!');
        }

        if (!$isGlobetrotter) {
            $this->checkAction(ACT_MOVE_WALKER);
            $possibleParkLocations = $this->dogWalkPark->getPossibleParkLocationIds($walker->id);
            if (!in_array($locationId, $possibleParkLocations)) {
                throw new BgaUserException('Location not allowed!');
            }
            $this->commandManager->addCommand($playerId, new MoveWalkerCommand($playerId, $walker->id, $walker->locationArg, $locationId));
        }

        $otherWalkersInLocation = array_filter(DogWalker::fromArray($this->dogWalkers->getCardsInLocation(LOCATION_PARK, $locationId)), fn($dogWalker) => $dogWalker->id !== $walker->id);

        $this->actionManager->clear($playerId);

        if (sizeof($otherWalkersInLocation) > 0) {
            $firstSocialButterflyDog = $this->dogManager->getFirstDogOnLeadWithAbility($playerId, SOCIAL_BUTTERFLY);
            if ($firstSocialButterflyDog != null && !$isGlobetrotter) {
                $this->actionManager->addAction($playerId, new AdditionalAction(USE_DOG_ABILITY, (object) [
                    "dogId" => $firstSocialButterflyDog->id,
                    "dogName" => $firstSocialButterflyDog->name,
                    "abilityTitle" => $firstSocialButterflyDog->abilityTitle
                ], $firstSocialButterflyDog->isAbilityOptional(), true));
            } else {
                if ($this->getPlayerScore($playerId) > 0) {
                    $this->actionManager->addAction($playerId, new AdditionalAction(WALKING_PAY_REPUTATION_ACCEPT, (object) ["accepted" => true, "locationId" => $locationId, "isGlobetrotter" => $isGlobetrotter ]));
                }
                if (!$isGlobetrotter) {
                    $this->actionManager->addAction($playerId, new AdditionalAction(WALKING_PAY_REPUTATION_DENY, (object) ["accepted" => false, "locationId" => $locationId, "isGlobetrotter" => $isGlobetrotter]));
                }
            }
        } else if ($locationId > 90) {
            if ($locationId == 91) {
                $this->actionManager->addAction($playerId, new AdditionalAction(WALKING_GAIN_LEAVING_THE_PARK_BONUS, (object) ["bonusType" => REPUTATION, "amount" => 3]));
            } else if ($locationId == 92) {
                $this->actionManager->addAction($playerId, new AdditionalAction(WALKING_GAIN_LEAVING_THE_PARK_BONUS, (object) ["bonusType" => REPUTATION, "amount" => 2]));
            } else if ($locationId == 93) {
                $action1 = new AdditionalAction(WALKING_GAIN_LEAVING_THE_PARK_BONUS, (object) ["bonusType" => REPUTATION, "amount" => 1]);
                $action2 = new AdditionalAction(WALKING_GAIN_LEAVING_THE_PARK_BONUS, (object) ["bonusType" => SWAP, "swapIncludesWalkedToken" => true, "leavingTheParkOtherActionId" => $action1->id, "amount" => 1]);
                $this->actionManager->addActions($playerId, [$action1, $action2]);
            }
        } else {
            $this->dogWalkPark->createLocationBonusActions($playerId, $locationId);
        }

        $continue = true;
        if (!$isGlobetrotter) {
            $firstGlobetrotterDog = $this->dogManager->getFirstDogOnLeadWithAbility($playerId, GLOBETROTTER);
            if ($firstGlobetrotterDog != null && $locationId < 90) {
                $possibleGlobetrotterLocations = $this->dogWalkPark->getPossibleParkLocationsWithWalkers($walker->id, 3);
                if (sizeof($possibleGlobetrotterLocations) > 1) {
                    $this->gamestate->jumpToState(ST_ACTION_GLOBETROTTER);
                    $continue = false;
                }
            }
        }

        if ($continue) {
            $this->gamestate->nextState("");
        }
    }

    function additionalAction($actionId) {
        $this->checkAction(ACT_ADDITIONAL_ACTION);
        $playerId = $this->getCurrentPlayerId();
        $action = $this->actionManager->getAction($playerId, $actionId);
        if ($action == null) {
            throw new BgaUserException("Action not found!");
        }

        if ($action->type == WALKING_GAIN_LOCATION_BONUS) {
            $this->commandManager->addCommand($playerId, new GainLocationBonusCommand($playerId, $actionId));
            if ($action->additionalArgs->bonusType == SWAP) {
                $this->setGlobalVariable(STATE_AFTER_SWAP, ST_WALKING_MOVE_WALKER_AFTER);
                $this->setGlobalVariable(CURRENT_ACTION_ID, $actionId);
                $this->gamestate->jumpToState(ST_ACTION_SWAP);
            } else if ($action->additionalArgs->bonusType == SCOUT) {
                $this->setGlobalVariable(STATE_AFTER_SCOUT, ST_WALKING_MOVE_WALKER_AFTER);
                $this->setGlobalVariable(CURRENT_ACTION_ID, $actionId);
                $this->gamestate->jumpToState(ST_ACTION_SCOUT_START);
            } else {
                $this->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
            }
        } else if ($action->type == WALKING_PAY_REPUTATION_ACCEPT) {
            $this->commandManager->addCommand($playerId, new PayReputationForLocationCommand($playerId, $actionId));
            $this->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
        } else if ($action->type == WALKING_PAY_REPUTATION_DENY) {
            $this->commandManager->addCommand($playerId, new PayReputationForLocationCommand($playerId, $actionId));
            $this->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
        } else if ($action->type == WALKING_GAIN_LEAVING_THE_PARK_BONUS) {
            if ($action->additionalArgs->bonusType == SWAP) {
                $this->setGlobalVariable(STATE_AFTER_SWAP, ST_WALKING_MOVE_WALKER_AFTER);
                $this->setGlobalVariable(CURRENT_ACTION_ID, $actionId);
                $this->gamestate->jumpToState(ST_ACTION_SWAP);
            } else {
                $this->commandManager->addCommand($playerId, new GainLeavingTheParkBonusCommand($playerId, $actionId));
                $this->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
            }
        } else if ($action->type == USE_DOG_ABILITY) {
            $dog = DogCard::from($this->dogCards->getCard($action->additionalArgs->dogId));
            if ($dog->ability == EAGER) {
                $this->commandManager->addCommand($playerId, new EagerDogAbilityCommand($playerId, $actionId));
            } else if ($dog->ability == CRAFTY) {
                $this->setGlobalVariable(CURRENT_ACTION_ID .$playerId, $actionId);
                $this->gamestate->setPrivateState($playerId, ST_ACTION_CRAFTY);
            } else if ($dog->ability == GO_FETCH) {
                $this->commandManager->addCommand($playerId, new GoFetchDogAbilityCommand($playerId, $actionId));
            } else if ($dog->ability == OBEDIENT) {
                $this->commandManager->addCommand($playerId, new ObedientDogAbilityCommand($playerId, $actionId));
            } else if ($dog->ability == PLAYMATE) {
                $this->commandManager->addCommand($playerId, new PlaymateDogAbilityCommand($playerId, $actionId));
            } else if ($dog->ability == SOCIAL_BUTTERFLY) {
                $this->commandManager->addCommand($playerId, new SocialButterflyDogAbilityCommand($playerId, $actionId));
            } else if ($dog->ability == SEARCH_AND_RESCUE) {
                $this->setGlobalVariable(STATE_AFTER_SWAP, ST_WALKING_MOVE_WALKER_AFTER);
                $this->setGlobalVariable(CURRENT_ACTION_ID, $actionId);
                $this->gamestate->jumpToState(ST_ACTION_SWAP);
            } else if ($dog->ability == WELL_TRAINED) {
                $this->commandManager->addCommand($playerId, new WellTrainedDogAbilityCommand($playerId, $actionId));
            } else if ($dog->ability == SHOW_OFF) {
                $this->commandManager->addCommand($playerId, new ShowOffDogAbilityCommand($playerId, $actionId));
            } else if ($dog->ability == GLOBETROTTER) {
                $this->gamestate->jumpToState(ST_ACTION_GLOBETROTTER);
            }
        } else if ($action->type == USE_FORECAST_ABILITY) {
            $forecastCardType = $action->additionalArgs->forecastCardTypeArg;
            if ($forecastCardType == 1) {
                $this->setGlobalVariable(CURRENT_ACTION_ID .$playerId, $actionId);
                $this->gamestate->setPrivateState($playerId, ST_ACTION_GAIN_RESOURCES_PRIVATE);
            } else if ($forecastCardType == 7) {
                $this->setGlobalVariable(GAIN_RESOURCES_NR_OF_RESOURCES .$playerId, 1);
                $this->setGlobalVariable(GAIN_RESOURCES_RESOURCE_OPTIONS .$playerId, [RESOURCE_STICK, RESOURCE_BALL, RESOURCE_TREAT, RESOURCE_TOY]);
                $this->setGlobalVariable(STATE_AFTER_GAIN_RESOURCES, ST_WALKING_MOVE_WALKER_AFTER);
                $this->setGlobalVariable(GAIN_RESOURCES_PLAYER_IDS, [$playerId]);
                $this->setGlobalVariable(CURRENT_ACTION_ID .$playerId, $actionId);

                $this->gamestate->setPlayersMultiactive([$playerId], '');
                $this->gamestate->jumpToState(ST_ACTION_GAIN_RESOURCES);
            }
        }
    }

    function confirmWalking() {
        $this->checkAction(ACT_CONFIRM_WALKING);

        $playerId = $this->getActivePlayerId();
        $this->actionManager->clear($playerId);
        $this->commandManager->clearCommands();

        $this->deleteGlobalVariable(SCOUTED_CARDS);
        $this->setGlobalVariable(LAST_WALKED_WALKER_ID, $this->playerManager->getWalkerId($playerId));

        $this->gamestate->nextState("");
    }

    function cancelSwap() {
        $this->checkAction(ACT_CANCEL);
        $this->gamestate->jumpToState(intval($this->getGlobalVariable(STATE_AFTER_SWAP)));
    }

    function confirmSwap($fieldDogId, $kennelDogId) {
        $this->checkAction(ACT_SWAP);

        $playerId = $this->getActivePlayerId();
        $fieldDog = DogCard::from($this->dogCards->getCard($fieldDogId));
        if ($fieldDog->location != LOCATION_FIELD) {
            throw new BgaUserException("Dog not in field");
        }

        $kennelDog = DogCard::from($this->dogCards->getCard($kennelDogId));
        if ($kennelDog->location != LOCATION_PLAYER || $kennelDog->locationArg != $playerId) {
            throw new BgaUserException("Dog not in kennel of active player");
        }

        $actionId = $this->getGlobalVariable(CURRENT_ACTION_ID);
        $this->commandManager->addCommand($playerId, new SwapCommand($playerId, $actionId, $fieldDogId, $kennelDogId, $kennelDog->resourcesOnCard));

        $this->gamestate->jumpToState(intval($this->getGlobalVariable(STATE_AFTER_SWAP)));
    }

    function confirmScout($fieldDogId, $scoutDogId) {
        $this->checkAction(ACT_SCOUT_REPLACE);

        $playerId = $this->getActivePlayerId();
        $fieldDog = DogCard::from($this->dogCards->getCard($fieldDogId));
        if ($fieldDog->location != LOCATION_FIELD) {
            throw new BgaUserException("Dog not in field");
        }

        $scoutedDogIds = $this->getGlobalVariable(SCOUTED_CARDS);
        if (!in_array($scoutDogId, $scoutedDogIds)) {
            throw new BgaUserException("Dog not scouted");
        }

        $this->commandManager->addCommand($playerId, new ScoutCommand($playerId, $fieldDogId, $scoutDogId));
        $this->commandManager->addCommand($playerId, new EndScoutCommand($playerId, $this->getGlobalVariable(CURRENT_ACTION_ID)));

        $this->gamestate->jumpToState(intval($this->getGlobalVariable(STATE_AFTER_SCOUT)));
    }

    function endScout() {
        $this->checkAction(ACT_SCOUT_END);

        $playerId = $this->getActivePlayerId();
        $this->commandManager->addCommand($playerId, new EndScoutCommand($playerId, $this->getGlobalVariable(CURRENT_ACTION_ID)));

        $this->gamestate->jumpToState(intval($this->getGlobalVariable(STATE_AFTER_SCOUT)));
    }

    function moveAutoWalker($locationId) {
        $this->checkAction(ACT_MOVE_AUTO_WALKER);

        $walker = DogWalker::from($this->dogWalkers->getCard(intval($this->getGlobalVariable(LAST_WALKED_WALKER_ID))));
        $autoWalker = $this->getAutoWalkers()[$walker->typeArg];
        $nrOfPlaces = intval($this->getGlobalVariable(MOVE_AUTO_WALKER_STEPS));

        $autoWalker->moveWalkerToLocation($walker->id, $locationId, $nrOfPlaces);
    }

    function confirmCrafty($resource) {
        $this->checkAction(ACT_CRAFTY_CONFIRM);
        $playerId = $this->getCurrentPlayerId();

        $actionId = $this->getGlobalVariable(CURRENT_ACTION_ID .$playerId);
        $this->commandManager->addCommand($playerId, new CraftyDogAbilityCommand($playerId, $actionId, $resource));
    }

    function cancelCrafty() {
        $this->checkAction(ACT_CANCEL);
        $playerId = $this->getCurrentPlayerId();

        $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }

    function confirmGainResources($resources) {
        $this->checkAction(ACT_GAIN_RESOURCES_CONFIRM);
        $playerId = $this->getCurrentPlayerId();

        $nrOfResourcesToGain = intval($this->getGlobalVariable(GAIN_RESOURCES_NR_OF_RESOURCES .$playerId));
        $reputationToGain = intval($this->getGlobalVariable(GAIN_RESOURCES_REPUTATION .$playerId));
        $resourceOptions = $this->getGlobalVariable(GAIN_RESOURCES_RESOURCE_OPTIONS .$playerId);

        if (sizeof($resources) != $nrOfResourcesToGain) {
            throw new BgaUserException('Incorrect amount of resources supplied');
        }

        $validResources = array_filter($resources, function($resource) use ($resourceOptions) {return in_array($resource, $resourceOptions);});
        if (sizeof($validResources) != sizeof($resources)) {
            throw new BgaUserException('Invalid resources supplied');
        }

        $forecastCard = $this->forecastManager->getCurrentForecastCard();
        if ($forecastCard->typeArg == 1) {
            $actionId = $this->getGlobalVariable(CURRENT_ACTION_ID .$playerId);
            $this->commandManager->addCommand($playerId, new ActivateForecastCardCommand($playerId, $actionId, $resources, 0, clienttranslate('${player_name} activates the current round Forecast Card gaining ${resourcesLog}'), clienttranslate('Undo: <s>${player_name} activates the current round Forecast Card gaining ${resourcesLog}<s>')));
            $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
        } else if ($forecastCard->typeArg == 2) {
            $this->commandManager->addCommand($playerId, new ActivateForecastCardCommand($playerId, null, $resources, 0, clienttranslate('${player_name} activates the current round Forecast Card gaining ${resourcesLog}'), clienttranslate('Undo: <s>${player_name} activates the current round Forecast Card gaining ${resourcesLog}<s>')));
            $this->gamestate->setPlayerNonMultiactive($playerId, 'homeTime');
        } else if ($forecastCard->typeArg == 4) {
            $this->commandManager->addCommand($playerId, new ActivateForecastCardCommand($playerId, null, $resources, $reputationToGain, clienttranslate('${player_name} activates the current round Forecast Card gaining ${resourcesLog} and ${gainedReputation} reputation'), clienttranslate('Undo: <s>${player_name} activates the current round Forecast Card gaining ${resourcesLog} and ${lostReputation} reputation<s>')));
            $this->gamestate->setPlayerNonMultiactive($playerId, 'homeTime');
        } else if ($forecastCard->typeArg == 7) {
            $currentPhase = $this->getGlobalVariable(CURRENT_PHASE);
            $doLogMessage = clienttranslate('${player_name} activates the current round Forecast Card gaining ${resourcesLog} and ${gainedReputation} reputation');
            $undoLogMessage = clienttranslate('Undo: <s>${player_name} activates the current round Forecast Card gaining ${resourcesLog} and ${lostReputation} reputation<s>');

            if ($currentPhase == PHASE_RECRUITMENT_1 || $currentPhase == PHASE_RECRUITMENT_2) {
                $this->commandManager->addCommand($playerId, new ActivateForecastCardCommand($playerId, null, $resources, 1, $doLogMessage, $undoLogMessage));

                $playerIds = $this->getGlobalVariable(GAIN_RESOURCES_PLAYER_IDS, true);
                $playerIds = array_filter($playerIds, function ($i) use ($playerId) { return $i != $playerId;});
                $this->setGlobalVariable(GAIN_RESOURCES_PLAYER_IDS, $playerIds);

                $this->gamestate->setPlayerNonMultiactive($playerId, 'recruitment');
            } else if ($currentPhase == PHASE_WALKING) {
                $actionId = $this->getGlobalVariable(CURRENT_ACTION_ID .$playerId);
                $this->commandManager->addCommand($playerId, new ActivateForecastCardCommand($playerId, $actionId, $resources, 1, $doLogMessage, $undoLogMessage));
                $this->gamestate->jumpToState(intval($this->getGlobalVariable(STATE_AFTER_GAIN_RESOURCES)));
            }
        }
    }

    function cancelGainResources() {
        $this->checkAction(ACT_CANCEL);
        $playerId = $this->getCurrentPlayerId();

        if ($this->gamestate->state()['name'] == 'selectionActions') {
            $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
        }
    }

    function confirmGlobetrotter($locationId) {
        $this->checkAction(ACT_GLOBETROTTER_CONFIRM);

        $this->moveWalker($locationId, true);
    }


    function undoLast() {
        $this->checkAction(ACT_UNDO);

        $playerId = $this->getCurrentPlayerId();
        $lastRemovedCommand = $this->commandManager->removeLastCommand($playerId);

        $this->redirectAfterUndo($playerId, $lastRemovedCommand);
    }

    function undoAll() {
        $this->checkAction(ACT_UNDO);

        $playerId = $this->getCurrentPlayerId();
        $lastRemovedCommand = $this->commandManager->removeAllCommands($playerId);

        $this->redirectAfterUndo($playerId, $lastRemovedCommand);
    }

    function redirectAfterUndo($playerId, $lastRemovedCommand) {
        if (get_class($lastRemovedCommand) == "commands\MoveWalkerCommand") {
            $this->gamestate->jumpToState(ST_WALKING_MOVE_WALKER);
        } else if (get_class($lastRemovedCommand) == "commands\ScoutCommand" || get_class($lastRemovedCommand) == "commands\EndScoutCommand") {
            $this->gamestate->jumpToState(ST_ACTION_SCOUT);
        } else if ($this->getGlobalVariable(CURRENT_PHASE) == PHASE_SELECTION) {
            $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
        } else if ($this->getGlobalVariable(CURRENT_PHASE) == PHASE_WALKING) {
            $this->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
        }
    }
}
