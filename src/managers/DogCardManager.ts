class DogCardManager extends CardManager<DogCard> {

    public static CARD_WIDTH = 195;
    public static CARD_HEIGHT = 266;

    public cardTokenVoidStocks: {[id: number]: VoidStock<Token>} = {}
    public cardTokenStocks: {[id: number]: LineStock<Token>} = {}
    public discardPile: AllVisibleDeck<DogCard>;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-dog-card-${card.id}`,
            setupDiv: (card: DogCard, div: HTMLElement) => {
                div.classList.add('blackjack-size-portrait')
                div.dataset.id = `${card.id}`
                div.dataset.type = 'dog'

                const cardTokenVoidStockElement = document.createElement("div");
                cardTokenVoidStockElement.id = `dp-dog-card-token-void-stock-${card.id}`;
                div.appendChild(cardTokenVoidStockElement);
                this.cardTokenVoidStocks[card.id] = new VoidStock<Token>(dogParkGame.tokenManager, $(cardTokenVoidStockElement.id))

                const cardTokenStockElement = document.createElement("div");
                cardTokenStockElement.id = `dp-dog-card-token-stock-${card.id}`;
                cardTokenStockElement.classList.add('dp-dog-card-token-stock')
                div.appendChild(cardTokenStockElement);
                this.cardTokenStocks[card.id] = new LineStock<Token>(dogParkGame.tokenManager, $(cardTokenStockElement.id), {gap: '2px'})

                const helpButtonElement = document.createElement("div");
                helpButtonElement.classList.add('dp-help-button-wrapper')
                helpButtonElement.classList.add('position-floating-bottom')
                helpButtonElement.innerHTML = `<i id="dp-help-dog-${card.id}" class="dp-help-button fa fa-question-circle"  aria-hidden="true"></i>`
                div.appendChild(helpButtonElement);
                this.addInitialResourcesToDog(card);

                dojo.connect($(`dp-help-dog-${card.id}`), 'onclick', (event) => this.dogParkGame.helpDialogManager.showDogHelpDialog(event, card));
            },
            setupFrontDiv: (card: DogCard, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`dog-card-art`)
                div.classList.add(`dog-card-art-${card.typeArg}`)
                div.dataset.set = card.type;
            },
            isCardVisible: (card: DogCard) => !!card.typeArg,
            cardWidth: DogCardManager.CARD_WIDTH,
            cardHeight: DogCardManager.CARD_HEIGHT,
        })
    }

    public setUp(data: DogParkGameData) {
        dojo.place(`<div class="label-wrapper" style="margin-bottom: 16px;">
                  <h2> ${_('Discard Pile')}</h2>
                </div>
                <div id="dp-dog-discard-pile"></div>`, $('dp-last-row'))
        this.discardPile = new AllVisibleDeck<DogCard>(this, $("dp-dog-discard-pile"), {});
        this.discardPile.addCards(data.discardPile.filter(dogCard => !data.field.scoutedDogs.map(dog => dog.id).includes(dogCard.id)));
    }

    public addResourceToDog(dogId: number, type: Token['type']) {
        const token = this.dogParkGame.tokenManager.createToken(type);
        return this.cardTokenStocks[dogId].addCard(token);
    }

    public removeResourceFromDog(dogId: number, type: Token['type']) {
        const token = this.cardTokenStocks[dogId].getCards().find(token => token.type === type);
        if (token) {
            this.cardTokenStocks[dogId].removeCard(token);
        }
    }

    public addInitialResourcesToDog(dog: DogCard) {
        for (const resource in dog.resourcesOnCard) {
            for (let i = 0; i < Number(dog.resourcesOnCard[resource]); i++) {
                this.addResourceToDog(dog.id, resource as any);
            }
        }
    }

    public removeAllResourcesFromDog(id: number) {
        this.cardTokenStocks[id].removeAll();
    }
}