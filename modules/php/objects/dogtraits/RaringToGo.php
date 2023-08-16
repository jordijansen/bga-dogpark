<?php

namespace objects\dogtraits;

trait RaringToGo
{
    protected function getAbility() : string
    {
        return RARING_TO_GO;
    }

    protected function getAbilityTitle() : string
    {
        return _('Raring to Go');
    }

    protected function getAbilityText() : string
    {
        return _('During FINAL SCORING, gain 2 _icon-reputation_ for each _icon-walked_ on this dog.');
    }
}