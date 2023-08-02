class DogPayCosts {

    private remainingResources: {stick: number, ball: number, treat: number, toy: number} = {stick: 0, ball: 0, treat: 0, toy: 0};
    private selectedPayment= [];
    private initiallyMissingResources = false;

    constructor(private elementId: string,
                private resources: {stick: number, ball: number, treat: number, toy: number},
                private dog: DogCard,
                private onCancel: () => void,
                private onConfirm: (resources: string[]) => void) {
        dojo.place('<div id="dp-dog-cost-pay-wrapper"></div>', $(this.elementId))

        this.resetSelection();
        this.updateUi();
    }

    private resetSelection() {
        this.remainingResources = {...this.resources};
        this.selectedPayment = [];

        Object.entries(this.dog.costs).forEach(([resource, cost]) => {
            for (let i = 0; i < cost; i++) {
                if (this.remainingResources[resource] >= 1) {
                    this.remainingResources[resource] -= 1;
                    this.selectedPayment.push({resource: resource, payUsing: [resource]});
                } else {
                    this.initiallyMissingResources = true;
                    this.selectedPayment.push({resource: resource, payUsing: ["placeholder", "placeholder"]});
                }
            }
        })
    }

    private updateUi() {
        const wrapperElement = $('dp-dog-cost-pay-wrapper');
        dojo.empty(wrapperElement);
        if (this.initiallyMissingResources) {
            dojo.place(this.createResourceButtons(), wrapperElement)
        }
        dojo.place(this.createCostRows(), wrapperElement)
        dojo.place(this.createMainButtons(), wrapperElement)

        if (this.initiallyMissingResources) {
            dojo.connect($(`dp-dog-cost-pay-reset-button`), 'onclick', () => {
                this.resetSelection();
                this.updateUi();
            });

            Object.entries(this.remainingResources)
                .forEach(([resource, nr]) => dojo.connect($(`dp-dog-cost-pay-${resource}-button`), 'onclick', () => this.useResource(resource)))
        }

        dojo.connect($(`dp-dog-cost-pay-cancel-button`), 'onclick', () => { this.onCancel(); });
        dojo.connect($(`dp-dog-cost-pay-confirm-button`), 'onclick', () => { this.onConfirm(this.selectedPayment.map(costRow => costRow.payUsing).flat()); });

    }

    private createCostRows() {
        let result = `<div class="dp-dog-cost-pay-row">${_('Cost')}<i class="fa fa-long-arrow-right" aria-hidden="true"></i>${_('Pay using')}</div>`;
        this.selectedPayment.forEach((costRow) => {
            result += `<div class="dp-dog-cost-pay-row"><span class="dp-token-token" data-type="${costRow.resource}"></span><i class="fa fa-long-arrow-right" aria-hidden="true"></i>`
            costRow.payUsing.forEach(selectedResource => {
                result += `<span class="dp-token-token" data-type="${selectedResource}"></span>`;
            })
            result += '</div>';
        })
        return result;
    }

    private createResourceButtons() {
        let result = `<div class="dp-dog-cost-pay-row">`;
        Object.entries(this.remainingResources)
            .forEach(([resource, nr]) => {
                const disabled = this.remainingResources[resource] <= 0 || this.selectedPayment.map(costRow => costRow.payUsing).filter(payment => payment.includes('placeholder')).length == 0;
                result += `<a id="dp-dog-cost-pay-${resource}-button" class="bgabutton bgabutton_blue ${disabled ? 'disabled' : ''}"><span class="dp-token-token" data-type="${resource}"></span></a>`
            })

        result += '</div>';
        return result;
    }

    private createMainButtons() {
        let result = `<div class="dp-dog-cost-pay-row">`;
        const disabled = this.selectedPayment.map(costRow => costRow.payUsing).filter(payment => payment.includes('placeholder')).length > 0;
        result += `<a id="dp-dog-cost-pay-confirm-button" class="bgabutton bgabutton_blue ${disabled ? 'disabled' : ''}">${_('Confirm')}</a>`
        if (this.initiallyMissingResources) {
            result += `<a id="dp-dog-cost-pay-reset-button" class="bgabutton bgabutton_gray">${_('Reset')}</a>`
        }
        result += `<a id="dp-dog-cost-pay-cancel-button" class="bgabutton bgabutton_gray">${_('Cancel')}</a>`

        result += '</div>';
        return result;
    }


    private useResource(resource: string) {
        console.log('useResource');
        for (const costRow of this.selectedPayment) {
            const indexOf = costRow.payUsing.indexOf('placeholder');
            if (indexOf >= 0) {
                this.remainingResources[resource] -= 1;
                costRow.payUsing[indexOf] = resource;
                this.updateUi();
                break;
            }
        }
    }
}