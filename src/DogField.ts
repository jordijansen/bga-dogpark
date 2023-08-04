class DogField {
    private dogStocks: {[slotId: number]: LineStock<DogCard>} = {}
    private walkerStocks: {[slotId: number]: LineStock<DogWalker>} = {}
    constructor(private game: DogParkGame) {}

    public setUp(gameData: DogParkGameData) {
        for(let i = 1; i <= gameData.field.nrOfFields; i++) {
            dojo.place(this.createFieldSlot(i), 'dp-game-board-field');
            this.dogStocks[i] = new LineStock<DogCard>(this.game.dogCardManager, $(`dp-field-slot-${i}-dog`), {})
            this.walkerStocks[i] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(`dp-field-slot-${i}-walkers`), {})
        }
        this.addDogCardsToField(gameData.field.dogs);
        this.addWalkersToField(gameData.field.walkers);
    }

    public addDogCardsToField(dogs: DogCard[]) {
        return dogs.filter(dog => dog.location === 'field')
            .map(dog => this.dogStocks[dog.locationArg].addCard(dog))
    }

    public addWalkersToField(walkers: DogWalker[]) {
        return walkers.filter(walker => walker.location.startsWith('field_'))
            .map(walker => this.walkerStocks[Number(walker.location.replace('field_', ''))].addCard(walker))
    }

    public setDogSelectionMode(selectionMode: CardSelectionMode) {
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

    public getSelectedDog() {
        for (const slotId in this.dogStocks) {
            if (this.dogStocks[slotId].getSelection() && this.dogStocks[slotId].getSelection().length === 1) {
                return this.dogStocks[slotId].getSelection()[0];
            }
        }
        return null;
    }

    private createFieldSlot(id: number) {
        return `<div id="dp-field-slot-${id}" class="dp-field-slot">
                    <div id="dp-field-slot-${id}-dog" class="dp-field-slot-card">
                    </div>
                    <div id="dp-field-slot-${id}-walkers" class="dp-field-slot-walkers">
                    </div>
                </div>`;
    }
}