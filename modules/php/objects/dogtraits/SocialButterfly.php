<?php

namespace objects\dogtraits;

trait SocialButterfly
{
    protected function getAbility() : string
    {
        return SOCIAL_BUTTERFLY;
    }
    protected function getAbilityTitle() : string
    {
        return _('Social Butterfly');
    }

    protected function getAbilityText() : string
    {
        return _("When <b>WALKING</b> this dog, if you land on an occupied location, do not pay 1 _icon-reputation_ to gain the location reward.");
    }

    public function isAbilityOptional() : bool
    {
        return false;
    }
}