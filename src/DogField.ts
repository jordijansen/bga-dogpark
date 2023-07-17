class DogField {
    private dogStocks: {[slotId: number]: LineStock<DogCard>} = {}
    constructor(private game: DogParkGame) {}

    public setUp(gameData: DogParkGameData) {
        for(let i = 1; i <= gameData.field.nrOfFields; i++) {
            dojo.place(this.createFieldSlot(i), 'dp-game-board-field');
            this.dogStocks[i] = new LineStock<DogCard>(this.game.dogCardManager, $(`dp-field-slot-${i}-dog`), {})
        }
        this.addDogCardsToField(gameData.field.dogs);
    }

    public addDogCardsToField(dogs: DogCard[]) {
        return dogs.filter(dog => dog.location === 'field')
            .map(dog => this.dogStocks[dog.locationArg].addCard(dog))
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