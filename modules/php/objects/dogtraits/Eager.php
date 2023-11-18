<?php

namespace objects\dogtraits;

use DogPark;

trait Eager
{
    public string $eagerResource;

    protected function getAbility() : string
    {
        return EAGER;
    }
    protected function getAbilityTitle() : string
    {
        return DogPark::$instance->ABILITIES[$this->getAbility()];
    }

    protected function getAbilityText() : string
    {
        return sprintf(DogPark::totranslate("During <b>SELECTION</b>, when you place this dog on the Lead, gain <icon-%s>."), $this->eagerResource);
    }

}