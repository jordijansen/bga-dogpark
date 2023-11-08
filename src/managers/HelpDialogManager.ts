class HelpDialogManager {

    private readonly dialogId: string = 'dpHelpDialogId';
    private dialog;

    constructor(private dogParkGame: DogParkGame) {
    }

    showDogHelpDialog(event, card: DogCard) {
        let html = `<div class="dp-help-dialog-content"><div class="dp-help-dialog-content-left">`;
        html += `<div class="dog-card-art dog-card-art-${card.typeArg}"></div>`
        html += `</div>`
        html += `<div class="dp-help-dialog-content-right">`
        html += `<p>${dojo.string.substitute(_('<b>Breed(s)</b>: ${breeds}'), { breeds: card.breeds.map(breed => _(breed.charAt(0).toUpperCase() + breed.slice(1))).join(', ') })}</p>`
        html += `<p>${dojo.string.substitute(_('<b>Walking cost</b>: ${cost}'), { cost: this.dogParkGame.formatWithIcons(Object.entries(card.costs).map(([resource, quantity]) => {
                let result = [];
                for (let i = 0; i < quantity; i++) {
                    result.push(`<icon-${resource}>`)
                }
                return result.join(' ');
            }).join('')) })}</p>`
        html += `<p><b>${card.abilityTitle}</b></p>`
        html += `<p>${this.dogParkGame.formatWithIcons(card.abilityText)}</p>`
        html += `</div>`
        html += `</div>`
        this.showDialog(event, card.name, html)
    }

    showObjectiveHelpDialog(event, card: ObjectiveCard) {
        let html = `<div class="dp-help-dialog-content"><div class="dp-help-dialog-content-left">`;
        html += `<div class="objective-art objective-art-${card.typeArg}"></div>`
        html += `</div>`
        html += `<div class="dp-help-dialog-content-right">`
        html += `<p>${dojo.string.substitute(_('<b>Type</b>: ${type}'), { type: _(card.type).toUpperCase() })}</p>`
        html += `<p>${this.dogParkGame.formatWithIcons(_(card.description))}</p>`
        html += `</div>`
        html += `</div>`
        this.showDialog(event, card.name, html)
    }

    showForecastHelpDialog(event, card: ForecastCard) {
        let html = `<div class="dp-help-dialog-content"><div class="dp-help-dialog-content-left">`;
        html += `<div class="forecast-art forecast-art-${card.typeArg}"></div>`
        html += `</div>`
        html += `<div class="dp-help-dialog-content-right">`
        html += `<p>${this.dogParkGame.formatWithIcons(_(card.description))}</p>`
        html += `</div>`
        html += `</div>`
        this.showDialog(event, dojo.string.substitute(_('Round ${roundNumber} Forecast Card'), { roundNumber: card.locationArg }), html)
    }

    getPlayerAidHtml() {
        let html = '';
        html += `<h2>${_('Icons')}</h2>`
        html += `<h3>${_('Resources')}</h3>`
        html += `<div class="dp-player-aid-resource-wrapper">`
        html += `<div class="dp-player-aid-resource">${this.dogParkGame.tokenIcon('stick')} - ${_('stick')}</div>`
        html += `<div class="dp-player-aid-resource">${this.dogParkGame.tokenIcon('ball')} - ${_('ball')}</div>`
        html += `<div class="dp-player-aid-resource">${this.dogParkGame.tokenIcon('treat')} - ${_('treat')}</div>`
        html += `<div class="dp-player-aid-resource">${this.dogParkGame.tokenIcon('toy')} - ${_('toy')}</div>`
        html += `<div class="dp-player-aid-resource">${this.dogParkGame.tokenIcon('all-resources')} - ${_('any resource')}</div>`
        html += `<div class="dp-player-aid-resource">${this.dogParkGame.tokenIcon('walked')} - ${_('walked')}</div>`
        html += `<div class="dp-player-aid-resource">${this.dogParkGame.tokenIcon('reputation')} - ${_('reputation')}</div>`
        html += `</div>`
        html += `<h3>${_('Actions')}</h3>`
        html += `<p>${this.dogParkGame.tokenIcon('swap')} - ${_('Swap: You may exchange 1 Dog from your Kennel with a Dog in the Field.')}</p>`
        html += `<p>${this.dogParkGame.tokenIcon('scout')} - ${_('Scout: You may reveal the top two cards of the deck. You may replace a Dog in the Field with 1 of the Dogs drawn. Unselected cards are removed from the game.')}</p>`
        html += `<h2>${_('Game Round')}</h2>`
        html += `<p>${_('Dog Park is played over 4 rounds, and each round is split into 4 phases.')}</p>`
        html += `<h3>${_('Phase 1: Recruitment')}</h3>`
        html += `<p>${this.dogParkGame.formatWithIcons(_('Over 2 rounds of Offers, use your <icon-reputation> to Offer on Dogs in the Field and add them to your Kennel.'))}</p>`
        html += `<h3>${_('Phase 2: Selection')}</h3>`
        html += `<p>${this.dogParkGame.formatWithIcons(_('Prepare to walk up to 3 Dogs by paying their walking costs, placing them on your Lead. Dogs placed on your lead gain a <icon-walked> token.'))}</p>`
        html += `<p>${this.dogParkGame.formatWithIcons(_('<strong>Remember!</strong> If needed you may use <icon-all-resources> + <icon-all-resources> =  <icon-all-resources>'))}</p>`
        html += `<h3>${_('Phase 3: Walking')}</h3>`
        html += `<p>${_('Take turns to move your Walker through the Park. You immediately claim the Leaving Bonus when you leave the park.')}</p>`
        html += `<h3>${_('Phase 4: Home Time')}</h3>`
        html += `<p>${this.dogParkGame.formatWithIcons(_('You gain 2 <icon-reputation> for each Dog on your Lead.'))}</p>`
        html += `<p>${this.dogParkGame.formatWithIcons(_('You lose 1 <icon-reputation> for each Dog in your kennel without a <icon-walked>.'))}</p>`
        html += `<p>${_('Finally the Forecast card is flipped and new Location Bonuses are added to the park. The First Walker token is passed clockwise and a new round begins.')}</p>`
        html += `<h2>${_('Final Scoring')}</h2>`
        html += `<p>${_('Your final score is calculated by adding the following items together:')}</p>`
        html += `<p>+ ${this.dogParkGame.formatWithIcons(_('<icon-reputation> gained during the game'))}</p>`
        html += `<p>+ ${this.dogParkGame.formatWithIcons(_('<icon-reputation> from dogs with <strong>FINAL SCORING</strong> abilities'))}</p>`
        html += `<p>+ ${this.dogParkGame.formatWithIcons(_('<icon-reputation> from won <strong>Breed Expert</strong> awards'))}</p>`
        html += `<p>+ ${this.dogParkGame.formatWithIcons(_('<icon-reputation> from your completed <strong>Objective card</strong>'))}</p>`
        html += `<p>+ ${this.dogParkGame.formatWithIcons(_('Every <strong>5 remaining resources</strong> = <icon-reputation>'))}</p>`
        html += `<p>${this.dogParkGame.formatWithIcons(_('The player with the most <icon-reputation> wins. If there is a tie, the player who won the highest valued Breed Expert award wins. If players are still tied, they share the victory.'))}</p>`

        return html;
    }

    private showDialog(event, title: string, html: string) {
        dojo.stopEvent(event);
        this.dialog = new ebg.popindialog();
        this.dialog.create(this.dialogId);
        this.dialog.setTitle(`<i class="fa fa-question-circle" aria-hidden="true"></i> ${_(title)}`);
        this.dialog.setContent( html );
        this.dialog.show();
    }


}