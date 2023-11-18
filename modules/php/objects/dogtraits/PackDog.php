<?php

namespace objects\dogtraits;

use DogPark;

trait PackDog
{
    protected function getAbility() : string
    {
        return PACK_DOG;
    }

    protected function getAbilityTitle() : string
    {
        return DogPark::$instance->ABILITIES[$this->getAbility()];
    }

    protected function getAbilityText() : string
    {
        return sprintf(DogPark::totranslate("During <b>FINAL SCORING</b>, gain 2 <icon-reputation> for each <breed-%s> dog in your Kennel."), current($this->breeds));
    }
}