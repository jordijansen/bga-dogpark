<?php

namespace objects\dogs;

use objects\DogCard;

class Dog1 extends DogCard
{
    public string $name = 'Australian Shepherd'; // TODO REPLACE
    public array $breeds = [BREED_TOY];
    public array $costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TREAT => 1];
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }
}