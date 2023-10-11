class DogWalkPark {
    private static readonly spotColumnMap = {
        '1': 1,
        '2': 2,
        '3': 3,
        '4': 4,
        '5': 5,
        '6': 5,
        '7': 6,
        '8': 6,
        '9': 7,
        '10': 7,
        '11': 8,
        '12': 8,
        '13': 9,
        '14': 9,
        '15': 10,
    };

    private element: HTMLElement;
    private walkerSpots: {[spotId: number]: LineStock<DogWalker>} = {}
    public resourceSpots: {[spotId: number]: LineStock<Token>} = {}
    private locationBonusCardPile: AllVisibleDeck<Card>;
    private possibleParkLocationIds: number[] = [];
    private clickHandlers: any[] = [];

    constructor(private game: DogParkGame) {
        this.element = $("dp-game-board-park");
    }

    public setUp(gameData: DogParkGameData) {
        dojo.place(`<div id="dp-walk-trail-start" class="dp-walk-trail start"></div>`, this.element);
        dojo.place(`<div id="dp-walk-trail-end" class="dp-walk-trail end"></div>`, this.element);
        dojo.place(`<div id="dp-walk-trail" class="dp-walk-trail"></div>`, this.element);
        const trailWrapper = $('dp-walk-trail');
        for (let i = 1; i <= 10; i++) {
            dojo.place(`<div id="park-column-${i}" class="dp-park-column"></div>`, trailWrapper)
        }

        this.walkerSpots[0] = new LineStock<DogWalker>(this.game.dogWalkerManager, $("dp-walk-trail-start"), {direction: "column", wrap: "nowrap", gap: '0px'})
        for (let i = 1; i <= 15; i++) {
            this.createParkSpot(i, `park-column-${DogWalkPark.spotColumnMap[i]}`);
            this.walkerSpots[i] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(`dp-walker-spot-${i}`), {direction: "column", wrap: "nowrap", gap: '0px'})
            this.resourceSpots[i] = new LineStock<Token>(this.game.tokenManager, $(`dp-resource-spot-${i}`), {direction: "column", wrap: "nowrap"})
        }

        for (let i = 91; i <= 94; i++) {
            this.createParkSpot(i, `dp-walk-trail-end`);
            this.walkerSpots[i] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(`dp-walker-spot-${i}`), {direction: "column", wrap: "nowrap", gap: '0px'})
        }

        this.moveWalkers(gameData.park.walkers);


        // Park Bonuses
        this.locationBonusCardPile = new AllVisibleDeck(this.game.locationBonusCardManager, $('dp-game-board-park-location-card-deck'), {})
        gameData.park.locationBonusCards.forEach(card => this.addLocationBonusCard(card))
        this.addExtraLocationBonuses(gameData.park.extraLocationBonuses);
    }

    public addExtraLocationBonuses(extraLocationBonuses: LocationBonus[]) {
        Object.values(this.resourceSpots).forEach((stock) => stock.removeAll());
        extraLocationBonuses.forEach(locationBonus => this.resourceSpots[locationBonus.locationId].addCard(this.game.tokenManager.createToken(locationBonus.bonus)))
    }

    public moveWalkers(walkers: DogWalker[]) {
        return Promise.all(walkers.map(walker => this.walkerSpots[walker.locationArg].addCard(walker)));
    }

    public enterWalkerSpotsSelection(possibleParkLocationIds: number[], onClick: (locationId: number) => void) {
        this.possibleParkLocationIds = possibleParkLocationIds;
        this.possibleParkLocationIds.forEach(possibleParkLocationId => {
            const element = $(`dp-walk-spot-${possibleParkLocationId}`);
            this.clickHandlers.push(dojo.connect(element, 'onclick', () => { onClick(possibleParkLocationId); }));
            element.classList.add('selectable')
        });
    }

    public exitWalkerSpotsSelection() {
        this.possibleParkLocationIds.forEach(possibleParkLocationId => {
            const element = $(`dp-walk-spot-${possibleParkLocationId}`);
            element.classList.remove('selectable')
        });

        this.clickHandlers.forEach(clickHandler => dojo.disconnect(clickHandler))
        this.clickHandlers = [];
    }

    public addLocationBonusCard(card: LocationBonusCard) {
        return this.locationBonusCardPile.addCard(card);
    }

    private createParkSpot(id: number, parentElement: string) {
        dojo.place(`<div id="dp-walk-spot-${id}" class="dp-walk-spot" data-spot-id="${id}">
                            <div id="dp-resource-spot-${id}" class="dp-resource-spot"></div>
                            <div id="dp-walker-spot-${id}" class="dp-walker-spot"></div>
                         </div>`, $(parentElement))

    }


}