<?php

namespace objects\dogtraits;

trait ToyCollector
{
    protected function getAbility() : string
    {
        return TOY_COLLECTOR;
    }

    protected function getAbilityTitle() : string
    {
        return _('Toy Collector');
    }

    protected function getAbilityText() : string
    {
        return _('During <b>FINAL SCORING</b>, gain 2 _icon-reputation_ for each leftover _icon-toy_ assigned to this dog. Max. 6 _icon-reputation_.');
    }
}