<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * DogPark implementation : © Jordi Jansen <jordi@itbyjj.com>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */

use actions\ActionManager;
use commands\CommandManager;
use managers\DogField;
use managers\DogManager;
use managers\PlayerManager;
use objects\DogCard;
use objects\DogWalker;
use objects\ObjectiveCard;
use traits\ActionTrait;
use traits\ArgsTrait;
use traits\DebugTrait;
use traits\SetupTrait;
use traits\StateTrait;
use traits\UtilsTrait;

require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );

require_once('modules/php/Constants.inc.php');

require_once('modules/php/commands/CommandManager.php');
require_once('modules/php/commands/BaseCommand.php');
require_once('modules/php/commands/PlaceDogOnLeadCommand.php');
require_once('modules/php/commands/MoveWalkerCommand.php');
require_once('modules/php/commands/GainLocationBonusCommand.php');
require_once('modules/php/commands/PayReputationForLocationCommand.php');
require_once('modules/php/commands/GainLeavingTheParkBonusCommand.php');
require_once('modules/php/commands/SwapCommand.php');
require_once('modules/php/commands/ScoutCommand.php');
require_once('modules/php/commands/EndScoutCommand.php');
require_once('modules/php/commands/EagerDogAbilityCommand.php');
require_once('modules/php/commands/CraftyDogAbilityCommand.php');
require_once('modules/php/commands/GoFetchDogAbilityCommand.php');
require_once('modules/php/commands/ObedientDogAbilityCommand.php');
require_once('modules/php/commands/PlaymateDogAbilityCommand.php');
require_once('modules/php/commands/SocialButterflyDogAbilityCommand.php');
require_once('modules/php/commands/ActivateForecastCardCommand.php');

require_once('modules/php/objects/Card.php');
require_once('modules/php/objects/BreedExpertCard.php');
require_once('modules/php/objects/ForecastCard.php');
require_once('modules/php/objects/ObjectiveCard.php');
require_once('modules/php/objects/LocationBonusCard.php');
require_once('modules/php/objects/LocationBonus.php');
require_once('modules/php/objects/DogCard.php');
require_once('modules/php/objects/DogWalker.php');
require_once('modules/php/objects/AutoWalker.php');
require_once('modules/php/actions/AdditionalAction.php');
require_once('modules/php/ReflectionUtils.php');

require_once('modules/php/traits/UtilsTrait.php');
require_once('modules/php/traits/ActionTrait.php');
require_once('modules/php/traits/StateTrait.php');
require_once('modules/php/traits/ArgsTrait.php');
require_once('modules/php/traits/DebugTrait.php');
require_once('modules/php/traits/SetupTrait.php');

require_once('modules/php/DogField.php');
require_once('modules/php/DogWalkPark.php');
require_once('modules/php/DogManager.php');
require_once('modules/php/PlayerManager.php');
require_once('modules/php/DogBreedExpertAwardManager.php');
require_once('modules/php/ForecastManager.php');
require_once('modules/php/actions/ActionManager.php');
require_once('modules/php/ScoreManager.php');

class DogPark extends Table
{
    use SetupTrait;
    use ActionTrait;
    use StateTrait;
    use ArgsTrait;
    use UtilsTrait;
    use DebugTrait;

    public static $instance = null;

    // CARDS & TOKENS
    public Deck $dogCards;
    public Deck $dogWalkers;
    public Deck $breedCards;
    public Deck $forecastCards;
    public Deck $locationBonusCards;
    public Deck $objectiveCards;

    // MANAGERS
    public CommandManager $commandManager;
    public PlayerManager $playerManager;
    public DogField $dogField;
    public DogWalkPark $dogWalkPark;
    public DogManager $dogManager;
    public DogBreedExpertAwardManager $breedExpertAwardManager;
    public ForecastManager $forecastManager;
    public ActionManager $actionManager;
    public ScoreManager $scoreManager;

