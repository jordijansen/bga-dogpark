<?php


use objects\DogWalker;

class DogWalkPark
{

    public function __construct() {}

    /**
     * @param $playerIds
     * @return DogWalker[]
     */
    public function moveWalkersOfPlayersToStartOfPark($playerIds): array
    {
        $walkers = [];
        foreach ($playerIds as $playerId) {
            $walkerId = DogPark::$instance->playerManager->getWalkerId($playerId);
            DogPark::$instance->dogWalkers->moveCard($walkerId, LOCATION_PARK, 0);
            $walkers[] = DogWalker::from(DogPark::$instance->dogWalkers->getCard($walkerId));
        }
        return $walkers;
    }

    public function getWalkers() {
        return DogWalker::fromArray(DogPark::$instance->dogWalkers->getCardsInLocation(LOCATION_PARK));
    }

}