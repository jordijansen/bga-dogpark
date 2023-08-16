<?php

namespace objects\dogtraits;

trait Sociable
{
    protected function getAbility() : string
    {
        return SOCIABLE;
    }

    protected function getAbilityTitle() : string
    {
        return _('Sociable');
    }

    protected function getAbilityText() : string
    {
        return _('During <b>FINAL SCORING</b>, gain 1 _icon-reputation_ for each breed category represented in your Kennel.');
    }
}