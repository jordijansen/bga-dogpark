<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DogPark implementation : © Jordi Jansen <jordi@itbyjj.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *
 */
class action_dogpark extends APP_GameAction
{
    // Constructor: please do not modify
    public function __default()
    {
        if (self::isArg('notifwindow')) {
            $this->view = "common_notifwindow";
            $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
        } else {
            $this->view = "dogpark_dogpark";
            self::trace("Complete reinitialization of board game");
        }
    }

    public function chooseObjective()
    {
        self::setAjaxMode();

        $cardId = self::getArg("cardId", AT_posint, true);
        $this->game->chooseObjective($cardId);

        self::ajaxResponse();
    }

    public function changeObjective()
    {
        self::setAjaxMode();

        $this->game->changeObjective();

        self::ajaxResponse();
    }

    public function skipPlaceOfferOnDog()
    {
        self::setAjaxMode();

        $this->game->skipPlaceOfferOnDog();

        self::ajaxResponse();
    }

    public function recruitDog()
    {
        self::setAjaxMode();

        $dogId = self::getArg("dogId", AT_posint, false);
        $this->game->recruitDog($dogId, false);

        self::ajaxResponse();
    }

    public function placeOfferOnDog()
    {
        self::setAjaxMode();

        $dogId = self::getArg("dogId", AT_posint, false);
        $offerValue = self::getArg("offerValue", AT_posint, true);
        $this->game->placeOfferOnDog($dogId, $offerValue);

        self::ajaxResponse();
    }

    public function placeDogOnLead()
    {
        self::setAjaxMode();

        $dogId = self::getArg("dogId", AT_posint, true);
        $this->game->placeDogOnLead($dogId);

        self::ajaxResponse();
    }

    public function placeDogOnLeadPayResources()
    {
        self::setAjaxMode();

        $dogId = self::getArg("dogId", AT_posint, true);
        $isFreePlacement = self::getArg('isFreePlacement', AT_bool, true);
        $resources = self::getArg('resources', AT_json, true);
        $this->validateJSonAlphaNum($resources, 'resources');

        $this->game->placeDogOnLeadPayResources($dogId, $resources, $isFreePlacement);

        self::ajaxResponse();
    }

    public function placeDogOnLeadCancel()
    {
        self::setAjaxMode();

        $this->game->placeDogOnLeadCancel();

        self::ajaxResponse();
    }

    public function confirmSelection()
    {
        self::setAjaxMode();

        $this->game->confirmSelection();

        self::ajaxResponse();
    }

    public function changeSelection()
    {
        self::setAjaxMode();

        $this->game->changeSelection();

        self::ajaxResponse();
    }

    public function moveWalker()
    {
        self::setAjaxMode();

        $locationId = self::getArg("locationId", AT_posint, true);
        $this->game->moveWalker($locationId);

        self::ajaxResponse();
    }

    public function additionalAction()
    {
        self::setAjaxMode();

        $actionId = self::getArg("actionId", AT_alphanum, true);
        $this->game->additionalAction($actionId);

        self::ajaxResponse();
    }

    public function confirmWalking()
    {
        self::setAjaxMode();

        $this->game->confirmWalking();

        self::ajaxResponse();
    }

    public function confirmSwap()
    {
        self::setAjaxMode();

        $fieldDogId = self::getArg("fieldDogId", AT_posint, true);
        $kennelDogId = self::getArg("kennelDogId", AT_posint, true);
        $this->game->confirmSwap($fieldDogId, $kennelDogId);

        self::ajaxResponse();
    }

    public function cancelSwap()
    {
        self::setAjaxMode();

        $this->game->cancelSwap();

        self::ajaxResponse();
    }

    public function confirmScout()
    {
        self::setAjaxMode();

        $fieldDogId = self::getArg("fieldDogId", AT_posint, true);
        $scoutDogId = self::getArg("scoutDogId", AT_posint, true);
        $this->game->confirmScout($fieldDogId, $scoutDogId);

        self::ajaxResponse();
    }

    public function endScout()
    {
        self::setAjaxMode();

        $this->game->endScout();

        self::ajaxResponse();
    }

    public function moveAutoWalker()
    {
        self::setAjaxMode();

        $locationId = self::getArg("locationId", AT_posint, true);
        $this->game->moveAutoWalker($locationId);

        self::ajaxResponse();
    }

    public function confirmCrafty()
    {
        self::setAjaxMode();

        $resource = self::getArg("resource", AT_alphanum, true);
        $this->game->confirmCrafty($resource);

        self::ajaxResponse();
    }

    public function cancelCrafty()
    {
        self::setAjaxMode();

        $this->game->cancelCrafty();

        self::ajaxResponse();
    }

    public function confirmGainResources()
    {
        self::setAjaxMode();

        $resources = self::getArg('resources', AT_json, true);
        $this->validateJSonAlphaNum($resources, 'resources');
        $this->game->confirmGainResources($resources);

        self::ajaxResponse();
    }

    public function cancelGainResources()
    {
        self::setAjaxMode();

        $this->game->cancelGainResources();

        self::ajaxResponse();
    }


    public function confirmGlobetrotter()
    {
        self::setAjaxMode();

        $locationId = self::getArg("locationId", AT_posint, true);
        $this->game->confirmGlobetrotter($locationId);

        self::ajaxResponse();
    }

    public function undoLast()
    {
        self::setAjaxMode();

        $this->game->undoLast();

        self::ajaxResponse();
    }

    public function undoAll()
    {
        self::setAjaxMode();

        $this->game->undoAll();

        self::ajaxResponse();
    }

    private function validateJSonAlphaNum($value, $argName = 'unknown')
    {
        if (is_array($value)) {
            foreach ($value as $key => $v) {
                $this->validateJSonAlphaNum($key, $argName);
                $this->validateJSonAlphaNum($v, $argName);
            }
            return true;
        }
        if (is_int($value)) {
            return true;
        }

        $bValid = preg_match("/^[_0-9a-zA-Z- ]*$/", $value) === 1; // NOI18N
        if (!$bValid) {
            throw new BgaSystemException("Bad value for: $argName", true, true, FEX_bad_input_argument);
        }
        return true;
    }
}
  

