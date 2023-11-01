<?php

namespace objects\dogtraits;

use DogPark;

trait Crafty
{
    public string $craftyResource;

    protected function getAbility() : string
    {
        return CRAFTY;
    }

    protected function getAbilityTitle() : string
    {
        return DogPark::totranslate('Crafty');
    }

    protected function getAbilityText() : string
    {
        return sprintf(DogPark::totranslate("During <b>SELECTION</b>, when you place this dog on the Lead, you may discard 1 <icon-all-resources> to gain up to 1 <icon-%s>."), $this->craftyResource);
    }

    public function isAbilityOptional() : bool
    {
        return true;
    }
}