<?php


use objects\BreedExpertCard;

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
}