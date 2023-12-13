<?php

namespace objects\dogtraits;

use DogPark;

trait Fusspot
{
    public string $fusspotResource;
    protected function getAbility() : string
    {
        return FUSSPOT;
    }

    protected function getAbilityText() : string
    {
        return sprintf(DogPark::totranslate('During <b>FINAL SCORING</b>, gain 4 <icon-reputation> if this dog has <icon-walked>, has been assigned <icon-%s>, and you have another %s dog in your Kennel.'), $this->fusspotResource, current($this->breeds));
    }
}