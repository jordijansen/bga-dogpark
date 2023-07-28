class DogCardManager extends CardManager<DogCard> {

    public static CARD_WIDTH = 195;
    public static CARD_HEIGHT = 266;

    public cardTokenVoidStocks: {[id: number]: VoidStock<Token>} = {}
    public cardTokenStocks: {[id: number]: LineStock<Token>} = {}

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

                this.addInitialResourcesToDog(card);
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

    private addInitialResourcesToDog(dog: DogCard) {
        for (const resource in dog.resourcesOnCard) {
            for (let i = 0; i < Number(dog.resourcesOnCard[resource]); i++) {
                this.addResourceToDog(dog.id, resource as any);
            }
        }
    }
}