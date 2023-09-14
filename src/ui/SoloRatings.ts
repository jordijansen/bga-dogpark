class SoloRatings {
    constructor(private elementId: string) {
        dojo.place(`<div id="dp-solo-ratings-wrapper">
                            <h3>${_('Solo Ratings')}</h3>
                            <p>${_('To calculate your rating, add any star value from your objective card to the star value below, depending on your total score.')}</p>
                            <table>
                                <thead><th>${_('Total Score')}</th><th>${_('Star Value')}</th></thead>
                                <tbody>
                                    <tr><td>&#x2264; 40</td><td>-</td></tr>
                                    <tr><td>41-47</td><td><i class="fa fa-star" aria-hidden="true"></i></td></tr>
                                    <tr><td>48-54</td><td><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></td></tr>
                                    <tr><td>55-61</td><td><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></td></tr>
                                    <tr><td>&#x2265; 62</td><td><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></td></tr>
                                </tbody>
                            </table>
                            <p><a id="dp-solo-ratings-dialog-button" class="bgabutton bgabutton_gray">${_('Compare Your Rating')}</a></p>
                         </div>`, $(elementId));
        dojo.connect($('dp-solo-ratings-dialog-button'), 'onclick', (event) => {
            dojo.stopEvent(event);
            const dialog = new ebg.popindialog();
            dialog.create('dp-solo-ratings-dialog');
            dialog.setTitle(`${_('Solo Ratings')}`);
            dialog.setContent(SoloRatings.getSoloRatingsTable());
            dialog.show();
        });
    }

    public static getSoloRatingsTable() {
        return `      <div id="dp-solo-ratings-wrapper">
                            <table>
                                <thead><th>${_('Stars')}</th><th>${_('Your Rating')}</th></thead>
                                <tbody>
                                    <tr><td>0-1 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Let's go again")}</td></tr>
                                    <tr><td>2 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Better luck next time")}</td></tr>
                                    <tr><td>3 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("More training required")}</td></tr>
                                    <tr><td>4 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Still an underdog")}</td></tr>
                                    <tr><td>5 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Middle of the pack")}</td></tr>
                                    <tr><td>6 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Not to be sniffed at")}</td></tr>
                                    <tr><td>7 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Rising star")}</td></tr>
                                    <tr><td>8 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Top dog")}</td></tr>
                                    <tr><td>9 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Super walker")}</td></tr>
                                    <tr><td>10 <i class="fa fa-star" aria-hidden="true"></i></td><td>${_("Best in show")}</td></tr>
                                </tbody>
                            </table>
                         </div>
            `;
    }
}