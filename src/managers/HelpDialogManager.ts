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
        html += `<p>${dojo.string.substitute(_('<b>Breed(s)</b>: ${breeds}'), { breeds: card.breeds.map(breed => _(breed).toUpperCase()).join(', ') })}</p>`
        html += `<p>${dojo.string.substitute(_('<b>Walking cost</b>: ${cost}'), { cost: this.dogParkGame.formatWithIcons(Object.entries(card.costs).map(([resource, quantity]) => {
                let result = [];
                for (let i = 0; i < quantity; i++) {
                    result.push(`<icon-${resource}>`)
                }
                return result.join(' ');
            }).join('')) })}</p>`
        html += `<p><b>${_(card.abilityTitle)}</b></p>`
        html += `<p>${this.dogParkGame.formatWithIcons(_(card.abilityText))}</p>`
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

    private showDialog(event, title: string, html: string) {
        dojo.stopEvent(event);
        this.dialog = new ebg.popindialog();
        this.dialog.create(this.dialogId);
        this.dialog.setTitle(`<i class="fa fa-question-circle" aria-hidden="true"></i> ${_(title)}`);
        this.dialog.setContent( html );
        this.dialog.show();
    }


}