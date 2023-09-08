<?php

namespace traits;
trait SetupTrait
{

    //////////////////////////////////////////////////////////////////////////////
    //////////// Setup
    //////////////////////////////////////////////////////////////////////////////

    /*
        setupNewGame:

        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, player_score) VALUES ";
        $extraSql = "INSERT INTO extra_player (player_id) VALUES ";
        $values = [];
        $extraValues = [];
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."', 5)";
            $extraValues[] = "('".$player_id."')";
        }
        $sql .= implode( ',', $values );
        $extraSql .= implode( ',', $extraValues );
        self::DbQuery( $extraSql );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();


        $autoWalkers = $this->getAutoWalkers();
        foreach ($autoWalkers as $autoWalker) {
            self::DbQuery("INSERT INTO extra_player (player_id) VALUES ($autoWalker->id)");
        }

        $this->playerManager->setInitialPlayerOder();
        $this->playerManager->setInitialResources();

        /************ Init Global Variables *************/
        $this->setGlobalVariable(CURRENT_ROUND, 1);
        $this->setGlobalVariable(CURRENT_PHASE, PHASE_SET_UP);
        $this->setGlobalVariable(OFFER_VALUE_REVEALED, false);
        $this->setGlobalVariable(OBJECTIVES_REVEALED, false);
        $this->setGlobalVariable(USED_WALK_ABILITIES, []);

        /************ Create Card Decks *****************/
        $this->createDogCards();
        $this->createWalkers();
        $this->createBreedCards();
        $this->createForecastCards();
        $this->createLocationBonusCards();
        $this->createObjectiveCards();

        /************ Start the game initialization *****/
        $this->dogField->fillField();
        $this->dogWalkPark->drawLocationBonusCardAndFillPark();
        $this->breedExpertAwardManager->fillExpertAwards();
        $this->forecastManager->fillForecast();
        $this->playerManager->dealObjectiveCardsToPlayers();
        /************ End of the game initialization *****/

        $this->gamestate->setAllPlayersMultiactive();
    }

    private function createDogCards() {
        $cards = array();
        // Load BASE_GAME dogs
        for($i = 1; $i < 163; $i++) {
            $cards[] = array( 'type' => BASE_GAME, 'type_arg' => $i, 'nbr' => 1);
        }

        $this->dogCards->createCards($cards, LOCATION_DECK);
        $this->dogCards->shuffle(LOCATION_DECK);
    }

    private function createWalkers() {
        $players = $this->loadPlayersBasicInfos();
        foreach( $players as $playerId => $player )
        {
            $cards = array();
            $cards[] = array( 'type' => $player['player_color'], 'type_arg' => $playerId, 'nbr' => 1);
            $this->dogWalkers->createCards($cards, LOCATION_PLAYER, $playerId);
        }

        $autoWalkers = $this->getAutoWalkers();
        foreach ($autoWalkers as $autoWalker)
        {
            $cards = array();
            $cards[] = array( 'type' => $autoWalker->color, 'type_arg' => $autoWalker->id, 'nbr' => 1);
            $this->dogWalkers->createCards($cards, LOCATION_PLAYER, $autoWalker->id);
        }
    }
    private function createBreedCards() {
        $cards = array();
        foreach ($this->BREED_EXPERT_CARDS as $id => $breed) {
            $cards[] = array( 'type' => $breed, 'type_arg' => $id, 'nbr' => 1);
        }

        $this->breedCards->createCards($cards, LOCATION_DECK);
        $this->breedCards->shuffle(LOCATION_DECK);
    }

    private function createForecastCards() {
        $cards = array();
        foreach ($this->FORECAST_CARDS[BASE_GAME] as $id => $foreCastCard) {
            $cards[] = array( 'type' => BASE_GAME, 'type_arg' => $id, 'nbr' => 1);
        }

        $this->forecastCards->createCards($cards, LOCATION_DECK);
        $this->forecastCards->shuffle(LOCATION_DECK);
    }

    private function createLocationBonusCards() {
        $cards = array();
        $locationBonusType = $this->determineLocationBonusCardsToUse();
        foreach ($this->LOCATION_BONUS_CARDS[$locationBonusType] as $id => $locationBonusCard) {
            $cards[] = array( 'type' => $locationBonusType, 'type_arg' => $id, 'nbr' => 1);
        }

        $this->locationBonusCards->createCards($cards, LOCATION_DECK);
        $this->locationBonusCards->shuffle(LOCATION_DECK);
    }

    private function createObjectiveCards() {
        $cards = array();
        foreach ($this->OBJECTIVE_CARDS as $id => $objectiveCard) {
            $cards[] = array( 'type' => $objectiveCard['type'], 'type_arg' => intval($id), 'nbr' => 1);
        }

        $this->objectiveCards->createCards($cards, LOCATION_DECK);
        $this->objectiveCards->shuffle(LOCATION_DECK);
    }

    private function determineLocationBonusCardsToUse() {
        if ($this->getGameStateValue(VARIANT_PACKED_PARK_OPTION) == VARIANT_PACKED_PARK_OPTION_INCLUDED) {
            return LOCATION_BONUS_REROUTED;
        }

        if ($this->getGameStateValue(VARIANT_GENTLE_WALK_OPTION) == VARIANT_GENTLE_WALK_OPTION_INCLUDED) {
            return LOCATION_BONUS_PLENTIFUL;
        }

        if ($this->getPlayersNumber() >= 4) {
            return LOCATION_BONUS_PLENTIFUL;
        } else {
            return LOCATION_BONUS_REROUTED;
        }
    }
}