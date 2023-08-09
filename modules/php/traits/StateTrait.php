<?php

namespace traits;
use objects\BreedExpertCard;
use objects\DogCard;
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

        $this->playerManager->resetAllOfferValues();
        $this->notifyAllPlayers('resetAllOfferValues', clienttranslate('Offer dials are reset'), []);

        $firstPlayer = current($this->playerManager->getPlayerIdsInTurnOrder());
        $playerId = intval($firstPlayer['player_id']);
        $this->gamestate->changeActivePlayer($playerId);
        $this->gamestate->nextState("recruitmentOffer");
    }

    function stRecruitmentOfferNext() {
        $playerCustomOrderNo = $this->playerManager->getPlayerCustomOrderNo($this->getActivePlayerId());
        if ($playerCustomOrderNo == $this->getPlayersNumber()) {
            // All Human Players have had their turn to place an offer - Auto walkers take turn if present in this game
            $autoWalkers = $this->getAutoWalkers();
            foreach ($autoWalkers as $autoWalker) {
                $autoWalker->takeRecruitmentTurn();
            }

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
                'offerValue' => $this->playerManager->getPlayerOfferValue($playerId)
            ]);
        }

        $autoWalkers = $this->getAutoWalkers();
        foreach ($autoWalkers as $autoWalker) {
            $this->notifyAllPlayers('offerValueRevealed', clienttranslate('${name} reveals an offer of ${offerValue}'),[
                'playerId' => $autoWalker->id,
                'name' => $autoWalker->name,
                'offerValue' => $this->playerManager->getPlayerOfferValue($autoWalker->id)
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
                        $currentHighestOffer = $this->playerManager->getPlayerOfferValue($highestBid->typeArg);
                        $newOffer = $this->playerManager->getPlayerOfferValue($walker->typeArg);
                        if ($newOffer > $currentHighestOffer) {
                            $highestBid = $walker;
                        } else if ($newOffer == $currentHighestOffer) {
                            // If there is a tie in the offers, compare position in walkerQueue
                            $currentHighestLocation = $this->playerManager->getPlayerOfferValue($highestBid->locationArg);
                            $newLocation = $this->playerManager->getPlayerOfferValue($walker->locationArg);
                            if ($newLocation > $currentHighestLocation) {
                                $highestBid = $walker;
                            }
                        }
                    } else {
                        $highestBid = $walker;
                    }
                }

                $this->dogWalkers->moveCard($highestBid->id, LOCATION_PLAYER, $highestBid->typeArg);
                $this->dogManager->recruitDog($highestBid->typeArg, $dogCard->id, intval($this->playerManager->getPlayerOfferValue($highestBid->typeArg)), $highestBid->id);
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
                    if ($this->playerManager->getPlayerOfferValue($playerId) == 0) {
                        $this->playerManager->updatePlayerOfferValue($playerId, null);
                        $this->gamestate->changeActivePlayer($playerId);
                        $this->gamestate->nextState("nextPlayer");
                        return;
                    }
                }
            }
        }

        $autoWalkers = $this->getAutoWalkers();
        foreach ($autoWalkers as $autoWalker) {
            $walker = $this->playerManager->getWalker($autoWalker->id);
            if (substr($walker->location, 0, 5) === "field") {
                $leftOverDog = current($this->dogField->getDogCards());
                $this->dogManager->recruitDog($autoWalker->id, $leftOverDog->id, 0, $walker->id);
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

        $playerIdsInOrder = $this->playerManager->getPlayerIdsInTurnOrder();
        foreach ($playerIdsInOrder as $playerOrderNo => $player) {
            $playerId = intval($player['player_id']);

            //1. Gain 2 Reputation for each Dog on their Lead.
            $dogsOnlead = DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId));
            $reputationGained = sizeof($dogsOnlead) * 2;
            $playerScore = $this->getPlayerScore($playerId);
            $this->updatePlayerScore($playerId, $playerScore + $reputationGained);

            $this->notifyAllPlayers('playerGainsReputation', clienttranslate('${player_name} receives ${reputationGained} reputation for walking ${nrOfDogsWalked} dog(s)'), [
                'playerId' => $playerId,
                'player_name' => $this->getPlayerName($playerId),
                'reputationGained' => $reputationGained,
                'nrOfDogsWalked' => sizeof($dogsOnlead),
                'score' => $this->getPlayerScore($playerId)
            ]);

            //2. Lose 1 Reputation for each Dog without a Walked token in their Kennel.
            $dogsInKennel = DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
            $unwalkedDogsInKennel = array_filter($dogsInKennel, function ($dog) { return $dog->resourcesOnCard['walked'] == 0;});
            $reputationLost = sizeof($unwalkedDogsInKennel);
            $playerScore = $this->getPlayerScore($playerId);
            $this->updatePlayerScore($playerId, $playerScore - $reputationLost);

            $this->notifyAllPlayers('playerLosesReputation', clienttranslate('${player_name} loses ${reputationLost} reputation for not walking ${nrOfDogsUnwalked} dog(s)'), [
                'playerId' => $playerId,
                'player_name' => $this->getPlayerName($playerId),
                'reputationLost' => $reputationLost,
                'nrOfDogsUnwalked' => sizeof($unwalkedDogsInKennel),
                'score' => $this->getPlayerScore($playerId)
            ]);

            //3. Return the Dogs on the Lead to their Kennel
            $dogIdsInLead = array_map(function ($dog) {return $dog->id;}, $dogsOnlead);
            $this->dogCards->moveCards($dogIdsInLead, LOCATION_PLAYER, $playerId);

            $this->notifyAllPlayers('moveDogsToKennel', clienttranslate('${player_name} lead dogs are returned to kennel'),[
                'playerId' => $playerId,
                'player_name' => $this->getPlayerName($playerId),
                'dogs' => DogCard::fromArray($this->dogCards->getCards($dogIdsInLead))
            ]);

            // 4. Return their Walker to their Lead
            $walkerId = $this->playerManager->getWalkerId($playerId);
            $this->dogWalkers->moveCard($walkerId, LOCATION_PLAYER, $playerId);

            $this->notifyAllPlayers('moveWalkerBackToPlayer', clienttranslate('${player_name} walker returns home'),[
                'playerId' => $playerId,
                'player_name' => $this->getPlayerName($playerId),
                'walker' => DogWalker::from($this->dogWalkers->getCard($walkerId))
            ]);
        }

        // 1. The current Forecast card is flipped over.
        $foreCastCard = $this->forecastManager->getCurrentForecastCard();
        $foreCastCard->typeArg = null;
        $this->notifyAllPlayers('flipForecastCard', '',[
            'foreCastCard' => $foreCastCard
        ]);

        // 2. Tokens from this round’s Location Bonus card are returned to the general supply. A new Location Bonus card is revealed and new tokens are placed accordingly.
        $this->dogWalkPark->drawLocationBonusCardAndFillPark();
        $locationBonuses = $this->dogWalkPark->getAllLocationBonuses();
        $locationBonusCards = $this->dogWalkPark->getLocationBonusCards();
        $locationBonusCard = end($locationBonusCards);

        $this->notifyAllPlayers('newLocationBonusCardDrawn', clienttranslate('The park is replenished with new bonuses'),[
            'locationBonuses' => $locationBonuses,
            'locationBonusCard' => $locationBonusCard
        ]);

        // 4. The First Walker token is passed clockwise.
        $newFirstPlayerId = $this->playerManager->passFirstPlayerMarker();
        $this->notifyAllPlayers('newFirstWalker', clienttranslate('${player_name} becomes the new First Walker'),[
            'playerId' => $newFirstPlayerId,
            'player_name' => $this->getPlayerName($newFirstPlayerId),
        ]);

        // 3. The round tracker is moved onto the next round.
        if ($currentRound < 4) {
            $this->setGlobalVariable(CURRENT_ROUND, $currentRound + 1);

            $this->gamestate->nextState( "nextRound");
        } else {
            $this->gamestate->nextState( "endGame");
        }
    }

    function stActionScoutStart() {
        $topTwoCards = $this->dogCards->getCardsOnTop(2, LOCATION_DECK);
        $topTwoCardIds = array_map(fn($dbCard) => $dbCard['id'], $topTwoCards);

        $this->dogCards->moveCards($topTwoCardIds, LOCATION_DISCARD);

        $this->setGlobalVariable(SCOUTED_CARDS, $topTwoCardIds);
        $this->commandManager->clearCommands();

        $actionId = $this->getGlobalVariable(CURRENT_ACTION_ID);
        $this->actionManager->markActionPerformed($this->getActivePlayerId(), $actionId);

        $this->notifyAllPlayers('gameLog', clienttranslate('${player_name} performs a scout action and reveals the top two cards from the deck'),[
            'playerId' => $this->getActivePlayerId(),
            'player_name' => $this->getPlayerName($this->getActivePlayerId()),
            'scoutedDogs' => DogCard::fromArray($this->dogCards->getCards($topTwoCardIds))
        ]);

        $this->gamestate->nextState( "");
    }
}