<?php

namespace managers;

use DogPark;
use objects\DogCard;

class DogField
{
    private DogPark $game;

    public function __construct(DogPark $game)
    {
        $this->game = $game;
    }

    public function fillField() {
        for ($i = 1; $i <= $this->getNumberOfFields(); $i++) {
            $this->game->dogCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_FIELD, $i);
        }
    }

    public function getNumberOfFields(): int
    {
        switch ($this->game->getPlayersNumber()) {
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

    public function getDogCards(): array
    {
        return DogCard::fromArray($this->game->dogCards->getCardsInLocation(LOCATION_FIELD));
    }

}