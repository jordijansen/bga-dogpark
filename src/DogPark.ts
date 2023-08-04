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
    public animationManager: AnimationManager;


    // UI elements
    private currentPlayerOfferDial: DogOfferDial;
    private currentPlayerPayCosts: DogPayCosts;
    private currentPlayerChooseObjectives: ChooseObjectives;

    // Managers
    public dogCardManager: DogCardManager;
    public dogWalkerManager: DogWalkerManager;
    public tokenManager: TokenManager;
    public breedExpertAwardManager: BreedExpertAwardManager;
    public forecastManager: ForecastManager;
    public objectiveCardManager: ObjectiveCardManager;
    public locationBonusCardManager: LocationBonusCardManager;

    // Modules
    private dogField: DogField;
    public dogWalkPark: DogWalkPark;
    private playerArea: PlayerArea;
    private roundTracker: RoundTracker;
    private playerResources: PlayerResources;

    constructor() {
        // Init Managers
        this.dogCardManager = new DogCardManager(this);
        this.dogWalkerManager = new DogWalkerManager(this);
        this.tokenManager = new TokenManager(this);
        this.breedExpertAwardManager = new BreedExpertAwardManager(this);
        this.forecastManager = new ForecastManager(this);
        this.objectiveCardManager = new ObjectiveCardManager(this);
        this.locationBonusCardManager = new LocationBonusCardManager(this);

        // Init Modules
        this.dogField = new DogField(this);
        this.dogWalkPark = new DogWalkPark(this);
        this.playerArea = new PlayerArea(this);
        this.playerResources = new PlayerResources(this);
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
        this.dogWalkPark.setUp(gamedatas);
        this.playerArea.setUp(gamedatas);
        this.roundTracker.setUp(gamedatas);
        this.playerResources.setUp(gamedatas);
        this.breedExpertAwardManager.setUp(gamedatas);
        this.forecastManager.setUp(gamedatas);

        this.zoomManager = new AutoZoomManager('dp-game', 'dp-zoom-level')
        this.animationManager = new AnimationManager(this, {duration: ANIMATION_MS})

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
            case 'chooseObjectives':
                this.enteringChooseObjectives();
                break;
            case 'recruitmentOffer':
                this.enteringRecruitmentOffer(args.args as RecruitmentOfferArgs);
                break;
            case 'recruitmentTakeDog':
                this.enteringRecruitmentTakeDog();
                break;
            case 'selectionPlaceDogOnLead':
                this.enteringSelectionPlaceDogOnLead(args.args as SelectionPlaceDogOnLeadArgs);
                break;
            case 'selectionPlaceDogOnLeadSelectResources':
                this.enteringSelectionPlaceDogOnLeadSelectResources(args.args as SelectionPlaceDogOnLeadSelectResourcesArgs);
                break;
            case 'walkingMoveWalker':
                this.enteringWalkingMoveWalker(args.args as WalkingMoveWalkerArgs);
        }
    }

    private enteringChooseObjectives() {
        this.currentPlayerChooseObjectives = new ChooseObjectives(this, "dp-choose-objectives");
        this.currentPlayerChooseObjectives.enter();
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

    private enteringSelectionPlaceDogOnLead(args: SelectionPlaceDogOnLeadArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            this.playerArea.setSelectionModeForKennel('single', this.getPlayerId(), Object.values(args.dogs), (selection) => {
                if (selection.length === 1) {
                    this.playerArea.setSelectionModeForKennel('none', this.getPlayerId());
                    (this as any).takeNoLockAction("placeDogOnLead", {dogId: selection[0].id});
                }
            });
        }
    }

    private enteringSelectionPlaceDogOnLeadSelectResources(args: SelectionPlaceDogOnLeadSelectResourcesArgs) {
        this.gamedatas.gamestate.descriptionmyturn = dojo.string.substitute(_('Select resources for ${dogName}'), { dogName: args.dog.name })
        this.gamedatas.gamestate.descriptionmyturn = this.gamedatas.gamestate.descriptionmyturn + '<br /><div id="dp-pay-costs"></div>';
        (this as any).updatePageTitle();
        this.currentPlayerPayCosts = new DogPayCosts("dp-pay-costs", args.resources, args.dog, () => {
            dojo.destroy('dp-pay-costs');
            this.takeNoLockAction('placeDogOnLeadCancel')
        }, (resources) => {
            dojo.destroy('dp-pay-costs');
            this.takeNoLockAction('placeDogOnLeadPayResources', {dogId: args.dog.id, resources: JSON.stringify(resources)})
        });
    }

    private enteringWalkingMoveWalker(args: WalkingMoveWalkerArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogWalkPark.enterWalkerSpotsSelection(args.possibleParkLocationIds, (locationId)=> {this.takeAction('moveWalker', {locationId})});
        }
    }

    public onLeavingState(stateName: string) {
        log( 'Leaving state: '+stateName );

        switch (stateName) {
            case 'recruitmentOffer':
            case 'recruitmentTakeDog':
                this.dogField.setDogSelectionMode('none');
                break;
            case 'selectionPlaceDogOnLead':
                this.leavingSelectionPlaceDogOnLead();
                break;
            case 'walkingMoveWalker':
                this.leavingWalkingMoveWalker();
        }
    }

    private leavingSelectionPlaceDogOnLead() {
        if ((this as any).isCurrentPlayerActive()) {
            this.playerArea.setSelectionModeForKennel('none', this.getPlayerId());
        }
    }

    private leavingWalkingMoveWalker() {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogWalkPark.exitWalkerSpotsSelection()
        }
    }

    // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
    //                        action status bar (ie: the HTML links in the status bar).
    //
    public onUpdateActionButtons(stateName: string, args: any) {

        if ((this as any).isCurrentPlayerActive()) {
            switch (stateName) {
                case 'chooseObjectives':
                    (this as any).addActionButton('confirmObjective', _("Confirm objective"), () => this.confirmObjective());
                    break;
                case 'recruitmentOffer':
                    if (args.maxOfferValue > 0) {
                        (this as any).addActionButton('placeOfferOnDog', _("Confirm"), () => this.placeOfferOnDog());
                    } else {
                        (this as any).addActionButton('skipPlaceOfferOnDog', _("Skip"), () => this.skipPlaceOfferOnDog());
                    }
                    break;
                case 'recruitmentTakeDog':
                    (this as any).addActionButton('takeDog', _("Confirm"), () => this.recruitDog());
                    break;
                case 'selectionPlaceDogOnLead':
                    const selectionPlaceDogOnLeadArgs = args as SelectionPlaceDogOnLeadArgs;
                    (this as any).addActionButton('confirmSelection', _("Confirm Selection"), () => this.confirmSelection(selectionPlaceDogOnLeadArgs));
                    if (selectionPlaceDogOnLeadArgs.numberOfDogsOnlead < 1 && Object.keys(selectionPlaceDogOnLeadArgs.dogs).length > 1) {
                        dojo.addClass('confirmSelection', 'disabled');
                    }
                    break;
                case 'walkingMoveWalkerAfter':
                    let walkingMoveWalkerAfterArgs = args as WalkingMoveWalkerAfterArgs;
                    this.addWalkingAdditionalActionButtons(walkingMoveWalkerAfterArgs);
                    if (walkingMoveWalkerAfterArgs.additionalActions.every(action => action.optional)) {
                        (this as any).addActionButton('confirmWalking', _("Confirm Walking"), () => this.confirmWalking());
                    }
            }

            if (args?.canCancelMoves) {
                (this as any).addActionButton('undoLast', _("Undo last action"), () => this.undoLast(), null, null, 'red');
                (this as any).addActionButton('undoAll', _("Restart turn"), () => this.undoAll(), null, null, 'red');
            }
        } else {
            if (!this.isReadOnly()) {
                switch (stateName) {
                    case 'selectionActions':
                        (this as any).addActionButton('changeSelection', _("Change Selection"), () => this.changeSelection());
                        break;
                    case 'chooseObjectives':
                        (this as any).addActionButton('changeObjective', _("Change Objective"), () => this.changeObjective());
                        break;
                }
            }
        }
    }

    private addWalkingAdditionalActionButtons(args: WalkingMoveWalkerAfterArgs) {
        if (args.additionalActions && args.additionalActions.length > 0) {
            args.additionalActions.forEach(additionalAction => {
               switch (additionalAction.type) {
                   case 'WALKING_PAY_REPUTATION_ACCEPT':
                       (this as any).addActionButton(`payReputationAccept`, dojo.string.substitute(_('Pay ${resourceType} to unlock location bonus(es)'), { resourceType: this.tokenIcon('reputation') }), () => this.walkingAdditionalAction(additionalAction), null, null, 'gray');
                       break;
                   case 'WALKING_PAY_REPUTATION_DENY':
                       (this as any).addActionButton(`payReputationDeny`, _('Skip location bonuses'), () => this.walkingAdditionalAction(additionalAction), null, null, 'gray');
                       break;
                   case 'WALKING_GAIN_LOCATION_BONUS':
                       (this as any).addActionButton(`gainLocationBonus${additionalAction.id}`, dojo.string.substitute(_('Gain location bonus ${resourceType}'), { resourceType: this.tokenIcon(additionalAction.additionalArgs['bonusType']) }), () => this.walkingAdditionalAction(additionalAction), null, null, 'gray');
                        break;
                   case 'WALKING_GAIN_LEAVING_THE_PARK_BONUS':
                       (this as any).addActionButton(`gainLeavingPark${additionalAction.id}`, dojo.string.substitute(_('Gain leaving the park bonus ${resourceType}'), { resourceType: this.tokenIcons(additionalAction.additionalArgs['bonusType'], additionalAction.additionalArgs['amount']) }), () => this.walkingAdditionalAction(additionalAction), null, null, 'gray');
                       break;
               }
            });
        }
    }

    private walkingAdditionalAction(args: { id: string; type: string; additionalArgs: any[] }) {
        this.takeAction('walkingAdditionalAction', {actionId: args.id})
    }

    private confirmObjective() {
        const cardId = this.currentPlayerChooseObjectives.getSelectedObjectiveId();
        if (cardId) {
            this.currentPlayerChooseObjectives.exit();
            this.takeNoLockAction('chooseObjective', {cardId})
        } else {
            (this as any).showMessage(_("You must select an objective first"), 'error')
        }
    }

    private changeObjective() {
        this.takeNoLockAction('changeObjective', null, () => this.currentPlayerChooseObjectives.enter())
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

    private confirmSelection(args: SelectionPlaceDogOnLeadArgs) {
        if (args.numberOfDogsOnlead < args.maxNumberOfDogs && Object.keys(args.dogs).length > 0) {
            this.wrapInConfirm(() => this.takeNoLockAction('confirmSelection'), _('You can still place dogs on your lead, are you sure you want confirm your selection?'));
        } else {
            this.takeNoLockAction('confirmSelection');
        }
    }

    private changeSelection() {
        this.takeNoLockAction('changeSelection')
    }

    private confirmWalking() {
        this.takeAction('confirmWalking')
    }

    private undoLast() {
        this.takeNoLockAction('undoLast');
    }

    private undoAll() {
        this.takeNoLockAction('undoAll');
    }

    ///////////////////////////////////////////////////
    //// Utility methods
    ///////////////////////////////////////////////////

    private disableActionButtons() {
        const buttons = document.querySelectorAll('.action-button')
        buttons.forEach(button => {
            button.classList.add('disabled');
        })
    }

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
        this.disableActionButtons();
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

    private wrapInConfirm(runnable: () => void, message: string = _("This action can not be undone. Are you sure?")) {
        if (this.isAskForConfirmation()) {
            (this as any).confirmationDialog(message, () => {
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
            ['objectivesChosen', undefined],
            ['dogRecruited', undefined],
            ['dogOfferPlaced', undefined],
            ['offerValueRevealed', ANIMATION_MS],
            ['resetAllOfferValues', ANIMATION_MS],
            ['fieldRefilled', undefined],
            ['newPhase', ANIMATION_MS],
            ['dogPlacedOnLead', undefined],
            ['undoDogPlacedOnLead', 1],
            ['playerGainsResources', undefined],
            ['playerGainsLocationBonusResource', undefined],
            ['undoPlayerGainsLocationBonusResource', undefined],
            ['moveWalkers', undefined],
            ['moveWalker', undefined],
            ['playerPaysReputationForLocation', undefined],
            ['playerLeavesThePark', undefined],
            ['playerGainsReputation', undefined],
            ['playerLosesReputation', undefined],
            ['moveDogsToKennel', undefined],
            ['moveWalkerBackToPlayer', undefined],
            ['flipForecastCard', undefined],
            ['newLocationBonusCardDrawn', undefined],
            ['newFirstWalker', undefined]
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

    private notif_objectivesChosen(args: NotifObjectivesChosen) {
        return Promise.all(args.chosenObjectiveCards.map(({playerId, cardId}) => {
            const objectives = this.getPlayer(playerId).objectives;
            const chosenObjective = objectives.find(card => card.id === cardId);
            return this.playerArea.moveObjectiveToPlayer(playerId, chosenObjective);
        })).then(() => {
            this.currentPlayerChooseObjectives?.destroy();
            this.currentPlayerChooseObjectives = null;
        });
    }

    private notif_dogRecruited(args: NotifDogRecruited) {
        this.setScore(args.playerId, args.score);
        this.playerArea.moveWalkerToPlayer(args.playerId, args.walker)
        return this.playerArea.moveDogsToKennel(args.playerId, [args.dog])
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
        this.roundTracker.updateRound(args.round);
        this.roundTracker.updatePhase(args.newPhase);
    }

    private notif_dogPlacedOnLead(args: NotifDogPlacedOnLead) {
        return this.playerArea.moveDogsToLead(args.playerId, [args.dog])
            .then(() => this.playerResources.payResourcesForDog(args.playerId, args.dog, args.resources))
            .then(() => this.dogCardManager.addResourceToDog(args.dog.id, 'walked'));
    }
    private notif_undoDogPlacedOnLead(args: NotifDogPlacedOnLead) {
        this.playerResources.gainResourcesFromDog(args.playerId, args.dog, args.resources);
        this.playerArea.moveDogsToKennel(args.playerId, [args.dog]);
        this.dogCardManager.removeResourceFromDog(args.dog.id, 'walked');
    }

    private notif_playerGainsResources(args: NotifPlayerGainsResources) {
        return this.playerResources.gainResources(args.playerId, args.resources);
    }

    private notif_playerGainsLocationBonusResource(args: NotifPlayerGainsLocationBonusResource) {
        if (args.resource === 'reputation') {
            this.setScore(args.playerId, args.score);
            if (!!args.extraBonus) {
                this.dogWalkPark.resourceSpots[args.locationId].removeCard(this.dogWalkPark.resourceSpots[args.locationId].getCards().find(token => token.type === args.resource))
            }
        } else {
            return this.playerResources.gainResourceFromLocation(args.playerId, args.locationId, args.resource, args.extraBonus);
        }
        return Promise.resolve();
    }

    private async notif_undoPlayerGainsLocationBonusResource(args: NotifPlayerGainsLocationBonusResource) {
        if (args.extraBonus) {
            await this.dogWalkPark.resourceSpots[args.locationId].addCard(this.tokenManager.createToken(args.resource as any))
        }
        if (args.resource === 'reputation') {
            this.setScore(args.playerId, args.score);
        } else {
            await this.playerResources.payResources(args.playerId, [args.resource]);
        }
    }

    private notif_moveWalkers(args: NotifMoveWalkers) {
        return this.dogWalkPark.moveWalkers(args.walkers);
    }

    private notif_moveWalker(args: NotifMoveWalker) {
        return this.dogWalkPark.moveWalkers([args.walker]);
    }

    private notif_playerPaysReputationForLocation(args: NotifPlayerPaysReputationForLocation) {
        this.setScore(args.playerId, args.score);
        return Promise.resolve();
    }

    private notif_playerLeavesThePark(args: NotifPlayerLeavesThePark) {
        this.setScore(args.playerId, args.score);
        if (args.walker) {
            return this.dogWalkPark.moveWalkers([args.walker]);
        }
        return Promise.resolve();
    }

    private notif_playerGainsReputation(args: NotifPlayerGainsReputation) {
        this.setScore(args.playerId, args.score);
        return Promise.resolve();
    }

    private notif_playerLosesReputation(args: NotifPlayerLosesReputation) {
        this.setScore(args.playerId, args.score);
        return Promise.resolve();
    }

    private notif_moveDogsToKennel(args: NotifMoveDogsToKennel) {
        return this.playerArea.moveDogsToKennel(args.playerId, args.dogs);
    }

    private notif_moveWalkerBackToPlayer(args: NotifMoveWalkerBackToPlayer) {
        return this.playerArea.moveWalkerToPlayer(args.playerId, args.walker);
    }

    private notif_flipForecastCard(args: NotifFlipForecastCard) {
        this.forecastManager.flipCard(args.foreCastCard);
        return Promise.resolve();
    }

    private notif_newLocationBonusCardDrawn(args: NotifNewLocationBonusCardDrawn) {
        return this.dogWalkPark.addLocationBonusCard(args.locationBonusCard)
            .then(() => this.dogWalkPark.addExtraLocationBonuses(args.locationBonuses))

    }

    private notif_newFirstWalker(args: NotifNewFirstWalker) {
        return this.playerArea.setNewFirstWalker(args.playerId);
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

    public tokenIcon(type: string) {
        return `<div class="dp-token-token small" data-type="${type}"></div>`
    }

    public tokenIcons(type: string, nrOfIcons: number) {
        let tokens = [];
        for (let i = 0; i < nrOfIcons; i++) {
            tokens.push(this.tokenIcon(type));
        }
        return tokens.join(' ');
    }
}