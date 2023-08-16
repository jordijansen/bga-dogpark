<?php

namespace objects\dogtraits;

trait BallHog
{
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
        return _('During <b>FINAL SCORING</b>, gain 1 _icon-reputation_ for each leftover _icon-ball_ assigned to this dog. Max. 6 _icon-reputation_.');
    }
}