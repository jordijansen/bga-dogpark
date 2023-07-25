class DogPayCosts {

    private remainingResources: {stick: number, ball: number, treat: number, toy: number} = {stick: 0, ball: 0, treat: 0, toy: 0};
    private selectedPayment: {[resource: string]: string[]} = {};
    private needsSelection = false;

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

        Object.entries(this.dog.costs).forEach(([resource, cost]) => {
            if (this.remainingResources[resource] >= this.dog.costs[resource]) {
                this.remainingResources[resource] -= 1;
                this.selectedPayment[resource] = [resource];
            } else {
                this.needsSelection = true;
                this.selectedPayment[resource] = ["placeholder", "placeholder"];
            }
        })
    }

    private updateUi() {
        const wrapperElement = $('dp-dog-cost-pay-wrapper');
        dojo.empty(wrapperElement);
        dojo.place(this.createMainButtons(), wrapperElement)
        dojo.place(this.createCostRows(), wrapperElement)
        dojo.place(this.createResourceButtons(), wrapperElement)

        if (this.needsSelection) {
            dojo.connect($(`dp-dog-cost-pay-reset-button`), 'onclick', () => {
                this.resetSelection();
                this.updateUi();
            });

            Object.entries(this.remainingResources)
                .forEach(([resource, nr]) => dojo.connect($(`dp-dog-cost-pay-${resource}-button`), 'onclick', () => this.useResource(resource)))
        }
        dojo.connect($(`dp-dog-cost-pay-cancel-button`), 'onclick', () => this.onCancel());
        dojo.connect($(`dp-dog-cost-pay-confirm-button`), 'onclick', () => this.onConfirm(Object.values(this.selectedPayment).flat()));
    }

    private createCostRows() {
        let result = `<div class="dp-dog-cost-pay-row">${_('Cost')}<i class="fa fa-long-arrow-right" aria-hidden="true"></i>${_('Pay using')}</div>`;
        Object.entries(this.selectedPayment).forEach(([resource, selectedResources]) => {
            result += `<div class="dp-dog-cost-pay-row"><span class="dp-token-token" data-type="${resource}"></span><i class="fa fa-long-arrow-right" aria-hidden="true"></i>`
            selectedResources.forEach(selectedResource => {
                result += `<span class="dp-token-token" data-type="${selectedResource}"></span>`;
            })
            result += '</div>';
        })
        return result;
    }

    private createResourceButtons() {
        let stillNeedResources = false;
        for (let costResource in this.selectedPayment) {
            const indexOf = this.selectedPayment[costResource].indexOf('placeholder');
            if (indexOf >= 0) {
                stillNeedResources = true;
                break;
            }
        }

        let result = `<div class="dp-dog-cost-pay-row">`;
        if (stillNeedResources) {
            Object.entries(this.remainingResources)
                .forEach(([resource, nr]) => {
                    const disabled = this.remainingResources[resource] <= 0;
                    result += `<a id="dp-dog-cost-pay-${resource}-button" class="bgabutton bgabutton_blue ${disabled ? 'disabled' : ''}"><span class="dp-token-token" data-type="${resource}"></span></a>`
                })
        }

        result += '</div>';
        return result;
    }

    private createMainButtons() {
        let result = `<div class="dp-dog-cost-pay-row">`;
        const disabled = false;
        result += `<a id="dp-dog-cost-pay-confirm-button" class="bgabutton bgabutton_blue ${disabled ? 'disabled' : ''}">${_('Confirm')}</a>`
        if (this.needsSelection) {
            result += `<a id="dp-dog-cost-pay-reset-button" class="bgabutton bgabutton_gray">${_('Reset')}</a>`
        }
        result += `<a id="dp-dog-cost-pay-cancel-button" class="bgabutton bgabutton_gray">${_('Cancel')}</a>`

        result += '</div>';
        return result;
    }


    private useResource(resource: string) {
        for (let costResource in this.selectedPayment) {
            const indexOf = this.selectedPayment[costResource].indexOf('placeholder');
            if (indexOf >= 0) {
                this.remainingResources[resource] -= 1;
                this.selectedPayment[costResource][indexOf] = resource;
            }
        }
        this.updateUi();
    }
}