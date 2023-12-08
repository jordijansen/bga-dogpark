<?php


use objects\DogCard;
use objects\ObjectiveCard;

class ScoreManager
{
    public function __construct() {}

    public function getSoloStarValue($playerId, $score): int
    {
        $objectiveCard = current(ObjectiveCard::fromArray(DogPark::$instance->objectiveCards->getCardsInLocation(LOCATION_SELECTED, $playerId)));
        $stars = 0;
        switch ($objectiveCard->typeArg) {
            case 21:
                $stars = 2;
                break;
            case 22:
                $stars = 4;
                break;
            case 23:
                $stars = 6;
                break;
        }

        if ($score >= 41 && $score <= 47) {
            $stars += 1;
        } else if ($score >= 48 && $score <= 54) {
            $stars += 2;
        } else if ($score >= 55 && $score <= 61) {
            $stars += 3;
        } else if ($score >= 62) {
            $stars += 4;
        }

        return $stars;
    }

    public function getScoreBreakDown($breedExpertAwardResults, $soloObjectiveAchieved)
    {
        $result = [];
        $players = DogPark::$instance->loadPlayersBasicInfos();
        foreach ($players as $playerId => $player) {
            $result[$playerId] = $this->getPlayerScoreBreakDown($playerId, $breedExpertAwardResults, $soloObjectiveAchieved);
        }
        return $result;
    }

    private function getPlayerScoreBreakDown($playerId, $breedExpertAwardResults, $soloObjectiveAchieved) {
        $result = [];
        $result['dogFinalScoring'] = $this->getPlayerDogFinalScore($playerId);

        $result['parkBoardScore'] = DogPark::$instance->getPlayerScore($playerId);
        $result['dogFinalScoringScore'] = array_sum(array_values($result['dogFinalScoring']));
        $result['breedExpertAwardScore'] = $this->getPlayerBreedExpertAwardScore($playerId, $breedExpertAwardResults);
        $result['objectiveCardScore'] = $this->getPlayerObjectiveCardScore($playerId, $breedExpertAwardResults);
        $result['remainingResourcesScore'] = $this->getPlayerRemainingResourcesScore($playerId);

        $result['score'] = $result['parkBoardScore'] + $result['dogFinalScoringScore'] + $result['breedExpertAwardScore'] + $result['objectiveCardScore'] + $result['remainingResourcesScore'];
        $result['scoreAux'] = 0;
        $result['soloStarRating'] = 0;

        if (DogPark::$instance->getPlayersNumber() == 1 && $soloObjectiveAchieved) {
            $result['soloStarRating'] = $this->getSoloStarValue($playerId, $result['score']);
        }

        if (array_key_exists($playerId, $breedExpertAwardResults)) {
            foreach ($breedExpertAwardResults[$playerId] as $breedExpertCard) {
                // Highest value breedExpertAward is tiebreaker
                if ($breedExpertCard->reputation > $result['scoreAux']) {
                    $result['scoreAux'] = $breedExpertCard->reputation;
                }
            }
        }
        return $result;
    }

    private function getPlayerDogFinalScore($playerId)
    {
        $result = [];
        $playerDogs = DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
        foreach ($playerDogs as $scoringDog) {
            if ($scoringDog->ability == PACK_DOG) {
                $count = 0;
                $breed = current($scoringDog->breeds);
                foreach ($playerDogs as $dog) {
                    if (in_array($breed, $dog->breeds)) {
                        $count = $count + 1;
                    }
                }
                $result[$scoringDog->id] = $count * 2;
            } else if ($scoringDog->ability == RARING_TO_GO) {
                $result[$scoringDog->id] = $scoringDog->resourcesOnCard[WALKED] * 2;
            } else if ($scoringDog->ability == SOCIABLE) {
                $breeds = [];
                foreach ($playerDogs as $dog) {
                    foreach ($dog->breeds as $breed) {
                        $breeds[$breed] = 1;
                    }
                }
                $result[$scoringDog->id] = sizeof(array_keys($breeds));
            } else if ($scoringDog->ability == BALL_HOG) {
                $result[$scoringDog->id] = intval($scoringDog->resourcesOnCard[RESOURCE_BALL]);
            } else if ($scoringDog->ability == STICK_CHASER) {
                $result[$scoringDog->id] = intval($scoringDog->resourcesOnCard[RESOURCE_STICK]);
            } else if ($scoringDog->ability == TOY_COLLECTOR) {
                $result[$scoringDog->id] = $scoringDog->resourcesOnCard[RESOURCE_TOY] * 2;
            } else if ($scoringDog->ability == TREAT_LOVER) {
                $result[$scoringDog->id] = $scoringDog->resourcesOnCard[RESOURCE_TREAT] * 2;
            } else if ($scoringDog->ability == LONE_WOLF) {
                $loneWolfBreed = current($scoringDog->breeds);
                $otherDogsWithSameBreed = array_filter($playerDogs, fn($dog) => in_array($loneWolfBreed, $dog->breeds) && $dog->id !== $scoringDog->id);
                $result[$scoringDog->id] = sizeof($otherDogsWithSameBreed) === 0 ? 3 : 0;
            } else if ($scoringDog->ability == HOARDER) {
                $result[$scoringDog->id] = intval(array_sum(array_values($scoringDog->resourcesOnCard))) / 2;
            }
        }
        return $result;
    }

