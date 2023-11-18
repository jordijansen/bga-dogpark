<?php

namespace objects\dogtraits;

use DogPark;

trait Sociable
{
    protected function getAbility() : string
    {
        return SOCIABLE;
    }

    protected function getAbilityTitle() : string
    {
        return DogPark::$instance->ABILITIES[$this->getAbility()];
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate('During <b>FINAL SCORING</b>, gain 1 <icon-reputation> for each breed category represented in your Kennel.');
    }
}