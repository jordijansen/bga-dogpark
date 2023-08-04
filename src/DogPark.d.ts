interface DogCard extends Card {

    name: string,
    breeds: string[],
    costs: {stick: number, ball: number, treat: number, toy: number}
    resourcesOnCard: {stick: string, ball: string, treat: string, toy: string, walked: string}

}

interface LocationBonusCard extends Card {
    bonusesOnLocation: {[locationId: number]: []}
}

interface DogWalker extends Card {
}

interface LocationBonus {
    id: number, locationId: number, bonus: Token['type']
}

interface DogParkGame extends Game {
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
}

interface DogParkPlayer extends Player {
    walker: DogWalker,
    kennelDogs: DogCard[],
    leadDogs: DogCard[],
    offerValue: number,
    resources: {stick: number, ball: number, treat: number, toy: number},
    objectives: Card[],
    selectedObjectiveCardId: number,
    chosenObjective: Card,
    orderNo: number
}

interface DogParkGameData extends GameData {
    currentRound: number,
    currentPhase: string,
    breedExpertAwards: Card[],
    forecastCards: Card[],
    field: { nrOfFields: number, dogs: DogCard[], walkers: DogWalker[] }
    park: { walkers: DogWalker[], locationBonusCards: LocationBonusCard[], extraLocationBonuses: LocationBonus[]}
}

// ARGS
interface RecruitmentOfferArgs {
    maxOfferValue: number
}

interface SelectionPlaceDogOnLeadArgs {
    maxNumberOfDogs: number
    numberOfDogsOnlead: number,
    dogs: {[dogId: number]: DogCard},
}

interface SelectionPlaceDogOnLeadSelectResourcesArgs {
    dog: DogCard,
    resources: {stick: number, ball: number, treat: number, toy: number}
}

interface WalkingMoveWalkerArgs {
    possibleParkLocationIds: number[]
}

interface WalkingMoveWalkerAfterArgs {
    locationId: number,
    additionalActions: [{id: string, type: string, additionalArgs: any[], optional: boolean}]
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
    dog: DogCard,
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
    resource: string,
    locationId: number,
    extraBonus: boolean,
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
    foreCastCard: Card
}

interface NotifNewLocationBonusCardDrawn {
    locationBonusCard: LocationBonusCard,
    locationBonuses: LocationBonus[]
}

interface NotifNewFirstWalker {
    playerId: number
}


