<?php

namespace objects\dogtraits;

trait Obedient
{
    public string $obedientResource;

    protected function getAbility() : string
    {
        return OBEDIENT;
    }
    protected function getAbilityTitle() : string
    {
        return _('Obedient');
    }

    protected function getAbilityText() : string
    {
        return sprintf(_("When <b>WALKING</b> this dog, whenever you gain 1 or more <icon-%s>, gain 1 <icon-reputation>. Activates once per movement."), $this->obedientResource);
    }
}