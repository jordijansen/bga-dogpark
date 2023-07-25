interface DogCard extends Card {

    name: string,
    breeds: string[],
    costs: {stick: number, ball: number, treat: number, toy: number}

}

interface DogWalker extends Card {
}

interface DogParkGame extends Game {
    dogCardManager: DogCardManager;
    dogWalkerManager: DogWalkerManager;
    tokenManager: TokenManager;
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
    resources: {stick: number, ball: number, treat: number, toy: number}
}

interface DogParkGameData extends GameData {
    currentRound: number,
    currentPhase: string,
    field: { nrOfFields: number, dogs: DogCard[], walkers: DogWalker[] }
}

// ARGS
interface RecruitmentOfferArgs {
    maxOfferValue: number
}

interface SelectionPlaceDogOnLeadArgs {
    maxNumberOfDogs: number
    dogs: {[dogId: number]: DogCard},
    resources: {stick: number, ball: number, treat: number, toy: number}
}

interface SelectionPlaceDogOnLeadSelectResourcesArgs {
    dog: DogCard,
    resources: {stick: number, ball: number, treat: number, toy: number}
}

// NOTIFS
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
    newPhase: string
}

interface NotifDogPlacedOnLead {
    playerId: number,
    dog: DogCard,
    resources: string[]
}

