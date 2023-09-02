<?php

namespace objects\dogtraits;

trait Playmate
{
    public string $playmateResource;

    protected function getAbility() : string
    {
        return PLAYMATE;
    }
    protected function getAbilityTitle() : string
    {
        return _('Playmate');
    }

    protected function getAbilityText() : string
    {
        return sprintf(_("When <b>WALKING</b> this dog, whenever you gain 1 or more <icon-%s>, you may <icon-swap>. Activates once per movement."), $this->playmateResource);
    }

    public function isAbilityOptional() : bool
    {
        return true;
    }
}