<?php


use objects\ForecastCard;

class ForecastManager
{

    public function __construct() {}

    public function fillForecast()
    {
        $cards = ForecastCard::fromArray(DogPark::$instance->forecastCards->getCardsInLocation(LOCATION_DECK));
        if (DogPark::$instance->getGameStateValue(VARIANT_PREDICTABLE_FORECAST_OPTION) == VARIANT_PACKED_PARK_OPTION_INCLUDED) {
            $cards = array_filter($cards, fn($card) => in_array($card->typeArg, [8, 9, 10, 11]));
        }

        for ($i = 1; $i <= 4; $i++) {
            $card = array_shift($cards);
            if ($i == 1 && $card->typeArg == 11) { // CARD 11 CANT BE PLACED IN SPOT 1
                $newCard = array_shift($cards);
                $cards = [$card, ...$cards];
                $card = $newCard;
            }
            DogPark::$instance->forecastCards->moveCard($card->id, LOCATION_FORECAST, $i);
        }
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
        if ($currentForecast->typeArg == 11) {
            // Forecast card 11 allows players to place 4 dogs on their lead during selection instead of 3.
            return 4;
        }
        return 3;
    }
}