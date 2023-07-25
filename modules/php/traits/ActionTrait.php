<?php

namespace traits;
use BgaUserException;
use commands\PlaceDogOnLeadCommand;
use objects\DogCard;
use objects\DogWalker;

trait ActionTrait
{

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in nicodemus.action.php)
    */

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

        $this->notifyAllPlayers('skipPlaceOfferOnDog', '${player_name} can not place an offer (insufficient reputation)',[
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

        $command = new PlaceDogOnLeadCommand($playerId, $dogId, $resources);
        $this->commandManager->addCommand($playerId, $command);

        $dogHasSelectionAbility = false;
        if ($dogHasSelectionAbility) {
            $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD_AFTER);
        } else {
            $this->gamestate->setPrivateState($playerId, ST_SELECTION_PLACE_DOG_ON_LEAD);
        }
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
