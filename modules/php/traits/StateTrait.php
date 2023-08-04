<?php

namespace traits;
use objects\DogWalker;
use objects\ObjectiveCard;
use objects\SelectionUndo;

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
    // CHOOSE OBJECTIVES
    //////////////////////////////////
    function stChooseObjectivesEnd() {
        $players = $this->loadPlayersBasicInfos();
        $chosenObjectiveCards = [];
        foreach ($players as $playerId => $player) {
            $objectiveCardId = $this->getGlobalVariable(OBJECTIVE_ID_ .$playerId);
            $this->objectiveCards->moveCard($objectiveCardId, LOCATION_SELECTED, $playerId);
            $chosenObjectiveCards[] = ['playerId' => $playerId, 'cardId' => intval($objectiveCardId)];
        }

        $this->notifyAllPlayers('objectivesChosen', clienttranslate('All players have chosen an objective card'),[
            'chosenObjectiveCards' => $chosenObjectiveCards
        ]);
        $this->gamestate->nextState("");
    }


    //////////////////////////////////
    // RECRUITMENT
    //////////////////////////////////
    function stRecruitmentStart() {
        $currentPhase = $this->getGlobalVariable(CURRENT_PHASE);
        $newPhase = PHASE_RECRUITMENT_1;
        $newPhaseLabel = clienttranslate('Round ${round}: entering new Phase: Recruitment (1/2)');
        if ($currentPhase == PHASE_RECRUITMENT_1) {
            $newPhase = PHASE_RECRUITMENT_2;
            $newPhaseLabel = clienttranslate('Round ${round}: entering new Phase: Recruitment (2/2)');
        }
        $this->setGlobalVariable(CURRENT_PHASE, $newPhase);

        $this->notifyAllPlayers('newPhase', $newPhaseLabel, [
            'round' => intval($this->getGlobalVariable(CURRENT_ROUND)),
            'newPhase' => $newPhase
        ]);

        $firstPlayer = current($this->playerManager->getPlayerIdsInTurnOrder());
        $playerId = intval($firstPlayer['player_id']);
        $this->gamestate->changeActivePlayer($playerId);
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

        $this->notifyAllPlayers('newPhase', clienttranslate('Round ${round}: entering new Phase: Selection'), [
            'round' => intval($this->getGlobalVariable(CURRENT_ROUND)),
            'newPhase' => PHASE_SELECTION
        ]);

        $this->gamestate->nextState("playerTurns");
    }

    function stSelectionActions()
    {
        $this->gamestate->setAllPlayersMultiactive();

        //this is needed when starting private parallel states; players will be transitioned to initialprivate state defined in master state
        $this->gamestate->initializePrivateStateForAllActivePlayers();
    }

    function stSelectionPlaceDogOnLeadAfter($playerId) {
    }

    function stSelectionEnd() {
        // Check if players have not placed a dog on their Lead
        $players = $this->playerManager->getPlayerIdsInTurnOrder();
        foreach ($players as $orderNo => $player) {
            $playerId = intval($player['player_id']);
            if (sizeof($this->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId)) == 0) {
                $resources = [RESOURCE_BALL, RESOURCE_STICK];
                $this->playerManager->gainResources($playerId, $resources);
                $this->notifyAllPlayers('playerGainsResources', clienttranslate('${player_name} gains resources because they can not walk any dog'),[
                    'playerId' => $playerId,
                    'player_name' => $this->getPlayerName($playerId),
                    'resources' => $resources
                ]);
            }
        }

        // Remove commands as they no longer can be undone
        $this->commandManager->clearCommands();

        $this->gamestate->nextState("walking");
    }

    //////////////////////////////////
    // WALKING
    //////////////////////////////////
    function stWalkingStart()
    {
        $this->setGlobalVariable(CURRENT_PHASE, PHASE_WALKING);

        $this->notifyAllPlayers('newPhase', clienttranslate('Round ${round}: entering new Phase: Walking'), [
            'round' => intval($this->getGlobalVariable(CURRENT_ROUND)),
            'newPhase' => PHASE_WALKING
        ]);

        $players = $this->playerManager->getPlayerIdsInTurnOrder();
        $playersWithDogsOnLead = [];
        foreach ($players as $orderNo => $player) {
            $playerId = intval($player['player_id']);
            if (sizeof($this->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId)) > 0) {
                $playersWithDogsOnLead[] = $playerId;
            }
        }

        $walkers = $this->dogWalkPark->moveWalkersOfPlayersToStartOfPark($playersWithDogsOnLead);
        $this->notifyAllPlayers('moveWalkers', '', [
            'walkers' => $walkers
        ]);

        $nextPlayerId = current($playersWithDogsOnLead);
        if (isset($nextPlayerId)) {
            $this->gamestate->changeActivePlayer($nextPlayerId);
            $this->gamestate->nextState("playerTurn");
        } else {
            // TODO WHAT IF NO PLAYERS WALK DOGS
        }
    }

    function stWalkingNext()
    {
        $walkersInPark = DogWalker::fromArray($this->dogWalkers->getCardsInLocation(LOCATION_PARK));
        $walkersNotInLeavingParkSpace = array_filter($walkersInPark, function($walker) {return $walker->locationArg < 90;});
        if (sizeof($walkersNotInLeavingParkSpace) > 1) {
            $skipPlayer = true;
            while ($skipPlayer) {
                $this->activeNextPlayer();
                $playerId = $this->getActivePlayerId();

                $walker = $this->playerManager->getWalker($playerId);
                if ($walker->location != LOCATION_PARK) {
                    $skipPlayer = true;
                } else if ($walker->locationArg > 90) {
                    $skipPlayer = true;
                } else {
                    $skipPlayer = false;
                }
            }
            $this->giveExtraTime($this->getActivePlayerId());
            $this->gamestate->nextState( "playerTurn");
        } else {
            // Only one walker remains, ending this phase.
            $lastWalker = current($walkersNotInLeavingParkSpace);
            $playerId = $lastWalker->typeArg;

            $playerScore = $this->getPlayerScore($playerId);
            if ($playerScore > 0) {
                $this->updatePlayerScore($playerId, $playerScore);
            }
            $this->dogWalkers->moveCard($lastWalker->id, LOCATION_PARK, 94);

            $this->notifyAllPlayers('playerLeavesThePark', clienttranslate('${player_name} leaves the park and loses ${resource}'),[
                'playerId' => $playerId,
                'player_name' => $this->getPlayerName($playerId),
                'resource' => 'reputation',
                'locationId' => 94,
                'score' => $this->getPlayerScore($playerId),
                'walker' => $this->playerManager->getWalker($playerId)
            ]);

            $this->gamestate->nextState( "end");
        }
    }

    function stHomeTime() {
        $currentRound = intval($this->getGlobalVariable(CURRENT_ROUND));

        $this->notifyAllPlayers('newPhase', clienttranslate('Round ${round}: entering new Phase: Home Time'), [
            'round' => $currentRound,
            'newPhase' => PHASE_HOME_TIME
        ]);

        if ($currentRound < 4) {
            $this->setGlobalVariable(CURRENT_ROUND, $currentRound + 1);

            $this->gamestate->nextState( "nextRound");
        } else {
            $this->gamestate->nextState( "endGame");
        }
    }
}