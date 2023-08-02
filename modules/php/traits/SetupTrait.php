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
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."', 5)";
        }
        $sql .= implode( ',', $values );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();

        $this->playerManager->setInitialPlayerOder();
        $this->playerManager->setInitialResources();

        /************ Init Global Variables *************/
        $this->setGlobalVariable(CURRENT_ROUND, 1);
        $this->setGlobalVariable(CURRENT_PHASE, PHASE_SET_UP);
        $this->setGlobalVariable(OFFER_VALUE_REVEALED, false);

        /************ Create Card Decks *****************/
        $this->createDogCards();
        $this->createWalkers();
        $this->createBreedCards();
        $this->createForecastCards();
        $this->createLocationBonusCards();
        $this->createObjectiveCards();

        /************ Start the game initialization *****/
        $this->dogField->fillField();
        $this->breedExpertAwardManager->fillExpertAwards();
        $this->forecastManager->fillForecast();
        $this->playerManager->dealObjectiveCardsToPlayers();
        /************ End of the game initialization *****/

        $this->gamestate->setAllPlayersMultiactive();
    }

    private function createDogCards() {
        $cards = array();
        // Load BASE_GAME dogs
        foreach ($this->DOG_CARDS[BASE_GAME] as $id => $DOG_CARD) {
            $cards[] = array( 'type' => BASE_GAME, 'type_arg' => $id, 'nbr' => 1);
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
        foreach ($this->LOCATION_BONUS_CARDS as $locationBonusType => $locationBonusCards) {
            foreach ($locationBonusCards as $id => $locationBonusCard) {
                $cards[] = array( 'type' => $locationBonusType, 'type_arg' => $id, 'nbr' => 1);
            }
        }

        $this->locationBonusCards->createCards($cards, LOCATION_DECK);
        $this->locationBonusCards->shuffle(LOCATION_DECK);
    }

    private function createObjectiveCards() {
        $cards = array();
        foreach ($this->OBJECTIVE_CARDS as $objectiveType => $objectiveCards) {
            foreach ($objectiveCards as $id => $objectiveCard) {
                $cards[] = array( 'type' => $objectiveType, 'type_arg' => intval($id), 'nbr' => 1);
            }
        }

        $this->objectiveCards->createCards($cards, LOCATION_DECK);
        $this->objectiveCards->shuffle(LOCATION_DECK);
    }
}