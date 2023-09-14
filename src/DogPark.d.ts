interface DogCard extends Card {

    name: string,
    breeds: string[],
    abilityTitle: string,
    abilityText: string,
    costs: {stick: number, ball: number, treat: number, toy: number}
    resourcesOnCard: {stick: string, ball: string, treat: string, toy: string, walked: string}
    craftyResource?: string
}

interface ObjectiveCard extends Card {
    name: string,
    description: string,
}

interface ForecastCard extends Card {
    description: string,
}

interface LocationBonusCard extends Card {
    bonusesOnLocation: {[locationId: number]: []}
}

interface DogWalker extends Card {
}

interface AutoWalker {
    kennelDogs: DogCard[];
    id: number,
    name: string,
    color: string,
    offerValue: number,
    walker: DogWalker
}

interface LocationBonus {
    id: number, locationId: number, bonus: Token['type']
}

interface DogParkGame extends Game {
    playerArea: PlayerArea;
    helpDialogManager: HelpDialogManager,
    locationBonusCardManager: LocationBonusCardManager;
    dogCardManager: DogCardManager;
    dogWalkerManager: DogWalkerManager;
    dogWalkPark: DogWalkPark;
    tokenManager: TokenManager;
    objectiveCardManager: ObjectiveCardManager;
    forecastManager: ForecastManager,
    gamedatas: DogParkGameData;
    animationManager: AnimationManager
    getPlayerId(): number;
    getPlayer(playerId: number): DogParkPlayer;
    isReadOnly(): boolean;
    setTooltipToClass(className: string, html: string): void;
    formatWithIcons(description: string): string
    walkerIcon(color: string): string;
}

interface DogParkPlayer extends Player {
    walker: DogWalker,
    kennelDogs: DogCard[],
    leadDogs: DogCard[],
    offerValue: number,
    resources: {stick: number, ball: number, treat: number, toy: number},
    objectives: ObjectiveCard[],
    selectedObjectiveCardId: number,
    chosenObjective: ObjectiveCard,
    orderNo: number
}

interface DogParkGameData extends GameData {
    currentRound: number,
    currentPhase: string,
    breedExpertAwards: Card[],
    forecastCards: Card[],
    field: { nrOfFields: number, dogs: DogCard[], scoutedDogs: DogCard[], walkers: DogWalker[] }
    park: { walkers: DogWalker[], locationBonusCards: LocationBonusCard[], extraLocationBonuses: LocationBonus[]}
    autoWalkers: AutoWalker[],
    scoreBreakdown?: FinalScoringBreakdown,
    discardPile: DogCard[]
}

interface FinalScoringBreakdown {
    [playerId: number]: {
        breedExpertAwardScore: number,
        dogFinalScoring: {[dogId: number]: number},
        dogFinalScoringScore: number,
        objectiveCardScore: number,
        parkBoardScore: number,
        remainingResourcesScore: number,
        score: number,
        scoreAux: number,
        soloStarRating: number
    }
}

// ARGS
interface AdditionalAction {
    id: string, type: string, additionalArgs: any[], optional: boolean, canBeUndone: boolean
}

interface RecruitmentOfferArgs {
    maxOfferValue: number
}

interface SelectionPlaceDogOnLeadArgs {
    additionalActions: AdditionalAction[],
    maxNumberOfDogs: number
    numberOfDogsOnlead: number,
    freeDogsOnLead: number,
    dogs: {[dogId: number]: DogCard},
}

interface SelectionPlaceDogOnLeadSelectResourcesArgs {
    dog: DogCard,
    resources: {stick: number, ball: number, treat: number, toy: number},
    freeDogsOnLead: number
}

interface WalkingMoveWalkerArgs {
    possibleParkLocationIds: number[]
}

interface WalkingMoveWalkerAfterArgs {
    locationId: number,
    additionalActions: AdditionalAction[],
}

interface ActionSwapArgs {
    dogsInKennel: DogCard[],
    dogsInField: DogCard[]
}

interface ActionScoutArgs {
    scoutedDogCards: DogCard[]
}

interface ActionMoveAutoWalkerArgs {
    possibleParkLocationIds: number[]
}

interface ActionCraftyArgs {
    action: AdditionalAction,
    dog: DogCard,
    resources: {stick: number, ball: number, treat: number, toy: number}
}


// NOTIFS
interface NotifObjectivesChosen {
    chosenObjectiveCards: [{playerId: number, cardId: number}]
}

interface NotifDogRecruited {
    playerId: number,
    dog: DogCard,
    score: number,
    walker: DogWalker
}

interface NotifDogOfferPlaced {
    playerId: number,
    walker: DogWalker
}

interface NotifOfferValueRevealed {
    playerId: number,
    offerValue: number
}

interface NotifFieldRefilled {
    dogs: DogCard[]
}

interface NotifNewPhase {
    round: number,
    newPhase: string
}

interface NotifDogPlacedOnLead {
    playerId: number,
    dog: DogCard,
    resources: string[]
}

interface NotifPlayerGainsResources {
    playerId: number,
    resources: string[],
}

interface NotifPlayerGainsLocationBonusResource {
    playerId: number,
    resources: string[],
    locationId: number,
    score: number
}

interface NotifMoveWalkers {
    walkers: DogWalker[]
}

interface NotifMoveWalker {
    playerId: number,
    walker: DogWalker
}

interface NotifPlayerPaysReputationForLocation {
    playerId: number,
    score: number
}

interface NotifPlayerLeavesThePark {
    playerId: number,
    score: number,
    walker?: DogWalker
}

interface NotifPlayerGainsReputation {
    playerId: number,
    score: number
}

interface NotifPlayerLosesReputation {
    playerId: number,
    score: number
}

interface NotifMoveDogsToKennel {
    playerId: number,
    dogs: DogCard[]
}

interface NotifMoveWalkerBackToPlayer {
    playerId: number,
    walker: DogWalker
}

interface NotifFlipForecastCard {
    foreCastCard: ForecastCard
}

interface NotifNewLocationBonusCardDrawn {
    locationBonusCard: LocationBonusCard,
    locationBonuses: LocationBonus[]
}

interface NotifNewFirstWalker {
    playerId: number
}

interface NotifPlayerSwaps {
    playerId: number,
    kennelDog: DogCard,
    fieldDog: DogCard
}

interface NotifPlayerScouts {
    scoutedDogs: DogCard[]
}

interface NotifPlayerScoutReplaces {
    scoutDog: DogCard,
    fieldDog: DogCard
}

interface NotifActivateDogAbility {
    playerId: number,
    dog: DogCard,
    gainedResources?: string[],
    lostResources?: string[],
    score?: number
}

interface NotifActivateForecastCard {
    playerId: number,
    forecastCard: ForecastCard,
    gainedResources?: string[],
    lostResources?: string[],
    score?: number
}

interface NotifPlayerAssignsResources {
    playerId: number,
    resourcesAdded: string[]
    dog: DogCard,
    resources: {stick: number, ball: number, treat: number, toy: number},
}

interface NotifRevealObjectiveCards {
    objectiveCards: ObjectiveCard[]
}

interface NotifFinalScoringRevealed {
    scoreBreakDown: FinalScoringBreakdown
}



