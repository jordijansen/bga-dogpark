<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * game implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!
require_once("modules/php/Constants.inc.php");

$basicGameStates = [

    // The initial state. Please do not modify.
    ST_GAME_SETUP => [
        "name" => 'gameSetup',
        "description" => clienttranslate("Game setup"),
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => [ "" => ST_RECRUITMENT_START ]
    ],

    // Final state.
    // Please do not modify.
    ST_GAME_END => [
        "name" => 'gameEnd',
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ],
];

//////////////////////////////////
// RECRUITMENT
//////////////////////////////////
$recruitmentStates = [
    ST_RECRUITMENT_START => [
        "name" => "recruitmentStart",
        "description" => "",
        "type" => "game",
        "action" => "stRecruitmentStart",
        "transitions" => [
            "recruitmentOffer" => ST_RECRUITMENT_OFFER
        ]
    ],
    ST_RECRUITMENT_OFFER => [
        "name" => "recruitmentOffer",
        "description" => clienttranslate('Recruitment: ${actplayer} must place an offer'),
        "descriptionmyturn" => clienttranslate('Recruitment: ${you} must place an offer'),
        "args" => "argRecruitmentOffer",
        "type" => "activeplayer",
        "possibleactions" => [
            ACT_PLACE_OFFER_ON_DOG,
            ACT_SKIP_PLACE_OFFER_ON_DOG
        ],
        "transitions" => [
            "" => ST_RECRUITMENT_OFFER_NEXT
        ],
    ],
    ST_RECRUITMENT_OFFER_NEXT => [
        "name" => "recruitmentOfferNext",
        "description" => "",
        "type" => "game",
        "action" => "stRecruitmentOfferNext",
        "transitions" => [
            "nextPlayer" => ST_RECRUITMENT_OFFER,
            "resolveOffers" => ST_RECRUITMENT_RESOLVE_OFFERS
        ]
    ],
    ST_RECRUITMENT_RESOLVE_OFFERS => [
        "name" => "recruitmentResolveOffers",
        "description" => "",
        "type" => "game",
        "action" => "stRecruitmentResolveOffers",
        "transitions" => [
            '' => ST_RECRUITMENT_TAKE_DOG_NEXT
        ]
    ],
    ST_RECRUITMENT_TAKE_DOG => [
        "name" => "recruitmentTakeDog",
        "description" => clienttranslate('Recruitment: ${actplayer} must choose one of the remaining dogs'),
        "descriptionmyturn" => clienttranslate('Recruitment: ${you} must choose one of the remaining dogs'),
        "type" => "activeplayer",
        "possibleactions" => [
            ACT_RECRUIT_DOG
        ],
        "transitions" => [
            "" => ST_RECRUITMENT_TAKE_DOG_NEXT
        ],
    ],
    ST_RECRUITMENT_TAKE_DOG_NEXT => [
        "name" => "recruitmentTakeDogNext",
        "description" => "",
        "type" => "game",
        "action" => "stRecruitmentTakeDogNext",
        "transitions" => [
            "nextPlayer" => ST_RECRUITMENT_TAKE_DOG,
            "endRecruitment" => ST_RECRUITMENT_END
        ]
    ],
    ST_RECRUITMENT_END => [
        "name" => "recruitmentEnd",
        "description" => "",
        "type" => "game",
        "action" => "stRecruitmentEnd",
        "transitions" => [
            'recruitmentStart' => ST_RECRUITMENT_START,
            'recruitmentEnd' => ST_SELECTION_START
        ]
    ]
];

//////////////////////////////////
// SELECTION
//////////////////////////////////
$selectionStates = [
    ST_SELECTION_START => [
        "name" => "selectionStart",
        "description" => "",
        "type" => "game",
        "action" => "stSelectionStart",
        "transitions" => [
            'playerTurns' => ST_SELECTION_ACTIONS,
        ]
    ],
    ST_SELECTION_ACTIONS => [
        "name" => "selectionActions",
        "description" => clienttranslate('Selection: all players must select dog(s) to walk'),
        "descriptionmyturn" => clienttranslate('Selection: ${you} must select dog(s) to walk'),
        "action" => "stSelectionActions",
        "args" => "argSelectionActions",
        "type" => "multipleactiveplayer",
        "possibleactions" => [
            ACT_PLACE_DOG_ON_LEAD,
            ACT_CONFIRM_SELECTION
        ],
        "transitions" => [
            "next" => ST_SELECTION_END
        ],
    ],
    ST_SELECTION_END => [
        "name" => "selectionEnd",
        "description" => "",
        "type" => "game",
        "action" => "stSelectionEnd",
        "transitions" => [
        ]
    ],
];

$machinestates = $basicGameStates + $recruitmentStates + $selectionStates;


