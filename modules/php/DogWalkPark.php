<?php


use actions\AdditionalAction;
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

    public function moveWalkersOfAutoWalkersToStartOfPark(): array
    {
        $walkers = [];
        $autoWalkers = DogPark::$instance->getAutoWalkers();
        foreach ($autoWalkers as $autoWalker) {
            $walkerId = DogPark::$instance->playerManager->getWalkerId($autoWalker->id);
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
     * @return String[]
     */
    public function getLocationBonuses($locationId) {
        $locationBonuses = DogPark::$instance->PARK_LOCATIONS[$locationId]['bonus'];
        return [...$locationBonuses];
    }

    public function getExtraLocationBonuses($locationId) {
        $extraLocationBonuses = array_keys(self::getCollectionFromDB("SELECT bonus FROM `extra_location_bonus` WHERE `location_id` = $locationId"));
        return [...$extraLocationBonuses];
    }

    public function removeRemainingLocationBonuses() {
        self::DbQuery("DELETE FROM `extra_location_bonus`");
    }

    public function getPossibleParkLocationIds(int $walkerId)
    {
        $walker = DogWalker::from(DogPark::$instance->dogWalkers->getCard($walkerId));
        if ($walker->location != LOCATION_PARK) {
            throw new BgaUserException('Walker is not in park for player');
        }

        return $this->getNextLocations($walker->locationArg, 0, 4, []);
    }

    public function getNextLocations(int $locationId, int $currentDepth, int $maxDepth, array $result, bool $exact = false) {
        $location = DogPark::$instance->PARK_LOCATIONS[$locationId];
        if ($currentDepth == $maxDepth) {
            return $result;
        }

        $newResult = [...$result];
        foreach ($location['nextLocations'] as $newLocationId) {
            // If the location is blocked by a BLOCK token, we skip the location
            $newDepth = $currentDepth;
            if ($this->isLocationAccessible($newLocationId)) {
                if ($exact && $currentDepth == ($maxDepth - 1)) {
                    $newResult = [...$newResult, $newLocationId];
                } else if(!$exact) {
                    $newResult = [...$newResult, $newLocationId];
                }
                $newDepth += 1;
            }
            $newResult = $this->getNextLocations($newLocationId, $newDepth, $maxDepth, $newResult, $exact);
        }
        return $newResult;
    }

    public function isLocationAccessible($locationId): bool
    {
        // Only available in a 4 player game
        if ($locationId == 91 && DogPark::$instance->getPlayersNumber() < 4) {
            return false;
        }
        if ($locationId > 90) {
            $otherWalkersInLocation = DogPark::$instance->dogWalkers->getCardsInLocation(LOCATION_PARK, $locationId);
            if (sizeof($otherWalkersInLocation) > 0) {
                return false;
            }
        }
        $locationBonuses = [...$this->getLocationBonuses($locationId), ...$this->getExtraLocationBonuses($locationId)];
        return !in_array(BLOCK, $locationBonuses);
    }

    public function addExtraLocationBonus(int $locationId, $bonus)
    {
        self::DbQuery("INSERT INTO `extra_location_bonus` (`location_id`, `bonus`) VALUES ($locationId, '$bonus')");
    }

    public function removeExtraLocationBonus(int $locationId, $bonus)
    {
        self::DbQuery("DELETE FROM `extra_location_bonus` WHERE location_id = $locationId AND bonus = '$bonus';");
    }

    public function createLocationBonusActions($playerId, $locationId, $originActionId = null) {
        $locationBonuses = $this->getLocationBonuses($locationId);
        $extraLocationBonuses = $this->getExtraLocationBonuses($locationId);
        DogPark::$instance->actionManager->addActions($playerId, array_map(fn($bonus) => new AdditionalAction(WALKING_GAIN_LOCATION_BONUS, (object) ["bonusType" => $bonus, "extraBonus" => false],in_array($bonus, [SWAP, SCOUT]), $bonus != SCOUT, $originActionId), $locationBonuses));
        DogPark::$instance->actionManager->addActions($playerId, array_map(fn($bonus) => new AdditionalAction(WALKING_GAIN_LOCATION_BONUS, (object) ["bonusType" => $bonus, "extraBonus" => true], in_array($bonus, [SWAP, SCOUT]), $bonus != SCOUT, $originActionId), $extraLocationBonuses));
    }
}