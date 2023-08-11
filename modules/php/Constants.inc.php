<?php

/**
 * Game Specific Constants
 */

const BASE_GAME = 'BASE_GAME';

const BREED_GUNDOG      = 'gundog';
const BREED_HOUND       = 'hound';
const BREED_PASTORAL    = 'pastoral';
const BREED_TERRIER     = 'terrier';
const BREED_TOY         = 'toy';
const BREED_UTILITY     = 'utility';
const BREED_WORKING     = 'working';

const RESOURCE_STICK = 'stick';
const RESOURCE_BALL = 'ball';
const RESOURCE_TREAT = 'treat';
const RESOURCE_TOY = 'toy';

const WALKED = 'walked';
const BLOCK = 'block';
const REPUTATION = 'reputation';
const SWAP = 'swap';
const SCOUT = 'scout';

const LOCATION_BONUS_PLENTIFUL = 'plentiful';
const LOCATION_BONUS_REROUTED = 'rerouted';

const OBJECTIVE_EXPERIENCED = 'experienced';
const OBJECTIVE_STANDARD = 'standard';

// PHASES
const PHASE_SET_UP = 'PHASE_SET_UP';
const PHASE_RECRUITMENT_1 = 'PHASE_RECRUITMENT_1';
const PHASE_RECRUITMENT_2 = 'PHASE_RECRUITMENT_2';
const PHASE_SELECTION = 'PHASE_SELECTION';
const PHASE_WALKING = 'PHASE_WALKING';
const PHASE_HOME_TIME = 'PHASE_HOME_TIME';

/**
 * Options
 */


/**
 * State
 */
const ST_GAME_SETUP = 1;

const ST_CHOOSE_OBJECTIVES = 5;
const ST_CHOOSE_OBJECTIVES_END = 6;

const ST_RECRUITMENT_START = 10;
const ST_RECRUITMENT_OFFER = 11;
const ST_RECRUITMENT_OFFER_NEXT = 12;
const ST_RECRUITMENT_RESOLVE_OFFERS = 15;
const ST_RECRUITMENT_TAKE_DOG = 16;
const ST_RECRUITMENT_TAKE_DOG_NEXT = 17;
const ST_RECRUITMENT_END = 20;

const ST_SELECTION_START = 30;
const ST_SELECTION_ACTIONS = 31;
const ST_SELECTION_PLACE_DOG_ON_LEAD = 32;
const ST_SELECTION_PLACE_DOG_ON_LEAD_SELECT_RESOURCES = 33;
const ST_SELECTION_PLACE_DOG_ON_LEAD_AFTER = 35;
const ST_SELECTION_END = 40;

const ST_WALKING_START = 50;
const ST_WALKING_MOVE_WALKER = 51;
const ST_WALKING_MOVE_WALKER_AFTER = 55;
const ST_WALKING_NEXT = 60;
const ST_WALKING_END = 65;

const ST_HOME_TIME = 70;

const ST_ACTION_SWAP = 80;
const ST_ACTION_SCOUT_START = 81;
const ST_ACTION_SCOUT = 82;
const ST_ACTION_MOVE_AUTO_WALKER = 83;
const ST_ACTION_CRAFTY = 84;

const ST_GAME_END = 99;

/**
 * Actions
 */
const ACT_CHOOSE_OBJECTIVE = 'chooseObjective';
const ACT_CHANGE_OBJECTIVE = 'chooseObjective';
const ACT_PLACE_OFFER_ON_DOG = 'placeOfferOnDog';
const ACT_SKIP_PLACE_OFFER_ON_DOG = 'skipPlaceOfferOnDog';
const ACT_RECRUIT_DOG = 'recruitDog';
const ACT_PLACE_DOG_ON_LEAD = 'placeDogOnLead';
const ACT_PLACE_DOG_ON_LEAD_CANCEL = 'placeDogOnLeadCancel';
const ACT_PLACE_DOG_ON_LEAD_PAY_RESOURCES = 'placeDogOnLeadPayResources';
const ACT_CONFIRM_SELECTION = 'confirmSelection';
const ACT_CHANGE_SELECTION = 'changeSelection';
const ACT_MOVE_WALKER = 'moveWalker';
const ACT_ADDITIONAL_ACTION = 'additionalAction';
const ACT_CONFIRM_WALKING = 'confirmWalking';
const ACT_UNDO = 'undo';

