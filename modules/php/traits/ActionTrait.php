<?php

namespace traits;
use BgaUserException;
use commands\PlaceDogOnLeadCommand;
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
        $this->updatePlayerOfferValue($activePlayerId, $offerValue);

        $this->notifyAllPlayers('dogOfferPlaced', '${player_name} places an offer on <b>${dogName}</b>',[
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

        $this->updatePlayerOfferValue($activePlayerId, 0);

        $this->notifyAllPlayers('gameLog', '${player_name} can not place an offer (insufficient reputation)',[
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

        $this->notifyAllPlayers('gameLog', '${player_name} confirms selection',[
            'playerId' => $this->getCurrentPlayerId(),
            'player_name' => $this->getPlayerName($this->getCurrentPlayerId()),
        ]);

        $this->gamestate->unsetPrivateState($this->getCurrentPlayerId());
        $this->gamestate->setPlayerNonMultiactive($this->getCurrentPlayerId(), "");
    }

    function changeSelection() {
        $this->notifyAllPlayers('gameLog', '${player_name} wants to change the selection',[
            'playerId' => $this->getCurrentPlayerId(),
            'player_name' => $this->getPlayerName($this->getCurrentPlayerId()),
        ]);

        $this->gamestate->setPlayersMultiactive([$this->getCurrentPlayerId()], "");
        $this->gamestate->initializePrivateState($this->getCurrentPlayerId());
    }

    function undoLast() {
        $this->checkAction(ACT_UNDO);

        $playerId = $this->getCurrentPlayerId();
        $this->commandManager->removeLastCommand($playerId);

        $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }

    function undoAll() {
        $this->checkAction(ACT_UNDO);

        $playerId = $this->getCurrentPlayerId();
        $this->commandManager->removeAllCommands($playerId);

        $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
    }
}
