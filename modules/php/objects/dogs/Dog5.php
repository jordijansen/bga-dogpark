<?php

namespace objects\dogs;

use objects\DogCard;

class Dog5 extends DogCard
{
    public array $breeds = [BREED_TOY];
    public array $costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TOY => 1];
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }
}