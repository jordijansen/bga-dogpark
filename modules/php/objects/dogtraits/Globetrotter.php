<?php

namespace objects\dogtraits;

use DogPark;

trait Globetrotter
{
    protected function getAbility() : string
    {
        return GLOBETROTTER;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate("When <b>WALKING</b> this dog, you may pay 1 <icon-reputation> to claim an occupied location reward within 3 spaces instead of your current one.");
    }

}