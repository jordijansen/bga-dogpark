<?php

namespace objects\dogtraits;

use DogPark;

trait StickChaser
{
    public int $maxResources = 6;
    protected function getAbility() : string
    {
        return STICK_CHASER;
    }

    protected function getAbilityTitle() : string
    {
        return DogPark::$instance->ABILITIES[$this->getAbility()];
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate('During <b>FINAL SCORING</b>, gain 1 <icon-reputation> for each leftover <icon-stick> assigned to this dog. Max. 6 <icon-reputation>.');
    }
}