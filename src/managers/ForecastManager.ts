class ForecastManager extends CardManager<Card> {

    public static CARD_WIDTH = 195;
    public static CARD_HEIGHT = 142;

    private stock: SlotStock<Card>;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-forecast-${card.id}`,
            setupDiv: (card: Card, div: HTMLElement) => {
                div.classList.add('mini-size-landscape')
            },
            setupFrontDiv: (card: Card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`forecast-art`)
                div.classList.add(`forecast-art-${card.typeArg}`)
            },
            cardWidth: ForecastManager.CARD_WIDTH,
            cardHeight: ForecastManager.CARD_HEIGHT,
        })
    }

    public setUp(gameData: DogParkGameData) {
        this.stock = new SlotStock(this, $('dp-round-tracker-forecast-stock'), {
            slotsIds: [
                `dp-round-tracker-forecast-slot-1`,
                `dp-round-tracker-forecast-slot-2`,
                `dp-round-tracker-forecast-slot-3`,
                `dp-round-tracker-forecast-slot-4`,
            ],
            mapCardToSlot: card => `dp-round-tracker-forecast-slot-${card.locationArg}`,
            direction: 'row',
            gap: '43px',
            center: false
        })
        this.stock.addCards(gameData.forecastCards);
    }
}