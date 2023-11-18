<?php

namespace objects\dogtraits;

use DogPark;

trait TreatLover
{
    public int $maxResources = 3;
    protected function getAbility() : string
    {
        return TREAT_LOVER;
    }

    protected function getAbilityTitle() : string
    {
        return DogPark::$instance->ABILITIES[$this->getAbility()];
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate('During <b>FINAL SCORING</b>, gain 2 <icon-reputation> for each leftover <icon-treat> assigned to this dog. Max. 6 <icon-reputation>.');
    }
}