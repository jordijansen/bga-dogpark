interface DogCard extends Card {

}

interface DogWalker extends Card {
}

interface DogParkGame extends Game {
    dogCardManager: DogCardManager;
    dogWalkerManager: DogWalkerManager;
    gamedatas: DogParkGameData;
    getPlayerId(): number;
    getPlayer(playerId: number): DogParkPlayer;
    isReadOnly(): boolean;
    setTooltipToClass(className: string, html: string): void;
}

interface DogParkPlayer extends Player {
    walker: DogWalker,
    kennelDogs: DogCard[],
    offerValue: number
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


