<?php


use objects\ForecastCard;

class ForecastManager
{

    public function __construct() {}

    public function fillForecast()
    {
        DogPark::$instance->forecastCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_FORECAST, 1);

        $forecastCardDrawn = $this->getForeCastCardForRound(1);
        if ($forecastCardDrawn->typeArg == 11) {
            // Forecast 11 card can not be used in the first round.
            DogPark::$instance->forecastCards->moveCard($forecastCardDrawn->id, LOCATION_FORECAST, 2);
            DogPark::$instance->forecastCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_FORECAST, 1);
        } else {
            DogPark::$instance->forecastCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_FORECAST, 2);
        }
        DogPark::$instance->forecastCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_FORECAST, 3);
        DogPark::$instance->forecastCards->pickCardsForLocation(1, LOCATION_DECK, LOCATION_FORECAST, 4);
    }

    /**
     * @return ForecastCard[]
     */
    public function getForeCastCards(): array
    {
        return ForecastCard::fromArray(DogPark::$instance->forecastCards->getCardsInLocation(LOCATION_FORECAST));
    }

    public function getCurrentForecastCard(): ForecastCard
    {
        $currentRound = DogPark::$instance->getGlobalVariable(CURRENT_ROUND);
        return $this->getForeCastCardForRound($currentRound);
    }

    public function getForeCastCardForRound(int $round): ForecastCard
    {
        return current(ForecastCard::fromArray(DogPark::$instance->forecastCards->getCardsInLocation(LOCATION_FORECAST, $round)));
    }

    public function getCurrentRoundMaxNumberOfDogsForSelection(): int
    {
        $currentForecast = $this->getCurrentForecastCard();
        if ($currentForecast->id == 11) {
            // Forecast card 11 allows players to place 4 dogs on their lead during selection instead of 3.
            return 4;
        }
        return 3;
    }
}