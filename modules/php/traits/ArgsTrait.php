<?php

namespace traits;
trait ArgsTrait
{

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    //////////////////////////////////////////////////////////////////////////////
    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    function argRecruitmentOffer(): array
    {
        return [
            'maxOfferValue' => min($this->getPlayerScore($this->getActivePlayerId()), 5)
        ];
    }

    function argSelectionActions(): array
    {
        return [];
    }
}