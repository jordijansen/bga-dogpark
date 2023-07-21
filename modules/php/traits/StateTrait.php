<?php

namespace traits;
use objects\DogWalker;

trait StateTrait
{

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    //////////////////////////////////////////////////////////////////////////////
    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    //////////////////////////////////
    // RECRUITMENT
    //////////////////////////////////
    function stRecruitmentStart() {
        $currentPhase = $this->getGlobalVariable(CURRENT_PHASE);
        $newPhase = PHASE_RECRUITMENT_1;
        $newPhaseLabel = clienttranslate('Entering new Phase: Recruitment (1/2)');
        if ($currentPhase == PHASE_RECRUITMENT_1) {
            $newPhase = PHASE_RECRUITMENT_2;
            $newPhaseLabel = clienttranslate('Entering new Phase: Recruitment (2/2)');
        }
        $this->setGlobalVariable(CURRENT_PHASE, $newPhase);

        $this->notifyAllPlayers('newPhase', $newPhaseLabel, [
            'newPhase' => $newPhase
        ]);

        $this->activeNextPlayer();
        $this->gamestate->nextState("recruitmentOffer");
    }

    function stRecruitmentOfferNext() {
        $nextPlayerId = $this->getPlayerAfter($this->getActivePlayerId());
        if ($this->getPlayerOfferValue($nextPlayerId) != null) {
            // All Human Players have had their turn to place an offer
            $this->gamestate->nextState("resolveOffers");
        } else {
            $this->activeNextPlayer();
            $this->gamestate->nextState("nextPlayer");
        }
    }

    function stRecruitmentResolveOffers()
    {
        $players = $this->loadPlayersBasicInfos();
        foreach ($players as $playerId => $player) {
            $this->notifyAllPlayers('offerValueRevealed', clienttranslate('${player_name} reveals an offer of ${offerValue}'),[
                'playerId' => $playerId,
                'player_name' => $this->getPlayerName($playerId),
                'offerValue' => $this->getPlayerOfferValue($playerId)
            ]);
        }
        $this->setGlobalVariable(OFFER_VALUE_REVEALED, true);

        $dogCards = $this->dogField->getDogCards();
        foreach ($dogCards as $dogCard) {
            $walkersField = $dogCard->location. '_' .$dogCard->locationArg;
            $walkersInField = $this->dogField->getWalkersInField($walkersField);
            if (sizeof($walkersInField) > 0) {
                $highestBid = null;
                foreach ($walkersInField as $walker) {
                    if ($highestBid != null) {
                        // First we compare the offers, higher offer wins
                        $currentHighestOffer = $this->getPlayerOfferValue($highestBid->typeArg);
                        $newOffer = $this->getPlayerOfferValue($walker->typeArg);
                        if ($newOffer > $currentHighestOffer) {
                            $highestBid = $walker;
                        } else if ($newOffer == $currentHighestOffer) {
                            // If there is a tie in the offers, compare position in walkerQueue
                            $currentHighestLocation = $this->getPlayerOfferValue($highestBid->locationArg);
                            $newLocation = $this->getPlayerOfferValue($walker->locationArg);
                            if ($newLocation > $currentHighestLocation) {
                                $highestBid = $walker;
                            }
                        }
                    } else {
                        $highestBid = $walker;
                    }
                }

                $this->dogWalkers->moveCard($highestBid->id, LOCATION_PLAYER, $highestBid->typeArg);
                $this->dogManager->recruitDog($highestBid->typeArg, $dogCard->id, intval($this->getPlayerOfferValue($highestBid->typeArg)), $highestBid->id);

            }
        }

        $this->gamestate->nextState("");
    }

    function stRecruitmentTakeDogNext()
    {
        $playerIdsInOrder = $this->playerManager->getPlayerIdsInTurnOrder();
        $walkersStillInField = $this->dogField->getWalkers();
        $dogsInField = $this->dogField->getDogCards();
        if (sizeof($dogsInField) > 0) {
            if (sizeof($walkersStillInField) > 0) {
                foreach ($playerIdsInOrder as $playerOrderNo => $player) {
                    // There are sill walkers in the field, so players that offered to low get a change to choose a dog (for 1 reputation) in turn order
                    foreach ($walkersStillInField as $walker) {
                        $playerId = intval($player['player_id']);
                        if ($walker->typeArg == $playerId) {
                            $this->gamestate->changeActivePlayer($playerId);
                            $this->gamestate->nextState("nextPlayer");
                            return;
                        }
                    }
                }
            } else {
                foreach ($playerIdsInOrder as $playerOrderNo => $player) {
                    // Lastly all players that couldn't offer (insufficient reputation) get a change to choose a dog
                    $playerId = intval($player['player_id']);
                    if ($this->getPlayerOfferValue($playerId) == 0) {
                        $this->updatePlayerOfferValue($playerId, null);
                        $this->gamestate->changeActivePlayer($playerId);
                        $this->gamestate->nextState("nextPlayer");
                        return;
                    }
                }
            }
        }

        $this->gamestate->nextState("endRecruitment");
    }

    function stRecruitmentEnd()
    {
        $newDogCards = $this->dogField->fillField();
        $this->notifyAllPlayers('fieldRefilled', clienttranslate('The field is refilled with dog cards'), [
            "dogs" => $newDogCards
        ]);


        if ($this->getGlobalVariable(CURRENT_PHASE) == PHASE_RECRUITMENT_1) {
            $this->playerManager->resetAllOfferValues();
            $this->notifyAllPlayers('resetAllOfferValues', clienttranslate('Offer dials are reset'), []);
            $this->gamestate->nextState("recruitmentStart");
        } else {
            $this->gamestate->nextState("recruitmentEnd");
        }
    }

    //////////////////////////////////
    // SELECTION
    //////////////////////////////////
    function stSelectionStart()
    {
        $this->setGlobalVariable(CURRENT_PHASE, PHASE_SELECTION);
        $this->notifyAllPlayers('newPhase', clienttranslate('Entering new Phase: Selection'), [
            'newPhase' => PHASE_SELECTION
        ]);
        $this->gamestate->nextState("playerTurns");
    }

    function stSelectionActions()
    {
        $this->gamestate->setAllPlayersMultiactive();
    }
}