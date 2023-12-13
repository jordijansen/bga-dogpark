<?php

namespace objects\dogtraits;

use DogPark;

trait Friendly
{
    protected function getAbility() : string
    {
        return FRIENDLY;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate("During <b>SELECTION</b>, when you place this dog on the Lead, you may discard 1 <icon-all-resources> to place your next dog for free.");
    }
}