<?php

/**
 * Game Specific Constants
 */

const BASE_GAME = 'BASE_GAME';
const DOG_TYPE_TOY = 'TOY';

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

const ST_RECRUITMENT_START = 10;
const ST_RECRUITMENT_OFFER = 11;
const ST_RECRUITMENT_OFFER_NEXT = 12;
const ST_RECRUITMENT_RESOLVE_OFFERS = 15;
const ST_RECRUITMENT_TAKE_DOG = 16;
const ST_RECRUITMENT_TAKE_DOG_NEXT = 17;
const ST_RECRUITMENT_END = 20;

const ST_SELECTION_START = 30;
const ST_SELECTION_ACTIONS = 31;
const ST_SELECTION_END = 40;

const ST_GAME_END = 99;

/**
 * Actions
 */
const ACT_PLACE_OFFER_ON_DOG = 'placeOfferOnDog';
const ACT_SKIP_PLACE_OFFER_ON_DOG = 'skipPlaceOfferOnDog';
const ACT_RECRUIT_DOG = 'recruitDog';
const ACT_PLACE_DOG_ON_LEAD = 'placeDogOnLead';
const ACT_CONFIRM_SELECTION = 'confirmSelection';

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

const LOCATION_FIELDS = [LOCATION_FIELD_1, LOCATION_FIELD_2, LOCATION_FIELD_3, LOCATION_FIELD_4, LOCATION_FIELD_5];

const LOCATION_DISCARD = 'discard';
const LOCATION_PLAYER = 'player';


/**
 * Global variables
 */
const CURRENT_ROUND = 'CURRENT_ROUND';
const CURRENT_PHASE = 'CURRENT_PHASE';
const OFFER_VALUE_REVEALED = 'OFFER_VALUE_REVEALED';


/**
 * Stats
 */
