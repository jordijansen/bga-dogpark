class BreedExpertAwardManager extends CardManager<Card> {

    public static readonly SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY = 'dogpark-side-bar-collapsed';

    public static CARD_WIDTH = 195;
    public static CARD_HEIGHT = 142;

    private stock: SlotStock<Card>;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-breed-expert-${card.id}`,
            setupDiv: (card: Card, div: HTMLElement) => {
                div.classList.add('mini-size-landscape')
            },
            setupFrontDiv: (card: Card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`breed-expert-art`)
                div.classList.add(`breed-expert-art-${card.typeArg}`)
            },
            cardWidth: BreedExpertAwardManager.CARD_WIDTH,
            cardHeight: BreedExpertAwardManager.CARD_HEIGHT,
        })
    }

    public setUp(gameData: DogParkGameData) {
        const collapsed = Boolean(window.localStorage.getItem(BreedExpertAwardManager.SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY));
        dojo.place(` <div id="dp-game-board-side" class="dp-board ${collapsed ? 'hide-side-bar' : ''}">
            <div id="dp-game-board-breed-expert-awards" class="dp-board">
                <div id="dp-game-board-breed-expert-awards-stock">

                </div>
            </div>
            <div id="dp-game-board-side-toggle-button"><i class="fa fa-trophy" aria-hidden="true"></i> ${_('Breed Expert')} <i class="fa fa-trophy" aria-hidden="true"></i></div>
        </div>`, $('pagesection_gameview'))

        dojo.connect($('dp-game-board-side-toggle-button'), 'onclick', () => {
            dojo.toggleClass('dp-game-board-side', 'hide-side-bar');
            window.localStorage.setItem(BreedExpertAwardManager.SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY, dojo.hasClass('dp-game-board-side', 'hide-side-bar') + '')
        });

        this.stock = new SlotStock(this, $('dp-game-board-breed-expert-awards-stock'), {
            slotsIds: [
                `dp-game-board-breed-expert-awards-slot-1`,
                `dp-game-board-breed-expert-awards-slot-2`,
                `dp-game-board-breed-expert-awards-slot-3`,
                `dp-game-board-breed-expert-awards-slot-4`,
                `dp-game-board-breed-expert-awards-slot-5`,
                `dp-game-board-breed-expert-awards-slot-6`,
                `dp-game-board-breed-expert-awards-slot-7`
            ],
            mapCardToSlot: card => `dp-game-board-breed-expert-awards-slot-${card.locationArg}`,
            gap: '10px',
        })
        this.stock.addCards(gameData.breedExpertAwards);
    }
}