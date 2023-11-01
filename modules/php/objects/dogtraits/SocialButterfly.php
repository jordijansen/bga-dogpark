<?php

namespace objects\dogtraits;

use DogPark;

trait SocialButterfly
{
    protected function getAbility() : string
    {
        return SOCIAL_BUTTERFLY;
    }
    protected function getAbilityTitle() : string
    {
        return DogPark::totranslate('Social Butterfly');
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate("When <b>WALKING</b> this dog, if you land on an occupied location, do not pay 1 <icon-reputation> to gain the location reward.");
    }

    public function isAbilityOptional() : bool
    {
        return false;
    }
}