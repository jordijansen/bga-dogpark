<?php

namespace objects\dogtraits;

trait PackDog
{
    protected function getAbility() : string
    {
        return PACK_DOG;
    }

    protected function getAbilityTitle() : string
    {
        return _('Pack Dog');
    }

    protected function getAbilityText() : string
    {
        return sprintf(_("During <b>FINAL SCORING</b>, gain 2 <icon-reputation> for each ^%s^ in your Kennel."), current($this->breeds));
    }
}