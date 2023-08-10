<?php

namespace traits;
use actions\AdditionalAction;
use BgaUserException;
use commands\EndScoutCommand;
use commands\GainLeavingTheParkBonusCommand;
use commands\GainLocationBonusCommand;
use commands\MoveWalkerCommand;
use commands\PayReputationForLocationCommand;
use commands\PlaceDogOnLeadCommand;
use commands\ScoutCommand;
use commands\SwapCommand;
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
            throw new BgaUserException(clienttranslate("You must select a Dog Card to take"));
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
            throw new BgaUserException(clienttranslate("You must select a Dog Card to take"));
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

        $dogsForSelection = $this->dogManager->getDogsForSelection($playerId);
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

    function placeDogOnLeadPayResources($dogId, $resources) {
        $playerId = $this->getCurrentPlayerId();

        $dogsForSelection = $this->dogManager->getDogsForSelection($playerId);
        if (!array_key_exists($dogId, $dogsForSelection)) {
            throw new BgaUserException("This dog is not available for selection");
        }
        $maxNumberOfDogsOnLead = $this->forecastManager->getCurrentRoundMaxNumberOfDogsForSelection();
        $dogsAlreadyOnLead = sizeof($this->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId));
        if ($dogsAlreadyOnLead >= $maxNumberOfDogsOnLead) {
            throw new BgaUserException("You can't add any more dogs");
        }

        $command = new PlaceDogOnLeadCommand($playerId, $dogId, $resources);
        $this->commandManager->addCommand($playerId, $command);

        $dogHasSelectionAbility = false;
        if ($dogHasSelectionAbility) {
            $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD_AFTER);
        } else {
            $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
        }
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

    function moveWalker($locationId) {
        $this->checkAction(ACT_MOVE_WALKER);

        $playerId = $this->getActivePlayerId();
        $walker = DogWalker::from($this->dogWalkers->getCard($this->playerManager->getWalkerId($playerId)));
        if ($walker->location != LOCATION_PARK) {
            throw new BgaUserException('Walker not in park!');
        }

        $possibleParkLocations = $this->dogWalkPark->getPossibleParkLocationIds($walker->id);
        if (!in_array($locationId, $possibleParkLocations)) {
            throw new BgaUserException('Location not allowed!');
        }

        $otherWalkersInLocation = DogWalker::fromArray($this->dogWalkers->getCardsInLocation(LOCATION_PARK, $locationId));

        $this->commandManager->addCommand($playerId, new MoveWalkerCommand($playerId, $walker->id, $walker->locationArg, $locationId));

        $this->actionManager->clear($playerId);
        // TODO SOCIAL BUTTERFLY YOU DON'T NEED TO PAY REPUTATION
        if (sizeof($otherWalkersInLocation) > 0) {
            if ($this->getPlayerScore($playerId) > 0) {
                $this->actionManager->addAction($playerId, new AdditionalAction(WALKING_PAY_REPUTATION_ACCEPT, (object) ["accepted" => true]));
            }
            $this->actionManager->addAction($playerId, new AdditionalAction(WALKING_PAY_REPUTATION_DENY, (object) ["accepted" => false]));
        } else if ($locationId > 90) {
            if ($locationId == 91) {
                $this->actionManager->addAction($playerId, new AdditionalAction(WALKING_GAIN_LEAVING_THE_PARK_BONUS, (object) ["bonusType" => REPUTATION, "amount" => 3]));
            } else if ($locationId == 92) {
                $this->actionManager->addAction($playerId, new AdditionalAction(WALKING_GAIN_LEAVING_THE_PARK_BONUS, (object) ["bonusType" => REPUTATION, "amount" => 2]));
            } else if ($locationId == 93) {
                $action1 = new AdditionalAction(WALKING_GAIN_LEAVING_THE_PARK_BONUS, (object) ["bonusType" => REPUTATION, "amount" => 1]);
                $action2 = new AdditionalAction(WALKING_GAIN_LEAVING_THE_PARK_BONUS, (object) ["bonusType" => SWAP, "leavingTheParkOtherActionId" => $action1->id, "amount" => 1]);
                $this->actionManager->addActions($playerId, [$action1, $action2]);
            }
        } else {
            $locationBonuses = $this->dogWalkPark->getLocationBonuses($locationId);
            $extraLocationBonuses = $this->dogWalkPark->getExtraLocationBonuses($locationId);
            $this->actionManager->addActions($playerId, array_map(fn($bonus) => new AdditionalAction(WALKING_GAIN_LOCATION_BONUS, (object) ["bonusType" => $bonus, "extraBonus" => false], in_array($bonus, [SWAP, SCOUT]), $bonus != SCOUT), $locationBonuses));
            $this->actionManager->addActions($playerId, array_map(fn($bonus) => new AdditionalAction(WALKING_GAIN_LOCATION_BONUS, (object) ["bonusType" => $bonus, "extraBonus" => true], in_array($bonus, [SWAP, SCOUT]), $bonus != SCOUT), $extraLocationBonuses));
        }

        $this->gamestate->nextState("");
    }

    function walkingAdditionalAction($actionId) {
        $this->checkAction(ACT_WALKING_ADDITIONAL_ACTION);
        $playerId = $this->getActivePlayerId();
        $action = $this->actionManager->getAction($this->getActivePlayerId(), $actionId);
        if ($action == null) {
            throw new BgaUserException("Action not found!");
        }

        $refreshCurrentState = true;
        if ($action->type == WALKING_GAIN_LOCATION_BONUS) {
            $this->commandManager->addCommand($playerId, new GainLocationBonusCommand($playerId, $actionId));
            if ($action->additionalArgs->bonusType == SWAP) {
                $refreshCurrentState = false;
                $this->setGlobalVariable(STATE_AFTER_SWAP, ST_WALKING_MOVE_WALKER_AFTER);
                $this->setGlobalVariable(CURRENT_ACTION_ID, $actionId);
                $this->gamestate->jumpToState(ST_ACTION_SWAP);
            } else if ($action->additionalArgs->bonusType == SCOUT) {
                $refreshCurrentState = false;
                $this->setGlobalVariable(STATE_AFTER_SCOUT, ST_WALKING_MOVE_WALKER_AFTER);
                $this->setGlobalVariable(CURRENT_ACTION_ID, $actionId);
                $this->gamestate->jumpToState(ST_ACTION_SCOUT_START);
            }
        } else if ($action->type == WALKING_PAY_REPUTATION_ACCEPT) {
            $this->commandManager->addCommand($playerId, new PayReputationForLocationCommand($playerId, $actionId));
        } else if ($action->type == WALKING_PAY_REPUTATION_DENY) {
            $this->commandManager->addCommand($playerId, new PayReputationForLocationCommand($playerId, $actionId));
        } else if ($action->type == WALKING_GAIN_LEAVING_THE_PARK_BONUS) {
            if ($action->additionalArgs->bonusType == SWAP) {
                $refreshCurrentState = false;
                $this->setGlobalVariable(STATE_AFTER_SWAP, ST_WALKING_MOVE_WALKER_AFTER);
                $this->setGlobalVariable(CURRENT_ACTION_ID, $actionId);
                $this->gamestate->jumpToState(ST_ACTION_SWAP);
            } else {
                $this->commandManager->addCommand($playerId, new GainLeavingTheParkBonusCommand($playerId, $actionId));
            }
        }

        if ($refreshCurrentState) {
            // Default go back to same state so possible actions are refreshed.
            $this->gamestate->jumpToState(ST_WALKING_MOVE_WALKER_AFTER);
        }
    }

    function confirmWalking() {
        $this->checkAction(ACT_CONFIRM_WALKING);

        $playerId = $this->getActivePlayerId();
        $this->actionManager->clear($playerId);
        $this->commandManager->clearCommands();

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

        $this->gamestate->jumpToState(ST_ACTION_SCOUT);
    }

    function endScout() {
        $this->checkAction(ACT_SCOUT_END);

        $playerId = $this->getActivePlayerId();
        $this->commandManager->addCommand($playerId, new EndScoutCommand());

        $this->gamestate->jumpToState(intval($this->getGlobalVariable(STATE_AFTER_SCOUT)));
    }

    function moveAutoWalker($locationId) {
        $this->checkAction(ACT_MOVE_AUTO_WALKER);

        $walker = DogWalker::from($this->dogWalkers->getCard(intval($this->getGlobalVariable(LAST_WALKED_WALKER_ID))));
        $autoWalker = $this->getAutoWalkers()[$walker->typeArg];
        $nrOfPlaces = intval($this->getGlobalVariable(MOVE_AUTO_WALKER_STEPS));

        $autoWalker->moveWalkerToLocation($walker->id, $locationId, $nrOfPlaces);
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
