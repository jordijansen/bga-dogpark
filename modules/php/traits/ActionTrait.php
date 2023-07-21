<?php

namespace traits;
use BgaUserException;
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
        $dogCard = DogCard::from($this->dogCards->getCard($dogId));
        if ($dogCard->location != LOCATION_FIELD) {
            throw new BgaUserException('Dog not in field');
        }

        $locationArgForWalker = sizeof($this->dogWalkers->getCardsInLocation('field_'.$dogCard->locationArg)) + 1;
        $this->dogWalkers->moveAllCardsInLocation(LOCATION_PLAYER, 'field_'.$dogCard->locationArg, $activePlayerId, $locationArgForWalker);
        $this->updatePlayerOfferValue($activePlayerId, $offerValue);

        $this->notifyAllPlayers('dogOfferPlaced', '${player_name} places an offer on doggo',[
            'playerId' => $activePlayerId,
            'player_name' => $this->getPlayerName($activePlayerId),
            'dog' => $dogCard,
            'walker' => DogWalker::from(current($this->dogWalkers->getCardsInLocation('field_'.$dogCard->locationArg, $locationArgForWalker)))
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

        $this->notifyAllPlayers(ACT_SKIP_PLACE_OFFER_ON_DOG, '${player_name} can not place an offer (insufficient reputation)',[
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


}
