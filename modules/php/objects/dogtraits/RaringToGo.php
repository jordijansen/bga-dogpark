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
        return _('During <b>FINAL SCORING</b>, gain 2 <icon-reputation> for each <icon-walked> on this dog.');
    }
}