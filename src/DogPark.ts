declare const define;
declare const ebg;
declare const $;
declare const dojo: Dojo;
declare const _;
declare const g_gamethemeurl;
declare const g_replayFrom;
declare const g_archive_mode;

const ZOOM_LEVELS = [0.25, 0.375, 0.5, 0.625, 0.75, 0.875, 1]

const ANIMATION_MS = 800;
const TOOLTIP_DELAY = document.body.classList.contains('touch-device') ? 1500 : undefined;
const LOCAL_STORAGE_ZOOM_KEY = 'dogpark-zoom';

class DogPark implements DogParkGame {

    instantaneousMode: boolean;
    notifqueue: {};

    public gamedatas: DogParkGameData;
    private zoomManager: ZoomManager;

    // UI elements
    private currentPlayerOfferDial: DogOfferDial;

    // Managers
    public dogCardManager: DogCardManager;
    public dogWalkerManager: DogWalkerManager;

    // Modules
    private dogField: DogField;
    private playerArea: PlayerArea;
    private roundTracker: RoundTracker;

    constructor() {
        // Init Managers
        this.dogCardManager = new DogCardManager(this);
        this.dogWalkerManager = new DogWalkerManager(this);

        // Init Modules
        this.dogField = new DogField(this);
        this.playerArea = new PlayerArea(this);
        this.roundTracker = new RoundTracker(this);
    }

    /*
        setup:

        This method must set up the game user interface according to current game situation specified
        in parameters.

        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)

        "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
    */

    public setup(gamedatas: DogParkGameData) {
        log( "Starting game setup" );
        log('gamedatas', gamedatas);

        // Setup modules
        this.dogField.setUp(gamedatas);
        this.playerArea.setUp(gamedatas);
        this.roundTracker.setUp(gamedatas);

        this.zoomManager = new AutoZoomManager('dp-game-board-wrapper', 'dp-zoom-level')

        dojo.connect($('dp-game-board-side-toggle-button'), 'onclick', () => dojo.toggleClass('dp-game-board-side', 'hide-side-bar'));

        this.setupNotifications();
        log( "Ending game setup" );
    }

    ///////////////////////////////////////////////////
    //// Game & client states

    // onEnteringState: this method is called each time we are entering into a new game state.
    //                  You can use this method to perform some user interface changes at this moment.
    //
    public onEnteringState(stateName: string, args: any) {
        log('Entering state: ' + stateName, args.args);

        switch (stateName) {
            case 'recruitmentOffer':
                this.enteringRecruitmentOffer(args.args as RecruitmentOfferArgs);
                break;
            case 'recruitmentTakeDog':
                this.enteringRecruitmentTakeDog();
                break;
        }
    }

