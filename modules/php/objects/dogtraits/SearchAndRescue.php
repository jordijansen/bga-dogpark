<?php

namespace objects\dogtraits;

trait SearchAndRescue
{
    protected function getAbility() : string
    {
        return SEARCH_AND_RESCUE;
    }

    protected function getAbilityTitle() : string
    {
        return _('Search and Rescue');
    }

    protected function getAbilityText() : string
    {
        return _("When <b>WALKING</b> this dog, whenever you _icon-scout_, you may immediately _icon-swap_.");
    }

    public function isAbilityOptional() : bool
    {
        return true;
    }
}