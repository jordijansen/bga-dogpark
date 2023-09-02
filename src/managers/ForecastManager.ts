class ForecastManager extends CardManager<ForecastCard> {

    public static CARD_WIDTH = 195;
    public static CARD_HEIGHT = 142;

    public stock: SlotStock<Card>;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-forecast-${card.id}`,
            setupDiv: (card, div: HTMLElement) => {
                div.classList.add('mini-size-landscape')

                const helpButtonElement = document.createElement("div");
                helpButtonElement.classList.add('dp-help-button-wrapper')
                helpButtonElement.classList.add('position-floating-right')
                helpButtonElement.innerHTML = `<i id="dp-help-forecast-${card.id}" class="dp-help-button fa fa-question-circle"  aria-hidden="true"></i>`
                div.appendChild(helpButtonElement);

                dojo.connect($(`dp-help-forecast-${card.id}`), 'onclick', (event) => this.dogParkGame.helpDialogManager.showForecastHelpDialog(event, card));
            },
            setupFrontDiv: (card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`forecast-art`)
                div.classList.add(`forecast-art-${card.typeArg}`)
            },
            setupBackDiv: (card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-back`;
                div.classList.add(`forecast-art`)
                div.classList.add(`forecast-art-background`)
            },
            isCardVisible: (card) => !!card.typeArg,
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