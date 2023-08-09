<?php

namespace objects;

use DogPark;

class AutoWalker
{
    public int $id;
    public string $name;
    public string $color;

    public function __construct($id, $color)
    {
        $this->id = $id;
        $this->name = "Autowalker #$id";
        $this->color = $color;
    }

    public function takeRecruitmentTurn()
    {
        $breedExpertCards = DogPark::$instance->breedExpertAwardManager->getExpertAwards();
        $dogsInField = DogPark::$instance->dogField->getDogCards();

        foreach ($breedExpertCards as $breedExpertCard) {
            $suitableDogs = array_filter($dogsInField, function($dog) use($breedExpertCard){return in_array($breedExpertCard->type, $dog->breeds);});
            foreach ($suitableDogs as $suitableDog) {
                $walkersField = $suitableDog->location. '_' .$suitableDog->locationArg;
                $walkersInField = DogPark::$instance->dogField->getWalkersInField($walkersField);
                $autoWalkersInField = array_filter($walkersInField, function($walker) {return $walker->typeArg <= 2;});
                if (sizeof($autoWalkersInField) == 0) {
                    $locationArgForWalker = sizeof(DogPark::$instance->dogWalkers->getCardsInLocation('field_'.$suitableDog->locationArg)) + 1;
                    DogPark::$instance->dogWalkers->moveAllCardsInLocation(LOCATION_PLAYER, 'field_'.$suitableDog->locationArg, $this->id, $locationArgForWalker);
                    DogPark::$instance->playerManager->updatePlayerOfferValue($this->id, DogPark::$instance->getNextAutoWalkerDiceValue());

                    DogPark::$instance->notifyAllPlayers('dogOfferPlaced', clienttranslate('${name} places an offer on <b>${dogName}</b>'),[
                        'i18n' => ['dogName'],
                        'playerId' => $this->id,
                        'name' => $this->name,
                        'dogName' => $suitableDog->name,
                        'walker' => DogPark::$instance->playerManager->getWalker($this->id)
                    ]);

                    return;
                }
            }
        }


    }

}