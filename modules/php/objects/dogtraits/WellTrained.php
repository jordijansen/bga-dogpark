<?php

namespace objects\dogtraits;

use DogPark;

trait WellTrained
{
    protected function getAbility() : string
    {
        return WELL_TRAINED;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate("When <b>WALKING</b> this dog, whenever you <icon-swap> or <icon-scout>, gain 1 <icon-reputation>. Max 1 <icon-reputation> per movement.");
    }
}