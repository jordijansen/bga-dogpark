<?php

namespace objects\dogs;

use objects\DogCard;

class Dog18 extends DogCard
{
    public array $breeds = [BREED_TOY];
    public array $costs = [RESOURCE_TOY => 2];
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }
}