<?php

namespace objects\dogtraits;

use DogPark;

trait BallHog
{
    public int $maxResources = 6;
    protected function getAbility() : string
    {
        return BALL_HOG;
    }

    protected function getAbilityTitle() : string
    {
        return DogPark::totranslate('Ball Hog');
    }

    protected function getAbilityText() : string
    {
        return DogPark::totranslate('During <b>FINAL SCORING</b>, gain 1 <icon-reputation> for each leftover <icon-ball> assigned to this dog. Max. 6 <icon-reputation>.');
    }
}