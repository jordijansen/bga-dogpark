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
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( ',', $values );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();

        /************ Create Card Decks *****************/

        $this->createDogCards();
        $this->createWalkers();

        /************ Start the game initialization *****/
        // Fill the field with dogs
        $this->dogField->fillField();

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    private function createDogCards() {
        $cards = array();
        // Load BASE_GAME dogs
        foreach ($this->DOG_CARDS[BASE_GAME] as $id => $DOG_CARD) {
            $cards[] = array( 'type' => BASE_GAME, 'type_arg' => $id, 'nbr' => 1);
        }

        $this->dogCards->createCards($cards, 'deck');
        $this->dogCards->shuffle('deck');
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
}