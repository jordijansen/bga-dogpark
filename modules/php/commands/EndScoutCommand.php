<?php

namespace commands;

use actions\AdditionalAction;
use DogPark;

class EndScoutCommand extends BaseCommand
{
    private int $playerId;
    private string $actionId;

    public function __construct(int $playerId, string $actionId)
    {
        $this->playerId = $playerId;
        $this->actionId = $actionId;
    }

    public function do()
    {
        $firstSearchAndRescueDog = DogPark::$instance->dogManager->getFirstDogOnLeadWithAbility($this->playerId, SEARCH_AND_RESCUE);
        if ($firstSearchAndRescueDog != null) {
            DogPark::$instance->actionManager->addAction($this->playerId, new AdditionalAction(USE_DOG_ABILITY, (object) [
                "dogId" => $firstSearchAndRescueDog->id,
                "dogName" => $firstSearchAndRescueDog->name,
                "abilityTitle" => $firstSearchAndRescueDog->abilityTitle
            ], $firstSearchAndRescueDog->isAbilityOptional(), true, $this->actionId));
        }
    }

    public function undo()
    {
        DogPark::$instance->dogManager->undoWalkingAdditionalActionForDogsOnLead($this->playerId, $this->actionId);
    }
}