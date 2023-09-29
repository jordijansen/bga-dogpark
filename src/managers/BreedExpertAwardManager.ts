class BreedExpertAwardManager extends CardManager<Card> {

    public static readonly SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY = 'dogpark-side-bar-collapsed';

    public static CARD_WIDTH = 195;
    public static CARD_HEIGHT = 142;

    private stock: SlotStock<Card>;
    private slotsIds: string[] = [];

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
        const collapsed = window.localStorage.getItem(BreedExpertAwardManager.SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY) === 'true';
        dojo.place(` <div id="dp-game-board-side" class="dp-board ${Boolean(collapsed) ? 'hide-side-bar' : ''}">
            <div id="dp-game-board-side-flex-wrapper">
                <div id="dp-game-board-breed-expert-awards" class="dp-board">
                    <div id="dp-game-board-breed-expert-awards-stock">
    
                    </div>
                </div>
                <div id="dp-game-board-side-toggle-button"><i class="fa fa-trophy" aria-hidden="true"></i> ${_('Breed Expert')} <i class="fa fa-trophy" aria-hidden="true"></i></div>
            </div>
        </div>`, $('pagesection_gameview'))

        if (!collapsed) {
            $('bga-jump-to_controls').style.left = '340px';
        }

        dojo.connect($('dp-game-board-breed-expert-awards'), 'onclick', () => this.toggleSideBar());
        dojo.connect($('dp-game-board-side-toggle-button'), 'onclick', () => this.toggleSideBar());

        this.slotsIds = [
            `dp-game-board-breed-expert-awards-slot-1`,
            `dp-game-board-breed-expert-awards-slot-2`,
            `dp-game-board-breed-expert-awards-slot-3`,
            `dp-game-board-breed-expert-awards-slot-4`,
            `dp-game-board-breed-expert-awards-slot-5`,
            `dp-game-board-breed-expert-awards-slot-6`,
            `dp-game-board-breed-expert-awards-slot-7`
        ];

        this.stock = new SlotStock(this, $('dp-game-board-breed-expert-awards-stock'), {
            slotsIds: this.slotsIds,
            mapCardToSlot: card => `dp-game-board-breed-expert-awards-slot-${card.locationArg}`,
            gap: '10px',
        })
        this.stock.addCards(gameData.breedExpertAwards);

        this.slotsIds.forEach(slotId => {
            let html = `<div id="${slotId}-standings" class="dp-game-board-breed-expert-awards-slot-standings">`;
            html += '</div>';
            dojo.place(html, dojo.query(`[data-slot-id="${slotId}"]`)[0])
        })

        this.updateBreedExpertAwardStandings();
    }

    private toggleSideBar() {
        dojo.toggleClass('dp-game-board-side', 'hide-side-bar');
        window.localStorage.setItem(BreedExpertAwardManager.SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY, String(dojo.hasClass('dp-game-board-side', 'hide-side-bar')))
        if (!dojo.hasClass('dp-game-board-side', 'hide-side-bar')) {
            $('bga-jump-to_controls').style.left = '340px';
        } else {
            $('bga-jump-to_controls').style.left = '';
        }
    }

    public updateBreedExpertAwardStandings() {
        const playerDogBreeds: {[playerId: number]: string[]} = {}

        for (const playerId in this.dogParkGame.playerArea.kennelStocks) {
            playerDogBreeds[playerId] = this.dogParkGame.playerArea.kennelStocks[playerId].getCards().flatMap(dogCard => dogCard.breeds);
            if (this.dogParkGame.playerArea.leadStocks[playerId]) {
                playerDogBreeds[playerId] = [...playerDogBreeds[playerId], ...this.dogParkGame.playerArea.leadStocks[playerId].getCards().flatMap(dogCard => dogCard.breeds)];
            }
        }

        const cards = this.stock.getCards();
        for (const card of cards) {
            const elementId = `dp-game-board-breed-expert-awards-slot-${card.locationArg}-standings`;
            const element = $(elementId);
            dojo.empty(element);

            let html = '';
            for (const playerId in this.dogParkGame.playerArea.kennelStocks) {
                let playerColor = this.dogParkGame.getPlayer(Number(playerId))?.color;
                if (!playerColor) {
                    playerColor = this.dogParkGame.gamedatas.autoWalkers.find(autoWalker => autoWalker.id === Number(playerId))?.color;
                }

                const order = playerDogBreeds[playerId].filter(breed => breed === card.type).length;
                html += `<div id="dp-game-board-breed-expert-awards-slot-standings-${playerId}" class="dp-game-board-breed-expert-awards-slot-standings-wrapper ${order === 0 ? 'not-eligible': ''}" style="order: ${8 - order};"><span class="dp-dog-walker dp-token" data-color="#${playerColor}"><h1>${order}</h1></span></div>`
            }
            dojo.place(html, element);
        }

    }
}