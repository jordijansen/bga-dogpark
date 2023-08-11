<?php

namespace objects\dogtraits;

trait Crafty
{
    public string $craftyResource;

    protected function getAbility() : string
    {
        return CRAFTY;
    }

    protected function getAbilityTitle() : string
    {
        return _('Crafty');
    }

    protected function getAbilityText() : string
    {
        return sprintf(_("During SELECTION, when you place this dog on the Lead, you may discard 1 _icon-all-resources_ to gain up to 1 _icon-%s_."), $this->craftyResource);
    }

    public function isAbilityAutoResolve() : bool
    {
        return false;
    }

    public function isAbilityOptional() : bool
    {
        return true;
    }
}