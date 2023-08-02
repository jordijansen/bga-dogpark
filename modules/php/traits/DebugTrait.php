<?php

namespace traits;
trait DebugTrait
{

    function jumpState()
    {
        $this->gamestate->jumpToState(50);
    }
}
