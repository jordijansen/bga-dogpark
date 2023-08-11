<?php

namespace objects\dogtraits;

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
        return _('Go Fetch!');
    }

    protected function getAbilityText() : string
    {
        return sprintf(_("When <b>WALKING</b> this dog, whenever you gain 1 or more _icon-%s_, gain _icon-%s_. Activates once per movement."), $this->goFetchResource, $this->goFetchBonusResource);
    }

    public function isAbilityAutoResolve() : bool
    {
        return true;
    }
}