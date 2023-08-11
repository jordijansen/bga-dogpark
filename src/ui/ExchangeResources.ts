class ExchangeResources {

    private remainingResources: {stick: number, ball: number, treat: number, toy: number} = {stick: 0, ball: 0, treat: 0, toy: 0};
    private selectedPayment: string = 'placeholder';

    constructor(private elementId: string,
                private resources: {stick: number, ball: number, treat: number, toy: number},
                private targetResource: string,
                private onCancel: () => void,
                private onConfirm: (resource: string) => void) {
        dojo.place('<div id="dp-exchange-resources-wrapper"></div>', $(this.elementId))

        this.resetSelection();
        this.updateUi();
    }

    private resetSelection() {
        this.remainingResources = {...this.resources};
        this.selectedPayment = 'placeholder';
    }

    private updateUi() {
        const wrapperElement = $('dp-exchange-resources-wrapper');
        dojo.empty(wrapperElement);
        if (this.selectedPayment == 'placeholder') {
            dojo.place(this.createResourceButtons(), wrapperElement)
            Object.entries(this.remainingResources)
                .forEach(([resource, nr]) => dojo.connect($(`dp-dog-cost-pay-${resource}-button`), 'onclick', () => this.useResource(resource)))
        }
        dojo.place(this.createCostRow(), wrapperElement)
        dojo.place(this.createMainButtons(), wrapperElement)

        if (this.selectedPayment != 'placeholder') {
            dojo.connect($(`dp-dog-cost-pay-reset-button`), 'onclick', () => {
                this.resetSelection();
                this.updateUi();
            });
        }

        dojo.connect($(`dp-dog-cost-pay-cancel-button`), 'onclick', () => { this.onCancel(); });
        dojo.connect($(`dp-dog-cost-pay-confirm-button`), 'onclick', () => { this.onConfirm(this.selectedPayment); });

    }

    private createCostRow() {
        let result = `<div class="dp-dog-cost-pay-row">${_('Discard')}<i class="fa fa-long-arrow-right" aria-hidden="true"></i>${_('Gain')}</div>`;
        result += `<div class="dp-dog-cost-pay-row"><span class="dp-token-token" data-type="${this.selectedPayment}"></span><i class="fa fa-long-arrow-right" aria-hidden="true"></i>`
        result += `<span class="dp-token-token" data-type="${this.targetResource}"></span>`;
        result += '</div>';
        return result;
    }

    private createResourceButtons() {
        let result = `<div class="dp-dog-cost-pay-row">`;
        Object.entries(this.remainingResources)
            .forEach(([resource, nr]) => {
                const disabled = this.remainingResources[resource] <= 0;
                result += `<a id="dp-dog-cost-pay-${resource}-button" class="bgabutton bgabutton_blue ${disabled ? 'disabled' : ''}"><span class="dp-token-token" data-type="${resource}"></span></a>`
            })

        result += '</div>';
        return result;
    }

    private createMainButtons() {
        let result = `<div class="dp-dog-cost-pay-row">`;
        const disabled = this.selectedPayment == 'placeholder';
        result += `<a id="dp-dog-cost-pay-confirm-button" class="bgabutton bgabutton_blue ${disabled ? 'disabled' : ''}">${_('Confirm')}</a>`
        if (this.selectedPayment != 'placeholder') {
            result += `<a id="dp-dog-cost-pay-reset-button" class="bgabutton bgabutton_gray">${_('Reset')}</a>`
        }
        result += `<a id="dp-dog-cost-pay-cancel-button" class="bgabutton bgabutton_gray">${_('Cancel')}</a>`

        result += '</div>';
        return result;
    }


    private useResource(resource: string) {
        this.selectedPayment = resource;
        this.updateUi();
    }
}