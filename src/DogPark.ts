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
    private currentPlayerChooseObjectives: ChooseObjectives;
    private finalScoringPad: FinalScoringPad

    // Managers
    public dogCardManager: DogCardManager;
    public dogWalkerManager: DogWalkerManager;
    public tokenManager: TokenManager;
    public breedExpertAwardManager: BreedExpertAwardManager;
    public forecastManager: ForecastManager;
    public objectiveCardManager: ObjectiveCardManager;
    public locationBonusCardManager: LocationBonusCardManager;
    public helpDialogManager: HelpDialogManager;

    // Modules
    private dogField: DogField;
    public dogWalkPark: DogWalkPark;
    public playerArea: PlayerArea;
    private roundTracker: RoundTracker;
    private playerResources: PlayerResources;
    private jumpToManager: JumpToManager;
    private helpManager: HelpManager;

    private alwaysFixTopActions: boolean;
    private alwaysFixTopActionsMaximum: number;

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

        this.finalScoringPad = new FinalScoringPad(this, 'dp-final-scoring-pad-wrapper')

        // @ts-ignore
        this.default_viewport = 'width=1000';
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
        this.setAlwaysFixTopActions();

        log( "Starting game setup" );
        log('gamedatas', gamedatas);

        // Setup modules
        this.helpDialogManager = new HelpDialogManager(this);

        this.dogField.setUp(gamedatas);
        this.dogWalkPark.setUp(gamedatas);
        this.playerArea.setUp(gamedatas);
        this.roundTracker.setUp(gamedatas);
        this.playerResources.setUp(gamedatas);
        this.forecastManager.setUp(gamedatas);
        this.finalScoringPad.setUp(gamedatas);
        this.dogCardManager.setUp(gamedatas);

        this.zoomManager = new AutoZoomManager('dp-game', 'dp-zoom-level')
        this.animationManager = new AnimationManager(this, {duration: ANIMATION_MS})

        this.helpManager = new HelpManager(this, {
            buttons: [
                new BgaHelpPopinButton({
                    title: _("Player Aid"),
                    html: this.helpDialogManager.getPlayerAidHtml(),
                })
            ],
        });

        this.breedExpertAwardManager.setUp(gamedatas);
        this.tokenManager.setUp();

        dojo.place('<div id="custom-actions"></div>', $('maintitlebar_content'), 'last')

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
                break;
            case 'actionSwap':
                this.enteringActionSwap(args.args as ActionSwapArgs);
                break;
            case 'actionScout':
                this.enteringActionScout(args.args as ActionScoutArgs);
                break;
            case 'actionMoveAutoWalker':
                this.enteringActionMoveAutoWalker(args.args as ActionMoveAutoWalkerArgs)
                break;
            case 'actionCrafty':
                this.enteringActionCrafty(args.args as ActionCraftyArgs)
                break;
            case 'actionGainResourcesPrivate':
                this.enteringGainResources(args.args);
                break;
            case 'actionGainResources':
                this.enteringGainResources(args.args[this.getPlayerId()]);
                break;
            case 'actionGlobetrotter':
                this.enteringGlobetrotter(args.args as ActionGlobetrotterArgs);
                break;

        }
    }

    private enteringChooseObjectives() {
        if ((this as any).isCurrentPlayerActive()) {
            this.currentPlayerChooseObjectives = new ChooseObjectives(this, "dp-choose-objectives");
            this.currentPlayerChooseObjectives.enter();
        }
    }

    private enteringRecruitmentOffer(args: RecruitmentOfferArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            if (args.maxOfferValue > 0) {
                this.dogField.setDogSelectionModeField('single');
                this.currentPlayerOfferDial = new DogOfferDial({
                    elementId: 'dp-current-player-offer-dial',
                    parentId: 'custom-actions',
                    player: this.getPlayer(this.getPlayerId()),
                    initialValue: 1,
                    maxOfferValue: args.maxOfferValue,
                    readOnly: false,
                    onConfirm: () => {
                        this.placeOfferOnDog();
                    }
                });
            } else {
                this.gamedatas.gamestate.descriptionmyturn = _(this.gamedatas.gamestate.descriptionmyturn) + '<br />' + _('Insufficient reputation to place an offer') + '<br />';
                (this as any).updatePageTitle();
            }
        }
    }

    private enteringRecruitmentTakeDog() {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogField.setDogSelectionModeField('single');
        }
    }

    private enteringSelectionPlaceDogOnLead(args: SelectionPlaceDogOnLeadArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            if (args.numberOfDogsOnlead < args.maxNumberOfDogs) {
                this.playerArea.setSelectionModeForKennel('single', this.getPlayerId(), Object.values(args.dogs), (selection) => {
                    if (selection.length === 1) {
                        this.playerArea.setSelectionModeForKennel('none', this.getPlayerId());
                        (this as any).takeNoLockAction("placeDogOnLead", {dogId: selection[0].id});
                    }
                });
            }
        }
    }

    private enteringSelectionPlaceDogOnLeadSelectResources(args: SelectionPlaceDogOnLeadSelectResourcesArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            new DogPayCosts("custom-actions", args.resources, args.dog, args.freeDogsOnLead > 0, args.nextDogCosts1Resource, () => {
                dojo.empty('custom-actions');
                this.takeNoLockAction('placeDogOnLeadCancel')
            }, (resources, isFreePlacement) => {
                dojo.empty('custom-actions');
                this.takeNoLockAction('placeDogOnLeadPayResources', {dogId: args.dog.id, isFreePlacement, resources: JSON.stringify(resources)})
            });
        }
    }

    private enteringWalkingMoveWalker(args: WalkingMoveWalkerArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogWalkPark.enterWalkerSpotsSelection(args.possibleParkLocationIds, (locationId)=> {this.takeAction('moveWalker', {locationId})});
        }
    }

    private enteringActionSwap(args: ActionSwapArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            this.playerArea.setSelectionModeForKennel("single", this.getPlayerId(), args.dogsInKennel);
            this.dogField.setDogSelectionModeField('single');
            this.roundTracker.setSwapFocus();
        }
    }

    private enteringActionScout(args: ActionScoutArgs) {
        this.dogField.addDogCardsToScoutedField(args.scoutedDogCards)
        if ((this as any).isCurrentPlayerActive() && args.scoutedDogCards && args.scoutedDogCards.length > 0) {
            this.dogField.setDogSelectionModeField('single');
            this.dogField.setDogSelectionModeScout('single');
            this.roundTracker.setScoutFocus();
        }
    }

    private enteringActionMoveAutoWalker(args: ActionMoveAutoWalkerArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogWalkPark.enterWalkerSpotsSelection(args.possibleParkLocationIds, (locationId)=> {this.takeAction('moveAutoWalker', {locationId})});
        }
    }

    private enteringActionCrafty(args: ActionCraftyArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            new ExchangeResources("custom-actions", args.resources, args.dog.craftyResource, () => {
                dojo.empty('custom-actions');
                this.takeNoLockAction('cancelCrafty')
            }, (resource) => {
                dojo.empty('custom-actions');
                this.takeNoLockAction('confirmCrafty', {resource})
            });
        }
    }

    private enteringGainResources(args: {nrOfResourcesToGain: number, resourceOptions: string[], canCancel: boolean}) {
        if ((this as any).isCurrentPlayerActive()) {
            new GainResources('custom-actions', args.nrOfResourcesToGain, args.resourceOptions, args.canCancel, () => {
                dojo.empty('custom-actions');
                this.takeNoLockAction('cancelGainResources')
            }, (resources) => {
                dojo.empty('custom-actions');
                this.takeNoLockAction('confirmGainResources', {resources: JSON.stringify(resources)})
            });
        }
    }

    private enteringGlobetrotter(args: WalkingMoveWalkerArgs) {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogWalkPark.enterWalkerSpotsSelection(args.possibleParkLocationIds, (locationId)=> {this.takeAction('confirmGlobetrotter', {locationId})});
        }
    }

    public onLeavingState(stateName: string) {
        log( 'Leaving state: '+stateName );

        switch (stateName) {
            case 'recruitmentOffer':
            case 'recruitmentTakeDog':
                this.dogField.setDogSelectionModeField('none');
                dojo.empty('custom-actions');
                break;
            case 'selectionPlaceDogOnLead':
                this.leavingSelectionPlaceDogOnLead();
                break;
            case 'walkingMoveWalker':
                this.leavingWalkingMoveWalker();
                break;
            case 'actionSwap':
                this.leavingActionSwap();
                break;
            case 'actionScout':
                this.leavingActionScout();
                break;
            case 'actionMoveAutoWalker':
                this.leavingActionMoveAutoWalker();
                break;
            case 'actionGlobetrotter':
                this.leavingActionGlobetrotter();
                break;
        }
    }

    private leavingSelectionPlaceDogOnLead() {
        this.playerArea.setSelectionModeForKennel('none', this.getPlayerId());
    }

    private leavingWalkingMoveWalker() {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogWalkPark.exitWalkerSpotsSelection()
        }
    }

    private leavingActionGlobetrotter() {
        if ((this as any).isCurrentPlayerActive()) {
            this.dogWalkPark.exitWalkerSpotsSelection()
        }
    }

    private leavingActionSwap() {
        if ((this as any).isCurrentPlayerActive()) {
            this.playerArea.setSelectionModeForKennel('none', this.getPlayerId());
            this.dogField.setDogSelectionModeField('none');
            this.roundTracker.unsetFocus();
        }
    }

    private leavingActionScout() {
        this.dogCardManager.discardPile.addCards(this.dogField.scoutedDogStock.getCards())
        if ((this as any).isCurrentPlayerActive()) {
            this.dogField.setDogSelectionModeScout('none');
            this.dogField.setDogSelectionModeField('none');
            this.roundTracker.unsetFocus();
        }
    }

    private leavingActionMoveAutoWalker() {
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
                    if (args.maxOfferValue == 0) {
                        (this as any).addActionButton('skipPlaceOfferOnDog', _("Skip"), () => this.skipPlaceOfferOnDog());
                    }
                    break;
                case 'recruitmentTakeDog':
                    (this as any).addActionButton('takeDog', _("Confirm"), () => this.recruitDog());
                    break;
                case 'selectionPlaceDogOnLead':
                    const selectionPlaceDogOnLeadArgs = args as SelectionPlaceDogOnLeadArgs;
                    this.addAdditionalActionButtons(args.additionalActions);
                    (this as any).addActionButton('confirmSelection', _("Confirm Selection"), () => this.confirmSelection(selectionPlaceDogOnLeadArgs));
                    if ((selectionPlaceDogOnLeadArgs.numberOfDogsOnlead < 1 && Object.keys(selectionPlaceDogOnLeadArgs.dogs).length >= 1) || selectionPlaceDogOnLeadArgs.additionalActions.some(action => !action.optional)) {
                        dojo.addClass('confirmSelection', 'disabled');
                    }
                    break;
                case 'walkingMoveWalkerAfter':
                    let walkingMoveWalkerAfterArgs = args as WalkingMoveWalkerAfterArgs;
                    this.addAdditionalActionButtons(args.additionalActions)
                    if (walkingMoveWalkerAfterArgs.additionalActions.every(action => action.optional)) {
                        (this as any).addActionButton('confirmWalking', _("Confirm Walking"), () => this.confirmWalking());
                    }
                    break;
                case 'actionSwap':
                    (this as any).addActionButton('confirmSwap', _("Confirm"), () => this.confirmSwap());
                    (this as any).addActionButton('skipSwap', _("Skip"), () => this.skipSwap(), null, null, 'red');
                    break;
                case 'actionScout':
                    if ((args as ActionScoutArgs).scoutedDogCards.length > 0) {
                        (this as any).addActionButton('confirmScout', _("Replace Dog"), () => this.confirmScout());
                    }
                    (this as any).addActionButton('endScout', _("End Scout Action"), () => this.endScout(), null, null, 'red');
                    break;
            }

            if (args?.canCancelMoves) {
                (this as any).addActionButton('undoLast', _("Undo last"), () => this.undoLast(), null, null, 'red');
                (this as any).addActionButton('undoAll', _("Undo all"), () => this.undoAll(), null, null, 'red');
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

    private addAdditionalActionButtons(additionalActions: AdditionalAction[]) {
        if (additionalActions && additionalActions.length > 0) {
            additionalActions.forEach(additionalAction => {
                const buttonColor = additionalAction.optional ? 'gray' : 'blue';
                switch (additionalAction.type) {
                    case 'WALKING_PAY_REPUTATION_ACCEPT':
                        (this as any).addActionButton(`payReputationAccept`, dojo.string.substitute(_('Pay ${resourceType} to unlock location bonus(es)'), { resourceType: this.tokenIcon('reputation') }), () => this.additionalAction(additionalAction), null, null, buttonColor);
                        break;
                    case 'WALKING_PAY_REPUTATION_DENY':
                        (this as any).addActionButton(`payReputationDeny`, _('Skip location bonuses'), () => this.additionalAction(additionalAction), null, null, buttonColor);
                        break;
                    case 'WALKING_GAIN_LOCATION_BONUS':
                        (this as any).addActionButton(`gainLocationBonus${additionalAction.id}`, dojo.string.substitute(_('Gain location bonus ${resourceType}'), { resourceType: this.tokenIcons(additionalAction.additionalArgs['bonusType'], additionalAction.additionalArgs['amount']) }), () => this.additionalAction(additionalAction), null, null, buttonColor);
                         break;
                    case 'WALKING_GAIN_LEAVING_THE_PARK_BONUS':
                        (this as any).addActionButton(`gainLeavingPark${additionalAction.id}`, dojo.string.substitute(_('Gain leaving the park bonus ${resourceType}'), { resourceType: this.tokenIcons(additionalAction.additionalArgs['bonusType'], additionalAction.additionalArgs['amount'], additionalAction.additionalArgs['swapIncludesWalkedToken'] ? ['walked'] : []) }), () => this.additionalAction(additionalAction), null, null, buttonColor);
                        break;
                    case 'USE_DOG_ABILITY':
                        (this as any).addActionButton(`useDogAbility${additionalAction.id}`, dojo.string.substitute('<b>${dogName}</b>: <i>${abilityTitle}</i>', { dogName: _(additionalAction.additionalArgs['dogName']), abilityTitle: _(additionalAction.additionalArgs['abilityTitle']) }), () => this.additionalAction(additionalAction), null, null, buttonColor);
                        break;
                    case 'USE_FORECAST_ABILITY':
                        (this as any).addActionButton(`useForecastAbility${additionalAction.id}`, _('Use Forecast Card'), () => this.additionalAction(additionalAction), null, null, buttonColor);
                        break;
                }
            });
        }
    }

    private additionalAction(additionalAction: AdditionalAction) {
        if (additionalAction.canBeUndone) {
            this.takeNoLockAction('additionalAction', {actionId: additionalAction.id})
        } else {
            this.wrapInConfirm(() => this.takeNoLockAction('additionalAction', {actionId: additionalAction.id}))
        }
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
        const selectedDog = this.dogField.getSelectedFieldDog();
        this.takeAction('recruitDog', {dogId: selectedDog?.id})
    }

    private skipPlaceOfferOnDog() {
        this.takeAction('skipPlaceOfferOnDog');
    }

    private placeOfferOnDog() {
        const selectedDog = this.dogField.getSelectedFieldDog();
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

    private confirmSwap() {
        const fieldDog = this.dogField.getSelectedFieldDog();
        const kennelDog = this.playerArea.getSelectedKennelDog(this.getPlayerId());
        if (!fieldDog) {
            (this as any).showMessage(_("You must select 1 dog in the field"), 'error')
        } else if (!kennelDog) {
            (this as any).showMessage(_("You must select 1 dog in your kennel"), 'error')
        } else {
            this.takeAction("confirmSwap", {fieldDogId: fieldDog.id, kennelDogId: kennelDog.id})
        }
    }

    private skipSwap() {
        this.takeAction('cancelSwap');
    }

    private confirmScout() {
        const fieldDog = this.dogField.getSelectedFieldDog();
        const scoutDog = this.dogField.getSelectedScoutDog();
        if (!fieldDog) {
            (this as any).showMessage(_("You must select 1 dog in the field"), 'error')
        } else if (!scoutDog) {
            (this as any).showMessage(_("You must select 1 dog from the scout area"), 'error')
        } else {
            this.takeAction("confirmScout", {fieldDogId: fieldDog.id, scoutDogId: scoutDog.id})
        }
    }

    private endScout() {
        this.takeAction("endScout")
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

    public delay = async (ms: number) => {
        if (this.animationManager.animationsActive()) {
            await new Promise(resolve => setTimeout(resolve, ms))
        } else {
            await Promise.resolve();
        }
    }

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

    private getOrderedPlayers(players: Player[]) {
        const result = players.sort((a, b) => Number(a.playerNo) - Number(b.playerNo));
        const playerIndex = result.findIndex(player => Number(player.id) === Number(this.getPlayerId()));
        return playerIndex > 0 ? [...players.slice(playerIndex), ...players.slice(0, playerIndex)] : players;
    }

    public setAlwaysFixTopActions(alwaysFixed = true, maximum = 30) {
        this.alwaysFixTopActions = alwaysFixed;
        this.alwaysFixTopActionsMaximum = maximum;
        this.adaptStatusBar();
    }

    public adaptStatusBar() {
        (this as any).inherited(arguments);

        if (this.alwaysFixTopActions) {
            const afterTitleElem = document.getElementById('after-page-title');
            const titleElem = document.getElementById('page-title');
            //@ts-ignore
            let zoom = getComputedStyle(titleElem).zoom;
            if (!zoom) {
                zoom = 1;
            }

            const titleRect = afterTitleElem.getBoundingClientRect();
            if (titleRect.top < 0 && (titleElem.offsetHeight < (window.innerHeight * this.alwaysFixTopActionsMaximum / 100))) {
                const afterTitleRect = afterTitleElem.getBoundingClientRect();
                titleElem.classList.add('fixed-page-title');
                titleElem.style.width = ((afterTitleRect.width - 10) / zoom) + 'px';
                afterTitleElem.style.height = titleRect.height + 'px';
            } else {
                titleElem.classList.remove('fixed-page-title');
                titleElem.style.width = 'auto';
                afterTitleElem.style.height = '0px';
            }
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
            ['newFirstWalker', undefined],
            ['playerSwaps', undefined],
            ['playerScoutReplaces', undefined],
            ['undoPlayerScoutReplaces', undefined],
            ['activateDogAbility', undefined],
            ['activateForecastCard', undefined],
            ['playerAssignsResources', undefined],
            ['revealObjectiveCards', undefined],
            ['finalScoringRevealed', undefined],
            ['autoWalkerDieRolled', 1]
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
        return this.playerArea.moveDogsToKennel(args.playerId, [args.dog]).then(() => this.breedExpertAwardManager.updateBreedExpertAwardStandings())
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
        const promise = this.playerArea.moveDogsToLead(args.playerId, [args.dog])
            .then(() => this.playerResources.payResourcesToDog(args.playerId, args.dog, args.resources))
            .then(() => this.dogCardManager.addResourceToDog(args.dog.id, 'walked'));
        if (args.playerId == this.getPlayerId()) {
            return promise;
        }
        return Promise.resolve();
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
        if (args.resources.includes('reputation')) {
            this.setScore(args.playerId, args.score);
        } else {
            return Promise.all(args.resources.filter(resource => ['ball', 'stick', 'treat', 'toy'].includes(resource))
                .map(resource => this.playerResources.gainResourceFromLocation(args.playerId, args.locationId, resource)));
        }
        return Promise.resolve();
    }

    private async notif_undoPlayerGainsLocationBonusResource(args: NotifPlayerGainsLocationBonusResource) {
        if (args.resources.includes('reputation')) {
            this.setScore(args.playerId, args.score);
        } else {
            await this.playerResources.payResources(args.playerId, args.resources.filter(resource => ['ball', 'stick', 'treat', 'toy'].includes(resource)));
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

    private notif_playerSwaps(args: NotifPlayerSwaps) {
        this.dogCardManager.removeAllResourcesFromDog(args.kennelDog.id);
        this.dogCardManager.addInitialResourcesToDog(args.fieldDog);
        this.dogField.addDogCardsToField([args.kennelDog]);
        return this.playerArea.moveDogsToKennel(args.playerId,[args.fieldDog]).then(() => this.breedExpertAwardManager.updateBreedExpertAwardStandings());
    }

    private notif_playerScoutReplaces(args: NotifPlayerScoutReplaces) {
        this.dogField.discardDogFromField(args.fieldDog)
        return Promise.all(this.dogField.addDogCardsToField([args.scoutDog]));
    }

    private notif_undoPlayerScoutReplaces(args: NotifPlayerScoutReplaces) {
        this.dogField.addDogCardsToField([args.scoutDog])
        return this.dogField.addDogCardsToScoutedField([args.fieldDog]);
    }

    private notif_activateDogAbility(args: NotifActivateDogAbility) {
        const promises = [];
        if (args.gainedResources) {
            promises.push(this.playerResources.gainResourcesFromDog(args.playerId, args.dog, args.gainedResources));
        }
        if (args.lostResources) {
            promises.push(this.playerResources.payResourcesToDog(args.playerId, args.dog, args.lostResources));
        }
        if (args.score) {
            this.setScore(args.playerId, args.score);
        }
        if (args.playerId == this.getPlayerId()) {
            return Promise.all(promises);
        }
        return Promise.resolve();

    }

    private notif_activateForecastCard(args: NotifActivateForecastCard) {
        const promises = [];
        if (args.gainedResources) {
            promises.push(this.playerResources.gainResourcesFromForecastCard(args.playerId, args.forecastCard, args.gainedResources.filter(resource => resource !== 'reputation')));
        }
        if (args.lostResources) {
            promises.push(this.playerResources.payResources(args.playerId, args.lostResources.filter(resource => resource !== 'reputation')));
        }
        if (args.score || args.score === 0) {
            this.setScore(args.playerId, args.score);
        }

        if (args.playerId == this.getPlayerId()) {
            return Promise.all(promises);
        }
        return Promise.resolve();
    }

    private async notif_playerAssignsResources(args: NotifPlayerAssignsResources) {
        for (const resource of args.resourcesAdded) {
            await this.playerResources.payResourcesToDog(args.playerId, args.dog, [resource])
            await this.dogCardManager.addResourceToDog(args.dog.id, resource as any)
        }
    }

    private notif_revealObjectiveCards(args: NotifRevealObjectiveCards) {
        return Promise.all(args.objectiveCards.map(objectiveCard => this.objectiveCardManager.updateCardInformations(objectiveCard)));
    }

    private notif_finalScoringRevealed(args: NotifFinalScoringRevealed) {
        return this.finalScoringPad.showPad(args.scoreBreakDown, true).then(() => {
            Object.keys(args.scoreBreakDown).forEach(playerId => this.setScore(Number(playerId), args.scoreBreakDown[playerId]['score']))
        });
    }

    private notif_autoWalkerDieRolled(args: NotifAutoWalkerDieRolled) {
        const dieElement = $(`dp-autowalker-die-${args.walkerId}`);
        if (dieElement) {
            dieElement.dataset['value'] = 7 - args.side;
            this.delay(500).then(() => dieElement.dataset['value'] = args.side)
        }
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
        return `<span class="dp-token-token dp-token-${type} small" data-type="${type}"></span>`
    }

    public tokenIcons(type: string, nrOfIcons: number, additionalIcons?: string[]) {
        let tokens = [];
        for (let i = 0; i < nrOfIcons; i++) {
            tokens.push(this.tokenIcon(type));
        }
        if (additionalIcons && additionalIcons.length > 0) {
            additionalIcons.forEach(additionalIcon => tokens.push(this.tokenIcon(additionalIcon)))
        }
        return tokens.join(' ');
    }

    public walkerIcon(color: string) {
        return `<span class="dp-dog-walker" data-color="#${color}"></span>`
    }

    public breedIcon(breed: string) {
        return `<span>${_(breed.charAt(0).toUpperCase() + breed.slice(1))}</span>`
    }

    public updatePlayerOrdering() {
        (this as any).inherited(arguments);
        this.gamedatas.autoWalkers.forEach(autoWalker => {
            this.playerArea.initAutoWalkers(autoWalker);
        });

        if (Object.keys(this.gamedatas.players).length === 1) {
            dojo.place(`<div id="dp-solo-ratings-panel" class="player-board" style="height: auto;"></div>`, `player_boards`, 'first');
            new SoloRatings('dp-solo-ratings-panel')
        }
    }

    public formatWithIcons(description) {
        const tags = description.match(/<[^>]*?>/g);
        console.log(description);
        tags.forEach(originalTag => {
            if (originalTag.includes('icon-')) {
                let tag = '';
                tag = originalTag.replace('<', '')
                tag = tag.replace('>', '');
                const resultTag = this.tokenIcon(tag.replace('icon-', ''));
                description = description.replace(originalTag, resultTag)
            } else if (originalTag.includes('breed-')) {
                let tag = '';
                tag = originalTag.replace('<', '')
                tag = tag.replace('>', '');
                const resultTag = this.breedIcon(tag.replace('breed-', ''));
                description = description.replace(originalTag, resultTag)
            }
        })
        return description;
    }
}