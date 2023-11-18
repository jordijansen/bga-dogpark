<?php

namespace objects\dogtraits;

use DogPark;

trait GoFetch
{
    public string $goFetchResource;
    public string $goFetchBonusResource;

    protected function getAbility() : string
    {
        return GO_FETCH;
    }
    protected function getAbilityTitle() : string
    {
        return DogPark::$instance->ABILITIES[$this->getAbility()];
    }

    protected function getAbilityText() : string
    {
        return sprintf(DogPark::totranslate("When <b>WALKING</b> this dog, whenever you gain 1 or more <icon-%s>, gain <icon-%s>. Activates once per movement."), $this->goFetchResource, $this->goFetchBonusResource);
    }
}