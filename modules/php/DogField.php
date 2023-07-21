<?php

namespace managers;

use DogPark;
use objects\DogCard;
use objects\DogWalker;

class DogField
{

    public function __construct() {}

    /**
     * @return DogCard[]
     */
    public function fillField(): array
    {
        $cards = [];
        for ($i = 1; $i <= $this->getNumberOfFields(); $i++) {
            $cards = [...$cards, ...DogCard::fromArray(DogPark::$instance->dogCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_FIELD, $i))];
        }
        return $cards;
    }

    public function getNumberOfFields(): int
    {
        switch (DogPark::$instance->getPlayersNumber()) {
            case(4):
                return 4;
            case(5):
                return 5;
            case(1):
            case(2):
            case(3):
            default:
                return 3;
        }
    }

    /**
     * @return DogCard[]
     */
    public function getDogCards(): array
    {
        return DogCard::fromArray(DogPark::$instance->dogCards->getCardsInLocation(LOCATION_FIELD));
    }

    /**
     * @return DogWalker[]
     */
    public function getWalkers(): array
    {
        return [
            ...DogWalker::fromArray(DogPark::$instance->dogWalkers->getCardsInLocation(LOCATION_FIELD_1)),
            ...DogWalker::fromArray(DogPark::$instance->dogWalkers->getCardsInLocation(LOCATION_FIELD_2)),
            ...DogWalker::fromArray(DogPark::$instance->dogWalkers->getCardsInLocation(LOCATION_FIELD_3)),
            ...DogWalker::fromArray(DogPark::$instance->dogWalkers->getCardsInLocation(LOCATION_FIELD_4)),
            ...DogWalker::fromArray(DogPark::$instance->dogWalkers->getCardsInLocation(LOCATION_FIELD_5))
        ];
    }

    /**
     * @return DogWalker[]
     */
    public function getWalkersInField(string $location): array
    {
        return DogWalker::fromArray(DogPark::$instance->dogWalkers->getCardsInLocation($location));
    }

}