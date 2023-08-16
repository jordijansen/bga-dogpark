<?php


use objects\DogCard;

class ScoreManager
{
    public function __construct() {}

    public function getScoreBreakDown()
    {
        $result = [];
        $players = DogPark::$instance->loadPlayersBasicInfos();
        foreach ($players as $playerId => $player) {
            $result[$playerId] = $this->getPlayerScoreBreakDown($playerId);
        }
        return $result;
    }

    private function getPlayerScoreBreakDown($playerId) {
        $result = [];
        $result['parkBoardScore'] = DogPark::$instance->getPlayerScore($playerId);
        $result['dogFinalScoring'] = $this->getPlayerDogFinalScoring($playerId);
        return $result;
    }

    private function getPlayerDogFinalScoring($playerId)
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
                $result[$scoringDog->id] = $scoringDog->resourcesOnCard['walked'] * 2;
            } else if ($scoringDog->ability == SOCIABLE) {
                $breeds = [];
                foreach ($playerDogs as $dog) {
                    foreach ($dog->breeds as $breed) {
                        $breeds[$breed] = 1;
                    }
                }
                $result[$scoringDog->id] = sizeof(array_keys($breeds));
            }
        }
        return $result;
    }
}