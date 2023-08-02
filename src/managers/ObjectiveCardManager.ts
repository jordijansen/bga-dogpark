class ObjectiveCardManager extends CardManager<Card> {

    public static CARD_HEIGHT = 195;
    public static CARD_WIDTH = 266;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-objective-card-${card.id}`,
            setupDiv: (card: Card, div: HTMLElement) => {
                div.classList.add('blackjack-size-landscape')
            },
            setupBackDiv: (card: Card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-back`;
                div.classList.add(`objective-art`)
                div.classList.add(`objective-art-background`)
            },
            setupFrontDiv: (card: Card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`objective-art`)
                div.classList.add(`objective-art-${card.typeArg}`)
            },
            isCardVisible: (card) => !!card.typeArg,
            cardWidth: ObjectiveCardManager.CARD_WIDTH,
            cardHeight: ObjectiveCardManager.CARD_HEIGHT,
        })
    }
}