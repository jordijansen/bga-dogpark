<?php

namespace commands;

use DogPark;
use objects\DogCard;

class ScoutCommand extends BaseCommand
{
    private int $playerId;
    private int $fieldDogId;
    private int $scoutDogId;

    public function __construct(int $playerId, int $fieldDogId, int $scoutDogId)
    {
        $this->playerId = $playerId;
        $this->fieldDogId = $fieldDogId;
        $this->scoutDogId = $scoutDogId;
    }

    public function do()
    {
        $fieldDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->fieldDogId));

        DogPark::$instance->dogCards->moveCard($this->fieldDogId, LOCATION_DISCARD);
        DogPark::$instance->dogCards->moveCard($this->scoutDogId, LOCATION_FIELD, $fieldDog->locationArg);

        $scoutedDogIds = DogPark::$instance->getGlobalVariable(SCOUTED_CARDS);
        $scoutedDogIdsNew = array_filter($scoutedDogIds, function ($id) { return $id != $this->scoutDogId;});
        DogPark::$instance->setGlobalVariable(SCOUTED_CARDS, [...$scoutedDogIdsNew]);

        $fieldDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->fieldDogId));
        $scoutDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->scoutDogId));

        DogPark::$instance->notifyAllPlayers('playerScoutReplaces', clienttranslate('${player_name} scouts and replaces <b>${fieldDogName}</b> with <b>${scoutDogName}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'scoutDog' => $scoutDog,
            'scoutDogName' => $scoutDog->name,
            'fieldDog' => $fieldDog,
            'fieldDogName' => $fieldDog->name,
        ]);
    }

    public function undo()
    {
        $fieldDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->scoutDogId));
        $scoutDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->fieldDogId));

        DogPark::$instance->dogCards->moveCard($fieldDog->id, LOCATION_DISCARD);
        DogPark::$instance->dogCards->moveCard($scoutDog->id, LOCATION_FIELD, $fieldDog->locationArg);

        $scoutedDogIds = DogPark::$instance->getGlobalVariable(SCOUTED_CARDS);
        DogPark::$instance->setGlobalVariable(SCOUTED_CARDS, [...$scoutedDogIds, $fieldDog->id]);

        $fieldDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->scoutDogId));
        $scoutDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->fieldDogId));

        DogPark::$instance->notifyAllPlayers('undoPlayerScoutReplaces', clienttranslate('Undo: ${player_name} scouts and replaces <b>${fieldDogName}</b> with <b>${scoutDogName}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'scoutDog' => $scoutDog,
            'scoutDogName' => $scoutDog->name,
            'fieldDog' => $fieldDog,
            'fieldDogName' => $fieldDog->name,
        ]);
    }
}