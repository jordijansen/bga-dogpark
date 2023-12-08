<?php

namespace objects\dogtraits;

use DogPark;

trait ShowOff
{
    protected function getAbility() : string
    {
        return SHOW_OFF;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate("During <b>SELECTION</b>, when you place this dog on the Lead, gain 1 <icon-reputation>.");
    }
}