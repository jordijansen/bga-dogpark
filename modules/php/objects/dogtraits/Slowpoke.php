<?php

namespace objects\dogtraits;

use DogPark;

trait Slowpoke
{
    protected function getAbility() : string
    {
        return SLOWPOKE;
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate("When <b>WALKING</b> this dog, if you are the last Walker in the Park, gain 2 <icon-reputation>.");
    }
}