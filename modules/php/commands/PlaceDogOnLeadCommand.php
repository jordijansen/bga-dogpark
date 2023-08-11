<?php

namespace commands;

use actions\AdditionalAction;
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
    private string $originActionId;

    public function __construct(int $playerId, int $dogId, array $resources)
    {
        $this->playerId = $playerId;
        $this->dogId = $dogId;
        $this->resources = $resources;
        $this->originActionId = AdditionalAction::newId();
    }

    public function do()
    {
        DogPark::$instance->playerManager->payResources($this->playerId, $this->resources);
        DogPark::$instance->dogCards->moveCard($this->dogId, LOCATION_LEAD, $this->playerId);
        DogPark::$instance->dogManager->addResource($this->dogId, WALKED);

        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($this->dogId));
        DogPark::$instance->notifyAllPlayers('dogPlacedOnLead', clienttranslate('${player_name} places <b>${dogName}</b> on lead'),[
            'i18n' => ['dogName'],
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dogName' => $dog->name,
            'dog' => $dog,
            'resources' => $this->resources
        ]);

        if (in_array($dog->ability, SELECTION_ABILITIES)) {
            DogPark::$instance->actionManager->addAction($this->playerId, new AdditionalAction(USE_DOG_ABILITY, (object) [
                "dogId" => $this->dogId,
                "dogName" => $dog->name,
                "abilityTitle" => $dog->abilityTitle
            ], $dog->isAbilityOptional(), true, $this->originActionId));
        }
    }

    public function undo()
    {
        DogPark::$instance->playerManager->gainResources($this->playerId, $this->resources);
        DogPark::$instance->dogCards->moveCard($this->dogId, LOCATION_PLAYER, $this->playerId);
        DogPark::$instance->dogManager->removeResource($this->dogId, WALKED);

        $dog = DogCard::from(DogPark::$instance->dogCards->getCard($this->dogId));
        DogPark::$instance->notifyAllPlayers('undoDogPlacedOnLead',clienttranslate('Undo: ${player_name} places <b>${dogName}</b> on lead'),[
            'i18n' => ['dogName'],
            'playerId' => $this->playerId,
            'player_name' => DogPark::$instance->getPlayerName($this->playerId),
            'dogName' => $dog->name,
            'dog' => $dog,
            'resources' => $this->resources
        ]);

        DogPark::$instance->actionManager->removeActionsForOriginActionId($this->playerId, $this->originActionId);
    }
}