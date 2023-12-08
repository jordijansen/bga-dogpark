<?php

namespace objects\dogtraits;

use DogPark;

trait ToyCollector
{
    public int $maxResources = 3;
    protected function getAbility() : string
    {
        return TOY_COLLECTOR;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate('During <b>FINAL SCORING</b>, gain 2 <icon-reputation> for each leftover <icon-toy> assigned to this dog. Max. 6 <icon-reputation>.');
    }
}