    private enteringRecruitmentOffer(args: RecruitmentOfferArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            if (args.maxOfferValue > 0) {
                this.dogField.setDogSelectionMode('single');
                this.gamedatas.gamestate.descriptionmyturn = this.gamedatas.gamestate.descriptionmyturn + '<br />' + _('Select a dog and offer value (reputation cost)') + '<br />';
                this.gamedatas.gamestate.descriptionmyturn = this.gamedatas.gamestate.descriptionmyturn + '<div id="dp-offer-dial-controls-wrapper"></div>';
                (this as any).updatePageTitle();
                this.currentPlayerOfferDial = new DogOfferDial({
                    elementId: 'dp-current-player-offer-dial',
                    parentId: 'dp-offer-dial-controls-wrapper',
                    player: this.getPlayer(this.getPlayerId()),
                    initialValue: 1,
                    maxOfferValue: args.maxOfferValue,
                    readOnly: false
                });
            } else {
                this.gamedatas.gamestate.descriptionmyturn = this.gamedatas.gamestate.descriptionmyturn + '<br />' + _('Insufficient reputation to place an offer') + '<br />';
                (this as any).updatePageTitle();
            }
        }
    }

    private enteringRecruitmentTakeDog() {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogField.setDogSelectionMode('single');
        }
    }

    public onLeavingState(stateName: string) {
        log( 'Leaving state: '+stateName );

        switch (stateName) {
            case 'recruitmentOffer':
            case 'recruitmentTakeDog':
                this.dogField.setDogSelectionMode('none');
                break;
            case 'recruitmentEnd':
                this.dogField.removeFocusToField();
                break;
        }
    }

    // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
    //                        action status bar (ie: the HTML links in the status bar).
    //
    public onUpdateActionButtons(stateName: string, args: any) {

        if ((this as any).isCurrentPlayerActive()) {
            switch (stateName) {
                case 'recruitmentOffer':
                    if (args.maxOfferValue > 0) {
                        (this as any).addActionButton('placeOfferOnDog', _("Confirm"), () => this.placeOfferOnDog());
                    } else {
                        (this as any).addActionButton('skipPlaceOfferOnDog', _("Skip"), () => this.skipPlaceOfferOnDog());
                    }
                    break;
                case 'recruitmentTakeDog':
                    (this as any).addActionButton('takeDog', _("Confirm"), () => this.recruitDog());

            }

            if ([].includes(stateName) && args.canCancelMoves) {
                (this as any).addActionButton('undoLastMoves', _("Undo last moves"), () => this.undoLastMoves(), null, null, 'gray');
            }
        }
    }

    private recruitDog() {
        const selectedDog = this.dogField.getSelectedDog();
        this.takeAction('recruitDog', {dogId: selectedDog?.id})
    }

    private skipPlaceOfferOnDog() {
        this.takeAction('skipPlaceOfferOnDog');
    }

    private placeOfferOnDog() {
        const selectedDog = this.dogField.getSelectedDog();
        const offerValue = this.currentPlayerOfferDial.currentValue;

        this.takeAction('placeOfferOnDog', {dogId: selectedDog?.id, offerValue});
    }

    private undoLastMoves() {
        this.takeAction('undoLastMoves');
    }

    ///////////////////////////////////////////////////
    //// Utility methods
    ///////////////////////////////////////////////////

    public isReadOnly() {
        return (this as any).isSpectator || typeof g_replayFrom != 'undefined' || g_archive_mode;
    }

    public getPlayerId(): number {
        return Number((this as any).player_id);
    }

    public getPlayer(playerId: number): DogParkPlayer {
        return Object.values(this.gamedatas.players).find(player => Number(player.id) == playerId);
    }

    public takeAction(action: string, data?: any, onComplete: () => void = () => {}) {
        data = data || {};
        data.lock = true;
        (this as any).ajaxcall(`/dogpark/dogpark/${action}.html`, data, this, onComplete);
    }
    public takeNoLockAction(action: string, data?: any, onComplete: () => void = () => {}) {
        data = data || {};
        (this as any).ajaxcall(`/dogpark/dogpark/${action}.html`, data, this, onComplete);
    }

    public setTooltip(id: string, html: string) {
        (this as any).addTooltipHtml(id, html, TOOLTIP_DELAY);
    }
    public setTooltipToClass(className: string, html: string) {
        (this as any).addTooltipHtmlToClass(className, html, TOOLTIP_DELAY);
    }

    private setScore(playerId: number, score: number) {
        (this as any).scoreCtrl[playerId]?.toValue(score);
    }

    private isAskForConfirmation() {
        return true; // For now always ask for confirmation, might make this a preference later on.
    }

    private wrapInConfirm(runnable: () => void) {
        if (this.isAskForConfirmation()) {
            (this as any).confirmationDialog(_("This action can not be undone. Are you sure?"), () => {
                runnable();
            });
        } else {
            runnable();
        }
    }

    ///////////////////////////////////////////////////
    //// Reaction to cometD notifications

    /*
        setupNotifications:

        In this method, you associate each of your game notifications with your local method to handle it.

        Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                your pylos.game.php file.

    */
    setupNotifications() {
        log( 'notifications subscriptions setup' );

        const notifs = [
            ['dogRecruited', undefined],
            ['dogOfferPlaced', undefined],
            ['offerValueRevealed', ANIMATION_MS],
            ['resetAllOfferValues', ANIMATION_MS],
            ['fieldRefilled', undefined],
            ['newPhase', ANIMATION_MS]
            // ['shortTime', 1],
            // ['fixedTime', 1000]
        ];

        notifs.forEach((notif) => {
            dojo.subscribe(notif[0], this, notifDetails => {
                log(`notif_${notif[0]}`, notifDetails.args);

                const promise = this[`notif_${notif[0]}`](notifDetails.args);

                // tell the UI notification ends
                promise?.then(() => (this as any).notifqueue.onSynchronousNotificationEnd());
            });
            // make all notif as synchronous
            (this as any).notifqueue.setSynchronous(notif[0], notif[1]);
        });
    }

    private notif_dogRecruited(args: NotifDogRecruited) {
        this.setScore(args.playerId, args.score);
        return this.playerArea.moveDogsToKennel(args.playerId, [args.dog])
            .then(() => this.playerArea.moveWalkerToPlayer(args.playerId, args.walker))
    }

    private notif_dogOfferPlaced(args: NotifDogOfferPlaced) {
        if (Number(args.playerId) === Number(this.getPlayerId())) {
            this.playerArea.setPlayerOfferValue(this.getPlayerId(), this.currentPlayerOfferDial.currentValue);
        }
        return Promise.all(this.dogField.addWalkersToField([args.walker]));
    }

    private notif_offerValueRevealed(args: NotifOfferValueRevealed) {
        this.playerArea.setPlayerOfferValue(args.playerId, args.offerValue);
    }

    private notif_resetAllOfferValues() {
        this.playerArea.resetAllOfferValues();
    }

    private notif_fieldRefilled(args: NotifFieldRefilled) {
        return Promise.all(this.dogField.addDogCardsToField(args.dogs));
    }

    private notif_newPhase(args: NotifNewPhase) {
        this.roundTracker.updatePhase(args.newPhase);
    }

    public format_string_recursive(log: string, args: any) {
        try {
            if (log && args && !args.processed) {
                Object.keys(args).forEach(argKey => {
                })
            }
        } catch (e) {
            console.error(log, args, "Exception thrown", e.stack);
        }
        return (this as any).inherited(arguments);
    }
}