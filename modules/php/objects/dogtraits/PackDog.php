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
        return DogPark::totranslate('Pack Dog');
    }

    protected function getAbilityText() : string
    {
        return sprintf(DogPark::totranslate("During <b>FINAL SCORING</b>, gain 2 <icon-reputation> for each ^%s^ in your Kennel."), current($this->breeds));
    }
}