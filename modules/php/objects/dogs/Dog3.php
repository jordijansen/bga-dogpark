<?php

namespace objects\dogs;

use objects\DogCard;

class Dog3 extends DogCard
{
    public array $breeds = [BREED_TOY];
    public array $costs = [RESOURCE_BALL => 1];
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
    }
}