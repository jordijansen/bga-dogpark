<?php

namespace objects\dogtraits;

use DogPark;

trait Hoarder
{
    public int $maxResources = 12;
    protected function getAbility() : string
    {
        return HOARDER;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate("During <b>FINAL SCORING</b>, gain 1 <icon-reputation> for every 2 leftover <icon-all-resources> assigned to this dog. Max 6 <icon-reputation>.");
    }
}