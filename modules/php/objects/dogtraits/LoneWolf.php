<?php

namespace objects\dogtraits;

use DogPark;

trait LoneWolf
{
    protected function getAbility() : string
    {
        return LONE_WOLF;
    }

    protected function getAbilityText() : string
    {
        return sprintf(DogPark::totranslate("During <b>FINAL SCORING</b>, gain 3 <icon-reputation> if this is the only <breed-%s> in your Kennel."), current($this->breeds));
    }
}