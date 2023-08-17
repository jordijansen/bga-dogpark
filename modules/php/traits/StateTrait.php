<?php

namespace traits;
use actions\AdditionalAction;
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
                $this->dogWalkers->moveCard($walker->id, LOCATION_PLAYER, $autoWalker->id);
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

        // During SELECTION, gain 1 Location Bonus for each GUNDOG in your Kennel or on your Lead.
        if ($this->forecastManager->getCurrentForecastCard()->typeArg == 1) {
            // WARNING PROBLEMS WITH NEW TRICKS?
            $players = $this->playerManager->getPlayerIdsInTurnOrder();
            foreach ($players as $orderNo => $player) {
                $playerId = intval($player['player_id']);
                $dogs = DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
                $gundogs = array_filter($dogs,  function ($dog) { return in_array(BREED_GUNDOG, $dog->breeds);});
                if (sizeof($gundogs) > 0) {
                    $this->setGlobalVariable(GAIN_RESOURCES_NR_OF_RESOURCES .$playerId, sizeof($gundogs));
                    $locationBonuses = array_filter($this->dogWalkPark->getAllLocationBonuses(), function ($locationBonus) { return !in_array($locationBonus->bonus, [BLOCK, SWAP, SCOUT]);});
                    $this->setGlobalVariable(GAIN_RESOURCES_RESOURCE_OPTIONS .$playerId, array_values(array_map(function ($locationBonus) {return $locationBonus->bonus;}, $locationBonuses)));

                    $this->actionManager->addAction($playerId, new AdditionalAction(USE_FORECAST_ABILITY, (object) [
                        "forecastCardTypeArg" => 1
                    ], false, true));
                }
            }
        }

        $this->gamestate->nextState("playerTurns");
    }

    function stSelectionActions()
    {
        $this->gamestate->setAllPlayersMultiactive();

        //this is needed when starting private parallel states; players will be transitioned to initialprivate state defined in master state
        $this->gamestate->initializePrivateStateForAllActivePlayers();
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
        $walkers = [...$walkers, ...$this->dogWalkPark->moveWalkersOfAutoWalkersToStartOfPark()];
        $this->notifyAllPlayers('moveWalkers', '', [
            'walkers' => $walkers
        ]);

        $nextPlayerId = current($playersWithDogsOnLead);
        if (sizeof($playersWithDogsOnLead) > 0) {
            $this->gamestate->changeActivePlayer($nextPlayerId);
            $this->gamestate->nextState("playerTurn");
        } else {
            $this->gamestate->nextState("skipWalkingPhase");
        }
    }

    function stWalkingNext()
    {
        $this->setGlobalVariable(USED_WALK_ABILITIES, []);

        $walkersOrdered = [];
        $players = $this->playerManager->getPlayerIdsInTurnOrder();
        foreach ($players as $orderNo => $player) {
            $playerId = intval($player['player_id']);
            $walker = $this->playerManager->getWalker($playerId);
            $walkersOrdered[$walker->id] = $walker;
        }

        $autoWalkers = $this->getAutoWalkers();
        foreach ($autoWalkers as $autoWalker) {
            $walker = $this->playerManager->getWalker($autoWalker->id);
            $walkersOrdered[$walker->id] = $walker;
        }

        $walkersInPark = array_filter($walkersOrdered, function($walker) {return $walker->location == LOCATION_PARK;});
        $walkersInLeavingParkSpace = array_filter($walkersInPark, function($walker) {return $walker->locationArg >= 90;});
        $walkersNotInLeavingParkSpace = array_filter($walkersInPark, function($walker) {return $walker->locationArg < 90;});
        $playerWalkersNotInLeavingParkSpace = array_filter($walkersNotInLeavingParkSpace, function($walker) {return $walker->typeArg > 2;});

        if (sizeof($playerWalkersNotInLeavingParkSpace) == 0) {
            // All players have left the park (and potentially only auto walkers remain)
            $this->gamestate->nextState( "end");
        } else if (sizeof($walkersInLeavingParkSpace) > 0 && sizeof($playerWalkersNotInLeavingParkSpace) == 1) {
            // There is only one player walker remaining in the park and no autowalkers
            $lastWalker = current($playerWalkersNotInLeavingParkSpace);
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
        } else {
            // We should find the next walker and activate that
            $nextWalkerId = $this->get_next_key_array($walkersInPark, intval($this->getGlobalVariable(LAST_WALKED_WALKER_ID)));
            $nextWalker = null;
            while ($nextWalker == null) {
                $walker = DogWalker::from($this->dogWalkers->getCard($nextWalkerId));
                if ($walker->locationArg > 90) {
                    $nextWalkerId = $this->get_next_key_array($walkersInPark, $nextWalkerId);
                } else {
                    $nextWalker = $walker;
                }
            }

            if ($nextWalker->typeArg > 2) {
                // This is a player walker
                $this->gamestate->changeActivePlayer($nextWalker->typeArg);
                $this->giveExtraTime($this->getActivePlayerId());
                $this->gamestate->nextState( "playerTurn");
            } else {
                // This is a auto walker
                $autoWalker = $autoWalkers[$nextWalker->typeArg];
                $autoWalker->takeWalkingTurn();
            }
        }
    }

    function get_next_key_array($array,$key){
        $keys = array_keys($array);
        $position = array_search($key, $keys);
        if (isset($keys[$position + 1])) {
            $nextKey = $keys[$position + 1];
        } else {
            $nextKey = $keys[0];
        }
        return $nextKey;
    }

    function stHomeTime() {
        $currentRound = intval($this->getGlobalVariable(CURRENT_ROUND));

        $this->notifyAllPlayers('newPhase', clienttranslate('Round ${round}: entering new Phase: Home Time'), [
            'round' => $currentRound,
            'newPhase' => PHASE_HOME_TIME
        ]);

        $foreCastCard = $this->forecastManager->getCurrentForecastCard();

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

            //2. Lose Reputation for each Dog without a Walked token in their Kennel.
            if ($foreCastCard->typeArg != 9) { // During HOME TIME, Dogs without WALKED token do not lose 1 reputation.
                $dogsInKennel = DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
                $unwalkedDogsInKennel = array_filter($dogsInKennel, function ($dog) { return $dog->resourcesOnCard['walked'] == 0;});
                $reputationLostPerDog = 1;
                if ($foreCastCard->typeArg == 8) { // During HOME TIME, Dogs without WALKED token lose 2 reputation instead of 1.
                    $reputationLostPerDog = 2;
                }
                $playerScore = $this->getPlayerScore($playerId);
                $reputationLost = sizeof($unwalkedDogsInKennel) * $reputationLostPerDog;
                $this->updatePlayerScore($playerId, $playerScore - $reputationLost);

                $this->notifyAllPlayers('playerLosesReputation', clienttranslate('${player_name} loses ${reputationLost} reputation for not walking ${nrOfDogsUnwalked} dog(s)'), [
                    'playerId' => $playerId,
                    'player_name' => $this->getPlayerName($playerId),
                    'reputationLost' => $reputationLost,
                    'nrOfDogsUnwalked' => sizeof($unwalkedDogsInKennel),
                    'score' => $this->getPlayerScore($playerId)
                ]);
            }


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

        // Remove autowalkers from park
        $autoWalkers = $this->getAutoWalkers();
        foreach ($autoWalkers as $autoWalker) {
            $walkerId = $this->playerManager->getWalkerId($autoWalker->id);
            $this->dogWalkers->moveCard($walkerId, LOCATION_PLAYER, $autoWalker->id);

            $this->notifyAllPlayers('moveWalkerBackToPlayer', clienttranslate('${autoWalkerName} returns home'),[
                'playerId' => $autoWalker->id,
                'autoWalkerName' => $autoWalker->name,
                'walker' => DogWalker::from($this->dogWalkers->getCard($walkerId))
            ]);
        }

        // 1. The current Forecast card is flipped over.
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

    //////////////////////////////////
    // FINAL SCORING
    //////////////////////////////////
    function stFinalScoring() {
        // Assign resources to dogs
        $playerIdsInOrder = $this->playerManager->getPlayerIdsInTurnOrder();
        foreach ($playerIdsInOrder as $playerOrderNo => $player) {
            $playerId = intval($player['player_id']);

            $dogsInKennel = DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
            foreach ($dogsInKennel as $dogInKennel) {
                $resourceToAdd = null;
                if ($dogInKennel->ability == STICK_CHASER) {
                    $resourceToAdd = RESOURCE_STICK;
                } else if ($dogInKennel->ability == TOY_COLLECTOR) {
                    $resourceToAdd = RESOURCE_TOY;
                } else if ($dogInKennel->ability == BALL_HOG) {
                    $resourceToAdd = RESOURCE_BALL;
                } else if ($dogInKennel->ability == TREAT_LOVER) {
                    $resourceToAdd = RESOURCE_TREAT;
                }

                if ($resourceToAdd != null) {
                    $playerResources = $this->playerManager->getResources($playerId);
                    $nrOfResourcesToAdd = min($playerResources[$resourceToAdd], $dogInKennel->maxResources);

                    $resources = [];
                    for ($i = 0; $i < $nrOfResourcesToAdd; $i++) {
                        $resources[] = $resourceToAdd;
                    }

                    if (sizeof($resources) > 0) {
                        $this->playerManager->payResources($playerId, $resources);
                        $this->dogManager->addResources($dogInKennel->id, $resourceToAdd, $nrOfResourcesToAdd);
                        $this->notifyAllPlayers('playerAssignsResources', clienttranslate('${player_name} assigns ${nrOfResourcesAdded} ${resourceType} to <b>${dogName}</b>'),[
                            'playerId' => $playerId,
                            'player_name' => $this->getPlayerName($playerId),
                            'dogName' => $dogInKennel->name,
                            'nrOfResourcesAdded' => sizeof($resources),
                            'resourceType' => $resourceToAdd,
                            'resourcesAdded' => $resources,
                            'dog' => DogCard::from($this->dogCards->getCard($dogInKennel->id))
                        ]);
                    }
                }
            }
        }

        // Award Breed Expert
        $breedExpertAwardResults = $this->breedExpertAwardManager->getExpertAwardsWinners();
        foreach ($breedExpertAwardResults as $playerId => $breedExpertCards) {
            foreach ($breedExpertCards as $breedExpertCard) {
                $this->notifyAllPlayers('playerWinsBreedExpert', clienttranslate('${player_name} wins ${breed} Breed Expert and gains ${reputation} reputation'),[
                    'playerId' => $playerId,
                    'player_name' => $this->getPlayerName($playerId),
                    'breed' => $breedExpertCard->type,
                    'reputation' => $breedExpertCard->reputation,
                ]);
            }
        }

        if ($this->getPlayersNumber() > 1) {
            // Reveal Objective Cards
            $this->setGlobalVariable(OBJECTIVES_REVEALED, true);
            $objectiveCards = ObjectiveCard::fromArray($this->objectiveCards->getCardsInLocation(LOCATION_SELECTED));
            $this->notifyAllPlayers('revealObjectiveCards', clienttranslate('Objective Cards revealed'),[
                'objectiveCards' => $objectiveCards,
            ]);
        }

        $scoreBreakDown = $this->scoreManager->getScoreBreakDown($breedExpertAwardResults);
        $this->setGlobalVariable(FINAL_SCORING_BREAKDOWN, $scoreBreakDown);
        $this->notifyAllPlayers('finalScoringRevealed', '',[
            'scoreBreakDown' => $scoreBreakDown,
        ]);

        foreach ($scoreBreakDown as $playerId => $scores) {
            $this->updatePlayerScoreAndAux($playerId, $scores['score'], $scores['scoreAux']);
        }

        $this->gamestate->nextState("");
    }


}