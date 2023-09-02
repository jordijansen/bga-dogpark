<?php

namespace objects\dogtraits;

trait ToyCollector
{
    public int $maxResources = 3;
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
        return _('During <b>FINAL SCORING</b>, gain 2 <icon-reputation> for each leftover <icon-toy> assigned to this dog. Max. 6 <icon-reputation>.');
    }
}