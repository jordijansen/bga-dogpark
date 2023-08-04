{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- dogpark implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    dogpark_dogpark.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->
<div id="dp-game">
    <div id="dp-choose-objectives"></div>
    <div id="dp-game-board-wrapper">
        <div id="dp-game-board-main">
            <div id="dp-game-board-round-tracker" class="dp-board" style="order: 10;">
                <div id="dp-round-tracker-track">
                    <div id="dp-round-tracker" class="dp-token dp-round-marker" data-round="1" data-phase="1"></div>
                </div>
                <div id="dp-round-tracker-forecast-stock">

                </div>
            </div>
            <div id="dp-game-board-park-wrapper" class="dp-board" style="order: 11;" >
                <div id="dp-game-board-park">

                </div>
                <div id="dp-game-board-park-location-card-deck"></div>
            </div>
            <div id="dp-game-board-field-wrapper" style="order: 12;">
                <div id="dp-game-board-field" class="dp-board"></div>
                <div id="dp-game-board-offer-dials" class="whiteblock">
                </div>
            </div>
        </div>
        <div id="dp-game-board-side" class="dp-board hide-side-bar">
            <div id="dp-game-board-breed-expert-awards" class="dp-board">
                <div id="dp-game-board-breed-expert-awards-stock">

                </div>
            </div>
            <div id="dp-game-board-side-toggle-button">Awards</div>
        </div>
    </div>
    <div id="dp-player-areas">
    </div>
    <div class="location-bonus-art location-bonus-art-background" style="width: 266px; height: 195px;"></div>
    <div class="location-bonus-art location-bonus-art-2" style="width: 266px; height: 195px;"></div>

    <div class="breed-expert-art breed-expert-art-background" style="width: 195px; height: 142px;"></div>
    <div class="breed-expert-art breed-expert-art-2" style="width: 195px; height: 142px;"></div>

    <div class="forecast-art forecast-art-background" style="width: 195px; height: 142px;"></div>
    <div class="forecast-art forecast-art-2" style="width: 195px; height: 142px;"></div>

    <div class="objective-art objective-art-background" style="width: 266px; height: 195px;"></div>
    <div class="objective-art objective-art-2" style="width: 266px; height: 195px;"></div>
</div>

<div id="zoom-overall" style="width: 100%;"></div>



<div class="dp-token-token" data-type="walked"></div>
<div class="dp-token-token" data-type="reputation"></div>
<div class="dp-token-token" data-type="toy"></div>
<div class="dp-token-token" data-type="ball"></div>
<div class="dp-token-token" data-type="treat"></div>
<div class="dp-token-token" data-type="stick"></div>
<div class="dp-token-token" data-type="scout"></div>
<div class="dp-token-token" data-type="swap"></div>
<div class="dp-token-token" data-type="block"></div>


<script type="text/javascript">

// Javascript HTML templates

/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';

*/

</script>  

{OVERALL_GAME_FOOTER}
