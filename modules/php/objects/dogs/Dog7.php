<?php

namespace objects\dogs;

use objects\DogCard;

class Dog7 extends DogCard
{
    public array $breeds = [BREED_TOY];
    public array $costs = [RESOURCE_STICK => 2, RESOURCE_TOY => 1];
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }
}