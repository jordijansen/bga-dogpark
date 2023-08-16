<?php

namespace objects\dogtraits;

trait StickChaser
{
    protected function getAbility() : string
    {
        return STICK_CHASER;
    }

    protected function getAbilityTitle() : string
    {
        return _('Stick Chaser');
    }

    protected function getAbilityText() : string
    {
        return _('During <b>FINAL SCORING</b>, gain 1 _icon-reputation_ for each leftover _icon-stick_ assigned to this dog. Max. 6 _icon-reputation_.');
    }
}