<?php

namespace commands;

use DogPark;
use objects\DogCard;

class PlaceDogOnLeadCommand extends BaseCommand
{
    private int $playerId;
    private int $dogId;
    /**
     * @var string[]
     */
    private $resources;

    public function __construct(int $playerId, int $dogId, array $resources)
    {
        $this->playerId = $playerId;
        $this->dogId = $dogId;
        $this->resources = $resources;
    }

    public function do()
    {
        DogPark::$instance->playerManager->payResources($this->playerId, $this->resources);
        DogPark::$instance->dogCards->moveCard($this->dogId, LOCATION_LEAD, $this->playerId);

        DogPark::$instance->notifyAllPlayers('dogPlacedOnLead', clienttranslate('${player_name} places dog on lead'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => DogCard::from(DogPark::$instance->dogCards->getCard($this->dogId)),
            'resources' => $this->resources
        ]);
    }

    public function undo()
    {
        DogPark::$instance->playerManager->gainResources($this->playerId, $this->resources);
        DogPark::$instance->dogCards->moveCard($this->dogId, LOCATION_PLAYER, $this->playerId);

        DogPark::$instance->notifyAllPlayers('undoDogPlacedOnLead',clienttranslate('${player_name} places a dog back in their kennel'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dog' => DogCard::from(DogPark::$instance->dogCards->getCard($this->dogId)),
            'resources' => $this->resources
        ]);
    }
}