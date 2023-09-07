class DogField {
    private dogStocks: {[slotId: number]: LineStock<DogCard>} = {}
    private walkerStocks: {[slotId: number]: LineStock<DogWalker>} = {}
    public scoutedDogStock: LineStock<DogCard>;
    constructor(private game: DogParkGame) {}

    public setUp(gameData: DogParkGameData) {
        dojo.place(this.createScoutArea(), 'dp-game-board-field-scout-wrapper')
        this.scoutedDogStock = new LineStock<DogCard>(this.game.dogCardManager, $('dp-game-board-field-scout'), {gap: '16px'});
        for(let i = 1; i <= gameData.field.nrOfFields; i++) {
            dojo.place(this.createFieldSlot(i), 'dp-game-board-field');
            this.dogStocks[i] = new LineStock<DogCard>(this.game.dogCardManager, $(`dp-field-slot-${i}-dog`), {})
            this.walkerStocks[i] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(`dp-field-slot-${i}-walkers`), {})
        }

        this.addDogCardsToScoutedField(gameData.field.scoutedDogs);
        this.addDogCardsToField(gameData.field.dogs);
        this.addWalkersToField(gameData.field.walkers);
    }

    public addDogCardsToScoutedField(scoutedDogs: DogCard[]) {
        this.scoutedDogStock.removeAll();
        const promise = this.scoutedDogStock.addCards(scoutedDogs);
        this.showHideScoutField();
        return promise;
    }

    private showHideScoutField() {
        if (this.scoutedDogStock.getCards().length > 0) {
            $('dp-game-board-field-scout-wrapper').style.display = 'block';
        } else {
            $('dp-game-board-field-scout-wrapper').style.display = 'none';
        }
    }

    public addDogCardsToField(dogs: DogCard[]) {
        return dogs.filter(dog => dog.location === 'field')
            .map(dog => this.dogStocks[dog.locationArg].addCard(dog).then(() => this.showHideScoutField()))
    }

    public addWalkersToField(walkers: DogWalker[]) {
        return walkers.filter(walker => walker.location.startsWith('field_'))
            .map(walker => this.walkerStocks[Number(walker.location.replace('field_', ''))].addCard(walker))
    }

    public setDogSelectionModeField(selectionMode: CardSelectionMode) {
        for (const slotId in this.dogStocks) {
            this.dogStocks[slotId].onSelectionChange = selectionMode === 'none' ? undefined : () => {
                for (const otherSlotId in this.dogStocks) {
                    if (slotId !== otherSlotId) {
                        this.dogStocks[otherSlotId].unselectAll(true);
                    }
                }
            }
            this.dogStocks[slotId].setSelectionMode(selectionMode);
        }
    }

    public setDogSelectionModeScout(selectionMode: CardSelectionMode) {
        this.scoutedDogStock.setSelectionMode(selectionMode);
    }

    public getSelectedFieldDog() {
        for (const slotId in this.dogStocks) {
            if (this.dogStocks[slotId].getSelection() && this.dogStocks[slotId].getSelection().length === 1) {
                return this.dogStocks[slotId].getSelection()[0];
            }
        }
        return null;
    }

    public getSelectedScoutDog() {
        if (this.scoutedDogStock.getSelection() && this.scoutedDogStock.getSelection().length === 1) {
            return this.scoutedDogStock.getSelection()[0];
        }
        return null;
    }

    public discardDogFromField(fieldDog: DogCard) {
        for (const slotId in this.dogStocks) {
            if (this.dogStocks[slotId].contains(fieldDog)) {
                this.game.dogCardManager.discardPile.addCard(fieldDog);
            }
        }
    }

    private createFieldSlot(id: number) {
        return `<div id="dp-field-slot-${id}" class="dp-field-slot">
                    <div id="dp-field-slot-${id}-dog" class="dp-field-slot-card">
                    </div>
                    <div id="dp-field-slot-${id}-walkers" class="dp-field-slot-walkers">
                    </div>
                </div>`;
    }

    private createScoutArea() {
        return `<div class="label-wrapper" style="margin-bottom: 16px;">
                  <h2><div class="dp-token-token" data-type="scout"></div> ${_('Scouted Cards')}</h2>
                </div>
                <div id="dp-game-board-field-scout"></div>`
    }


}