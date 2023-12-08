<?php

namespace objects\dogtraits;

use DogPark;

trait SearchAndRescue
{
    protected function getAbility() : string
    {
        return SEARCH_AND_RESCUE;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate("When <b>WALKING</b> this dog, whenever you <icon-scout>, you may immediately <icon-swap>.");
    }

    public function isAbilityOptional() : bool
    {
        return true;
    }
}