<?php

namespace objects\dogtraits;

trait Eager
{
    public string $eagerResource;

    protected function getAbility() : string
    {
        return EAGER;
    }
    protected function getAbilityTitle() : string
    {
        return _('Eager');
    }

    protected function getAbilityText() : string
    {
        return sprintf(_("During <b>SELECTION</b>, when you place this dog on the Lead, gain _icon-%s_."), $this->eagerResource);
    }

}