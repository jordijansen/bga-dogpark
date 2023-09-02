<?php

namespace objects\dogtraits;

trait BallHog
{
    public int $maxResources = 6;
    protected function getAbility() : string
    {
        return BALL_HOG;
    }

    protected function getAbilityTitle() : string
    {
        return _('Ball Hog');
    }

    protected function getAbilityText() : string
    {
        return _('During <b>FINAL SCORING</b>, gain 1 <icon-reputation> for each leftover <icon-ball> assigned to this dog. Max. 6 <icon-reputation>.');
    }
}