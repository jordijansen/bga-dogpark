<?php

namespace objects\dogs;

use objects\DogCard;

class Dog9 extends DogCard
{
    public array $breeds = [BREED_TOY];
    public array $costs = [RESOURCE_TREAT => 1, RESOURCE_TOY => 1];
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }
}