    function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();

        self::initGameStateLabels( array(
            VARIANT_PACKED_PARK_OPTION => VARIANT_PACKED_PARK_OPTION_ID,
            VARIANT_GENTLE_WALK_OPTION => VARIANT_GENTLE_WALK_OPTION_ID,
            VARIANT_PREDICTABLE_FORECAST_OPTION => VARIANT_PREDICTABLE_FORECAST_OPTION_ID,
        ));

        self::$instance = $this;

        $this->dogCards = self::getNew("module.common.deck");
        $this->dogCards->init('dog');

        $this->dogWalkers = self::getNew("module.common.deck");
        $this->dogWalkers->init('walker');

        $this->breedCards = self::getNew("module.common.deck");
        $this->breedCards->init('breed');

        $this->forecastCards = self::getNew("module.common.deck");
        $this->forecastCards->init('forecast');

        $this->locationBonusCards = self::getNew("module.common.deck");
        $this->locationBonusCards->init('location_bonus');

        $this->objectiveCards = self::getNew("module.common.deck");
        $this->objectiveCards->init('objective');

        $this->commandManager = new CommandManager();
        $this->playerManager = new PlayerManager();
        $this->dogField = new DogField();
        $this->dogWalkPark = new DogWalkPark();
        $this->dogManager = new DogManager();
        $this->breedExpertAwardManager = new DogBreedExpertAwardManager();
        $this->forecastManager = new ForecastManager();
        $this->actionManager = new ActionManager();
        $this->scoreManager = new ScoreManager();
    }
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "dogpark";
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        $offerValueRevealed = $this->getGlobalVariable(OFFER_VALUE_REVEALED);
        $objectivesRevealed = $this->getGlobalVariable(OBJECTIVES_REVEALED);

        foreach($result['players'] as $playerId => &$player) {
            $hideInfo = $playerId != $current_player_id && $objectivesRevealed == false;
            $player['orderNo'] = $this->playerManager->getPlayerCustomOrderNo($playerId);
            $player['walker'] = $this->playerManager->getWalker($playerId);
            $player['kennelDogs'] = DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_PLAYER, $playerId));
            $player['leadDogs'] = DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_LEAD, $playerId));
            $player['offerValue'] = $current_player_id == $playerId || $offerValueRevealed ? $this->playerManager->getPlayerOfferValue($playerId) : 0;
            $player['resources'] = $this->playerManager->getResources($playerId);
            $player['objectives'] = ObjectiveCard::fromArray($this->objectiveCards->getCardsInLocation(LOCATION_PLAYER, $playerId), $hideInfo);
            $player['selectedObjectiveCardId'] = $this->getGlobalVariable(OBJECTIVE_ID_ .$playerId);
            $player['chosenObjective'] = current(ObjectiveCard::fromArray($this->objectiveCards->getCardsInLocation(LOCATION_SELECTED, $playerId), $hideInfo));
        }

        $result['currentRound'] = intval($this->getGlobalVariable(CURRENT_ROUND));
        $result['currentPhase'] = $this->getGlobalVariable(CURRENT_PHASE);

        $result['field'] = [
            'nrOfFields' => $this->dogField->getNumberOfFields(),
            'dogs' => $this->dogField->getDogCards(),
            'walkers' => $this->dogField->getWalkers(),
            'scoutedDogs' => is_array($this->getGlobalVariable(SCOUTED_CARDS)) ? DogCard::fromArray($this->dogCards->getCards($this->getGlobalVariable(SCOUTED_CARDS))) : []
        ];

        $result['park'] = [
            'walkers' => $this->dogWalkPark->getWalkers(),
            'locationBonusCards' => $this->dogWalkPark->getLocationBonusCards(),
            'extraLocationBonuses' => $this->dogWalkPark->getAllLocationBonuses()
        ];

        $result['breedExpertAwards'] = $this->breedExpertAwardManager->getExpertAwards();
        $result['forecastCards'] = $this->forecastManager->getForeCastCards();
        foreach ($result['forecastCards'] as $foreCastCard) {
            if($foreCastCard->locationArg < $result['currentRound']) {
                $foreCastCard->typeArg = null;
            }
        }

        $autoWalkers = $this->getAutoWalkers();
        $result['autoWalkers'] = [];
        foreach($autoWalkers as &$autoWalker) {
            $result['autoWalkers'][] = [
                'id' => $autoWalker->id,
                'name' => $autoWalker->name,
                'color' => $autoWalker->color,
                'lastDieRoll' => $autoWalker->lastDieRoll,
                'walker' => $this->playerManager->getWalker($autoWalker->id),
                'kennelDogs' => DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_PLAYER, $autoWalker->id)),
                'offerValue' => $offerValueRevealed ? $this->playerManager->getPlayerOfferValue($autoWalker->id) : 0

            ];
        }
        $result['discardPile'] = DogCard::fromArray($this->dogCards->getCardsInLocation(LOCATION_DISCARD));


        $result['scoreBreakdown'] = $this->getGlobalVariable(FINAL_SCORING_BREAKDOWN);

        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        $round = $this->getGlobalVariable(CURRENT_ROUND);
        $phase = $this->getGlobalVariable(CURRENT_PHASE);

        $gameProgression = 25 * (max(intval($round) - 1, 0));
        if ($phase == PHASE_RECRUITMENT_1) {
            $gameProgression = $gameProgression + 6;
        } else if ($phase == PHASE_RECRUITMENT_2) {
            $gameProgression = $gameProgression + 12;
        } else if ($phase == PHASE_SELECTION) {
            $gameProgression = $gameProgression + 18;
        } else if ($phase == PHASE_WALKING) {
            $gameProgression = $gameProgression + 24;
        }
        return $gameProgression;
    }

    public static function totranslate($text) {
        return self::_($text);
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */



//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in canvas.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
        $statename = $state['name'];

        if ($state['type'] === "multipleactiveplayer" || $state['type'] === "private") {
            $this->gamestate->setPlayerNonMultiactive($active_player, '');
            return;
        }

        if ($state['type'] === 'activeplayer') {
            switch ($statename) {
                case 'recruitmentTakeDog':
                    $leftOverDog = current($this->dogField->getDogCards());
                    $this->dogManager->recruitDog($active_player, $leftOverDog->id, 0, $this->playerManager->getWalkerId($active_player));
                    $this->gamestate->nextState('');
                    return;
                case 'walkingMoveWalker':
                    $this->setGlobalVariable(LAST_WALKED_WALKER_ID, $this->playerManager->getWalkerId($active_player));
                    $this->gamestate->nextState('');
                    return;
                case 'actionSwap':
                    $this->gamestate->jumpToState(intval($this->getGlobalVariable(STATE_AFTER_SWAP)));
                    return;
                case 'actionScout':
                    $this->gamestate->jumpToState(intval($this->getGlobalVariable(STATE_AFTER_SCOUT)));
                    return;
                case 'actionMoveAutoWalker':
                    $walker = DogWalker::from($this->dogWalkers->getCard(intval($this->getGlobalVariable(LAST_WALKED_WALKER_ID))));
                    $autoWalker = $this->getAutoWalkers()[$walker->typeArg];
                    $nrOfPlaces = intval($this->getGlobalVariable(MOVE_AUTO_WALKER_STEPS));
                    $locationId = current($this->getGlobalVariable(MOVE_AUTO_WALKER_LOCATIONS));
                    $autoWalker->moveWalkerToLocation($walker->id, $locationId, $nrOfPlaces);
                    return;
                default:
                    $this->gamestate->nextState('');
                    return;
            }
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
        //        if( $from_version <= 1404301345 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        if( $from_version <= 1405061421 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        // Please add your future database scheme changes here
        //
        //
    }    
}