    private function getPlayerBreedExpertAwardScore($playerId, $breedExpertAwardResults)
    {
        $score = 0;
        if (array_key_exists($playerId, $breedExpertAwardResults)) {
            $playerAwards = $breedExpertAwardResults[$playerId];
            foreach ($playerAwards as $playerAward) {
                $score += $playerAward->reputation;
            }
        }
        return $score;
    }

    private function getPlayerRemainingResourcesScore($playerId)
    {
        $totalResourceCount = 0;
        $playerResources = DogPark::$instance->playerManager->getResources($playerId);
        foreach ($playerResources as $resource => $count) {
            $totalResourceCount = $totalResourceCount + $count;
        }
        return floor($totalResourceCount / 5);
    }

    private function getPlayerObjectiveCardScore($playerId, $breedExpertAwardResults)
    {
        $objectiveCard = current(ObjectiveCard::fromArray(DogPark::$instance->objectiveCards->getCardsInLocation(LOCATION_SELECTED, $playerId)));
        $playerDogs = DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
        $playerBreeds = array_merge(...array_map(fn($dog) => $dog->breeds, $playerDogs));
        $playerBreedCounts = array_count_values($playerBreeds);
        if ($objectiveCard->typeArg == 1) {
            $breedsWith4OrMoreDogs = array_filter(array_values($playerBreedCounts), function ($i) { return $i >= 4;});
            return sizeof($breedsWith4OrMoreDogs) > 0 ? 7 : 0;
        } else if ($objectiveCard->typeArg == 2) {
            $playerDogsWith2OrMoreWalkedTokens = array_filter($playerDogs, function ($dog) { return $dog->resourcesOnCard[WALKED] >= 2;});
            return sizeof($playerDogsWith2OrMoreWalkedTokens) >= 3 ? 7 : 0;
        } else if ($objectiveCard->typeArg == 3) {
            $walkedCount = 0;
            foreach ($playerDogs as $dog) {
                $walkedCount = $walkedCount + $dog->resourcesOnCard[WALKED];
            }
            return $walkedCount >= 10 ? 7 : 0;
        } else if ($objectiveCard->typeArg == 4) {
            $playerAwards = $breedExpertAwardResults[$playerId];
            $objectiveThreshold = DogPark::$instance->getPlayersNumber() <= 3 ? 4 : 3;
            return sizeof($playerAwards) >= $objectiveThreshold ? 7 : 0;
        } else if ($objectiveCard->typeArg == 5) {
            $walkedDogs = array_filter($playerDogs, function ($dog) { return $dog->resourcesOnCard[WALKED] >= 1;});
            return sizeof($walkedDogs) >= 7 ? 7 : 0;
        } else if ($objectiveCard->typeArg == 6) {
            $playerDogsWith2OrMoreWalkedTokens = array_filter($playerDogs, function ($dog) { return $dog->resourcesOnCard[WALKED] >= 2;});
            return sizeof($playerDogsWith2OrMoreWalkedTokens) >= 2 ? 3 : 0;
        } else if ($objectiveCard->typeArg == 7) {
            $breedsWith3OrMoreDogs = array_filter(array_values($playerBreedCounts), function ($i) { return $i >= 3;});
            return sizeof($breedsWith3OrMoreDogs) > 0 ? 3 : 0;
        } else if ($objectiveCard->typeArg == 8) {
            // WARNING WILL THIS CAUSE PROBLEMS WITH NEW TRICKS EXP???
            $nrOfBreedsInKennel = sizeof(array_keys($playerBreedCounts));
            return $nrOfBreedsInKennel >= 4 ? 3 : 0;
        } else if ($objectiveCard->typeArg == 9) {
            $playerAwards = $breedExpertAwardResults[$playerId];
            $objectiveThreshold = DogPark::$instance->getPlayersNumber() <= 3 ? 3 : 2;
            return sizeof($playerAwards) >= $objectiveThreshold ? 3 : 0;
        } else if ($objectiveCard->typeArg == 10) {
            $walkedDogs = array_filter($playerDogs, function ($dog) { return $dog->resourcesOnCard[WALKED] >= 1;});
            return sizeof($walkedDogs) >= 6 ? 3 : 0;
        } else if ($objectiveCard->typeArg >= 20) {
            // SOLO CARDS
            return 0;
        }

        return 0;
    }
}