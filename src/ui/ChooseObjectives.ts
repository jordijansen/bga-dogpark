class ChooseObjectives {
    private stock: LineStock<Card>;

    constructor(private game: DogPark,
                private elementId: string) {
    }

    public enter() {
        dojo.place('<div id="dp-choose-objectives-stock"></div>', $(this.elementId))

        if (!this.stock) {
            this.stock = new LineStock<Card>(this.game.objectiveCardManager, $('dp-choose-objectives-stock'), {gap: '25px', sort: (a, b) => a.typeArg - b.typeArg});
        }

        const player = this.game.getPlayer(this.game.getPlayerId());
        this.stock.addCards(player.objectives);
        if ((this.game as any).isCurrentPlayerActive()) {
            this.stock.setSelectionMode('single', player.objectives);
        } else {
            const player = this.game.getPlayer(this.game.getPlayerId());
            const selectedCard = this.stock.getCards().find(card => card.id === Number(player.selectedObjectiveCardId));
            if (selectedCard) {
                this.stock.getCardElement(selectedCard).classList.add('bga-cards_selected-card');
            }
        }
    }

    public exit() {
        const selectedCard = this.stock.getSelection()[0];
        this.stock.setSelectionMode('none');
        this.stock.getCardElement(selectedCard).classList.add('bga-cards_selected-card');
    }

    public getSelectedObjectiveId() {
        const selection = this.stock.getSelection();
        if (selection.length > 0) {
            return selection[0].id;
        }
        return null;
    }

    public destroy() {
        dojo.empty($(this.elementId))
    }
}