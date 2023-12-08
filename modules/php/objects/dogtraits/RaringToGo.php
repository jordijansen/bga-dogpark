<?php

namespace objects\dogtraits;

use DogPark;

trait RaringToGo
{
    protected function getAbility() : string
    {
        return RARING_TO_GO;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate('During <b>FINAL SCORING</b>, gain 2 <icon-reputation> for each <icon-walked> on this dog.');
    }
}