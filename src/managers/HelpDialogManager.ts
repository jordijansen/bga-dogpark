class HelpDialogManager {

    private readonly dialogId: string = 'dpHelpDialogId';
    private dialog;

    constructor(private dogParkGame: DogParkGame) {
    }

    showDogHelpDialog(event, card: DogCard) {
        dojo.stopEvent(event);
        this.dialog = new ebg.popindialog();
        this.dialog.create(this.dialogId);
        this.dialog.setTitle(`<i class="fa fa-question-circle" aria-hidden="true"></i> ${_(card.name)}`);

        let html = `<div class="dp-help-dialog-content"><div class="dp-help-dialog-content-left">`;
        html += `<p><b>${_('Breed(s):')}</b> ${card.breeds.map(breed => _(breed)).join(', ')}</p>`
        html += `<p><b>${_('Walking cost:')}</b> ${this.dogParkGame.formatWithIcons(Object.entries(card.costs).map(([resource, quantity]) => {
            let result = [];
            for (let i = 0; i < quantity; i++) {
                result.push(`_icon-${resource}_`)
            }
            return result.join(' ');
        }).join(''))}</p>`
        html += `<p><b>${_(card.abilityTitle)}</b></p>`
        html += `<p>${this.dogParkGame.formatWithIcons(_(card.abilityText))}</p>`
        html += `</div>`
        html += `<div class="dp-help-dialog-content-right">`
        html += `<div class="dog-card-art dog-card-art-${card.typeArg}"></div>`
        html += `</div>`
        html += `</div>`


        this.dialog.setContent( html );
        this.dialog.show();
    }
}