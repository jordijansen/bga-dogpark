<?php


use objects\DogWalker;
use objects\LocationBonus;
use objects\LocationBonusCard;

class DogWalkPark extends APP_DbObject
{
    public function __construct() {

    }

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

    public function drawLocationBonusCardAndFillPark()
    {
        $this->removeRemainingLocationBonuses();

        $locationCard = LocationBonusCard::from(current(DogPark::$instance->locationBonusCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_PARK, intval(DogPark::$instance->getGlobalVariable(CURRENT_ROUND)))));
        $query = "INSERT INTO `extra_location_bonus` (`location_id`, `bonus`) VALUES ";
        foreach ($locationCard->bonusesOnLocation as $locationId => $bonuses) {
            foreach ($bonuses as $bonusIndex => $bonus) {
                $query .= "($locationId, '$bonus'),";
            }
        }
        $query = rtrim($query, ',');
        $query .= ";";

        self::DbQuery($query);

        DogPark::$instance->notifyAllPlayers('newLocationBonusCardDrawn', clienttranslate('The park is replenished with new bonuses'),[
            'locationCard' => $locationCard
        ]);

    }

    /**
     * @return LocationBonusCard[]
     */
    public function getLocationBonusCards() {
        return LocationBonusCard::fromArray(DogPark::$instance->locationBonusCards->getCardsInLocation(LOCATION_PARK, null, 'card_location_arg'));
    }


    /**
     * @return LocationBonus[]
     */
    public function getAllLocationBonuses() {
        return LocationBonus::fromArray(self::getCollectionFromDB("SELECT * FROM `extra_location_bonus`"));
    }

    /**
     * @return LocationBonus[]
     */
    public function getLocationBonus($locationId) {
        return LocationBonus::fromArray(self::getCollectionFromDB("SELECT * FROM `extra_location_bonus` WHERE `location_id` = $locationId"));
    }

    public function removeRemainingLocationBonuses() {
        self::DbQuery("DELETE FROM `extra_location_bonus`");
    }

}