const ACT_SWAP = 'swap';
const ACT_SCOUT_REPLACE = 'scoutReplace';
const ACT_SCOUT_END = 'scoutEnd';
const ACT_MOVE_AUTO_WALKER = 'moveAutoWalker';
const ACT_CANCEL = 'cancel';

const ACT_CRAFTY_CONFIRM = 'craftyConfirm';

/**
 * Locations
 */
const LOCATION_DECK = 'deck';
const LOCATION_FIELD = 'field';
const LOCATION_FIELD_1 = 'field_1';
const LOCATION_FIELD_2 = 'field_2';
const LOCATION_FIELD_3 = 'field_3';
const LOCATION_FIELD_4 = 'field_4';
const LOCATION_FIELD_5 = 'field_5';

const LOCATION_DISCARD = 'discard';
const LOCATION_PLAYER = 'player';
const LOCATION_LEAD = 'lead';

const LOCATION_PARK = 'park';

const LOCATION_SELECTED = 'selected';

const LOCATION_BREED_EXPERT_AWARDS = 'breed_expert';
const LOCATION_FORECAST = 'forecast';

/**
 * Global variables
 */
const CURRENT_ROUND = 'CURRENT_ROUND';
const CURRENT_PHASE = 'CURRENT_PHASE';
const OFFER_VALUE_REVEALED = 'OFFER_VALUE_REVEALED';

const SELECTION_DOG_ID_ = 'SELECTION_DOG_ID_';
const OBJECTIVE_ID_ = 'OBJECTIVE_ID_';

const ADDITIONAL_ACTIONS_ = 'ADDITIONAL_ACTIONS_';

const SCOUTED_CARDS = 'SCOUTED_CARDS';

const STATE_AFTER_SWAP = 'STATE_AFTER_SWAP';
const STATE_AFTER_SCOUT = 'STATE_AFTER_SCOUT';
const CURRENT_ACTION_ID = 'SWAP_ACTION_ID';

const LAST_WALKED_WALKER_ID = 'LAST_WALKED_WALKER_ID';
const MOVE_AUTO_WALKER_STEPS = 'MOVE_AUTO_WALKER_STEPS';
const MOVE_AUTO_WALKER_LOCATIONS = 'MOVE_AUTO_WALKER_LOCATIONS';

const USED_WALK_ABILITIES = 'USED_WALK_ABILITIES';

/**
 * Additional Actions
 */
const WALKING_PAY_REPUTATION_ACCEPT = 'WALKING_PAY_REPUTATION_ACCEPT';
const WALKING_PAY_REPUTATION_DENY = 'WALKING_PAY_REPUTATION_DENY';
const WALKING_GAIN_LOCATION_BONUS = 'WALKING_GAIN_LOCATION_BONUS';
const WALKING_GAIN_LEAVING_THE_PARK_BONUS = 'WALKING_GAIN_LEAVING_THE_PARK_BONUS';

const USE_DOG_ABILITY = 'USE_DOG_ABILITY';

/**
 * DOG ABILITIES
 */
const EAGER = 'eager';
const CRAFTY = 'crafty';
const GO_FETCH = 'gofetch';
const OBEDIENT = 'Obedient';
const PLAYMATE = 'Playmate';

const SELECTION_ABILITIES = [EAGER, CRAFTY];
const WALKING_ABILITIES = [GO_FETCH];

/**
 * Stats
 */
