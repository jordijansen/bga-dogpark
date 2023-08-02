<?php

namespace commands;

use DogPark;
use objects\DogWalker;

class MoveWalkerCommand extends BaseCommand
{
    private int $playerId;
    private int $walkerId;
    private int $currentLocation;
    private int $newLocation;

    public function __construct(int $playerId, int $walkerId, int $currentLocation, int $newLocation)
    {
        $this->playerId = $playerId;
        $this->walkerId = $walkerId;
        $this->currentLocation = $currentLocation;
        $this->newLocation = $newLocation;
    }

    public function do()
    {
        DogPark::$instance->dogWalkers->moveCard($this->walkerId, LOCATION_PARK, $this->newLocation);

        DogPark::$instance->notifyAllPlayers('moveWalker', clienttranslate('${player_name} moves their walker'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'walker' => DogWalker::from(DogPark::$instance->dogWalkers->getCard($this->walkerId))
        ]);
    }

    public function undo()
    {
        DogPark::$instance->dogWalkers->moveCard($this->walkerId, LOCATION_PARK, $this->currentLocation);

        DogPark::$instance->notifyAllPlayers('moveWalker', clienttranslate('Undo: ${player_name} moves their walker back'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'walker' => DogWalker::from(DogPark::$instance->dogWalkers->getCard($this->walkerId))
        ]);
    }
}