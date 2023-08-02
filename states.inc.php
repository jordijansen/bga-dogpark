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
        "transitions" => [ "" => ST_CHOOSE_OBJECTIVES ]
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
// SETUP
//////////////////////////////////
$setupStates = [
    ST_CHOOSE_OBJECTIVES => [
        "name" => "chooseObjectives",
        "description" => clienttranslate('Waiting for players to choose objectives'),
        "descriptionmyturn" => clienttranslate('You must choose an objective card'),
        "type" => "multipleactiveplayer",
        "possibleactions" => [
            ACT_CHOOSE_OBJECTIVE,
            ACT_CHANGE_OBJECTIVE
        ],
        "transitions" => [
            "" => ST_CHOOSE_OBJECTIVES_END
        ],
    ],

    ST_CHOOSE_OBJECTIVES_END => [
        "name" => 'chooseObjectivesEnd',
        "description" => clienttranslate("Choose objectives"),
        "type" => "game",
        "action" => "stChooseObjectivesEnd",
        "transitions" => [
            "" => ST_RECRUITMENT_START
        ],
    ]
];
//////////////////////////////////
// RECRUITMENT
//////////////////////////////////
$recruitmentStates = [
    ST_RECRUITMENT_START => [
        "name" => "recruitmentStart",
        "description" => clienttranslate('Recruit: starting recruitment phase...'),
        "type" => "game",
        "action" => "stRecruitmentStart",
        "transitions" => [
            "recruitmentOffer" => ST_RECRUITMENT_OFFER
        ]
    ],
    ST_RECRUITMENT_OFFER => [
        "name" => "recruitmentOffer",
        "description" => clienttranslate('Recruit (${recruitmentRound}/2): ${actplayer} must place an offer'),
        "descriptionmyturn" => clienttranslate('Recruit (${recruitmentRound}/2): ${you} must place an offer'),
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
        "description" => clienttranslate('Recruit (${recruitmentRound}/2): resolving offers'),
        "args" => "argRecruitmentResolveOffers",
        "type" => "game",
        "action" => "stRecruitmentResolveOffers",
        "transitions" => [
            '' => ST_RECRUITMENT_TAKE_DOG_NEXT
        ]
    ],
    ST_RECRUITMENT_TAKE_DOG => [
        "name" => "recruitmentTakeDog",
        "description" => clienttranslate('Recruit (${recruitmentRound}/2): ${actplayer} must choose one of the remaining dogs'),
        "descriptionmyturn" => clienttranslate('Recruit (${recruitmentRound}/2): ${you} must choose one of the remaining dogs'),
        "args" => "argRecruitmentTakeDog",
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
        "description" => clienttranslate('Recruit: ending recruitment phase...'),
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
        "description" => clienttranslate('Select: starting selection phase...'),
        "type" => "game",
        "action" => "stSelectionStart",
        "transitions" => [
            'playerTurns' => ST_SELECTION_ACTIONS,
        ]
    ],
    ST_SELECTION_ACTIONS => [
        "name" => "selectionActions",
        "description" => clienttranslate('Select: waiting for players to finish selection'),
        "descriptionmyturn" => '',
        "initialprivate" => ST_SELECTION_PLACE_DOG_ON_LEAD,
        "type" => "multipleactiveplayer",
        "action" => "stSelectionActions",
        "possibleactions" => [
            ACT_CHANGE_SELECTION
        ],
        "transitions" => [
            "" => ST_SELECTION_END
        ],
    ],
    ST_SELECTION_PLACE_DOG_ON_LEAD => [
        "name" => "selectionPlaceDogOnLead",
        "descriptionmyturn" => clienttranslate('Select (${numberOfDogsOnlead}/${maxNumberOfDogs}): ${you} must place a dog on the lead'),
        "type" => "private",
        "args" => "argSelectionPlaceDogOnLead",
        "possibleactions" => [
            ACT_PLACE_DOG_ON_LEAD,
            ACT_CONFIRM_SELECTION,
            ACT_UNDO
        ],
        "transitions" => [
            'placeDogOnLeadSelectResources' => ST_SELECTION_PLACE_DOG_ON_LEAD_SELECT_RESOURCES,
        ]
    ],
    ST_SELECTION_PLACE_DOG_ON_LEAD_SELECT_RESOURCES => [
        "name" => "selectionPlaceDogOnLeadSelectResources",
        "descriptionmyturn" => clienttranslate('Select (${numberOfDogsOnlead}/${maxNumberOfDogs}): ${you} must pay for ${dogName}'),
        "type" => "private",
        "args" => "argSelectionPlaceDogOnLeadSelectResources",
        "possibleactions" => [
            ACT_PLACE_DOG_ON_LEAD_PAY_RESOURCES,
            ACT_PLACE_DOG_ON_LEAD_CANCEL,
        ],
        "transitions" => [
            'placeDogOnLeadAfter' => ST_SELECTION_PLACE_DOG_ON_LEAD_AFTER,
        ]
    ],
    ST_SELECTION_PLACE_DOG_ON_LEAD_AFTER => [
        "name" => "selectionPlaceDogOnLeadAfter",
        "descriptionmyturn" => clienttranslate('Select (${numberOfDogsOnlead}/${maxNumberOfDogs}): ${you} must perform additional abilities'),
        "type" => "private",
        "action" => "stSelectionPlaceDogOnLeadAfter",
        "args" => "argSelectionPlaceDogOnLeadAfter",
        "possibleactions" => [
            ACT_CONFIRM_SELECTION,
            ACT_UNDO
        ],
        "transitions" => [
        ]
    ],
    ST_SELECTION_END => [
        "name" => "selectionEnd",
        "description" => clienttranslate("Select: ending selection phase..."),
        "type" => "game",
        "action" => "stSelectionEnd",
        "transitions" => [
            "walking" => ST_WALKING_START
        ]
    ],
];

//////////////////////////////////
// WALK
//////////////////////////////////
$walkingStates = [
    ST_WALKING_START => [
        "name" => "walkingStart",
        "description" => "",
        "type" => "game",
        "action" => "stWalkingStart",
        "transitions" => [
            "playerTurn" => ST_WALKING_MOVE_WALKER
        ]
    ],
    ST_WALKING_MOVE_WALKER => [
        "name" => "walkingMoveWalker",
        "description" => clienttranslate('Walk: ${actplayer} must move their walker'),
        "descriptionmyturn" => clienttranslate('Walk: ${you} must move your walker'),
        "args" => "argWalkingMoveWalker",
        "type" => "activeplayer",
        "possibleactions" => [
            ACT_MOVE_WALKER
        ],
        "transitions" => [
            '' => ST_WALKING_MOVE_WALKER_AFTER
        ],
    ],
    ST_WALKING_MOVE_WALKER_AFTER => [
        "name" => "walkingMoveWalkerAfter",
        "description" => clienttranslate('Walk: ${actplayer} may perform additional actions'),
        "descriptionmyturn" => clienttranslate('Walk: ${you} may perform additional actions'),
        "args" => "argWalkingMoveWalkerAfter",
        "type" => "activeplayer",
        "possibleactions" => [
            ACT_MOVE_WALKER_PAY_REPUTATION,
            ACT_CONFIRM_WALKING,
            ACT_UNDO
        ],
        "transitions" => [
        ],
    ],
];

$machinestates = $basicGameStates + $setupStates + $recruitmentStates + $selectionStates + $walkingStates;



