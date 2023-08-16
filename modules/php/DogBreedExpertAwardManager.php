<?php


use objects\BreedExpertCard;
use objects\DogCard;
use objects\LocationBonusCard;

class DogBreedExpertAwardManager
{

    public function __construct() {}

    public function fillExpertAwards()
    {
        DogPark::$instance->breedCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_BREED_EXPERT_AWARDS, 1);
        DogPark::$instance->breedCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_BREED_EXPERT_AWARDS, 2);
        DogPark::$instance->breedCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_BREED_EXPERT_AWARDS, 3);
        DogPark::$instance->breedCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_BREED_EXPERT_AWARDS, 4);
        DogPark::$instance->breedCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_BREED_EXPERT_AWARDS, 5);
        DogPark::$instance->breedCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_BREED_EXPERT_AWARDS, 6);
        DogPark::$instance->breedCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_BREED_EXPERT_AWARDS, 7);
    }

    /**
     * @return BreedExpertCard[]
     */
    public function getExpertAwards(): array
    {
        return BreedExpertCard::fromArray(DogPark::$instance->breedCards->getCardsInLocation(LOCATION_BREED_EXPERT_AWARDS, null, 'card_location_arg'));
    }

    public function getExpertAwardsWinners(): array
    {
        $playerBreedCounts = [];
        $playerIdsInOrder = DogPark::$instance->playerManager->getPlayerIdsInTurnOrder();
        foreach ($playerIdsInOrder as $playerOrderNo => $player) {
            $playerId = intval($player['player_id']);
            $playerDogs = DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
            $playerBreeds = array_merge(...array_map(fn($dog) => $dog->breeds, $playerDogs));
            $playerBreedCounts[$playerId] = array_count_values($playerBreeds);
        }

        $autoWalkers = DogPark::$instance->getAutoWalkers();
        foreach ($autoWalkers as $autoWalker) {
            $playerDogs = DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_PLAYER, $autoWalker->id));
            $playerBreeds = array_merge(...array_map(fn($dog) => $dog->breeds, $playerDogs));
            $playerBreedCounts[$autoWalker->id] = array_count_values($playerBreeds);
        }

        $result = [];
        $expertAwards = $this->getExpertAwards();
        foreach ($expertAwards as $expertAward) {
            $breed = $expertAward->type;
            $winners = [];
            $highestCount = 0;
            foreach ($playerBreedCounts as $playerId => $playerBreedCount) {
                if (array_key_exists($breed, $playerBreedCount)) {
                    if ($playerBreedCount[$breed] > $highestCount) {
                        $winners = [$playerId];
                        $highestCount = $playerBreedCount[$breed];
                    } else if ($playerBreedCount[$breed] == $highestCount) {
                        $winners[] = $playerId;
                    }
                }
            }

            foreach ($winners as $winnerId) {
                if (array_key_exists($winnerId, $result)) {
                    $result[$winnerId] = [...$result[$winnerId], $expertAward];
                } else {
                    $result[$winnerId] = [$expertAward];
                }
            }

        }
        return $result;
    }
}