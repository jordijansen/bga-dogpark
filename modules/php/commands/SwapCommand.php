<?php

namespace commands;

use DogPark;
use objects\DogCard;

class SwapCommand extends BaseCommand
{
    private int $playerId;
    private string $actionId;
    private int $fieldDogId;
    private int $kennelDogId;

    private int $walked;
    private int $ball;
    private int $stick;
    private int $toy;
    private int $treat;

    public function __construct(int $playerId, string $actionId, int $fieldDogId, int $kennelDogId, array $resourceOnCard)
    {
        $this->playerId = $playerId;
        $this->actionId = $actionId;
        $this->fieldDogId = $fieldDogId;
        $this->kennelDogId = $kennelDogId;
        $this->walked = $resourceOnCard[WALKED];
        $this->ball = $resourceOnCard[RESOURCE_BALL];
        $this->stick = $resourceOnCard[RESOURCE_STICK];
        $this->toy = $resourceOnCard[RESOURCE_TOY];
        $this->treat = $resourceOnCard[RESOURCE_TREAT];
    }

    public function do()
    {
        $fieldDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->fieldDogId));

        DogPark::$instance->dogManager->removeAllResources($this->kennelDogId);

        DogPark::$instance->dogCards->moveCard($this->kennelDogId, LOCATION_FIELD, $fieldDog->locationArg);
        DogPark::$instance->dogCards->moveCard($this->fieldDogId, LOCATION_PLAYER, $this->playerId);

        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        if (property_exists($action->additionalArgs, 'leavingTheParkOtherActionId')) {
            DogPark::$instance->dogManager->addResource($fieldDog->id, WALKED);
            DogPark::$instance->actionManager->markActionPerformed($this->playerId, $action->additionalArgs->leavingTheParkOtherActionId);
        }

        $fieldDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->fieldDogId));
        $kennelDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->kennelDogId));
        DogPark::$instance->notifyAllPlayers('playerSwaps', clienttranslate('${player_name} swaps <b>${kennelDogName}</b> with <b>${fieldDogName}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'kennelDog' => $kennelDog,
            'kennelDogName' => $kennelDog->name,
            'fieldDog' => $fieldDog,
            'fieldDogName' => $fieldDog->name,
        ]);

        DogPark::$instance->actionManager->markActionPerformed($this->playerId, $this->actionId);
    }

    public function undo()
    {
        $fieldDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->kennelDogId));

        DogPark::$instance->dogManager->setResource($this->kennelDogId, $this->walked, $this->stick, $this->ball, $this->treat, $this->toy);
        DogPark::$instance->dogManager->removeAllResources($this->fieldDogId);
        DogPark::$instance->dogCards->moveCard($this->fieldDogId, LOCATION_FIELD, $fieldDog->locationArg);
        DogPark::$instance->dogCards->moveCard($this->kennelDogId, LOCATION_PLAYER, $this->playerId);

        $fieldDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->fieldDogId));
        $kennelDog = DogCard::from(DogPark::$instance->dogCards->getCard($this->kennelDogId));
        DogPark::$instance->notifyAllPlayers('playerSwaps', clienttranslate('Undo: ${player_name} swaps <b>${kennelDogName}</b> with <b>${fieldDogName}</b>'),[
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'kennelDog' => $fieldDog,
            'kennelDogName' => $kennelDog->name,
            'fieldDog' => $kennelDog,
            'fieldDogName' => $fieldDog->name,
        ]);

        $action = DogPark::$instance->actionManager->getAction($this->playerId, $this->actionId);
        DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $this->actionId);
        if (property_exists($action->additionalArgs, 'leavingTheParkOtherActionId')) {
            DogPark::$instance->actionManager->unmarkActionPerformed($this->playerId, $action->additionalArgs->leavingTheParkOtherActionId);
        }
    }
}