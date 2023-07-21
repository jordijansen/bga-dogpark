<?php

namespace managers;

use DogPark;
use objects\DogCard;
use objects\DogWalker;

class DogManager
{
    function recruitDog($playerId, $dogId, $reputationCost, $dogWalkerId) {
        $playerScore = DogPark::$instance->getPlayerScore($playerId);
        $newScore = $playerScore - $reputationCost;
        DogPark::$instance->updatePlayerScore($playerId, $newScore);

        DogPark::$instance->dogCards->moveCard($dogId, LOCATION_PLAYER, $playerId);

        $dogCard = DogCard::from(DogPark::$instance->dogCards->getCard($dogId));
        DogPark::$instance->notifyAllPlayers('dogRecruited', clienttranslate('${player_name} pays ${reputationCost} and recruits Doggo'),[
            'playerId' => $playerId,
            'player_name' => DogPark::$instance->getPlayerName($playerId),
            'reputationCost' => $reputationCost,
            'dog' => $dogCard,
            'score' => $newScore,
            'walker' => DogWalker::from(DogPark::$instance->dogWalkers->getCard($dogWalkerId))
        ]);
    }

}