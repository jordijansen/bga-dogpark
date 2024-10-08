var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
var log = isDebug ? console.log.bind(window.console) : function () { };
define([
    "dojo", "dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock"
], function (dojo, declare) {
    return declare("bgagame.dogpark", ebg.core.gamegui, new DogPark());
});
var DEFAULT_ZOOM_LEVELS = [0.25, 0.375, 0.5, 0.625, 0.75, 0.875, 1];
var ZoomManager = /** @class */ (function () {
    /**
     * Place the settings.element in a zoom wrapper and init zoomControls.
     *
     * @param settings: a `ZoomManagerSettings` object
     */
    function ZoomManager(settings) {
        var _this = this;
        var _a, _b, _c, _d, _e;
        this.settings = settings;
        if (!settings.element) {
            throw new DOMException('You need to set the element to wrap in the zoom element');
        }
        this._zoomLevels = (_a = settings.zoomLevels) !== null && _a !== void 0 ? _a : DEFAULT_ZOOM_LEVELS;
        this._zoom = this.settings.defaultZoom || 1;
        if (this.settings.localStorageZoomKey) {
            var zoomStr = localStorage.getItem(this.settings.localStorageZoomKey);
            if (zoomStr) {
                this._zoom = Number(zoomStr);
            }
        }
        this.wrapper = document.createElement('div');
        this.wrapper.id = 'bga-zoom-wrapper';
        this.wrapElement(this.wrapper, settings.element);
        this.wrapper.appendChild(settings.element);
        settings.element.classList.add('bga-zoom-inner');
        if ((_b = settings.smooth) !== null && _b !== void 0 ? _b : true) {
            settings.element.dataset.smooth = 'true';
            settings.element.addEventListener('transitionend', function () { return _this.zoomOrDimensionChanged(); });
        }
        if ((_d = (_c = settings.zoomControls) === null || _c === void 0 ? void 0 : _c.visible) !== null && _d !== void 0 ? _d : true) {
            this.initZoomControls(settings);
        }
        if (this._zoom !== 1) {
            this.setZoom(this._zoom);
        }
        window.addEventListener('resize', function () {
            var _a;
            _this.zoomOrDimensionChanged();
            if ((_a = _this.settings.autoZoom) === null || _a === void 0 ? void 0 : _a.expectedWidth) {
                _this.setAutoZoom();
            }
        });
        if (window.ResizeObserver) {
            new ResizeObserver(function () { return _this.zoomOrDimensionChanged(); }).observe(settings.element);
        }
        if ((_e = this.settings.autoZoom) === null || _e === void 0 ? void 0 : _e.expectedWidth) {
            this.setAutoZoom();
        }
    }
    Object.defineProperty(ZoomManager.prototype, "zoom", {
        /**
         * Returns the zoom level
         */
        get: function () {
            return this._zoom;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(ZoomManager.prototype, "zoomLevels", {
        /**
         * Returns the zoom levels
         */
        get: function () {
            return this._zoomLevels;
        },
        enumerable: false,
        configurable: true
    });
    ZoomManager.prototype.setAutoZoom = function () {
        var _this = this;
        var _a, _b, _c;
        var zoomWrapperWidth = document.getElementById('bga-zoom-wrapper').clientWidth;
        if (!zoomWrapperWidth) {
            setTimeout(function () { return _this.setAutoZoom(); }, 200);
            return;
        }
        var expectedWidth = (_a = this.settings.autoZoom) === null || _a === void 0 ? void 0 : _a.expectedWidth;
        var newZoom = this.zoom;
        while (newZoom > this._zoomLevels[0] && newZoom > ((_c = (_b = this.settings.autoZoom) === null || _b === void 0 ? void 0 : _b.minZoomLevel) !== null && _c !== void 0 ? _c : 0) && zoomWrapperWidth / newZoom < expectedWidth) {
            newZoom = this._zoomLevels[this._zoomLevels.indexOf(newZoom) - 1];
        }
        if (this._zoom == newZoom) {
            if (this.settings.localStorageZoomKey) {
                localStorage.setItem(this.settings.localStorageZoomKey, '' + this._zoom);
            }
        }
        else {
            this.setZoom(newZoom);
        }
    };
    /**
     * Sets the available zoomLevels and new zoom to the provided values.
     * @param zoomLevels the new array of zoomLevels that can be used.
     * @param newZoom if provided the zoom will be set to this value, if not the last element of the zoomLevels array will be set as the new zoom
     */
    ZoomManager.prototype.setZoomLevels = function (zoomLevels, newZoom) {
        if (!zoomLevels || zoomLevels.length <= 0) {
            return;
        }
        this._zoomLevels = zoomLevels;
        var zoomIndex = newZoom && zoomLevels.includes(newZoom) ? this._zoomLevels.indexOf(newZoom) : this._zoomLevels.length - 1;
        this.setZoom(this._zoomLevels[zoomIndex]);
    };
    /**
     * Set the zoom level. Ideally, use a zoom level in the zoomLevels range.
     * @param zoom zool level
     */
    ZoomManager.prototype.setZoom = function (zoom) {
        var _a, _b, _c, _d;
        if (zoom === void 0) { zoom = 1; }
        this._zoom = zoom;
        if (this.settings.localStorageZoomKey) {
            localStorage.setItem(this.settings.localStorageZoomKey, '' + this._zoom);
        }
        var newIndex = this._zoomLevels.indexOf(this._zoom);
        (_a = this.zoomInButton) === null || _a === void 0 ? void 0 : _a.classList.toggle('disabled', newIndex === this._zoomLevels.length - 1);
        (_b = this.zoomOutButton) === null || _b === void 0 ? void 0 : _b.classList.toggle('disabled', newIndex === 0);
        this.settings.element.style.transform = zoom === 1 ? '' : "scale(".concat(zoom, ")");
        (_d = (_c = this.settings).onZoomChange) === null || _d === void 0 ? void 0 : _d.call(_c, this._zoom);
        this.zoomOrDimensionChanged();
    };
    /**
     * Call this method for the browsers not supporting ResizeObserver, everytime the table height changes, if you know it.
     * If the browsert is recent enough (>= Safari 13.1) it will just be ignored.
     */
    ZoomManager.prototype.manualHeightUpdate = function () {
        if (!window.ResizeObserver) {
            this.zoomOrDimensionChanged();
        }
    };
    /**
     * Everytime the element dimensions changes, we update the style. And call the optional callback.
     */
    ZoomManager.prototype.zoomOrDimensionChanged = function () {
        var _a, _b;
        this.settings.element.style.width = "".concat(this.wrapper.getBoundingClientRect().width / this._zoom, "px");
        this.wrapper.style.height = "".concat(this.settings.element.getBoundingClientRect().height, "px");
        (_b = (_a = this.settings).onDimensionsChange) === null || _b === void 0 ? void 0 : _b.call(_a, this._zoom);
    };
    /**
     * Simulates a click on the Zoom-in button.
     */
    ZoomManager.prototype.zoomIn = function () {
        if (this._zoom === this._zoomLevels[this._zoomLevels.length - 1]) {
            return;
        }
        var newIndex = this._zoomLevels.indexOf(this._zoom) + 1;
        this.setZoom(newIndex === -1 ? 1 : this._zoomLevels[newIndex]);
    };
    /**
     * Simulates a click on the Zoom-out button.
     */
    ZoomManager.prototype.zoomOut = function () {
        if (this._zoom === this._zoomLevels[0]) {
            return;
        }
        var newIndex = this._zoomLevels.indexOf(this._zoom) - 1;
        this.setZoom(newIndex === -1 ? 1 : this._zoomLevels[newIndex]);
    };
    /**
     * Changes the color of the zoom controls.
     */
    ZoomManager.prototype.setZoomControlsColor = function (color) {
        if (this.zoomControls) {
            this.zoomControls.dataset.color = color;
        }
    };
    /**
     * Set-up the zoom controls
     * @param settings a `ZoomManagerSettings` object.
     */
    ZoomManager.prototype.initZoomControls = function (settings) {
        var _this = this;
        var _a, _b, _c, _d, _e, _f;
        this.zoomControls = document.createElement('div');
        this.zoomControls.id = 'bga-zoom-controls';
        this.zoomControls.dataset.position = (_b = (_a = settings.zoomControls) === null || _a === void 0 ? void 0 : _a.position) !== null && _b !== void 0 ? _b : 'top-right';
        this.zoomOutButton = document.createElement('button');
        this.zoomOutButton.type = 'button';
        this.zoomOutButton.addEventListener('click', function () { return _this.zoomOut(); });
        if ((_c = settings.zoomControls) === null || _c === void 0 ? void 0 : _c.customZoomOutElement) {
            settings.zoomControls.customZoomOutElement(this.zoomOutButton);
        }
        else {
            this.zoomOutButton.classList.add("bga-zoom-out-icon");
        }
        this.zoomInButton = document.createElement('button');
        this.zoomInButton.type = 'button';
        this.zoomInButton.addEventListener('click', function () { return _this.zoomIn(); });
        if ((_d = settings.zoomControls) === null || _d === void 0 ? void 0 : _d.customZoomInElement) {
            settings.zoomControls.customZoomInElement(this.zoomInButton);
        }
        else {
            this.zoomInButton.classList.add("bga-zoom-in-icon");
        }
        this.zoomControls.appendChild(this.zoomOutButton);
        this.zoomControls.appendChild(this.zoomInButton);
        this.wrapper.appendChild(this.zoomControls);
        this.setZoomControlsColor((_f = (_e = settings.zoomControls) === null || _e === void 0 ? void 0 : _e.color) !== null && _f !== void 0 ? _f : 'black');
    };
    /**
     * Wraps an element around an existing DOM element
     * @param wrapper the wrapper element
     * @param element the existing element
     */
    ZoomManager.prototype.wrapElement = function (wrapper, element) {
        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);
    };
    return ZoomManager;
}());
var BgaAnimation = /** @class */ (function () {
    function BgaAnimation(animationFunction, settings) {
        this.animationFunction = animationFunction;
        this.settings = settings;
        this.played = null;
        this.result = null;
        this.playWhenNoAnimation = false;
    }
    return BgaAnimation;
}());
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        if (typeof b !== "function" && b !== null)
            throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
/**
 * Just use playSequence from animationManager
 *
 * @param animationManager the animation manager
 * @param animation a `BgaAnimation` object
 * @returns a promise when animation ends
 */
function attachWithAnimation(animationManager, animation) {
    var _a;
    var settings = animation.settings;
    var element = settings.animation.settings.element;
    var fromRect = element.getBoundingClientRect();
    settings.animation.settings.fromRect = fromRect;
    settings.attachElement.appendChild(element);
    (_a = settings.afterAttach) === null || _a === void 0 ? void 0 : _a.call(settings, element, settings.attachElement);
    return animationManager.play(settings.animation);
}
var BgaAttachWithAnimation = /** @class */ (function (_super) {
    __extends(BgaAttachWithAnimation, _super);
    function BgaAttachWithAnimation(settings) {
        var _this = _super.call(this, attachWithAnimation, settings) || this;
        _this.playWhenNoAnimation = true;
        return _this;
    }
    return BgaAttachWithAnimation;
}(BgaAnimation));
/**
 * Just use playSequence from animationManager
 *
 * @param animationManager the animation manager
 * @param animation a `BgaAnimation` object
 * @returns a promise when animation ends
 */
function cumulatedAnimations(animationManager, animation) {
    return animationManager.playSequence(animation.settings.animations);
}
var BgaCumulatedAnimation = /** @class */ (function (_super) {
    __extends(BgaCumulatedAnimation, _super);
    function BgaCumulatedAnimation(settings) {
        var _this = _super.call(this, cumulatedAnimations, settings) || this;
        _this.playWhenNoAnimation = true;
        return _this;
    }
    return BgaCumulatedAnimation;
}(BgaAnimation));
/**
 * Just does nothing for the duration
 *
 * @param animationManager the animation manager
 * @param animation a `BgaAnimation` object
 * @returns a promise when animation ends
 */
function pauseAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a;
        var settings = animation.settings;
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        setTimeout(function () { return success(); }, duration);
    });
    return promise;
}
var BgaPauseAnimation = /** @class */ (function (_super) {
    __extends(BgaPauseAnimation, _super);
    function BgaPauseAnimation(settings) {
        return _super.call(this, pauseAnimation, settings) || this;
    }
    return BgaPauseAnimation;
}(BgaAnimation));
/**
 * Show the element at the center of the screen
 *
 * @param animationManager the animation manager
 * @param animation a `BgaAnimation` object
 * @returns a promise when animation ends
 */
function showScreenCenterAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a, _b, _c;
        var settings = animation.settings;
        var element = settings.element;
        var elementBR = element.getBoundingClientRect();
        var xCenter = (elementBR.left + elementBR.right) / 2;
        var yCenter = (elementBR.top + elementBR.bottom) / 2;
        var x = xCenter - (window.innerWidth / 2);
        var y = yCenter - (window.innerHeight / 2);
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        var originalZIndex = element.style.zIndex;
        var originalTransition = element.style.transition;
        element.style.zIndex = "".concat((_b = settings === null || settings === void 0 ? void 0 : settings.zIndex) !== null && _b !== void 0 ? _b : 10);
        var timeoutId = null;
        var cleanOnTransitionEnd = function () {
            element.style.zIndex = originalZIndex;
            element.style.transition = originalTransition;
            success();
            element.removeEventListener('transitioncancel', cleanOnTransitionEnd);
            element.removeEventListener('transitionend', cleanOnTransitionEnd);
            document.removeEventListener('visibilitychange', cleanOnTransitionEnd);
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        };
        var cleanOnTransitionCancel = function () {
            var _a;
            element.style.transition = "";
            element.offsetHeight;
            element.style.transform = (_a = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _a !== void 0 ? _a : null;
            element.offsetHeight;
            cleanOnTransitionEnd();
        };
        element.addEventListener('transitioncancel', cleanOnTransitionEnd);
        element.addEventListener('transitionend', cleanOnTransitionEnd);
        document.addEventListener('visibilitychange', cleanOnTransitionCancel);
        element.offsetHeight;
        element.style.transition = "transform ".concat(duration, "ms linear");
        element.offsetHeight;
        element.style.transform = "translate(".concat(-x, "px, ").concat(-y, "px) rotate(").concat((_c = settings === null || settings === void 0 ? void 0 : settings.rotationDelta) !== null && _c !== void 0 ? _c : 0, "deg)");
        // safety in case transitionend and transitioncancel are not called
        timeoutId = setTimeout(cleanOnTransitionEnd, duration + 100);
    });
    return promise;
}
var BgaShowScreenCenterAnimation = /** @class */ (function (_super) {
    __extends(BgaShowScreenCenterAnimation, _super);
    function BgaShowScreenCenterAnimation(settings) {
        return _super.call(this, showScreenCenterAnimation, settings) || this;
    }
    return BgaShowScreenCenterAnimation;
}(BgaAnimation));
/**
 * Linear slide of the element from origin to destination.
 *
 * @param animationManager the animation manager
 * @param animation a `BgaAnimation` object
 * @returns a promise when animation ends
 */
function slideAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a, _b, _c, _d;
        var settings = animation.settings;
        var element = settings.element;
        var _e = getDeltaCoordinates(element, settings), x = _e.x, y = _e.y;
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        var originalZIndex = element.style.zIndex;
        var originalTransition = element.style.transition;
        element.style.zIndex = "".concat((_b = settings === null || settings === void 0 ? void 0 : settings.zIndex) !== null && _b !== void 0 ? _b : 10);
        element.style.transition = null;
        element.offsetHeight;
        element.style.transform = "translate(".concat(-x, "px, ").concat(-y, "px) rotate(").concat((_c = settings === null || settings === void 0 ? void 0 : settings.rotationDelta) !== null && _c !== void 0 ? _c : 0, "deg)");
        var timeoutId = null;
        var cleanOnTransitionEnd = function () {
            element.style.zIndex = originalZIndex;
            element.style.transition = originalTransition;
            success();
            element.removeEventListener('transitioncancel', cleanOnTransitionEnd);
            element.removeEventListener('transitionend', cleanOnTransitionEnd);
            document.removeEventListener('visibilitychange', cleanOnTransitionEnd);
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        };
        var cleanOnTransitionCancel = function () {
            var _a;
            element.style.transition = "";
            element.offsetHeight;
            element.style.transform = (_a = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _a !== void 0 ? _a : null;
            element.offsetHeight;
            cleanOnTransitionEnd();
        };
        element.addEventListener('transitioncancel', cleanOnTransitionCancel);
        element.addEventListener('transitionend', cleanOnTransitionEnd);
        document.addEventListener('visibilitychange', cleanOnTransitionCancel);
        element.offsetHeight;
        element.style.transition = "transform ".concat(duration, "ms linear");
        element.offsetHeight;
        element.style.transform = (_d = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _d !== void 0 ? _d : null;
        // safety in case transitionend and transitioncancel are not called
        timeoutId = setTimeout(cleanOnTransitionEnd, duration + 100);
    });
    return promise;
}
var BgaSlideAnimation = /** @class */ (function (_super) {
    __extends(BgaSlideAnimation, _super);
    function BgaSlideAnimation(settings) {
        return _super.call(this, slideAnimation, settings) || this;
    }
    return BgaSlideAnimation;
}(BgaAnimation));
/**
 * Linear slide of the element from origin to destination.
 *
 * @param animationManager the animation manager
 * @param animation a `BgaAnimation` object
 * @returns a promise when animation ends
 */
function slideToAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a, _b, _c, _d;
        var settings = animation.settings;
        var element = settings.element;
        var _e = getDeltaCoordinates(element, settings), x = _e.x, y = _e.y;
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        var originalZIndex = element.style.zIndex;
        var originalTransition = element.style.transition;
        element.style.zIndex = "".concat((_b = settings === null || settings === void 0 ? void 0 : settings.zIndex) !== null && _b !== void 0 ? _b : 10);
        var timeoutId = null;
        var cleanOnTransitionEnd = function () {
            element.style.zIndex = originalZIndex;
            element.style.transition = originalTransition;
            success();
            element.removeEventListener('transitioncancel', cleanOnTransitionEnd);
            element.removeEventListener('transitionend', cleanOnTransitionEnd);
            document.removeEventListener('visibilitychange', cleanOnTransitionEnd);
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        };
        var cleanOnTransitionCancel = function () {
            var _a;
            element.style.transition = "";
            element.offsetHeight;
            element.style.transform = (_a = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _a !== void 0 ? _a : null;
            element.offsetHeight;
            cleanOnTransitionEnd();
        };
        element.addEventListener('transitioncancel', cleanOnTransitionEnd);
        element.addEventListener('transitionend', cleanOnTransitionEnd);
        document.addEventListener('visibilitychange', cleanOnTransitionCancel);
        element.offsetHeight;
        element.style.transition = "transform ".concat(duration, "ms linear");
        element.offsetHeight;
        element.style.transform = "translate(".concat(-x, "px, ").concat(-y, "px) rotate(").concat((_c = settings === null || settings === void 0 ? void 0 : settings.rotationDelta) !== null && _c !== void 0 ? _c : 0, "deg) scale(").concat((_d = settings.scale) !== null && _d !== void 0 ? _d : 1, ")");
        // safety in case transitionend and transitioncancel are not called
        timeoutId = setTimeout(cleanOnTransitionEnd, duration + 100);
    });
    return promise;
}
var BgaSlideToAnimation = /** @class */ (function (_super) {
    __extends(BgaSlideToAnimation, _super);
    function BgaSlideToAnimation(settings) {
        return _super.call(this, slideToAnimation, settings) || this;
    }
    return BgaSlideToAnimation;
}(BgaAnimation));
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (g && (g = 0, op[0] && (_ = 0)), _) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var __spreadArray = (this && this.__spreadArray) || function (to, from, pack) {
    if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
        if (ar || !(i in from)) {
            if (!ar) ar = Array.prototype.slice.call(from, 0, i);
            ar[i] = from[i];
        }
    }
    return to.concat(ar || Array.prototype.slice.call(from));
};
var AnimationManager = /** @class */ (function () {
    /**
     * @param game the BGA game class, usually it will be `this`
     * @param settings: a `AnimationManagerSettings` object
     */
    function AnimationManager(game, settings) {
        this.game = game;
        this.settings = settings;
        this.zoomManager = settings === null || settings === void 0 ? void 0 : settings.zoomManager;
        if (!game) {
            throw new Error('You must set your game as the first parameter of AnimationManager');
        }
    }
    AnimationManager.prototype.getZoomManager = function () {
        return this.zoomManager;
    };
    /**
     * Set the zoom manager, to get the scale of the current game.
     *
     * @param zoomManager the zoom manager
     */
    AnimationManager.prototype.setZoomManager = function (zoomManager) {
        this.zoomManager = zoomManager;
    };
    AnimationManager.prototype.getSettings = function () {
        return this.settings;
    };
    /**
     * Returns if the animations are active. Animation aren't active when the window is not visible (`document.visibilityState === 'hidden'`), or `game.instantaneousMode` is true.
     *
     * @returns if the animations are active.
     */
    AnimationManager.prototype.animationsActive = function () {
        return document.visibilityState !== 'hidden' && !this.game.instantaneousMode;
    };
    /**
     * Plays an animation if the animations are active. Animation aren't active when the window is not visible (`document.visibilityState === 'hidden'`), or `game.instantaneousMode` is true.
     *
     * @param animation the animation to play
     * @returns the animation promise.
     */
    AnimationManager.prototype.play = function (animation) {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l;
        return __awaiter(this, void 0, void 0, function () {
            var settings, _m;
            return __generator(this, function (_o) {
                switch (_o.label) {
                    case 0:
                        animation.played = animation.playWhenNoAnimation || this.animationsActive();
                        if (!animation.played) return [3 /*break*/, 2];
                        settings = animation.settings;
                        (_a = settings.animationStart) === null || _a === void 0 ? void 0 : _a.call(settings, animation);
                        (_b = settings.element) === null || _b === void 0 ? void 0 : _b.classList.add((_c = settings.animationClass) !== null && _c !== void 0 ? _c : 'bga-animations_animated');
                        animation.settings = __assign(__assign({}, animation.settings), { duration: (_e = (_d = this.settings) === null || _d === void 0 ? void 0 : _d.duration) !== null && _e !== void 0 ? _e : 500, scale: (_g = (_f = this.zoomManager) === null || _f === void 0 ? void 0 : _f.zoom) !== null && _g !== void 0 ? _g : undefined });
                        _m = animation;
                        return [4 /*yield*/, animation.animationFunction(this, animation)];
                    case 1:
                        _m.result = _o.sent();
                        (_j = (_h = animation.settings).animationEnd) === null || _j === void 0 ? void 0 : _j.call(_h, animation);
                        (_k = settings.element) === null || _k === void 0 ? void 0 : _k.classList.remove((_l = settings.animationClass) !== null && _l !== void 0 ? _l : 'bga-animations_animated');
                        return [3 /*break*/, 3];
                    case 2: return [2 /*return*/, Promise.resolve(animation)];
                    case 3: return [2 /*return*/];
                }
            });
        });
    };
    /**
     * Plays multiple animations in parallel.
     *
     * @param animations the animations to play
     * @returns a promise for all animations.
     */
    AnimationManager.prototype.playParallel = function (animations) {
        return __awaiter(this, void 0, void 0, function () {
            var _this = this;
            return __generator(this, function (_a) {
                return [2 /*return*/, Promise.all(animations.map(function (animation) { return _this.play(animation); }))];
            });
        });
    };
    /**
     * Plays multiple animations in sequence (the second when the first ends, ...).
     *
     * @param animations the animations to play
     * @returns a promise for all animations.
     */
    AnimationManager.prototype.playSequence = function (animations) {
        return __awaiter(this, void 0, void 0, function () {
            var result, others;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!animations.length) return [3 /*break*/, 3];
                        return [4 /*yield*/, this.play(animations[0])];
                    case 1:
                        result = _a.sent();
                        return [4 /*yield*/, this.playSequence(animations.slice(1))];
                    case 2:
                        others = _a.sent();
                        return [2 /*return*/, __spreadArray([result], others, true)];
                    case 3: return [2 /*return*/, Promise.resolve([])];
                }
            });
        });
    };
    /**
     * Plays multiple animations with a delay between each animation start.
     *
     * @param animations the animations to play
     * @param delay the delay (in ms)
     * @returns a promise for all animations.
     */
    AnimationManager.prototype.playWithDelay = function (animations, delay) {
        return __awaiter(this, void 0, void 0, function () {
            var promise;
            var _this = this;
            return __generator(this, function (_a) {
                promise = new Promise(function (success) {
                    var promises = [];
                    var _loop_1 = function (i) {
                        setTimeout(function () {
                            promises.push(_this.play(animations[i]));
                            if (i == animations.length - 1) {
                                Promise.all(promises).then(function (result) {
                                    success(result);
                                });
                            }
                        }, i * delay);
                    };
                    for (var i = 0; i < animations.length; i++) {
                        _loop_1(i);
                    }
                });
                return [2 /*return*/, promise];
            });
        });
    };
    /**
     * Attach an element to a parent, then play animation from element's origin to its new position.
     *
     * @param animation the animation function
     * @param attachElement the destination parent
     * @returns a promise when animation ends
     */
    AnimationManager.prototype.attachWithAnimation = function (animation, attachElement) {
        var attachWithAnimation = new BgaAttachWithAnimation({
            animation: animation,
            attachElement: attachElement
        });
        return this.play(attachWithAnimation);
    };
    return AnimationManager;
}());
function shouldAnimate(settings) {
    var _a;
    return document.visibilityState !== 'hidden' && !((_a = settings === null || settings === void 0 ? void 0 : settings.game) === null || _a === void 0 ? void 0 : _a.instantaneousMode);
}
/**
 * Return the x and y delta, based on the animation settings;
 *
 * @param settings an `AnimationSettings` object
 * @returns a promise when animation ends
 */
function getDeltaCoordinates(element, settings) {
    var _a;
    if (!settings.fromDelta && !settings.fromRect && !settings.fromElement) {
        throw new Error("[bga-animation] fromDelta, fromRect or fromElement need to be set");
    }
    var x = 0;
    var y = 0;
    if (settings.fromDelta) {
        x = settings.fromDelta.x;
        y = settings.fromDelta.y;
    }
    else {
        var originBR = (_a = settings.fromRect) !== null && _a !== void 0 ? _a : settings.fromElement.getBoundingClientRect();
        // TODO make it an option ?
        var originalTransform = element.style.transform;
        element.style.transform = '';
        var destinationBR = element.getBoundingClientRect();
        element.style.transform = originalTransform;
        x = (destinationBR.left + destinationBR.right) / 2 - (originBR.left + originBR.right) / 2;
        y = (destinationBR.top + destinationBR.bottom) / 2 - (originBR.top + originBR.bottom) / 2;
    }
    if (settings.scale) {
        x /= settings.scale;
        y /= settings.scale;
    }
    return { x: x, y: y };
}
function logAnimation(animationManager, animation) {
    var settings = animation.settings;
    var element = settings.element;
    if (element) {
        console.log(animation, settings, element, element.getBoundingClientRect(), element.style.transform);
    }
    else {
        console.log(animation, settings);
    }
    return Promise.resolve(false);
}
/**
 * The abstract stock. It shouldn't be used directly, use stocks that extends it.
 */
var CardStock = /** @class */ (function () {
    /**
     * @param manager the card manager
     * @param element the stock element (should be an empty HTML Element)
     */
    function CardStock(manager, element, settings) {
        this.manager = manager;
        this.element = element;
        this.settings = settings;
        this.cards = [];
        this.selectedCards = [];
        this.selectionMode = 'none';
        manager.addStock(this);
        element === null || element === void 0 ? void 0 : element.classList.add('card-stock' /*, this.constructor.name.split(/(?=[A-Z])/).join('-').toLowerCase()* doesn't work in production because of minification */);
        this.bindClick();
        this.sort = settings === null || settings === void 0 ? void 0 : settings.sort;
    }
    /**
     * @returns the cards on the stock
     */
    CardStock.prototype.getCards = function () {
        return this.cards.slice();
    };
    /**
     * @returns if the stock is empty
     */
    CardStock.prototype.isEmpty = function () {
        return !this.cards.length;
    };
    /**
     * @returns the selected cards
     */
    CardStock.prototype.getSelection = function () {
        return this.selectedCards.slice();
    };
    /**
     * @returns the selected cards
     */
    CardStock.prototype.isSelected = function (card) {
        var _this = this;
        return this.selectedCards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
    };
    /**
     * @param card a card
     * @returns if the card is present in the stock
     */
    CardStock.prototype.contains = function (card) {
        var _this = this;
        return this.cards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
    };
    /**
     * @param card a card in the stock
     * @returns the HTML element generated for the card
     */
    CardStock.prototype.getCardElement = function (card) {
        return this.manager.getCardElement(card);
    };
    /**
     * Checks if the card can be added. By default, only if it isn't already present in the stock.
     *
     * @param card the card to add
     * @param settings the addCard settings
     * @returns if the card can be added
     */
    CardStock.prototype.canAddCard = function (card, settings) {
        return !this.contains(card);
    };
    /**
     * Add a card to the stock.
     *
     * @param card the card to add
     * @param animation a `CardAnimation` object
     * @param settings a `AddCardSettings` object
     * @returns the promise when the animation is done (true if it was animated, false if it wasn't)
     */
    CardStock.prototype.addCard = function (card, animation, settings) {
        var _this = this;
        var _a, _b, _c;
        if (!this.canAddCard(card, settings)) {
            return Promise.resolve(false);
        }
        var promise;
        // we check if card is in a stock
        var originStock = this.manager.getCardStock(card);
        var index = this.getNewCardIndex(card);
        var settingsWithIndex = __assign({ index: index }, (settings !== null && settings !== void 0 ? settings : {}));
        var updateInformations = (_a = settingsWithIndex.updateInformations) !== null && _a !== void 0 ? _a : true;
        if (originStock === null || originStock === void 0 ? void 0 : originStock.contains(card)) {
            var element = this.getCardElement(card);
            promise = this.moveFromOtherStock(card, element, __assign(__assign({}, animation), { fromStock: originStock }), settingsWithIndex);
            if (!updateInformations) {
                element.dataset.side = ((_b = settingsWithIndex === null || settingsWithIndex === void 0 ? void 0 : settingsWithIndex.visible) !== null && _b !== void 0 ? _b : this.manager.isCardVisible(card)) ? 'front' : 'back';
            }
        }
        else if ((animation === null || animation === void 0 ? void 0 : animation.fromStock) && animation.fromStock.contains(card)) {
            var element = this.getCardElement(card);
            promise = this.moveFromOtherStock(card, element, animation, settingsWithIndex);
        }
        else {
            var element = this.manager.createCardElement(card, ((_c = settingsWithIndex === null || settingsWithIndex === void 0 ? void 0 : settingsWithIndex.visible) !== null && _c !== void 0 ? _c : this.manager.isCardVisible(card)));
            promise = this.moveFromElement(card, element, animation, settingsWithIndex);
        }
        if (settingsWithIndex.index !== null && settingsWithIndex.index !== undefined) {
            this.cards.splice(index, 0, card);
        }
        else {
            this.cards.push(card);
        }
        if (updateInformations) { // after splice/push
            this.manager.updateCardInformations(card);
        }
        if (!promise) {
            console.warn("CardStock.addCard didn't return a Promise");
            promise = Promise.resolve(false);
        }
        if (this.selectionMode !== 'none') {
            // make selectable only at the end of the animation
            promise.then(function () { var _a; return _this.setSelectableCard(card, (_a = settingsWithIndex.selectable) !== null && _a !== void 0 ? _a : true); });
        }
        return promise;
    };
    CardStock.prototype.getNewCardIndex = function (card) {
        if (this.sort) {
            var otherCards = this.getCards();
            for (var i = 0; i < otherCards.length; i++) {
                var otherCard = otherCards[i];
                if (this.sort(card, otherCard) < 0) {
                    return i;
                }
            }
            return otherCards.length;
        }
        else {
            return undefined;
        }
    };
    CardStock.prototype.addCardElementToParent = function (cardElement, settings) {
        var _a;
        var parent = (_a = settings === null || settings === void 0 ? void 0 : settings.forceToElement) !== null && _a !== void 0 ? _a : this.element;
        if ((settings === null || settings === void 0 ? void 0 : settings.index) === null || (settings === null || settings === void 0 ? void 0 : settings.index) === undefined || !parent.children.length || (settings === null || settings === void 0 ? void 0 : settings.index) >= parent.children.length) {
            parent.appendChild(cardElement);
        }
        else {
            parent.insertBefore(cardElement, parent.children[settings.index]);
        }
    };
    CardStock.prototype.moveFromOtherStock = function (card, cardElement, animation, settings) {
        var promise;
        var element = animation.fromStock.contains(card) ? this.manager.getCardElement(card) : animation.fromStock.element;
        var fromRect = element.getBoundingClientRect();
        this.addCardElementToParent(cardElement, settings);
        this.removeSelectionClassesFromElement(cardElement);
        promise = this.animationFromElement(cardElement, fromRect, {
            originalSide: animation.originalSide,
            rotationDelta: animation.rotationDelta,
            animation: animation.animation,
        });
        // in the case the card was move inside the same stock we don't remove it
        if (animation.fromStock && animation.fromStock != this) {
            animation.fromStock.removeCard(card);
        }
        if (!promise) {
            console.warn("CardStock.moveFromOtherStock didn't return a Promise");
            promise = Promise.resolve(false);
        }
        return promise;
    };
    CardStock.prototype.moveFromElement = function (card, cardElement, animation, settings) {
        var promise;
        this.addCardElementToParent(cardElement, settings);
        if (animation) {
            if (animation.fromStock) {
                promise = this.animationFromElement(cardElement, animation.fromStock.element.getBoundingClientRect(), {
                    originalSide: animation.originalSide,
                    rotationDelta: animation.rotationDelta,
                    animation: animation.animation,
                });
                animation.fromStock.removeCard(card);
            }
            else if (animation.fromElement) {
                promise = this.animationFromElement(cardElement, animation.fromElement.getBoundingClientRect(), {
                    originalSide: animation.originalSide,
                    rotationDelta: animation.rotationDelta,
                    animation: animation.animation,
                });
            }
        }
        else {
            promise = Promise.resolve(false);
        }
        if (!promise) {
            console.warn("CardStock.moveFromElement didn't return a Promise");
            promise = Promise.resolve(false);
        }
        return promise;
    };
    /**
     * Add an array of cards to the stock.
     *
     * @param cards the cards to add
     * @param animation a `CardAnimation` object
     * @param settings a `AddCardSettings` object
     * @param shift if number, the number of milliseconds between each card. if true, chain animations
     */
    CardStock.prototype.addCards = function (cards, animation, settings, shift) {
        if (shift === void 0) { shift = false; }
        return __awaiter(this, void 0, void 0, function () {
            var promises, result, others, _loop_2, i, results;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!this.manager.animationsActive()) {
                            shift = false;
                        }
                        promises = [];
                        if (!(shift === true)) return [3 /*break*/, 4];
                        if (!cards.length) return [3 /*break*/, 3];
                        return [4 /*yield*/, this.addCard(cards[0], animation, settings)];
                    case 1:
                        result = _a.sent();
                        return [4 /*yield*/, this.addCards(cards.slice(1), animation, settings, shift)];
                    case 2:
                        others = _a.sent();
                        return [2 /*return*/, result || others];
                    case 3: return [3 /*break*/, 5];
                    case 4:
                        if (typeof shift === 'number') {
                            _loop_2 = function (i) {
                                setTimeout(function () { return promises.push(_this.addCard(cards[i], animation, settings)); }, i * shift);
                            };
                            for (i = 0; i < cards.length; i++) {
                                _loop_2(i);
                            }
                        }
                        else {
                            promises = cards.map(function (card) { return _this.addCard(card, animation, settings); });
                        }
                        _a.label = 5;
                    case 5: return [4 /*yield*/, Promise.all(promises)];
                    case 6:
                        results = _a.sent();
                        return [2 /*return*/, results.some(function (result) { return result; })];
                }
            });
        });
    };
    /**
     * Remove a card from the stock.
     *
     * @param card the card to remove
     * @param settings a `RemoveCardSettings` object
     */
    CardStock.prototype.removeCard = function (card, settings) {
        if (this.contains(card) && this.element.contains(this.getCardElement(card))) {
            this.manager.removeCard(card, settings);
        }
        this.cardRemoved(card, settings);
    };
    /**
     * Notify the stock that a card is removed.
     *
     * @param card the card to remove
     * @param settings a `RemoveCardSettings` object
     */
    CardStock.prototype.cardRemoved = function (card, settings) {
        var _this = this;
        var index = this.cards.findIndex(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
        if (index !== -1) {
            this.cards.splice(index, 1);
        }
        if (this.selectedCards.find(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); })) {
            this.unselectCard(card);
        }
    };
    /**
     * Remove a set of card from the stock.
     *
     * @param cards the cards to remove
     * @param settings a `RemoveCardSettings` object
     */
    CardStock.prototype.removeCards = function (cards, settings) {
        var _this = this;
        cards.forEach(function (card) { return _this.removeCard(card, settings); });
    };
    /**
     * Remove all cards from the stock.
     * @param settings a `RemoveCardSettings` object
     */
    CardStock.prototype.removeAll = function (settings) {
        var _this = this;
        var cards = this.getCards(); // use a copy of the array as we iterate and modify it at the same time
        cards.forEach(function (card) { return _this.removeCard(card, settings); });
    };
    /**
     * Set if the stock is selectable, and if yes if it can be multiple.
     * If set to 'none', it will unselect all selected cards.
     *
     * @param selectionMode the selection mode
     * @param selectableCards the selectable cards (all if unset). Calls `setSelectableCards` method
     */
    CardStock.prototype.setSelectionMode = function (selectionMode, selectableCards) {
        var _this = this;
        if (selectionMode !== this.selectionMode) {
            this.unselectAll(true);
        }
        this.cards.forEach(function (card) { return _this.setSelectableCard(card, selectionMode != 'none'); });
        this.element.classList.toggle('bga-cards_selectable-stock', selectionMode != 'none');
        this.selectionMode = selectionMode;
        if (selectionMode === 'none') {
            this.getCards().forEach(function (card) { return _this.removeSelectionClasses(card); });
        }
        else {
            this.setSelectableCards(selectableCards !== null && selectableCards !== void 0 ? selectableCards : this.getCards());
        }
    };
    CardStock.prototype.setSelectableCard = function (card, selectable) {
        if (this.selectionMode === 'none') {
            return;
        }
        var element = this.getCardElement(card);
        var selectableCardsClass = this.getSelectableCardClass();
        var unselectableCardsClass = this.getUnselectableCardClass();
        if (selectableCardsClass) {
            element === null || element === void 0 ? void 0 : element.classList.toggle(selectableCardsClass, selectable);
        }
        if (unselectableCardsClass) {
            element === null || element === void 0 ? void 0 : element.classList.toggle(unselectableCardsClass, !selectable);
        }
        if (!selectable && this.isSelected(card)) {
            this.unselectCard(card, true);
        }
    };
    /**
     * Set the selectable class for each card.
     *
     * @param selectableCards the selectable cards. If unset, all cards are marked selectable. Default unset.
     */
    CardStock.prototype.setSelectableCards = function (selectableCards) {
        var _this = this;
        if (this.selectionMode === 'none') {
            return;
        }
        var selectableCardsIds = (selectableCards !== null && selectableCards !== void 0 ? selectableCards : this.getCards()).map(function (card) { return _this.manager.getId(card); });
        this.cards.forEach(function (card) {
            return _this.setSelectableCard(card, selectableCardsIds.includes(_this.manager.getId(card)));
        });
    };
    /**
     * Set selected state to a card.
     *
     * @param card the card to select
     */
    CardStock.prototype.selectCard = function (card, silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        if (this.selectionMode == 'none') {
            return;
        }
        var element = this.getCardElement(card);
        var selectableCardsClass = this.getSelectableCardClass();
        if (!element || !element.classList.contains(selectableCardsClass)) {
            return;
        }
        if (this.selectionMode === 'single') {
            this.cards.filter(function (c) { return _this.manager.getId(c) != _this.manager.getId(card); }).forEach(function (c) { return _this.unselectCard(c, true); });
        }
        var selectedCardsClass = this.getSelectedCardClass();
        element.classList.add(selectedCardsClass);
        this.selectedCards.push(card);
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), card);
        }
    };
    /**
     * Set unselected state to a card.
     *
     * @param card the card to unselect
     */
    CardStock.prototype.unselectCard = function (card, silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        var element = this.getCardElement(card);
        var selectedCardsClass = this.getSelectedCardClass();
        element === null || element === void 0 ? void 0 : element.classList.remove(selectedCardsClass);
        var index = this.selectedCards.findIndex(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
        if (index !== -1) {
            this.selectedCards.splice(index, 1);
        }
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), card);
        }
    };
    /**
     * Select all cards
     */
    CardStock.prototype.selectAll = function (silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        if (this.selectionMode == 'none') {
            return;
        }
        this.cards.forEach(function (c) { return _this.selectCard(c, true); });
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), null);
        }
    };
    /**
     * Unelect all cards
     */
    CardStock.prototype.unselectAll = function (silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        var cards = this.getCards(); // use a copy of the array as we iterate and modify it at the same time
        cards.forEach(function (c) { return _this.unselectCard(c, true); });
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), null);
        }
    };
    CardStock.prototype.bindClick = function () {
        var _this = this;
        var _a;
        (_a = this.element) === null || _a === void 0 ? void 0 : _a.addEventListener('click', function (event) {
            var cardDiv = event.target.closest('.card');
            if (!cardDiv) {
                return;
            }
            var card = _this.cards.find(function (c) { return _this.manager.getId(c) == cardDiv.id; });
            if (!card) {
                return;
            }
            _this.cardClick(card);
        });
    };
    CardStock.prototype.cardClick = function (card) {
        var _this = this;
        var _a;
        if (this.selectionMode != 'none') {
            var alreadySelected = this.selectedCards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
            if (alreadySelected) {
                this.unselectCard(card);
            }
            else {
                this.selectCard(card);
            }
        }
        (_a = this.onCardClick) === null || _a === void 0 ? void 0 : _a.call(this, card);
    };
    /**
     * @param element The element to animate. The element is added to the destination stock before the animation starts.
     * @param fromElement The HTMLElement to animate from.
     */
    CardStock.prototype.animationFromElement = function (element, fromRect, settings) {
        var _a;
        return __awaiter(this, void 0, void 0, function () {
            var side, cardSides_1, animation, result;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        side = element.dataset.side;
                        if (settings.originalSide && settings.originalSide != side) {
                            cardSides_1 = element.getElementsByClassName('card-sides')[0];
                            cardSides_1.style.transition = 'none';
                            element.dataset.side = settings.originalSide;
                            setTimeout(function () {
                                cardSides_1.style.transition = null;
                                element.dataset.side = side;
                            });
                        }
                        animation = settings.animation;
                        if (animation) {
                            animation.settings.element = element;
                            animation.settings.fromRect = fromRect;
                        }
                        else {
                            animation = new BgaSlideAnimation({ element: element, fromRect: fromRect });
                        }
                        return [4 /*yield*/, this.manager.animationManager.play(animation)];
                    case 1:
                        result = _b.sent();
                        return [2 /*return*/, (_a = result === null || result === void 0 ? void 0 : result.played) !== null && _a !== void 0 ? _a : false];
                }
            });
        });
    };
    /**
     * Set the card to its front (visible) or back (not visible) side.
     *
     * @param card the card informations
     */
    CardStock.prototype.setCardVisible = function (card, visible, settings) {
        this.manager.setCardVisible(card, visible, settings);
    };
    /**
     * Flips the card.
     *
     * @param card the card informations
     */
    CardStock.prototype.flipCard = function (card, settings) {
        this.manager.flipCard(card, settings);
    };
    /**
     * @returns the class to apply to selectable cards. Use class from manager is unset.
     */
    CardStock.prototype.getSelectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectableCardClass) === undefined ? this.manager.getSelectableCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectableCardClass;
    };
    /**
     * @returns the class to apply to selectable cards. Use class from manager is unset.
     */
    CardStock.prototype.getUnselectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.unselectableCardClass) === undefined ? this.manager.getUnselectableCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.unselectableCardClass;
    };
    /**
     * @returns the class to apply to selected cards. Use class from manager is unset.
     */
    CardStock.prototype.getSelectedCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectedCardClass) === undefined ? this.manager.getSelectedCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectedCardClass;
    };
    CardStock.prototype.removeSelectionClasses = function (card) {
        this.removeSelectionClassesFromElement(this.getCardElement(card));
    };
    CardStock.prototype.removeSelectionClassesFromElement = function (cardElement) {
        var selectableCardsClass = this.getSelectableCardClass();
        var unselectableCardsClass = this.getUnselectableCardClass();
        var selectedCardsClass = this.getSelectedCardClass();
        cardElement === null || cardElement === void 0 ? void 0 : cardElement.classList.remove(selectableCardsClass, unselectableCardsClass, selectedCardsClass);
    };
    return CardStock;
}());
/**
 * A stock with manually placed cards
 */
var ManualPositionStock = /** @class */ (function (_super) {
    __extends(ManualPositionStock, _super);
    /**
     * @param manager the card manager
     * @param element the stock element (should be an empty HTML Element)
     */
    function ManualPositionStock(manager, element, settings, updateDisplay) {
        var _this = _super.call(this, manager, element, settings) || this;
        _this.manager = manager;
        _this.element = element;
        _this.updateDisplay = updateDisplay;
        element.classList.add('manual-position-stock');
        return _this;
    }
    /**
     * Add a card to the stock.
     *
     * @param card the card to add
     * @param animation a `CardAnimation` object
     * @param settings a `AddCardSettings` object
     * @returns the promise when the animation is done (true if it was animated, false if it wasn't)
     */
    ManualPositionStock.prototype.addCard = function (card, animation, settings) {
        var promise = _super.prototype.addCard.call(this, card, animation, settings);
        this.updateDisplay(this.element, this.getCards(), card, this);
        return promise;
    };
    ManualPositionStock.prototype.cardRemoved = function (card) {
        _super.prototype.cardRemoved.call(this, card);
        this.updateDisplay(this.element, this.getCards(), card, this);
    };
    return ManualPositionStock;
}(CardStock));
var AllVisibleDeck = /** @class */ (function (_super) {
    __extends(AllVisibleDeck, _super);
    function AllVisibleDeck(manager, element, settings) {
        var _this = this;
        var _a;
        _this = _super.call(this, manager, element, settings) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('all-visible-deck');
        var cardWidth = _this.manager.getCardWidth();
        var cardHeight = _this.manager.getCardHeight();
        if (cardWidth && cardHeight) {
            _this.element.style.setProperty('--width', "".concat(cardWidth, "px"));
            _this.element.style.setProperty('--height', "".concat(cardHeight, "px"));
        }
        else {
            throw new Error("You need to set cardWidth and cardHeight in the card manager to use Deck.");
        }
        element.style.setProperty('--shift', (_a = settings.shift) !== null && _a !== void 0 ? _a : '3px');
        return _this;
    }
    AllVisibleDeck.prototype.addCard = function (card, animation, settings) {
        var promise;
        var order = this.cards.length;
        promise = _super.prototype.addCard.call(this, card, animation, settings);
        var cardId = this.manager.getId(card);
        var cardDiv = document.getElementById(cardId);
        cardDiv.style.setProperty('--order', '' + order);
        this.element.style.setProperty('--tile-count', '' + this.cards.length);
        return promise;
    };
    /**
     * Set opened state. If true, all cards will be entirely visible.
     *
     * @param opened indicate if deck must be always opened. If false, will open only on hover/touch
     */
    AllVisibleDeck.prototype.setOpened = function (opened) {
        this.element.classList.toggle('opened', opened);
    };
    AllVisibleDeck.prototype.cardRemoved = function (card) {
        var _this = this;
        _super.prototype.cardRemoved.call(this, card);
        this.cards.forEach(function (c, index) {
            var cardId = _this.manager.getId(c);
            var cardDiv = document.getElementById(cardId);
            cardDiv.style.setProperty('--order', '' + index);
        });
        this.element.style.setProperty('--tile-count', '' + this.cards.length);
    };
    return AllVisibleDeck;
}(CardStock));
/**
 * A stock to make cards disappear (to automatically remove discarded cards, or to represent a bag)
 */
var VoidStock = /** @class */ (function (_super) {
    __extends(VoidStock, _super);
    /**
     * @param manager the card manager
     * @param element the stock element (should be an empty HTML Element)
     */
    function VoidStock(manager, element) {
        var _this = _super.call(this, manager, element) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('void-stock');
        return _this;
    }
    /**
     * Add a card to the stock.
     *
     * @param card the card to add
     * @param animation a `CardAnimation` object
     * @param settings a `AddCardToVoidStockSettings` object
     * @returns the promise when the animation is done (true if it was animated, false if it wasn't)
     */
    VoidStock.prototype.addCard = function (card, animation, settings) {
        var _this = this;
        var _a;
        var promise = _super.prototype.addCard.call(this, card, animation, settings);
        // center the element
        var cardElement = this.getCardElement(card);
        var originalLeft = cardElement.style.left;
        var originalTop = cardElement.style.top;
        cardElement.style.left = "".concat((this.element.clientWidth - cardElement.clientWidth) / 2, "px");
        cardElement.style.top = "".concat((this.element.clientHeight - cardElement.clientHeight) / 2, "px");
        if (!promise) {
            console.warn("VoidStock.addCard didn't return a Promise");
            promise = Promise.resolve(false);
        }
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.remove) !== null && _a !== void 0 ? _a : true) {
            return promise.then(function (result) {
                _this.removeCard(card);
                return result;
            });
        }
        else {
            cardElement.style.left = originalLeft;
            cardElement.style.top = originalTop;
            return promise;
        }
    };
    return VoidStock;
}(CardStock));
/**
 * A basic stock for a list of cards, based on flex.
 */
var LineStock = /** @class */ (function (_super) {
    __extends(LineStock, _super);
    /**
     * @param manager the card manager
     * @param element the stock element (should be an empty HTML Element)
     * @param settings a `LineStockSettings` object
     */
    function LineStock(manager, element, settings) {
        var _this = this;
        var _a, _b, _c, _d;
        _this = _super.call(this, manager, element, settings) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('line-stock');
        element.dataset.center = ((_a = settings === null || settings === void 0 ? void 0 : settings.center) !== null && _a !== void 0 ? _a : true).toString();
        element.style.setProperty('--wrap', (_b = settings === null || settings === void 0 ? void 0 : settings.wrap) !== null && _b !== void 0 ? _b : 'wrap');
        element.style.setProperty('--direction', (_c = settings === null || settings === void 0 ? void 0 : settings.direction) !== null && _c !== void 0 ? _c : 'row');
        element.style.setProperty('--gap', (_d = settings === null || settings === void 0 ? void 0 : settings.gap) !== null && _d !== void 0 ? _d : '8px');
        return _this;
    }
    return LineStock;
}(CardStock));
/**
 * A stock with fixed slots (some can be empty)
 */
var SlotStock = /** @class */ (function (_super) {
    __extends(SlotStock, _super);
    /**
     * @param manager the card manager
     * @param element the stock element (should be an empty HTML Element)
     * @param settings a `SlotStockSettings` object
     */
    function SlotStock(manager, element, settings) {
        var _this = this;
        var _a, _b;
        _this = _super.call(this, manager, element, settings) || this;
        _this.manager = manager;
        _this.element = element;
        _this.slotsIds = [];
        _this.slots = [];
        element.classList.add('slot-stock');
        _this.mapCardToSlot = settings.mapCardToSlot;
        _this.slotsIds = (_a = settings.slotsIds) !== null && _a !== void 0 ? _a : [];
        _this.slotClasses = (_b = settings.slotClasses) !== null && _b !== void 0 ? _b : [];
        _this.slotsIds.forEach(function (slotId) {
            _this.createSlot(slotId);
        });
        return _this;
    }
    SlotStock.prototype.createSlot = function (slotId) {
        var _a;
        this.slots[slotId] = document.createElement("div");
        this.slots[slotId].dataset.slotId = slotId;
        this.element.appendChild(this.slots[slotId]);
        (_a = this.slots[slotId].classList).add.apply(_a, __spreadArray(['slot'], this.slotClasses, true));
    };
    /**
     * Add a card to the stock.
     *
     * @param card the card to add
     * @param animation a `CardAnimation` object
     * @param settings a `AddCardToSlotSettings` object
     * @returns the promise when the animation is done (true if it was animated, false if it wasn't)
     */
    SlotStock.prototype.addCard = function (card, animation, settings) {
        var _a, _b;
        var slotId = (_a = settings === null || settings === void 0 ? void 0 : settings.slot) !== null && _a !== void 0 ? _a : (_b = this.mapCardToSlot) === null || _b === void 0 ? void 0 : _b.call(this, card);
        if (slotId === undefined) {
            throw new Error("Impossible to add card to slot : no SlotId. Add slotId to settings or set mapCardToSlot to SlotCard constructor.");
        }
        if (!this.slots[slotId]) {
            throw new Error("Impossible to add card to slot \"".concat(slotId, "\" : slot \"").concat(slotId, "\" doesn't exists."));
        }
        var newSettings = __assign(__assign({}, settings), { forceToElement: this.slots[slotId] });
        return _super.prototype.addCard.call(this, card, animation, newSettings);
    };
    /**
     * Change the slots ids. Will empty the stock before re-creating the slots.
     *
     * @param slotsIds the new slotsIds. Will replace the old ones.
     */
    SlotStock.prototype.setSlotsIds = function (slotsIds) {
        var _this = this;
        if (slotsIds.length == this.slotsIds.length && slotsIds.every(function (slotId, index) { return _this.slotsIds[index] === slotId; })) {
            // no change
            return;
        }
        this.removeAll();
        this.element.innerHTML = '';
        this.slotsIds = slotsIds !== null && slotsIds !== void 0 ? slotsIds : [];
        this.slotsIds.forEach(function (slotId) {
            _this.createSlot(slotId);
        });
    };
    SlotStock.prototype.canAddCard = function (card, settings) {
        var _a, _b;
        if (!this.contains(card)) {
            return true;
        }
        else {
            var currentCardSlot = this.getCardElement(card).closest('.slot').dataset.slotId;
            var slotId = (_a = settings === null || settings === void 0 ? void 0 : settings.slot) !== null && _a !== void 0 ? _a : (_b = this.mapCardToSlot) === null || _b === void 0 ? void 0 : _b.call(this, card);
            return currentCardSlot != slotId;
        }
    };
    /**
     * Swap cards inside the slot stock.
     *
     * @param cards the cards to swap
     * @param settings for `updateInformations` and `selectable`
     */
    SlotStock.prototype.swapCards = function (cards, settings) {
        var _this = this;
        if (!this.mapCardToSlot) {
            throw new Error('You need to define SlotStock.mapCardToSlot to use SlotStock.swapCards');
        }
        var promises = [];
        var elements = cards.map(function (card) { return _this.manager.getCardElement(card); });
        var elementsRects = elements.map(function (element) { return element.getBoundingClientRect(); });
        var cssPositions = elements.map(function (element) { return element.style.position; });
        // we set to absolute so it doesn't mess with slide coordinates when 2 div are at the same place
        elements.forEach(function (element) { return element.style.position = 'absolute'; });
        cards.forEach(function (card, index) {
            var _a, _b;
            var cardElement = elements[index];
            var promise;
            var slotId = (_a = _this.mapCardToSlot) === null || _a === void 0 ? void 0 : _a.call(_this, card);
            _this.slots[slotId].appendChild(cardElement);
            cardElement.style.position = cssPositions[index];
            var cardIndex = _this.cards.findIndex(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
            if (cardIndex !== -1) {
                _this.cards.splice(cardIndex, 1, card);
            }
            if ((_b = settings === null || settings === void 0 ? void 0 : settings.updateInformations) !== null && _b !== void 0 ? _b : true) { // after splice/push
                _this.manager.updateCardInformations(card);
            }
            _this.removeSelectionClassesFromElement(cardElement);
            promise = _this.animationFromElement(cardElement, elementsRects[index], {});
            if (!promise) {
                console.warn("CardStock.animationFromElement didn't return a Promise");
                promise = Promise.resolve(false);
            }
            promise.then(function () { var _a; return _this.setSelectableCard(card, (_a = settings === null || settings === void 0 ? void 0 : settings.selectable) !== null && _a !== void 0 ? _a : true); });
            promises.push(promise);
        });
        return Promise.all(promises);
    };
    return SlotStock;
}(LineStock));
var SlideAndBackAnimation = /** @class */ (function (_super) {
    __extends(SlideAndBackAnimation, _super);
    function SlideAndBackAnimation(manager, element, tempElement) {
        var distance = (manager.getCardWidth() + manager.getCardHeight()) / 2;
        var angle = Math.random() * Math.PI * 2;
        var fromDelta = {
            x: distance * Math.cos(angle),
            y: distance * Math.sin(angle),
        };
        return _super.call(this, {
            animations: [
                new BgaSlideToAnimation({ element: element, fromDelta: fromDelta, duration: 250 }),
                new BgaSlideAnimation({ element: element, fromDelta: fromDelta, duration: 250, animationEnd: tempElement ? (function () { return element.remove(); }) : undefined }),
            ]
        }) || this;
    }
    return SlideAndBackAnimation;
}(BgaCumulatedAnimation));
/**
 * Abstract stock to represent a deck. (pile of cards, with a fake 3d effect of thickness). *
 * Needs cardWidth and cardHeight to be set in the card manager.
 */
var Deck = /** @class */ (function (_super) {
    __extends(Deck, _super);
    function Deck(manager, element, settings) {
        var _this = this;
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k;
        _this = _super.call(this, manager, element) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('deck');
        var cardWidth = _this.manager.getCardWidth();
        var cardHeight = _this.manager.getCardHeight();
        if (cardWidth && cardHeight) {
            _this.element.style.setProperty('--width', "".concat(cardWidth, "px"));
            _this.element.style.setProperty('--height', "".concat(cardHeight, "px"));
        }
        else {
            throw new Error("You need to set cardWidth and cardHeight in the card manager to use Deck.");
        }
        _this.thicknesses = (_a = settings.thicknesses) !== null && _a !== void 0 ? _a : [0, 2, 5, 10, 20, 30];
        _this.setCardNumber((_b = settings.cardNumber) !== null && _b !== void 0 ? _b : 52);
        _this.autoUpdateCardNumber = (_c = settings.autoUpdateCardNumber) !== null && _c !== void 0 ? _c : true;
        _this.autoRemovePreviousCards = (_d = settings.autoRemovePreviousCards) !== null && _d !== void 0 ? _d : true;
        var shadowDirection = (_e = settings.shadowDirection) !== null && _e !== void 0 ? _e : 'bottom-right';
        var shadowDirectionSplit = shadowDirection.split('-');
        var xShadowShift = shadowDirectionSplit.includes('right') ? 1 : (shadowDirectionSplit.includes('left') ? -1 : 0);
        var yShadowShift = shadowDirectionSplit.includes('bottom') ? 1 : (shadowDirectionSplit.includes('top') ? -1 : 0);
        _this.element.style.setProperty('--xShadowShift', '' + xShadowShift);
        _this.element.style.setProperty('--yShadowShift', '' + yShadowShift);
        if (settings.topCard) {
            _this.addCard(settings.topCard, undefined);
        }
        else if (settings.cardNumber > 0) {
            console.warn("Deck is defined with ".concat(settings.cardNumber, " cards but no top card !"));
        }
        if (settings.counter && ((_f = settings.counter.show) !== null && _f !== void 0 ? _f : true)) {
            if (settings.cardNumber === null || settings.cardNumber === undefined) {
                throw new Error("You need to set cardNumber if you want to show the counter");
            }
            else {
                _this.createCounter((_g = settings.counter.position) !== null && _g !== void 0 ? _g : 'bottom', (_h = settings.counter.extraClasses) !== null && _h !== void 0 ? _h : 'round', settings.counter.counterId);
                if ((_j = settings.counter) === null || _j === void 0 ? void 0 : _j.hideWhenEmpty) {
                    _this.element.querySelector('.bga-cards_deck-counter').classList.add('hide-when-empty');
                }
            }
        }
        _this.setCardNumber((_k = settings.cardNumber) !== null && _k !== void 0 ? _k : 52);
        return _this;
    }
    Deck.prototype.createCounter = function (counterPosition, extraClasses, counterId) {
        var left = counterPosition.includes('right') ? 100 : (counterPosition.includes('left') ? 0 : 50);
        var top = counterPosition.includes('bottom') ? 100 : (counterPosition.includes('top') ? 0 : 50);
        this.element.style.setProperty('--bga-cards-deck-left', "".concat(left, "%"));
        this.element.style.setProperty('--bga-cards-deck-top', "".concat(top, "%"));
        this.element.insertAdjacentHTML('beforeend', "\n            <div ".concat(counterId ? "id=\"".concat(counterId, "\"") : '', " class=\"bga-cards_deck-counter ").concat(extraClasses, "\"></div>\n        "));
    };
    /**
     * Get the the cards number.
     *
     * @returns the cards number
     */
    Deck.prototype.getCardNumber = function () {
        return this.cardNumber;
    };
    /**
     * Set the the cards number.
     *
     * @param cardNumber the cards number
     */
    Deck.prototype.setCardNumber = function (cardNumber, topCard) {
        var _this = this;
        if (topCard === void 0) { topCard = null; }
        if (topCard) {
            this.addCard(topCard);
        }
        this.cardNumber = cardNumber;
        this.element.dataset.empty = (this.cardNumber == 0).toString();
        var thickness = 0;
        this.thicknesses.forEach(function (threshold, index) {
            if (_this.cardNumber >= threshold) {
                thickness = index;
            }
        });
        this.element.style.setProperty('--thickness', "".concat(thickness, "px"));
        var counterDiv = this.element.querySelector('.bga-cards_deck-counter');
        if (counterDiv) {
            counterDiv.innerHTML = "".concat(cardNumber);
        }
    };
    Deck.prototype.addCard = function (card, animation, settings) {
        var _this = this;
        var _a, _b;
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.autoUpdateCardNumber) !== null && _a !== void 0 ? _a : this.autoUpdateCardNumber) {
            this.setCardNumber(this.cardNumber + 1);
        }
        var promise = _super.prototype.addCard.call(this, card, animation, settings);
        if ((_b = settings === null || settings === void 0 ? void 0 : settings.autoRemovePreviousCards) !== null && _b !== void 0 ? _b : this.autoRemovePreviousCards) {
            promise.then(function () {
                var previousCards = _this.getCards().slice(0, -1); // remove last cards
                _this.removeCards(previousCards, { autoUpdateCardNumber: false });
            });
        }
        return promise;
    };
    Deck.prototype.cardRemoved = function (card, settings) {
        var _a;
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.autoUpdateCardNumber) !== null && _a !== void 0 ? _a : this.autoUpdateCardNumber) {
            this.setCardNumber(this.cardNumber - 1);
        }
        _super.prototype.cardRemoved.call(this, card, settings);
    };
    Deck.prototype.getTopCard = function () {
        var cards = this.getCards();
        return cards.length ? cards[cards.length - 1] : null;
    };
    /**
     * Shows a shuffle animation on the deck
     *
     * @param animatedCardsMax number of animated cards for shuffle animation.
     * @param fakeCardSetter a function to generate a fake card for animation. Required if the card id is not based on a numerci `id` field, or if you want to set custom card back
     * @returns promise when animation ends
     */
    Deck.prototype.shuffle = function (animatedCardsMax, fakeCardSetter) {
        if (animatedCardsMax === void 0) { animatedCardsMax = 10; }
        return __awaiter(this, void 0, void 0, function () {
            var animatedCards, elements, i, newCard, newElement;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!this.manager.animationsActive()) {
                            return [2 /*return*/, Promise.resolve(false)]; // we don't execute as it's just visual temporary stuff
                        }
                        animatedCards = Math.min(10, animatedCardsMax, this.getCardNumber());
                        if (!(animatedCards > 1)) return [3 /*break*/, 2];
                        elements = [this.getCardElement(this.getTopCard())];
                        for (i = elements.length; i <= animatedCards; i++) {
                            newCard = {};
                            if (fakeCardSetter) {
                                fakeCardSetter(newCard, i);
                            }
                            else {
                                newCard.id = -100000 + i;
                            }
                            newElement = this.manager.createCardElement(newCard, false);
                            newElement.dataset.tempCardForShuffleAnimation = 'true';
                            this.element.prepend(newElement);
                            elements.push(newElement);
                        }
                        return [4 /*yield*/, this.manager.animationManager.playWithDelay(elements.map(function (element) { return new SlideAndBackAnimation(_this.manager, element, element.dataset.tempCardForShuffleAnimation == 'true'); }), 50)];
                    case 1:
                        _a.sent();
                        return [2 /*return*/, true];
                    case 2: return [2 /*return*/, Promise.resolve(false)];
                }
            });
        });
    };
    return Deck;
}(CardStock));
var CardManager = /** @class */ (function () {
    /**
     * @param game the BGA game class, usually it will be `this`
     * @param settings: a `CardManagerSettings` object
     */
    function CardManager(game, settings) {
        var _a;
        this.game = game;
        this.settings = settings;
        this.stocks = [];
        this.updateFrontTimeoutId = [];
        this.updateBackTimeoutId = [];
        this.animationManager = (_a = settings.animationManager) !== null && _a !== void 0 ? _a : new AnimationManager(game);
    }
    /**
     * Returns if the animations are active. Animation aren't active when the window is not visible (`document.visibilityState === 'hidden'`), or `game.instantaneousMode` is true.
     *
     * @returns if the animations are active.
     */
    CardManager.prototype.animationsActive = function () {
        return this.animationManager.animationsActive();
    };
    CardManager.prototype.addStock = function (stock) {
        this.stocks.push(stock);
    };
    /**
     * @param card the card informations
     * @return the id for a card
     */
    CardManager.prototype.getId = function (card) {
        var _a, _b, _c;
        return (_c = (_b = (_a = this.settings).getId) === null || _b === void 0 ? void 0 : _b.call(_a, card)) !== null && _c !== void 0 ? _c : "card-".concat(card.id);
    };
    CardManager.prototype.createCardElement = function (card, visible) {
        var _a, _b, _c, _d, _e, _f;
        if (visible === void 0) { visible = true; }
        var id = this.getId(card);
        var side = visible ? 'front' : 'back';
        if (this.getCardElement(card)) {
            throw new Error('This card already exists ' + JSON.stringify(card));
        }
        var element = document.createElement("div");
        element.id = id;
        element.dataset.side = '' + side;
        element.innerHTML = "\n            <div class=\"card-sides\">\n                <div id=\"".concat(id, "-front\" class=\"card-side front\">\n                </div>\n                <div id=\"").concat(id, "-back\" class=\"card-side back\">\n                </div>\n            </div>\n        ");
        element.classList.add('card');
        document.body.appendChild(element);
        (_b = (_a = this.settings).setupDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element);
        (_d = (_c = this.settings).setupFrontDiv) === null || _d === void 0 ? void 0 : _d.call(_c, card, element.getElementsByClassName('front')[0]);
        (_f = (_e = this.settings).setupBackDiv) === null || _f === void 0 ? void 0 : _f.call(_e, card, element.getElementsByClassName('back')[0]);
        document.body.removeChild(element);
        return element;
    };
    /**
     * @param card the card informations
     * @return the HTML element of an existing card
     */
    CardManager.prototype.getCardElement = function (card) {
        return document.getElementById(this.getId(card));
    };
    /**
     * Remove a card.
     *
     * @param card the card to remove
     * @param settings a `RemoveCardSettings` object
     */
    CardManager.prototype.removeCard = function (card, settings) {
        var _a;
        var id = this.getId(card);
        var div = document.getElementById(id);
        if (!div) {
            return false;
        }
        div.id = "deleted".concat(id);
        div.remove();
        // if the card is in a stock, notify the stock about removal
        (_a = this.getCardStock(card)) === null || _a === void 0 ? void 0 : _a.cardRemoved(card, settings);
        return true;
    };
    /**
     * Returns the stock containing the card.
     *
     * @param card the card informations
     * @return the stock containing the card
     */
    CardManager.prototype.getCardStock = function (card) {
        return this.stocks.find(function (stock) { return stock.contains(card); });
    };
    /**
     * Return if the card passed as parameter is suppose to be visible or not.
     * Use `isCardVisible` from settings if set, else will check if `card.type` is defined
     *
     * @param card the card informations
     * @return the visiblility of the card (true means front side should be displayed)
     */
    CardManager.prototype.isCardVisible = function (card) {
        var _a, _b, _c, _d;
        return (_c = (_b = (_a = this.settings).isCardVisible) === null || _b === void 0 ? void 0 : _b.call(_a, card)) !== null && _c !== void 0 ? _c : ((_d = card.type) !== null && _d !== void 0 ? _d : false);
    };
    /**
     * Set the card to its front (visible) or back (not visible) side.
     *
     * @param card the card informations
     * @param visible if the card is set to visible face. If unset, will use isCardVisible(card)
     * @param settings the flip params (to update the card in current stock)
     */
    CardManager.prototype.setCardVisible = function (card, visible, settings) {
        var _this = this;
        var _a, _b, _c, _d, _e, _f, _g, _h, _j;
        var element = this.getCardElement(card);
        if (!element) {
            return;
        }
        var isVisible = visible !== null && visible !== void 0 ? visible : this.isCardVisible(card);
        element.dataset.side = isVisible ? 'front' : 'back';
        var stringId = JSON.stringify(this.getId(card));
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.updateFront) !== null && _a !== void 0 ? _a : true) {
            if (this.updateFrontTimeoutId[stringId]) { // make sure there is not a delayed animation that will overwrite the last flip request
                clearTimeout(this.updateFrontTimeoutId[stringId]);
                delete this.updateFrontTimeoutId[stringId];
            }
            var updateFrontDelay = (_b = settings === null || settings === void 0 ? void 0 : settings.updateFrontDelay) !== null && _b !== void 0 ? _b : 500;
            if (!isVisible && updateFrontDelay > 0 && this.animationsActive()) {
                this.updateFrontTimeoutId[stringId] = setTimeout(function () { var _a, _b; return (_b = (_a = _this.settings).setupFrontDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element.getElementsByClassName('front')[0]); }, updateFrontDelay);
            }
            else {
                (_d = (_c = this.settings).setupFrontDiv) === null || _d === void 0 ? void 0 : _d.call(_c, card, element.getElementsByClassName('front')[0]);
            }
        }
        if ((_e = settings === null || settings === void 0 ? void 0 : settings.updateBack) !== null && _e !== void 0 ? _e : false) {
            if (this.updateBackTimeoutId[stringId]) { // make sure there is not a delayed animation that will overwrite the last flip request
                clearTimeout(this.updateBackTimeoutId[stringId]);
                delete this.updateBackTimeoutId[stringId];
            }
            var updateBackDelay = (_f = settings === null || settings === void 0 ? void 0 : settings.updateBackDelay) !== null && _f !== void 0 ? _f : 0;
            if (isVisible && updateBackDelay > 0 && this.animationsActive()) {
                this.updateBackTimeoutId[stringId] = setTimeout(function () { var _a, _b; return (_b = (_a = _this.settings).setupBackDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element.getElementsByClassName('back')[0]); }, updateBackDelay);
            }
            else {
                (_h = (_g = this.settings).setupBackDiv) === null || _h === void 0 ? void 0 : _h.call(_g, card, element.getElementsByClassName('back')[0]);
            }
        }
        if ((_j = settings === null || settings === void 0 ? void 0 : settings.updateData) !== null && _j !== void 0 ? _j : true) {
            // card data has changed
            var stock = this.getCardStock(card);
            var cards = stock.getCards();
            var cardIndex = cards.findIndex(function (c) { return _this.getId(c) === _this.getId(card); });
            if (cardIndex !== -1) {
                stock.cards.splice(cardIndex, 1, card);
            }
        }
    };
    /**
     * Flips the card.
     *
     * @param card the card informations
     * @param settings the flip params (to update the card in current stock)
     */
    CardManager.prototype.flipCard = function (card, settings) {
        var element = this.getCardElement(card);
        var currentlyVisible = element.dataset.side === 'front';
        this.setCardVisible(card, !currentlyVisible, settings);
    };
    /**
     * Update the card informations. Used when a card with just an id (back shown) should be revealed, with all data needed to populate the front.
     *
     * @param card the card informations
     */
    CardManager.prototype.updateCardInformations = function (card, settings) {
        var newSettings = __assign(__assign({}, (settings !== null && settings !== void 0 ? settings : {})), { updateData: true });
        this.setCardVisible(card, undefined, newSettings);
    };
    /**
     * @returns the card with set in the settings (undefined if unset)
     */
    CardManager.prototype.getCardWidth = function () {
        var _a;
        return (_a = this.settings) === null || _a === void 0 ? void 0 : _a.cardWidth;
    };
    /**
     * @returns the card height set in the settings (undefined if unset)
     */
    CardManager.prototype.getCardHeight = function () {
        var _a;
        return (_a = this.settings) === null || _a === void 0 ? void 0 : _a.cardHeight;
    };
    /**
     * @returns the class to apply to selectable cards. Default 'bga-cards_selectable-card'.
     */
    CardManager.prototype.getSelectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectableCardClass) === undefined ? 'bga-cards_selectable-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectableCardClass;
    };
    /**
     * @returns the class to apply to selectable cards. Default 'bga-cards_disabled-card'.
     */
    CardManager.prototype.getUnselectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.unselectableCardClass) === undefined ? 'bga-cards_disabled-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.unselectableCardClass;
    };
    /**
     * @returns the class to apply to selected cards. Default 'bga-cards_selected-card'.
     */
    CardManager.prototype.getSelectedCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectedCardClass) === undefined ? 'bga-cards_selected-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectedCardClass;
    };
    return CardManager;
}());
function sortFunction() {
    var sortedFields = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        sortedFields[_i] = arguments[_i];
    }
    return function (a, b) {
        for (var i = 0; i < sortedFields.length; i++) {
            var direction = 1;
            var field = sortedFields[i];
            if (field[0] == '-') {
                direction = -1;
                field = field.substring(1);
            }
            else if (field[0] == '+') {
                field = field.substring(1);
            }
            var type = typeof a[field];
            if (type === 'string') {
                var compare = a[field].localeCompare(b[field]);
                if (compare !== 0) {
                    return compare;
                }
            }
            else if (type === 'number') {
                var compare = (a[field] - b[field]) * direction;
                if (compare !== 0) {
                    return compare * direction;
                }
            }
        }
        return 0;
    };
}
/**
 * Jump to entry.
 */
var JumpToEntry = /** @class */ (function () {
    function JumpToEntry(
    /**
     * Label shown on the entry. For players, it's player name.
     */
    label, 
    /**
     * HTML Element id, to scroll into view when clicked.
     */
    targetId, 
    /**
     * Any element that is useful to customize the link.
     * Basic ones are 'color' and 'colorback'.
     */
    data) {
        if (data === void 0) { data = {}; }
        this.label = label;
        this.targetId = targetId;
        this.data = data;
    }
    return JumpToEntry;
}());
var JumpToManager = /** @class */ (function () {
    function JumpToManager(game, settings) {
        var _a, _b, _c;
        this.game = game;
        this.settings = settings;
        var entries = __spreadArray(__spreadArray([], ((_a = settings === null || settings === void 0 ? void 0 : settings.topEntries) !== null && _a !== void 0 ? _a : []), true), ((_b = settings === null || settings === void 0 ? void 0 : settings.playersEntries) !== null && _b !== void 0 ? _b : this.createEntries(Object.values(game.gamedatas.players))), true);
        this.createPlayerJumps(entries);
        var folded = (_c = settings === null || settings === void 0 ? void 0 : settings.defaultFolded) !== null && _c !== void 0 ? _c : false;
        if (settings === null || settings === void 0 ? void 0 : settings.localStorageFoldedKey) {
            var localStorageValue = localStorage.getItem(settings.localStorageFoldedKey);
            if (localStorageValue) {
                folded = localStorageValue == 'true';
            }
        }
        document.getElementById('bga-jump-to_controls').classList.toggle('folded', folded);
    }
    JumpToManager.prototype.createPlayerJumps = function (entries) {
        var _this = this;
        var _a, _b, _c, _d;
        document.getElementById("game_play_area_wrap").insertAdjacentHTML('afterend', "\n        <div id=\"bga-jump-to_controls\">        \n            <div id=\"bga-jump-to_toggle\" class=\"bga-jump-to_link ".concat((_b = (_a = this.settings) === null || _a === void 0 ? void 0 : _a.entryClasses) !== null && _b !== void 0 ? _b : '', " toggle\" style=\"--color: ").concat((_d = (_c = this.settings) === null || _c === void 0 ? void 0 : _c.toggleColor) !== null && _d !== void 0 ? _d : 'black', "\">\n                \u21D4\n            </div>\n        </div>"));
        document.getElementById("bga-jump-to_toggle").addEventListener('click', function () { return _this.jumpToggle(); });
        entries.forEach(function (entry) {
            var _a, _b, _c, _d, _e, _f, _g, _h, _j;
            var html = "<div id=\"bga-jump-to_".concat(entry.targetId, "\" class=\"bga-jump-to_link ").concat((_b = (_a = _this.settings) === null || _a === void 0 ? void 0 : _a.entryClasses) !== null && _b !== void 0 ? _b : '', "\">");
            if ((_d = (_c = _this.settings) === null || _c === void 0 ? void 0 : _c.showEye) !== null && _d !== void 0 ? _d : true) {
                html += "<div class=\"eye\"></div>";
            }
            if (((_f = (_e = _this.settings) === null || _e === void 0 ? void 0 : _e.showAvatar) !== null && _f !== void 0 ? _f : true) && ((_g = entry.data) === null || _g === void 0 ? void 0 : _g.id)) {
                var cssUrl = (_h = entry.data) === null || _h === void 0 ? void 0 : _h.avatarUrl;
                if (!cssUrl) {
                    var img = document.getElementById("avatar_".concat(entry.data.id));
                    var url = img === null || img === void 0 ? void 0 : img.src;
                    // ? Custom image : Bga Image
                    //url = url.replace('_32', url.indexOf('data/avatar/defaults') > 0 ? '' : '_184');
                    if (url) {
                        cssUrl = "url('".concat(url, "')");
                    }
                }
                if (cssUrl) {
                    html += "<div class=\"bga-jump-to_avatar\" style=\"--avatar-url: ".concat(cssUrl, ";\"></div>");
                }
            }
            html += "\n                <span class=\"bga-jump-to_label\">".concat(entry.label, "</span>\n            </div>");
            //
            document.getElementById("bga-jump-to_controls").insertAdjacentHTML('beforeend', html);
            var entryDiv = document.getElementById("bga-jump-to_".concat(entry.targetId));
            Object.getOwnPropertyNames((_j = entry.data) !== null && _j !== void 0 ? _j : []).forEach(function (key) {
                entryDiv.dataset[key] = entry.data[key];
                entryDiv.style.setProperty("--".concat(key), entry.data[key]);
            });
            entryDiv.addEventListener('click', function () { return _this.jumpTo(entry.targetId); });
        });
        var jumpDiv = document.getElementById("bga-jump-to_controls");
        jumpDiv.style.marginTop = "-".concat(Math.round(jumpDiv.getBoundingClientRect().height / 2), "px");
    };
    JumpToManager.prototype.jumpToggle = function () {
        var _a;
        var jumpControls = document.getElementById('bga-jump-to_controls');
        jumpControls.classList.toggle('folded');
        if ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.localStorageFoldedKey) {
            localStorage.setItem(this.settings.localStorageFoldedKey, jumpControls.classList.contains('folded').toString());
        }
    };
    JumpToManager.prototype.jumpTo = function (targetId) {
        document.getElementById(targetId).scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
    };
    JumpToManager.prototype.getOrderedPlayers = function (unorderedPlayers) {
        var _this = this;
        var players = unorderedPlayers.sort(function (a, b) { return Number(a.playerNo) - Number(b.playerNo); });
        var playerIndex = players.findIndex(function (player) { return Number(player.id) === Number(_this.game.player_id); });
        var orderedPlayers = playerIndex > 0 ? __spreadArray(__spreadArray([], players.slice(playerIndex), true), players.slice(0, playerIndex), true) : players;
        return orderedPlayers;
    };
    JumpToManager.prototype.createEntries = function (players) {
        var orderedPlayers = this.getOrderedPlayers(players);
        return orderedPlayers.map(function (player) { return new JumpToEntry(player.name, "player-table-".concat(player.id), {
            'color': '#' + player.color,
            'colorback': player.color_back ? '#' + player.color_back : null,
            'id': player.id,
        }); });
    };
    return JumpToManager;
}());
var BgaHelpButton = /** @class */ (function () {
    function BgaHelpButton() {
    }
    return BgaHelpButton;
}());
var BgaHelpPopinButton = /** @class */ (function (_super) {
    __extends(BgaHelpPopinButton, _super);
    function BgaHelpPopinButton(settings) {
        var _this = _super.call(this) || this;
        _this.settings = settings;
        return _this;
    }
    BgaHelpPopinButton.prototype.add = function (toElement) {
        var _a;
        var _this = this;
        var button = document.createElement('button');
        (_a = button.classList).add.apply(_a, __spreadArray(['bga-help_button', 'bga-help_popin-button'], (this.settings.buttonExtraClasses ? this.settings.buttonExtraClasses.split(/\s+/g) : []), false));
        button.innerHTML = "?";
        if (this.settings.buttonBackground) {
            button.style.setProperty('--background', this.settings.buttonBackground);
        }
        if (this.settings.buttonColor) {
            button.style.setProperty('--color', this.settings.buttonColor);
        }
        toElement.appendChild(button);
        button.addEventListener('click', function () { return _this.showHelp(); });
    };
    BgaHelpPopinButton.prototype.showHelp = function () {
        var _a, _b, _c;
        var popinDialog = new window.ebg.popindialog();
        popinDialog.create('bgaHelpDialog');
        popinDialog.setTitle(this.settings.title);
        popinDialog.setContent("<div id=\"help-dialog-content\">".concat((_a = this.settings.html) !== null && _a !== void 0 ? _a : '', "</div>"));
        (_c = (_b = this.settings).onPopinCreated) === null || _c === void 0 ? void 0 : _c.call(_b, document.getElementById('help-dialog-content'));
        popinDialog.show();
    };
    return BgaHelpPopinButton;
}(BgaHelpButton));
var BgaHelpExpandableButton = /** @class */ (function (_super) {
    __extends(BgaHelpExpandableButton, _super);
    function BgaHelpExpandableButton(settings) {
        var _this = _super.call(this) || this;
        _this.settings = settings;
        return _this;
    }
    BgaHelpExpandableButton.prototype.add = function (toElement) {
        var _a;
        var _this = this;
        var _b, _c, _d, _e, _f, _g, _h, _j;
        var folded = (_b = this.settings.defaultFolded) !== null && _b !== void 0 ? _b : true;
        if (this.settings.localStorageFoldedKey) {
            var localStorageValue = localStorage.getItem(this.settings.localStorageFoldedKey);
            if (localStorageValue) {
                folded = localStorageValue == 'true';
            }
        }
        var button = document.createElement('button');
        button.dataset.folded = folded.toString();
        (_a = button.classList).add.apply(_a, __spreadArray(['bga-help_button', 'bga-help_expandable-button'], (this.settings.buttonExtraClasses ? this.settings.buttonExtraClasses.split(/\s+/g) : []), false));
        button.innerHTML = "\n            <div class=\"bga-help_folded-content ".concat(((_c = this.settings.foldedContentExtraClasses) !== null && _c !== void 0 ? _c : '').split(/\s+/g), "\">").concat((_d = this.settings.foldedHtml) !== null && _d !== void 0 ? _d : '', "</div>\n            <div class=\"bga-help_unfolded-content  ").concat(((_e = this.settings.unfoldedContentExtraClasses) !== null && _e !== void 0 ? _e : '').split(/\s+/g), "\">").concat((_f = this.settings.unfoldedHtml) !== null && _f !== void 0 ? _f : '', "</div>\n        ");
        button.style.setProperty('--expanded-width', (_g = this.settings.expandedWidth) !== null && _g !== void 0 ? _g : 'auto');
        button.style.setProperty('--expanded-height', (_h = this.settings.expandedHeight) !== null && _h !== void 0 ? _h : 'auto');
        button.style.setProperty('--expanded-radius', (_j = this.settings.expandedRadius) !== null && _j !== void 0 ? _j : '10px');
        toElement.appendChild(button);
        button.addEventListener('click', function () {
            button.dataset.folded = button.dataset.folded == 'true' ? 'false' : 'true';
            if (_this.settings.localStorageFoldedKey) {
                localStorage.setItem(_this.settings.localStorageFoldedKey, button.dataset.folded);
            }
        });
    };
    return BgaHelpExpandableButton;
}(BgaHelpButton));
var HelpManager = /** @class */ (function () {
    function HelpManager(game, settings) {
        this.game = game;
        if (!(settings === null || settings === void 0 ? void 0 : settings.buttons)) {
            throw new Error('HelpManager need a `buttons` list in the settings.');
        }
        var leftSide = document.getElementById('left-side');
        var buttons = document.createElement('div');
        buttons.id = "bga-help_buttons";
        leftSide.appendChild(buttons);
        settings.buttons.forEach(function (button) { return button.add(buttons); });
    }
    return HelpManager;
}());
var determineBoardWidth = function () {
    return 1000;
};
var determineMaxZoomLevel = function () {
    var bodycoords = dojo.marginBox("zoom-overall");
    var contentWidth = bodycoords.w;
    var rowWidth = determineBoardWidth();
    return contentWidth / rowWidth;
};
var getZoomLevels = function (maxZoomLevel) {
    var zoomLevels = [];
    var increments = 0.05;
    if (maxZoomLevel > 1) {
        var maxZoomLevelsAbove1 = maxZoomLevel - 1;
        increments = (maxZoomLevelsAbove1 / 9);
        zoomLevels = [];
        for (var i = 1; i <= 9; i++) {
            zoomLevels.push((increments * i) + 1);
        }
    }
    for (var i = 1; i <= 9; i++) {
        zoomLevels.push(1 - (increments * i));
    }
    zoomLevels = __spreadArray(__spreadArray([], zoomLevels, true), [1, maxZoomLevel], false);
    zoomLevels = zoomLevels.sort();
    zoomLevels = zoomLevels.filter(function (zoomLevel) { return (zoomLevel <= maxZoomLevel) && (zoomLevel > 0.3); });
    return zoomLevels;
};
var AutoZoomManager = /** @class */ (function (_super) {
    __extends(AutoZoomManager, _super);
    function AutoZoomManager(elementId, localStorageKey) {
        var storedZoomLevel = localStorage.getItem(localStorageKey);
        var maxZoomLevel = determineMaxZoomLevel();
        if (storedZoomLevel && Number(storedZoomLevel) > maxZoomLevel) {
            localStorage.removeItem(localStorageKey);
        }
        var zoomLevels = getZoomLevels(determineMaxZoomLevel());
        return _super.call(this, {
            element: document.getElementById(elementId),
            smooth: false,
            zoomLevels: zoomLevels,
            defaultZoom: 1,
            localStorageZoomKey: localStorageKey,
            zoomControls: {
                color: 'black',
                position: 'top-right'
            },
            onZoomChange: function (zoom) {
                var sideBoard = $('dp-game-board-side-zoom-wrapper');
                if (sideBoard && zoom > 0) {
                    sideBoard.style.transform = "scale(".concat(zoom - 0.2, ")");
                }
            }
        }) || this;
    }
    return AutoZoomManager;
}(ZoomManager));
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * earth implementation : © Guillaume Benny bennygui@gmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 */
var Numbers = /** @class */ (function () {
    function Numbers(game, initialValue, targetIdsOrElements) {
        if (initialValue === void 0) { initialValue = 0; }
        if (targetIdsOrElements === void 0) { targetIdsOrElements = []; }
        this.game = game;
        this.targetIdsOrElements = targetIdsOrElements;
        this.currentValue = initialValue;
        this.targetValue = initialValue;
        this.onFinishStepValues = [];
        this.ensureNumbers();
        this.update();
    }
    Numbers.prototype.addTarget = function (targetIdOrElement) {
        if (this.targetIdsOrElements instanceof Array) {
            this.targetIdsOrElements.push(targetIdOrElement);
        }
        else {
            this.targetIdsOrElements = [this.targetIdsOrElements, targetIdOrElement];
        }
        this.update();
    };
    Numbers.prototype.registerOnFinishStepValues = function (callback) {
        this.onFinishStepValues.push(callback);
    };
    Numbers.prototype.getValue = function () {
        return this.currentValue;
    };
    Numbers.prototype.setValue = function (value) {
        this.currentValue = value;
        this.targetValue = value;
        this.ensureNumbers();
        this.update();
    };
    Numbers.prototype.toValue = function (value, isInstantaneous) {
        if (isInstantaneous === void 0) { isInstantaneous = false; }
        if (isInstantaneous || this.game.instantaneousMode) {
            this.setValue(value);
        }
        else {
            this.targetValue = value;
            this.ensureNumbers();
            this.stepValues(true);
        }
    };
    Numbers.prototype.stepValues = function (firstCall) {
        var _this = this;
        if (firstCall === void 0) { firstCall = false; }
        if (this.currentAtTarget()) {
            this.update();
            if (!firstCall) {
                for (var _i = 0, _a = this.onFinishStepValues; _i < _a.length; _i++) {
                    var callback = _a[_i];
                    callback(this);
                }
            }
            return;
        }
        if (this.currentValue instanceof Array) {
            var newValues = [];
            for (var i = 0; i < this.currentValue.length; ++i) {
                newValues.push(this.stepOneValue(this.currentValue[i], this.targetValue[i]));
            }
            this.currentValue = newValues;
        }
        else {
            this.currentValue = this.stepOneValue(this.currentValue, this.targetValue);
        }
        this.update();
        setTimeout(function () { return _this.stepValues(); }, this.DELAY);
    };
    Numbers.prototype.stepOneValue = function (current, target) {
        if (current === null) {
            current = 0;
        }
        if (target === null) {
            return null;
        }
        var step = Math.ceil(Math.abs(current - target) / this.STEPS);
        return (current + (current < target ? 1 : -1) * step);
    };
    Numbers.prototype.update = function () {
        if (this.targetIdsOrElements instanceof Array) {
            for (var _i = 0, _a = this.targetIdsOrElements; _i < _a.length; _i++) {
                var target = _a[_i];
                this.updateOne(target);
            }
        }
        else {
            this.updateOne(this.targetIdsOrElements);
        }
    };
    Numbers.prototype.updateOne = function (targetIdOrElement) {
        var elem = this.getElement(targetIdOrElement);
        elem.innerHTML = this.format();
    };
    Numbers.prototype.getTargetElements = function () {
        var _this = this;
        if (this.targetIdsOrElements instanceof Array) {
            return this.targetIdsOrElements.map(function (id) { return _this.getElement(id); });
        }
        else {
            return [this.getElement(this.targetIdsOrElements)];
        }
    };
    Numbers.prototype.getTargetElement = function () {
        var elems = this.getTargetElements();
        if (elems.length == 0) {
            return null;
        }
        return elems[0];
    };
    Numbers.prototype.format = function () {
        if (this.currentValue instanceof Array) {
            var formatted = [];
            for (var i = 0; i < this.currentValue.length; ++i) {
                formatted.push(this.formatOne(this.currentValue[i], this.targetValue[i]));
            }
            return this.formatMultiple(formatted);
        }
        else {
            return this.formatOne(this.currentValue, this.targetValue);
        }
    };
    Numbers.prototype.formatOne = function (currentValue, targetValue) {
        var span = document.createElement('span');
        if (currentValue != targetValue) {
            span.classList.add('bx-counter-in-progress');
        }
        span.innerText = (currentValue === null ? '-' : currentValue);
        return span.outerHTML;
    };
    Numbers.prototype.formatMultiple = function (formattedValues) {
        return formattedValues.join('/');
    };
    Numbers.prototype.ensureNumbers = function () {
        var _this = this;
        if (this.currentValue instanceof Array) {
            this.currentValue = this.currentValue.map(function (v) { return _this.ensureOneNumber(v); });
            this.targetValue = this.targetValue.map(function (v) { return _this.ensureOneNumber(v); });
        }
        else {
            this.currentValue = this.ensureOneNumber(this.currentValue);
            this.targetValue = this.ensureOneNumber(this.targetValue);
        }
    };
    Numbers.prototype.ensureOneNumber = function (value) {
        return (value === null ? null : parseInt(value));
    };
    Numbers.prototype.currentAtTarget = function () {
        var _this = this;
        if (this.currentValue instanceof Array) {
            return this.currentValue.every(function (v, i) { return v == _this.targetValue[i]; });
        }
        else {
            return (this.currentValue == this.targetValue);
        }
    };
    Numbers.prototype.getElement = function (targetIdOrElement) {
        if (typeof targetIdOrElement == "string") {
            return document.getElementById(targetIdOrElement);
        }
        return targetIdOrElement;
    };
    return Numbers;
}());
var CounterVoidStock = /** @class */ (function (_super) {
    __extends(CounterVoidStock, _super);
    function CounterVoidStock(game, manager, setting) {
        var _this = _super.call(this, manager, document.createElement("div")) || this;
        _this.game = game;
        _this.manager = manager;
        _this.setting = setting;
        var targetElement = document.getElementById(setting.targetElement);
        if (!targetElement) {
            console.warn('targetElement not found');
            return _this;
        }
        var wrapperElement = document.createElement("div");
        wrapperElement.classList.add("counter-void-stock-wrapper");
        if (setting.setupWrapper) {
            setting.setupWrapper(wrapperElement);
        }
        var iconElement = document.createElement("div");
        iconElement.classList.add("counter-void-stock-icon");
        if (setting.setupIcon) {
            setting.setupIcon(iconElement);
        }
        wrapperElement.appendChild(iconElement);
        var counterElement = document.createElement("div");
        counterElement.classList.add("counter-void-stock-counter");
        counterElement.id = setting.counterId;
        if (setting.setupCounter) {
            setting.setupCounter(counterElement);
        }
        wrapperElement.appendChild(counterElement);
        _this.element.classList.add("counter-void-stock-stock");
        if (setting.setupStock) {
            setting.setupStock(_this.element);
        }
        wrapperElement.appendChild(_this.element);
        targetElement.appendChild(wrapperElement);
        _this.counter = new Numbers(game);
        _this.counter.addTarget(setting.counterId);
        _this.counter.setValue(setting.initialCounterValue);
        return _this;
    }
    CounterVoidStock.prototype.create = function (nodeId) { };
    CounterVoidStock.prototype.getValue = function () { return this.counter.getValue(); };
    CounterVoidStock.prototype.incValue = function (by) { this.counter.setValue(this.counter.getValue() + by); };
    CounterVoidStock.prototype.decValue = function (by) { this.counter.setValue(this.counter.getValue() - by); };
    CounterVoidStock.prototype.setValue = function (value) { this.counter.setValue(value); };
    CounterVoidStock.prototype.toValue = function (value) { this.counter.toValue(value); };
    CounterVoidStock.prototype.disable = function () { };
    return CounterVoidStock;
}(VoidStock));
var DogCardManager = /** @class */ (function (_super) {
    __extends(DogCardManager, _super);
    function DogCardManager(dogParkGame) {
        var _this = _super.call(this, dogParkGame, {
            getId: function (card) { return "dp-dog-card-".concat(card.id); },
            setupDiv: function (card, div) {
                div.classList.add('blackjack-size-portrait');
                div.dataset.id = "".concat(card.id);
                div.dataset.type = 'dog';
                var cardTokenVoidStockElement = document.createElement("div");
                cardTokenVoidStockElement.id = "dp-dog-card-token-void-stock-".concat(card.id);
                div.appendChild(cardTokenVoidStockElement);
                _this.cardTokenVoidStocks[card.id] = new VoidStock(dogParkGame.tokenManager, $(cardTokenVoidStockElement.id));
                var cardTokenStockElement = document.createElement("div");
                cardTokenStockElement.id = "dp-dog-card-token-stock-".concat(card.id);
                cardTokenStockElement.classList.add('dp-dog-card-token-stock');
                div.appendChild(cardTokenStockElement);
                _this.cardTokenStocks[card.id] = new LineStock(dogParkGame.tokenManager, $(cardTokenStockElement.id), { gap: '2px' });
                var helpButtonElement = document.createElement("div");
                helpButtonElement.classList.add('dp-help-button-wrapper');
                helpButtonElement.classList.add('position-floating-bottom');
                helpButtonElement.innerHTML = "<i id=\"dp-help-dog-".concat(card.id, "\" class=\"dp-help-button fa fa-question-circle\"  aria-hidden=\"true\"></i>");
                div.appendChild(helpButtonElement);
                _this.addInitialResourcesToDog(card);
                dojo.connect($("dp-help-dog-".concat(card.id)), 'onclick', function (event) { return _this.dogParkGame.helpDialogManager.showDogHelpDialog(event, card); });
            },
            setupFrontDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-front");
                div.classList.add("dog-card-art");
                div.classList.add("dog-card-art-".concat(card.typeArg));
                div.dataset.set = card.type;
            },
            isCardVisible: function (card) { return !!card.typeArg; },
            cardWidth: DogCardManager.CARD_WIDTH,
            cardHeight: DogCardManager.CARD_HEIGHT,
        }) || this;
        _this.dogParkGame = dogParkGame;
        _this.cardTokenVoidStocks = {};
        _this.cardTokenStocks = {};
        return _this;
    }
    DogCardManager.prototype.setUp = function (data) {
        dojo.place("<div class=\"label-wrapper\" style=\"margin-bottom: 16px;\">\n                  <h2> ".concat(_('Discard Pile'), "</h2>\n                </div>\n                <div id=\"dp-dog-discard-pile\"></div>"), $('dp-last-row'));
        this.discardPile = new AllVisibleDeck(this, $("dp-dog-discard-pile"), {});
        this.discardPile.addCards(data.discardPile.filter(function (dogCard) { return !data.field.scoutedDogs.map(function (dog) { return dog.id; }).includes(dogCard.id); }));
    };
    DogCardManager.prototype.addResourceToDog = function (dogId, type) {
        var token = this.dogParkGame.tokenManager.createToken(type);
        return this.cardTokenStocks[dogId].addCard(token);
    };
    DogCardManager.prototype.removeResourceFromDog = function (dogId, type) {
        var token = this.cardTokenStocks[dogId].getCards().find(function (token) { return token.type === type; });
        if (token) {
            this.cardTokenStocks[dogId].removeCard(token);
        }
    };
    DogCardManager.prototype.addInitialResourcesToDog = function (dog) {
        for (var resource in dog.resourcesOnCard) {
            for (var i = 0; i < Number(dog.resourcesOnCard[resource]); i++) {
                this.addResourceToDog(dog.id, resource);
            }
        }
    };
    DogCardManager.prototype.removeAllResourcesFromDog = function (id) {
        this.cardTokenStocks[id].removeAll();
    };
    DogCardManager.CARD_WIDTH = 195;
    DogCardManager.CARD_HEIGHT = 266;
    return DogCardManager;
}(CardManager));
var BreedExpertAwardManager = /** @class */ (function (_super) {
    __extends(BreedExpertAwardManager, _super);
    function BreedExpertAwardManager(dogParkGame) {
        var _this = _super.call(this, dogParkGame, {
            getId: function (card) { return "dp-breed-expert-".concat(card.id); },
            setupDiv: function (card, div) {
                div.classList.add('mini-size-landscape');
            },
            setupFrontDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-front");
                div.classList.add("breed-expert-art");
                div.classList.add("breed-expert-art-".concat(card.typeArg));
            },
            cardWidth: BreedExpertAwardManager.CARD_WIDTH,
            cardHeight: BreedExpertAwardManager.CARD_HEIGHT,
        }) || this;
        _this.dogParkGame = dogParkGame;
        _this.slotsIds = [];
        return _this;
    }
    BreedExpertAwardManager.prototype.setUp = function (gameData) {
        var _this = this;
        var collapsed = window.localStorage.getItem(BreedExpertAwardManager.SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY) === 'true';
        dojo.place("<div id=\"dp-game-board-side-zoom-wrapper\">\n                            <div id=\"dp-game-board-side-wrapper\" class=\"".concat(Boolean(collapsed) ? 'hide-side-bar' : '', "\">\n                                <div id=\"dp-game-board-side\">\n                                    <div id=\"dp-game-board-breed-expert-awards\" class=\"dp-board\">\n                                        <div id=\"dp-game-board-breed-expert-awards-stock\">\n                           \n                                        </div>\n                                    </div>\n                                </div>\n                                <div id=\"dp-game-board-side-toggle-button\"><i class=\"fa fa-trophy\" aria-hidden=\"true\"></i> ").concat(_('Breed Expert'), " <i class=\"fa fa-trophy\" aria-hidden=\"true\"></i></div>\n                            </div>\n                        </div>"), $('bga-zoom-wrapper'), 'first');
        dojo.connect($('dp-game-board-side-zoom-wrapper'), 'onclick', function () { return _this.toggleSideBar(); });
        this.slotsIds = [
            "dp-game-board-breed-expert-awards-slot-1",
            "dp-game-board-breed-expert-awards-slot-2",
            "dp-game-board-breed-expert-awards-slot-3",
            "dp-game-board-breed-expert-awards-slot-4",
            "dp-game-board-breed-expert-awards-slot-5",
            "dp-game-board-breed-expert-awards-slot-6",
            "dp-game-board-breed-expert-awards-slot-7"
        ];
        this.stock = new SlotStock(this, $('dp-game-board-breed-expert-awards-stock'), {
            slotsIds: this.slotsIds,
            mapCardToSlot: function (card) { return "dp-game-board-breed-expert-awards-slot-".concat(card.locationArg); },
            gap: '10px',
        });
        this.stock.addCards(gameData.breedExpertAwards);
        this.slotsIds.forEach(function (slotId) {
            var html = "<div id=\"".concat(slotId, "-standings\" class=\"dp-game-board-breed-expert-awards-slot-standings\">");
            html += '</div>';
            dojo.place(html, dojo.query("[data-slot-id=\"".concat(slotId, "\"]"))[0]);
        });
        this.updateBreedExpertAwardStandings();
    };
    BreedExpertAwardManager.prototype.toggleSideBar = function () {
        dojo.toggleClass('dp-game-board-side-wrapper', 'hide-side-bar');
        window.localStorage.setItem(BreedExpertAwardManager.SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY, String(dojo.hasClass('dp-game-board-side-wrapper', 'hide-side-bar')));
    };
    BreedExpertAwardManager.prototype.updateBreedExpertAwardStandings = function () {
        var _a, _b;
        var playerDogBreeds = {};
        for (var playerId in this.dogParkGame.playerArea.kennelStocks) {
            playerDogBreeds[playerId] = this.dogParkGame.playerArea.kennelStocks[playerId].getCards().flatMap(function (dogCard) { return dogCard.breeds; });
            if (this.dogParkGame.playerArea.leadStocks[playerId]) {
                playerDogBreeds[playerId] = __spreadArray(__spreadArray([], playerDogBreeds[playerId], true), this.dogParkGame.playerArea.leadStocks[playerId].getCards().flatMap(function (dogCard) { return dogCard.breeds; }), true);
            }
        }
        var cards = this.stock.getCards();
        var _loop_3 = function (card) {
            var elementId = "dp-game-board-breed-expert-awards-slot-".concat(card.locationArg, "-standings");
            var element = $(elementId);
            dojo.empty(element);
            var html = '';
            var _loop_4 = function (playerId) {
                var playerColor = (_a = this_1.dogParkGame.getPlayer(Number(playerId))) === null || _a === void 0 ? void 0 : _a.color;
                if (!playerColor) {
                    playerColor = (_b = this_1.dogParkGame.gamedatas.autoWalkers.find(function (autoWalker) { return autoWalker.id === Number(playerId); })) === null || _b === void 0 ? void 0 : _b.color;
                }
                var order = playerDogBreeds[playerId].filter(function (breed) { return breed === card.type; }).length;
                html += "<div id=\"dp-game-board-breed-expert-awards-slot-standings-".concat(playerId, "\" class=\"dp-game-board-breed-expert-awards-slot-standings-wrapper ").concat(order === 0 ? 'not-eligible' : '', "\" style=\"order: ").concat(8 - order, ";\"><span class=\"dp-dog-walker dp-token\" data-color=\"#").concat(playerColor, "\"><h1>").concat(order, "</h1></span></div>");
            };
            for (var playerId in this_1.dogParkGame.playerArea.kennelStocks) {
                _loop_4(playerId);
            }
            dojo.place(html, element);
        };
        var this_1 = this;
        for (var _i = 0, cards_1 = cards; _i < cards_1.length; _i++) {
            var card = cards_1[_i];
            _loop_3(card);
        }
    };
    BreedExpertAwardManager.SIDE_BAR_COLLAPSED_LOCAL_STORAGE_KEY = 'dogpark-side-bar-collapsed';
    BreedExpertAwardManager.CARD_WIDTH = 195;
    BreedExpertAwardManager.CARD_HEIGHT = 142;
    return BreedExpertAwardManager;
}(CardManager));
var ObjectiveCardManager = /** @class */ (function (_super) {
    __extends(ObjectiveCardManager, _super);
    function ObjectiveCardManager(dogParkGame) {
        var _this = _super.call(this, dogParkGame, {
            getId: function (card) { return "dp-objective-card-".concat(card.id); },
            setupDiv: function (card, div) {
                div.classList.add('blackjack-size-landscape');
                if (_this.isCardVisible(card)) {
                    var helpButtonElement = document.createElement("div");
                    helpButtonElement.classList.add('dp-help-button-wrapper');
                    helpButtonElement.classList.add(card.typeArg >= 20 ? 'position-bottom-right' : 'position-top-right');
                    helpButtonElement.innerHTML = "<i id=\"dp-help-objective-".concat(card.id, "\" class=\"dp-help-button fa fa-question-circle\"  aria-hidden=\"true\"></i>");
                    div.appendChild(helpButtonElement);
                    dojo.connect($("dp-help-objective-".concat(card.id)), 'onclick', function (event) { return _this.dogParkGame.helpDialogManager.showObjectiveHelpDialog(event, card); });
                }
            },
            setupBackDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-back");
                div.classList.add("objective-art");
                div.classList.add("objective-art-background");
            },
            setupFrontDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-front");
                div.classList.add("objective-art");
                div.classList.add("objective-art-".concat(card.typeArg));
            },
            isCardVisible: function (card) { return !!card.typeArg; },
            cardWidth: ObjectiveCardManager.CARD_WIDTH,
            cardHeight: ObjectiveCardManager.CARD_HEIGHT,
        }) || this;
        _this.dogParkGame = dogParkGame;
        return _this;
    }
    ObjectiveCardManager.CARD_HEIGHT = 195;
    ObjectiveCardManager.CARD_WIDTH = 266;
    return ObjectiveCardManager;
}(CardManager));
var ForecastManager = /** @class */ (function (_super) {
    __extends(ForecastManager, _super);
    function ForecastManager(dogParkGame) {
        var _this = _super.call(this, dogParkGame, {
            getId: function (card) { return "dp-forecast-".concat(card.id); },
            setupDiv: function (card, div) {
                div.classList.add('mini-size-landscape');
                var helpButtonElement = document.createElement("div");
                helpButtonElement.classList.add('dp-help-button-wrapper');
                helpButtonElement.classList.add('position-floating-right');
                helpButtonElement.innerHTML = "<i id=\"dp-help-forecast-".concat(card.id, "\" class=\"dp-help-button fa fa-question-circle\"  aria-hidden=\"true\"></i>");
                div.appendChild(helpButtonElement);
                dojo.connect($("dp-help-forecast-".concat(card.id)), 'onclick', function (event) { return _this.dogParkGame.helpDialogManager.showForecastHelpDialog(event, card); });
            },
            setupFrontDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-front");
                div.classList.add("forecast-art");
                div.classList.add("forecast-art-".concat(card.typeArg));
            },
            setupBackDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-back");
                div.classList.add("forecast-art");
                div.classList.add("forecast-art-background");
            },
            isCardVisible: function (card) { return !!card.typeArg; },
            cardWidth: ForecastManager.CARD_WIDTH,
            cardHeight: ForecastManager.CARD_HEIGHT,
        }) || this;
        _this.dogParkGame = dogParkGame;
        return _this;
    }
    ForecastManager.prototype.setUp = function (gameData) {
        this.stock = new SlotStock(this, $('dp-round-tracker-forecast-stock'), {
            slotsIds: [
                "dp-round-tracker-forecast-slot-1",
                "dp-round-tracker-forecast-slot-2",
                "dp-round-tracker-forecast-slot-3",
                "dp-round-tracker-forecast-slot-4",
            ],
            mapCardToSlot: function (card) { return "dp-round-tracker-forecast-slot-".concat(card.locationArg); },
            direction: 'row',
            gap: '43px',
            center: false
        });
        this.stock.addCards(gameData.forecastCards);
    };
    ForecastManager.CARD_WIDTH = 195;
    ForecastManager.CARD_HEIGHT = 142;
    return ForecastManager;
}(CardManager));
var DogWalkerManager = /** @class */ (function (_super) {
    __extends(DogWalkerManager, _super);
    function DogWalkerManager(dogParkGame) {
        var _this = _super.call(this, dogParkGame, {
            getId: function (walker) { return "dp-dog-walker-".concat(walker.id); },
            setupDiv: function (walker, div) {
                div.classList.add('dp-dog-walker-token');
                div.classList.add('dp-token');
                div.dataset.id = "".concat(walker.id);
                div.dataset.type = 'walker';
            },
            setupFrontDiv: function (walker, div) {
                div.id = "".concat(_this.getId(walker), "-front");
                div.classList.add("dp-dog-walker");
                div.dataset.color = "#".concat(walker.type);
            },
            cardWidth: DogWalkerManager.WIDTH,
            cardHeight: DogWalkerManager.HEIGHT,
        }) || this;
        _this.dogParkGame = dogParkGame;
        return _this;
    }
    DogWalkerManager.WIDTH = 45;
    DogWalkerManager.HEIGHT = 65;
    return DogWalkerManager;
}(CardManager));
var TokenManager = /** @class */ (function (_super) {
    __extends(TokenManager, _super);
    function TokenManager(dogParkGame) {
        var _this = _super.call(this, dogParkGame, {
            getId: function (token) { return "dp-token-".concat(token.id); },
            setupDiv: function (token, div) {
                div.classList.add('dp-card-token');
                div.classList.add('dp-token-token');
                div.classList.add("dp-token-".concat(token.type));
                div.classList.add('small');
                div.dataset.type = token.type;
            },
            setupFrontDiv: function (token, div) {
            },
            cardWidth: TokenManager.TOKEN_WIDTH,
            cardHeight: TokenManager.TOKEN_HEIGHT
        }) || this;
        _this.dogParkGame = dogParkGame;
        _this.idSequence = 0;
        return _this;
    }
    TokenManager.prototype.setUp = function () {
        var swapHtml = "<p>".concat(_('Swap: You may exchange 1 Dog from your Kennel with a Dog in the Field'), "</p>");
        var swapPlusWalkedHtml = "<p>".concat(_('Choose between 1 reputation or Swap.'), "</p>") + swapHtml + "<p>".concat(_('The new Dog also receives a walked token.'), "</p>");
        var scoutHtml = "<p>".concat(_('Scout: You may reveal the top two cards of the deck. You may replace a Dog in the Field with 1 of the Dogs drawn. Unselected cards are discarded.'), "</p>");
        this.dogParkGame.addTooltipHtmlToClass("dp-token-swap", swapHtml, TOOLTIP_DELAY);
        this.dogParkGame.addTooltipHtmlToClass("dp-token-scout", scoutHtml, TOOLTIP_DELAY);
        this.dogParkGame.addTooltipHtml("dp-walk-spot-6", scoutHtml, TOOLTIP_DELAY);
        this.dogParkGame.addTooltipHtml("dp-walk-spot-9", scoutHtml, TOOLTIP_DELAY);
        this.dogParkGame.addTooltipHtml("dp-walk-spot-10", swapHtml, TOOLTIP_DELAY);
        this.dogParkGame.addTooltipHtml("dp-walk-spot-93", swapPlusWalkedHtml, TOOLTIP_DELAY);
    };
    TokenManager.prototype.createToken = function (type) {
        return { id: this.idSequence++, type: type };
    };
    TokenManager.TOKEN_WIDTH = 39.375;
    return TokenManager;
}(CardManager));
var LocationBonusCardManager = /** @class */ (function (_super) {
    __extends(LocationBonusCardManager, _super);
    function LocationBonusCardManager(dogParkGame) {
        var _this = _super.call(this, dogParkGame, {
            getId: function (card) { return "dp-location-bonus-card-".concat(card.id); },
            setupDiv: function (card, div) {
                div.classList.add('blackjack-size-landscape');
            },
            setupBackDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-back");
                div.classList.add("location-bonus-art");
                div.classList.add("location-bonus-art-background");
            },
            setupFrontDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-front");
                div.classList.add("location-bonus-art");
                div.classList.add("location-bonus-art-".concat(card.typeArg));
            },
            isCardVisible: function (card) { return !!card.typeArg; },
            cardWidth: LocationBonusCardManager.CARD_WIDTH,
            cardHeight: LocationBonusCardManager.CARD_HEIGHT,
        }) || this;
        _this.dogParkGame = dogParkGame;
        return _this;
    }
    LocationBonusCardManager.CARD_WIDTH = 266;
    LocationBonusCardManager.CARD_HEIGHT = 195;
    return LocationBonusCardManager;
}(CardManager));
var HelpDialogManager = /** @class */ (function () {
    function HelpDialogManager(dogParkGame) {
        this.dogParkGame = dogParkGame;
        this.dialogId = 'dpHelpDialogId';
    }
    HelpDialogManager.prototype.showDogHelpDialog = function (event, card) {
        var html = "<div class=\"dp-help-dialog-content\"><div class=\"dp-help-dialog-content-left\">";
        html += "<div class=\"dog-card-art dog-card-art-".concat(card.typeArg, "\"></div>");
        html += "</div>";
        html += "<div class=\"dp-help-dialog-content-right\">";
        html += "<p>".concat(dojo.string.substitute(_('<b>Breed(s)</b>: ${breeds}'), { breeds: card.breeds.map(function (breed) { return _(breed.charAt(0).toUpperCase() + breed.slice(1)); }).join(', ') }), "</p>");
        html += "<p>".concat(dojo.string.substitute(_('<b>Walking cost</b>: ${cost}'), { cost: this.dogParkGame.formatWithIcons(Object.entries(card.costs).map(function (_a) {
                var resource = _a[0], quantity = _a[1];
                var result = [];
                for (var i = 0; i < quantity; i++) {
                    result.push("<icon-".concat(resource, ">"));
                }
                return result.join(' ');
            }).join('')) }), "</p>");
        html += "<p><b>".concat(_(card.abilityTitle), "</b></p>");
        html += "<p>".concat(this.dogParkGame.formatWithIcons(card.abilityText), "</p>");
        html += "</div>";
        html += "</div>";
        this.showDialog(event, card.name, html);
    };
    HelpDialogManager.prototype.showObjectiveHelpDialog = function (event, card) {
        var html = "<div class=\"dp-help-dialog-content\"><div class=\"dp-help-dialog-content-left\">";
        html += "<div class=\"objective-art objective-art-".concat(card.typeArg, "\"></div>");
        html += "</div>";
        html += "<div class=\"dp-help-dialog-content-right\">";
        html += "<p>".concat(dojo.string.substitute(_('<b>Type</b>: ${type}'), { type: _(card.type).toUpperCase() }), "</p>");
        html += "<p>".concat(this.dogParkGame.formatWithIcons(_(card.description)), "</p>");
        html += "</div>";
        html += "</div>";
        this.showDialog(event, card.name, html);
    };
    HelpDialogManager.prototype.showForecastHelpDialog = function (event, card) {
        var html = "<div class=\"dp-help-dialog-content\"><div class=\"dp-help-dialog-content-left\">";
        html += "<div class=\"forecast-art forecast-art-".concat(card.typeArg, "\"></div>");
        html += "</div>";
        html += "<div class=\"dp-help-dialog-content-right\">";
        html += "<p>".concat(this.dogParkGame.formatWithIcons(_(card.description)), "</p>");
        html += "</div>";
        html += "</div>";
        this.showDialog(event, dojo.string.substitute(_('Round ${roundNumber} Forecast Card'), { roundNumber: card.locationArg }), html);
    };
    HelpDialogManager.prototype.getPlayerAidHtml = function () {
        var html = '';
        html += "<h2>".concat(_('Icons'), "</h2>");
        html += "<h3>".concat(_('Resources'), "</h3>");
        html += "<div class=\"dp-player-aid-resource-wrapper\">";
        html += "<div class=\"dp-player-aid-resource\">".concat(this.dogParkGame.tokenIcon('stick'), " - ").concat(_('stick'), "</div>");
        html += "<div class=\"dp-player-aid-resource\">".concat(this.dogParkGame.tokenIcon('ball'), " - ").concat(_('ball'), "</div>");
        html += "<div class=\"dp-player-aid-resource\">".concat(this.dogParkGame.tokenIcon('treat'), " - ").concat(_('treat'), "</div>");
        html += "<div class=\"dp-player-aid-resource\">".concat(this.dogParkGame.tokenIcon('toy'), " - ").concat(_('toy'), "</div>");
        html += "<div class=\"dp-player-aid-resource\">".concat(this.dogParkGame.tokenIcon('all-resources'), " - ").concat(_('any resource'), "</div>");
        html += "<div class=\"dp-player-aid-resource\">".concat(this.dogParkGame.tokenIcon('walked'), " - ").concat(_('walked'), "</div>");
        html += "<div class=\"dp-player-aid-resource\">".concat(this.dogParkGame.tokenIcon('reputation'), " - ").concat(_('reputation'), "</div>");
        html += "</div>";
        html += "<h3>".concat(_('Actions'), "</h3>");
        html += "<p>".concat(this.dogParkGame.tokenIcon('swap'), " - ").concat(_('Swap: You may exchange 1 Dog from your Kennel with a Dog in the Field.'), "</p>");
        html += "<p>".concat(this.dogParkGame.tokenIcon('scout'), " - ").concat(_('Scout: You may reveal the top two cards of the deck. You may replace a Dog in the Field with 1 of the Dogs drawn. Unselected cards are removed from the game.'), "</p>");
        html += "<h2>".concat(_('Game Round'), "</h2>");
        html += "<p>".concat(_('Dog Park is played over 4 rounds, and each round is split into 4 phases.'), "</p>");
        html += "<h3>".concat(_('Phase 1: Recruitment'), "</h3>");
        html += "<p>".concat(this.dogParkGame.formatWithIcons(_('Over 2 rounds of Offers, use your <icon-reputation> to Offer on Dogs in the Field and add them to your Kennel.')), "</p>");
        html += "<h3>".concat(_('Phase 2: Selection'), "</h3>");
        html += "<p>".concat(this.dogParkGame.formatWithIcons(_('Prepare to walk up to 3 Dogs by paying their walking costs, placing them on your Lead. Dogs placed on your lead gain a <icon-walked> token.')), "</p>");
        html += "<p>".concat(this.dogParkGame.formatWithIcons(_('<strong>Remember!</strong> If needed you may use <icon-all-resources> + <icon-all-resources> =  <icon-all-resources>')), "</p>");
        html += "<h3>".concat(_('Phase 3: Walking'), "</h3>");
        html += "<p>".concat(_('Take turns to move your Walker through the Park. You immediately claim the Leaving Bonus when you leave the park.'), "</p>");
        html += "<h3>".concat(_('Phase 4: Home Time'), "</h3>");
        html += "<p>".concat(this.dogParkGame.formatWithIcons(_('You gain 2 <icon-reputation> for each Dog on your Lead.')), "</p>");
        html += "<p>".concat(this.dogParkGame.formatWithIcons(_('You lose 1 <icon-reputation> for each Dog in your kennel without a <icon-walked>.')), "</p>");
        html += "<p>".concat(_('Finally the Forecast card is flipped and new Location Bonuses are added to the park. The First Walker token is passed clockwise and a new round begins.'), "</p>");
        html += "<h2>".concat(_('Final Scoring'), "</h2>");
        html += "<p>".concat(_('Your final score is calculated by adding the following items together:'), "</p>");
        html += "<p>+ ".concat(this.dogParkGame.formatWithIcons(_('<icon-reputation> gained during the game')), "</p>");
        html += "<p>+ ".concat(this.dogParkGame.formatWithIcons(_('<icon-reputation> from dogs with <strong>FINAL SCORING</strong> abilities')), "</p>");
        html += "<p>+ ".concat(this.dogParkGame.formatWithIcons(_('<icon-reputation> from won <strong>Breed Expert</strong> awards')), "</p>");
        html += "<p>+ ".concat(this.dogParkGame.formatWithIcons(_('<icon-reputation> from your completed <strong>Objective card</strong>')), "</p>");
        html += "<p>+ ".concat(this.dogParkGame.formatWithIcons(_('Every <strong>5 remaining resources</strong> = <icon-reputation>')), "</p>");
        html += "<p>".concat(this.dogParkGame.formatWithIcons(_('The player with the most <icon-reputation> wins. If there is a tie, the player who won the highest valued Breed Expert award wins. If players are still tied, they share the victory.')), "</p>");
        return html;
    };
    HelpDialogManager.prototype.showDialog = function (event, title, html) {
        dojo.stopEvent(event);
        this.dialog = new ebg.popindialog();
        this.dialog.create(this.dialogId);
        this.dialog.setTitle("<i class=\"fa fa-question-circle\" aria-hidden=\"true\"></i> ".concat(_(title)));
        this.dialog.setContent(html);
        this.dialog.show();
    };
    return HelpDialogManager;
}());
var DogOfferDial = /** @class */ (function () {
    function DogOfferDial(settings) {
        var _this = this;
        this.settings = settings;
        this._currentValue = 1;
        dojo.place(this.createDial(), settings.parentId);
        this.currentValue = settings.initialValue;
        if (!this.settings.readOnly) {
            this.increaseButton = $('dp-dial-button-increase');
            this.decreaseButton = $('dp-dial-button-decrease');
            this.confirmButton = $('dp-dial-button-confirm');
            this.updateDial(settings.initialValue);
            dojo.connect(this.increaseButton, 'onclick', function () { return _this.updateDial(_this._currentValue + 1); });
            dojo.connect(this.decreaseButton, 'onclick', function () { return _this.updateDial(_this._currentValue - 1); });
            dojo.connect(this.confirmButton, 'onclick', function () { return _this.settings.onConfirm(); });
        }
    }
    DogOfferDial.prototype.updateDial = function (newValue) {
        this.currentValue = newValue;
        this.increaseButton.classList.remove('disabled');
        this.decreaseButton.classList.remove('disabled');
        if (this._currentValue === 1) {
            this.decreaseButton.classList.add('disabled');
        }
        if (this._currentValue === this.settings.maxOfferValue) {
            this.increaseButton.classList.add('disabled');
        }
    };
    DogOfferDial.prototype.createDial = function () {
        var result = '';
        if (!this.settings.readOnly) {
            result += "<div id=\"dp-offer-dial-controls-wrapper\"><a id=\"dp-dial-button-decrease\" class=\"bgabutton bgabutton_blue\"><i class=\"fa fa-minus\" aria-hidden=\"true\"></i></a>";
        }
        result += " <div class=\"dp-dial-wrapper\">\n                        <div id=\"".concat(this.settings.elementId, "\" class=\"dp-dial side-front\" data-color=\"#").concat(this.settings.player.color, "\" data-value=\"").concat(this._currentValue, "\">\n                            <div class=\"side-front-numbers\"></div>\n                            <div class=\"side-front-overlay\"></div>\n                        </div>\n                    </div>");
        if (!this.settings.readOnly) {
            result += "<a id=\"dp-dial-button-increase\" class=\"bgabutton bgabutton_blue\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></a></div>";
            result += "<a id=\"dp-dial-button-confirm\" class=\"bgabutton bgabutton_blue\">".concat(_('Confirm'), "</a>");
        }
        return result;
    };
    Object.defineProperty(DogOfferDial.prototype, "currentValue", {
        get: function () {
            return this._currentValue;
        },
        set: function (value) {
            this._currentValue = value;
            $(this.settings.elementId).dataset.value = value;
        },
        enumerable: false,
        configurable: true
    });
    return DogOfferDial;
}());
var DogPayCosts = /** @class */ (function () {
    function DogPayCosts(elementId, resources, dog, canBePlacedForFree, canBePlacedFor1Resource, onCancel, onConfirm) {
        this.elementId = elementId;
        this.resources = resources;
        this.dog = dog;
        this.canBePlacedForFree = canBePlacedForFree;
        this.canBePlacedFor1Resource = canBePlacedFor1Resource;
        this.onCancel = onCancel;
        this.onConfirm = onConfirm;
        this.remainingResources = { stick: 0, ball: 0, treat: 0, toy: 0 };
        this.selectedPayment = [];
        this.initiallyMissingResources = false;
        dojo.place('<div id="dp-dog-cost-pay-wrapper"></div>', $(this.elementId));
        this.resetSelection();
        this.updateUi();
    }
    DogPayCosts.prototype.resetSelection = function () {
        var _this = this;
        this.remainingResources = __assign({}, this.resources);
        this.selectedPayment = [];
        if (this.canBePlacedFor1Resource) {
            this.initiallyMissingResources = true;
            this.selectedPayment.push({ payUsing: ["placeholder"] });
        }
        else {
            Object.entries(this.dog.costs).forEach(function (_a) {
                var resource = _a[0], cost = _a[1];
                for (var i = 0; i < cost; i++) {
                    if (_this.remainingResources[resource] >= 1) {
                        _this.remainingResources[resource] -= 1;
                        _this.selectedPayment.push({ resource: resource, payUsing: [resource] });
                    }
                    else {
                        _this.initiallyMissingResources = true;
                        _this.selectedPayment.push({ resource: resource, payUsing: ["placeholder", "placeholder"] });
                    }
                }
            });
        }
    };
    DogPayCosts.prototype.updateUi = function () {
        var _this = this;
        var wrapperElement = $('dp-dog-cost-pay-wrapper');
        dojo.empty(wrapperElement);
        if (this.initiallyMissingResources) {
            dojo.place(this.createResourceButtons(), wrapperElement);
        }
        dojo.place(this.createCostRows(), wrapperElement);
        dojo.place(this.createMainButtons(), wrapperElement);
        if (this.initiallyMissingResources) {
            dojo.connect($("dp-dog-cost-pay-reset-button"), 'onclick', function () {
                _this.resetSelection();
                _this.updateUi();
            });
            Object.entries(this.remainingResources)
                .forEach(function (_a) {
                var resource = _a[0], nr = _a[1];
                return dojo.connect($("dp-dog-cost-pay-".concat(resource, "-button")), 'onclick', function () { return _this.useResource(resource); });
            });
        }
        dojo.connect($("dp-dog-cost-pay-cancel-button"), 'onclick', function () { _this.onCancel(); });
        dojo.connect($("dp-dog-cost-pay-confirm-button"), 'onclick', function () { _this.onConfirm(_this.selectedPayment.map(function (costRow) { return costRow.payUsing; }).flat(), false); });
        if (this.canBePlacedForFree) {
            dojo.connect($("dp-dog-cost-free-button"), 'onclick', function () { _this.onConfirm([], true); });
        }
    };
    DogPayCosts.prototype.createCostRows = function () {
        var _this = this;
        var result = "<div class=\"dp-dog-cost-pay-row\">".concat(_('Cost'), "<i class=\"fa fa-long-arrow-right\" aria-hidden=\"true\"></i>").concat(_('Pay using'), "</div>");
        if (this.canBePlacedFor1Resource) {
            result = "<i>".concat(_('Friendly: cost reduced to 1 resource of your choice'), "</i>");
        }
        this.selectedPayment.forEach(function (costRow) {
            result += "<div class=\"dp-dog-cost-pay-row\">";
            if (!_this.canBePlacedFor1Resource) {
                result += "<span class=\"dp-token-token\" data-type=\"".concat(costRow.resource, "\"></span><i class=\"fa fa-long-arrow-right\" aria-hidden=\"true\"></i>");
            }
            costRow.payUsing.forEach(function (selectedResource) {
                result += "<span class=\"dp-token-token\" data-type=\"".concat(selectedResource, "\"></span>");
            });
            result += '</div>';
        });
        return result;
    };
    DogPayCosts.prototype.createResourceButtons = function () {
        var _this = this;
        var result = "<div class=\"dp-dog-cost-pay-row\">";
        Object.entries(this.remainingResources)
            .forEach(function (_a) {
            var resource = _a[0], nr = _a[1];
            var disabled = _this.remainingResources[resource] <= 0 || _this.selectedPayment.map(function (costRow) { return costRow.payUsing; }).filter(function (payment) { return payment.includes('placeholder'); }).length == 0;
            result += "<a id=\"dp-dog-cost-pay-".concat(resource, "-button\" class=\"bgabutton bgabutton_blue ").concat(disabled ? 'disabled' : '', "\"><span class=\"dp-token-token\" data-type=\"").concat(resource, "\"></span></a>");
        });
        result += '</div>';
        return result;
    };
    DogPayCosts.prototype.createMainButtons = function () {
        var result = "<div class=\"dp-dog-cost-pay-row\">";
        var disabled = this.selectedPayment.map(function (costRow) { return costRow.payUsing; }).filter(function (payment) { return payment.includes('placeholder'); }).length > 0;
        if (this.canBePlacedForFree) {
            result += "<a id=\"dp-dog-cost-free-button\" class=\"bgabutton bgabutton_blue\">".concat(_('Place for Free (Forecast Card)'), "</a>");
        }
        result += "<a id=\"dp-dog-cost-pay-confirm-button\" class=\"bgabutton bgabutton_blue ".concat(disabled ? 'disabled' : '', "\">").concat(_('Confirm'), "</a>");
        if (this.initiallyMissingResources) {
            result += "<a id=\"dp-dog-cost-pay-reset-button\" class=\"bgabutton bgabutton_gray\">".concat(_('Reset'), "</a>");
        }
        result += "<a id=\"dp-dog-cost-pay-cancel-button\" class=\"bgabutton bgabutton_gray\">".concat(_('Cancel'), "</a>");
        result += '</div>';
        return result;
    };
    DogPayCosts.prototype.useResource = function (resource) {
        console.log('useResource');
        for (var _i = 0, _a = this.selectedPayment; _i < _a.length; _i++) {
            var costRow = _a[_i];
            var indexOf = costRow.payUsing.indexOf('placeholder');
            if (indexOf >= 0) {
                this.remainingResources[resource] -= 1;
                costRow.payUsing[indexOf] = resource;
                this.updateUi();
                break;
            }
        }
    };
    return DogPayCosts;
}());
var ChooseObjectives = /** @class */ (function () {
    function ChooseObjectives(game, elementId) {
        this.game = game;
        this.elementId = elementId;
    }
    ChooseObjectives.prototype.enter = function () {
        dojo.place('<div id="dp-choose-objectives-stock"></div>', $(this.elementId));
        if (!this.stock) {
            this.stock = new LineStock(this.game.objectiveCardManager, $('dp-choose-objectives-stock'), { gap: '25px', sort: function (a, b) { return a.typeArg - b.typeArg; } });
        }
        var player = this.game.getPlayer(this.game.getPlayerId());
        this.stock.addCards(player.objectives);
        if (this.game.isCurrentPlayerActive()) {
            this.stock.setSelectionMode('single', player.objectives);
        }
        else {
            var player_1 = this.game.getPlayer(this.game.getPlayerId());
            var selectedCard = this.stock.getCards().find(function (card) { return card.id === Number(player_1.selectedObjectiveCardId); });
            if (selectedCard) {
                this.stock.getCardElement(selectedCard).classList.add('bga-cards_selected-card');
            }
        }
    };
    ChooseObjectives.prototype.exit = function () {
        var selectedCard = this.stock.getSelection()[0];
        this.stock.setSelectionMode('none');
        this.stock.getCardElement(selectedCard).classList.add('bga-cards_selected-card');
    };
    ChooseObjectives.prototype.getSelectedObjectiveId = function () {
        var selection = this.stock.getSelection();
        if (selection.length > 0) {
            return selection[0].id;
        }
        return null;
    };
    ChooseObjectives.prototype.destroy = function () {
        dojo.empty($(this.elementId));
    };
    return ChooseObjectives;
}());
var ExchangeResources = /** @class */ (function () {
    function ExchangeResources(elementId, resources, targetResource, onCancel, onConfirm) {
        this.elementId = elementId;
        this.resources = resources;
        this.targetResource = targetResource;
        this.onCancel = onCancel;
        this.onConfirm = onConfirm;
        this.remainingResources = { stick: 0, ball: 0, treat: 0, toy: 0 };
        this.selectedPayment = 'placeholder';
        dojo.place('<div id="dp-exchange-resources-wrapper"></div>', $(this.elementId));
        this.resetSelection();
        this.updateUi();
    }
    ExchangeResources.prototype.resetSelection = function () {
        this.remainingResources = __assign({}, this.resources);
        this.selectedPayment = 'placeholder';
    };
    ExchangeResources.prototype.updateUi = function () {
        var _this = this;
        var wrapperElement = $('dp-exchange-resources-wrapper');
        dojo.empty(wrapperElement);
        if (this.selectedPayment == 'placeholder') {
            dojo.place(this.createResourceButtons(), wrapperElement);
            Object.entries(this.remainingResources)
                .forEach(function (_a) {
                var resource = _a[0], nr = _a[1];
                return dojo.connect($("dp-dog-cost-pay-".concat(resource, "-button")), 'onclick', function () { return _this.useResource(resource); });
            });
        }
        dojo.place(this.createCostRow(), wrapperElement);
        dojo.place(this.createMainButtons(), wrapperElement);
        if (this.selectedPayment != 'placeholder') {
            dojo.connect($("dp-dog-cost-pay-reset-button"), 'onclick', function () {
                _this.resetSelection();
                _this.updateUi();
            });
        }
        dojo.connect($("dp-dog-cost-pay-cancel-button"), 'onclick', function () { _this.onCancel(); });
        dojo.connect($("dp-dog-cost-pay-confirm-button"), 'onclick', function () { _this.onConfirm(_this.selectedPayment); });
    };
    ExchangeResources.prototype.createCostRow = function () {
        var result = "<div class=\"dp-dog-cost-pay-row\">".concat(_('Discard'), "<i class=\"fa fa-long-arrow-right\" aria-hidden=\"true\"></i>").concat(_('Gain'), "</div>");
        result += "<div class=\"dp-dog-cost-pay-row\"><span class=\"dp-token-token\" data-type=\"".concat(this.selectedPayment, "\"></span><i class=\"fa fa-long-arrow-right\" aria-hidden=\"true\"></i>");
        result += "<span class=\"dp-token-token\" data-type=\"".concat(this.targetResource, "\"></span>");
        result += '</div>';
        return result;
    };
    ExchangeResources.prototype.createResourceButtons = function () {
        var _this = this;
        var result = "<div class=\"dp-dog-cost-pay-row\">";
        Object.entries(this.remainingResources)
            .forEach(function (_a) {
            var resource = _a[0], nr = _a[1];
            var disabled = _this.remainingResources[resource] <= 0;
            result += "<a id=\"dp-dog-cost-pay-".concat(resource, "-button\" class=\"bgabutton bgabutton_blue ").concat(disabled ? 'disabled' : '', "\"><span class=\"dp-token-token\" data-type=\"").concat(resource, "\"></span></a>");
        });
        result += '</div>';
        return result;
    };
    ExchangeResources.prototype.createMainButtons = function () {
        var result = "<div class=\"dp-dog-cost-pay-row\">";
        var disabled = this.selectedPayment == 'placeholder';
        result += "<a id=\"dp-dog-cost-pay-confirm-button\" class=\"bgabutton bgabutton_blue ".concat(disabled ? 'disabled' : '', "\">").concat(_('Confirm'), "</a>");
        if (this.selectedPayment != 'placeholder') {
            result += "<a id=\"dp-dog-cost-pay-reset-button\" class=\"bgabutton bgabutton_gray\">".concat(_('Reset'), "</a>");
        }
        result += "<a id=\"dp-dog-cost-pay-cancel-button\" class=\"bgabutton bgabutton_gray\">".concat(_('Cancel'), "</a>");
        result += '</div>';
        return result;
    };
    ExchangeResources.prototype.useResource = function (resource) {
        this.selectedPayment = resource;
        this.updateUi();
    };
    return ExchangeResources;
}());
var FinalScoringPad = /** @class */ (function () {
    function FinalScoringPad(game, elementId) {
        var _this = this;
        this.game = game;
        this.elementId = elementId;
        this.scoringPadRows = [
            { key: 'parkBoardScore', getLabel: function () { return _this.getLabel('parkBoardScore'); } },
            { key: 'dogFinalScoringScore', getLabel: function () { return _this.getLabel('dogFinalScoringScore'); } },
            { key: 'breedExpertAwardScore', getLabel: function () { return _this.getLabel('breedExpertAwardScore'); } },
            { key: 'objectiveCardScore', getLabel: function () { return _this.getLabel('objectiveCardScore'); } },
            { key: 'remainingResourcesScore', getLabel: function () { return _this.getLabel('remainingResourcesScore'); } },
            { key: 'score', getLabel: function () { return _this.getLabel('score'); } }
        ];
    }
    FinalScoringPad.prototype.getLabel = function (category) {
        switch (category) {
            case 'parkBoardScore':
                return _('<icon-reputation> during game');
            case 'dogFinalScoringScore':
                return _('<icon-reputation> from dogs with <b>FINAL SCORING</b> abilities');
            case 'breedExpertAwardScore':
                return _('<icon-reputation> from won Breed Expert awards');
            case 'objectiveCardScore':
                return _('<icon-reputation> from completed Objective Card');
            case 'remainingResourcesScore':
                return _('Remaining resources = <icon-reputation> for every 5');
            case 'score':
                return _('Total <icon-reputation>');
        }
    };
    FinalScoringPad.prototype.setUp = function (gamedatas) {
        if (gamedatas.scoreBreakdown) {
            this.showPad(gamedatas.scoreBreakdown, false);
        }
    };
    FinalScoringPad.prototype.showPad = function (scoreBreakdown, animate) {
        if (Object.keys(scoreBreakdown).length == 1) {
            var playerScore = scoreBreakdown[Object.keys(scoreBreakdown)[0]];
            dojo.place("<div class=\"dp-solo-rating dp-board\">\n                                <h1>".concat(dojo.string.substitute(_('Solo Rating: ${soloStarRating} <i class="fa fa-star" aria-hidden="true"></i>'), { soloStarRating: playerScore.soloStarRating }), "</h1>\n                                ").concat(SoloRatings.getSoloRatingsTable(), "\n                             </div>"), $(this.elementId));
        }
        dojo.place(this.createPad(scoreBreakdown, animate), $(this.elementId));
        if (animate) {
            var elementsToReveal_1 = document.getElementById('dp-final-scoring-pad').querySelectorAll('.reveal-score-value');
            var currentIndex_1 = 0;
            return new Promise(function (resolve, reject) {
                var interval = setInterval(function () {
                    var element = elementsToReveal_1[currentIndex_1];
                    element.style.opacity = '1';
                    currentIndex_1++;
                    if (currentIndex_1 >= elementsToReveal_1.length) {
                        clearInterval(interval);
                        resolve('');
                    }
                }, 500);
            });
        }
        return Promise.resolve('');
    };
    FinalScoringPad.prototype.createPad = function (scoreBreakdown, animate) {
        var _this = this;
        var result = "\n            <div id=\"dp-final-scoring-pad\" class=\"dp-board\">\n                    <table>\n                        <thead>\n                        <th></th>\n";
        Object.keys(scoreBreakdown).forEach(function (playerId) {
            result += "<th style=\"color: #".concat(_this.game.getPlayer(Number(playerId)).color, "\">").concat(_this.game.getPlayer(Number(playerId)).name, "</th>");
        });
        result += "</thead><tbody>";
        this.scoringPadRows.forEach(function (row) {
            result += "<tr>";
            result += "<td>".concat(_this.game.formatWithIcons(row.getLabel()), "</td>");
            Object.keys(scoreBreakdown).forEach(function (playerId) {
                result += "<td class=\"reveal-score-value\" style=\"opacity: ".concat(animate ? 0 : 1, ";\">").concat(scoreBreakdown[playerId][row.key], " ").concat(row.key == 'breedExpertAwardScore' ? '<span class="breed-expert-additional-text">(' + scoreBreakdown[playerId]['scoreAux'] + '&uarr;)*</span>' : '', "</td>");
            });
            result += "</tr>";
        });
        result += "</tbody>\n                    </table>\n                    <p class=\"dp-final-scoring-pad-tiebreaker-explanation\">* ".concat(_('If there is a tie, the player who won the highest value Breed Expert award wins. If this is a tie, the winning players share the victory.'), "</p>\n                </div>");
        return result;
    };
    return FinalScoringPad;
}());
var GainResources = /** @class */ (function () {
    function GainResources(elementId, nrOfResourcesToGain, resourceOptions, canCancel, onCancel, onConfirm) {
        this.elementId = elementId;
        this.nrOfResourcesToGain = nrOfResourcesToGain;
        this.resourceOptions = resourceOptions;
        this.canCancel = canCancel;
        this.onCancel = onCancel;
        this.onConfirm = onConfirm;
        this.selectedResources = [];
        dojo.place('<div id="dp-gain-resources-wrapper"></div>', $(this.elementId));
        this.resetSelection();
        this.updateUi();
    }
    GainResources.prototype.resetSelection = function () {
        this.selectedResources = [];
        for (var i = 0; i < this.nrOfResourcesToGain; i++) {
            this.selectedResources.push('placeholder');
        }
    };
    GainResources.prototype.updateUi = function () {
        var _this = this;
        var wrapperElement = $('dp-gain-resources-wrapper');
        dojo.empty(wrapperElement);
        if (this.selectedResources.includes('placeholder')) {
            dojo.place(this.createResourceButtons(), wrapperElement);
            this.resourceOptions.forEach(function (resource) { return dojo.connect($("dp-dog-cost-pay-".concat(resource, "-button")), 'onclick', function () { return _this.useResource(resource); }); });
        }
        dojo.place(this.createCostRow(), wrapperElement);
        dojo.place(this.createMainButtons(), wrapperElement);
        if (this.selectedResources.some(function (value) { return value != 'placeholder'; })) {
            dojo.connect($("dp-dog-cost-pay-reset-button"), 'onclick', function () {
                _this.resetSelection();
                _this.updateUi();
            });
        }
        if (this.canCancel) {
            dojo.connect($("dp-dog-cost-pay-cancel-button"), 'onclick', function () { _this.onCancel(); });
        }
        dojo.connect($("dp-dog-cost-pay-confirm-button"), 'onclick', function () { _this.onConfirm(_this.selectedResources); });
    };
    GainResources.prototype.createCostRow = function () {
        var result = "<div class=\"dp-dog-cost-pay-row\">".concat(_('Gain'), "</div>");
        result += "<div class=\"dp-dog-cost-pay-row\">";
        this.selectedResources.forEach(function (resource) {
            result += "<span class=\"dp-token-token\" data-type=\"".concat(resource, "\"></span>");
        });
        result += '</div>';
        return result;
    };
    GainResources.prototype.createResourceButtons = function () {
        var result = "<div class=\"dp-dog-cost-pay-row\">";
        this.resourceOptions
            .forEach(function (resource) {
            result += "<a id=\"dp-dog-cost-pay-".concat(resource, "-button\" class=\"bgabutton bgabutton_blue\"><span class=\"dp-token-token\" data-type=\"").concat(resource, "\"></span></a>");
        });
        result += '</div>';
        return result;
    };
    GainResources.prototype.createMainButtons = function () {
        var result = "<div class=\"dp-dog-cost-pay-row\">";
        var disabled = this.selectedResources.includes('placeholder');
        result += "<a id=\"dp-dog-cost-pay-confirm-button\" class=\"bgabutton bgabutton_blue ".concat(disabled ? 'disabled' : '', "\">").concat(_('Confirm'), "</a>");
        if (this.selectedResources.some(function (value) { return value != 'placeholder'; })) {
            result += "<a id=\"dp-dog-cost-pay-reset-button\" class=\"bgabutton bgabutton_gray\">".concat(_('Reset'), "</a>");
        }
        if (this.canCancel) {
            result += "<a id=\"dp-dog-cost-pay-cancel-button\" class=\"bgabutton bgabutton_gray\">".concat(_('Cancel'), "</a>");
        }
        result += '</div>';
        return result;
    };
    GainResources.prototype.useResource = function (resource) {
        var index = this.selectedResources.indexOf('placeholder');
        this.selectedResources[index] = resource;
        this.updateUi();
    };
    return GainResources;
}());
var SoloRatings = /** @class */ (function () {
    function SoloRatings(elementId) {
        this.elementId = elementId;
        dojo.place("<div id=\"dp-solo-ratings-wrapper\">\n                            <h3>".concat(_('Solo Ratings'), "</h3>\n                            <p>").concat(_('To calculate your rating, add any star value from your objective card to the star value below, depending on your total score.'), "</p>\n                            <table>\n                                <thead><th>").concat(_('Total Score'), "</th><th>").concat(_('Star Value'), "</th></thead>\n                                <tbody>\n                                    <tr><td>&#x2264; 40</td><td>-</td></tr>\n                                    <tr><td>41-47</td><td><i class=\"fa fa-star\" aria-hidden=\"true\"></i></td></tr>\n                                    <tr><td>48-54</td><td><i class=\"fa fa-star\" aria-hidden=\"true\"></i><i class=\"fa fa-star\" aria-hidden=\"true\"></i></td></tr>\n                                    <tr><td>55-61</td><td><i class=\"fa fa-star\" aria-hidden=\"true\"></i><i class=\"fa fa-star\" aria-hidden=\"true\"></i><i class=\"fa fa-star\" aria-hidden=\"true\"></i></td></tr>\n                                    <tr><td>&#x2265; 62</td><td><i class=\"fa fa-star\" aria-hidden=\"true\"></i><i class=\"fa fa-star\" aria-hidden=\"true\"></i><i class=\"fa fa-star\" aria-hidden=\"true\"></i><i class=\"fa fa-star\" aria-hidden=\"true\"></i></td></tr>\n                                </tbody>\n                            </table>\n                            <p><a id=\"dp-solo-ratings-dialog-button\" class=\"bgabutton bgabutton_gray\">").concat(_('Compare Your Rating'), "</a></p>\n                         </div>"), $(elementId));
        dojo.connect($('dp-solo-ratings-dialog-button'), 'onclick', function (event) {
            dojo.stopEvent(event);
            var dialog = new ebg.popindialog();
            dialog.create('dp-solo-ratings-dialog');
            dialog.setTitle("".concat(_('Solo Ratings')));
            dialog.setContent(SoloRatings.getSoloRatingsTable());
            dialog.show();
        });
    }
    SoloRatings.getSoloRatingsTable = function () {
        return "      <div id=\"dp-solo-ratings-wrapper\">\n                            <table>\n                                <thead><th>".concat(_('Stars'), "</th><th>").concat(_('Your Rating'), "</th></thead>\n                                <tbody>\n                                    <tr><td>0-1 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Let's go again"), "</td></tr>\n                                    <tr><td>2 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Better luck next time"), "</td></tr>\n                                    <tr><td>3 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("More training required"), "</td></tr>\n                                    <tr><td>4 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Still an underdog"), "</td></tr>\n                                    <tr><td>5 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Middle of the pack"), "</td></tr>\n                                    <tr><td>6 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Not to be sniffed at"), "</td></tr>\n                                    <tr><td>7 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Rising star"), "</td></tr>\n                                    <tr><td>8 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Top dog"), "</td></tr>\n                                    <tr><td>9 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Super walker"), "</td></tr>\n                                    <tr><td>10 <i class=\"fa fa-star\" aria-hidden=\"true\"></i></td><td>").concat(_("Best in show"), "</td></tr>\n                                </tbody>\n                            </table>\n                         </div>\n            ");
    };
    return SoloRatings;
}());
var DogField = /** @class */ (function () {
    function DogField(game) {
        this.game = game;
        this.dogStocks = {};
        this.walkerStocks = {};
    }
    DogField.prototype.setUp = function (gameData) {
        dojo.place(this.createScoutArea(), 'dp-game-board-field-scout-wrapper');
        this.scoutedDogStock = new LineStock(this.game.dogCardManager, $('dp-game-board-field-scout'), { gap: '16px' });
        for (var i = 1; i <= gameData.field.nrOfFields; i++) {
            dojo.place(this.createFieldSlot(i), 'dp-game-board-field');
            this.dogStocks[i] = new LineStock(this.game.dogCardManager, $("dp-field-slot-".concat(i, "-dog")), {});
            this.walkerStocks[i] = new LineStock(this.game.dogWalkerManager, $("dp-field-slot-".concat(i, "-walkers")), {});
        }
        this.addDogCardsToScoutedField(gameData.field.scoutedDogs);
        this.addDogCardsToField(gameData.field.dogs);
        this.addWalkersToField(gameData.field.walkers);
    };
    DogField.prototype.addDogCardsToScoutedField = function (scoutedDogs) {
        this.scoutedDogStock.removeAll();
        var promise = this.scoutedDogStock.addCards(scoutedDogs);
        this.showHideScoutField();
        return promise;
    };
    DogField.prototype.showHideScoutField = function () {
        if (this.scoutedDogStock.getCards().length > 0) {
            $('dp-game-board-field-scout-wrapper').style.display = 'block';
        }
        else {
            $('dp-game-board-field-scout-wrapper').style.display = 'none';
        }
    };
    DogField.prototype.addDogCardsToField = function (dogs) {
        var _this = this;
        return dogs.filter(function (dog) { return dog.location === 'field'; })
            .map(function (dog) { return _this.dogStocks[dog.locationArg].addCard(dog).then(function () { return _this.showHideScoutField(); }); });
    };
    DogField.prototype.addWalkersToField = function (walkers) {
        var _this = this;
        return walkers.filter(function (walker) { return walker.location.startsWith('field_'); })
            .map(function (walker) { return _this.walkerStocks[Number(walker.location.replace('field_', ''))].addCard(walker); });
    };
    DogField.prototype.setDogSelectionModeField = function (selectionMode) {
        var _this = this;
        var _loop_5 = function (slotId) {
            this_2.dogStocks[slotId].onSelectionChange = selectionMode === 'none' ? undefined : function () {
                for (var otherSlotId in _this.dogStocks) {
                    if (slotId !== otherSlotId) {
                        _this.dogStocks[otherSlotId].unselectAll(true);
                    }
                }
            };
            this_2.dogStocks[slotId].setSelectionMode(selectionMode);
        };
        var this_2 = this;
        for (var slotId in this.dogStocks) {
            _loop_5(slotId);
        }
    };
    DogField.prototype.setDogSelectionModeScout = function (selectionMode) {
        this.scoutedDogStock.setSelectionMode(selectionMode);
    };
    DogField.prototype.getSelectedFieldDog = function () {
        for (var slotId in this.dogStocks) {
            if (this.dogStocks[slotId].getSelection() && this.dogStocks[slotId].getSelection().length === 1) {
                return this.dogStocks[slotId].getSelection()[0];
            }
        }
        return null;
    };
    DogField.prototype.getSelectedScoutDog = function () {
        if (this.scoutedDogStock.getSelection() && this.scoutedDogStock.getSelection().length === 1) {
            return this.scoutedDogStock.getSelection()[0];
        }
        return null;
    };
    DogField.prototype.discardDogFromField = function (fieldDog) {
        for (var slotId in this.dogStocks) {
            if (this.dogStocks[slotId].contains(fieldDog)) {
                this.game.dogCardManager.discardPile.addCard(fieldDog);
            }
        }
    };
    DogField.prototype.createFieldSlot = function (id) {
        return "<div id=\"dp-field-slot-".concat(id, "\" class=\"dp-field-slot\">\n                    <div id=\"dp-field-slot-").concat(id, "-dog\" class=\"dp-field-slot-card\">\n                    </div>\n                    <div id=\"dp-field-slot-").concat(id, "-walkers\" class=\"dp-field-slot-walkers\">\n                    </div>\n                </div>");
    };
    DogField.prototype.createScoutArea = function () {
        return "<div class=\"label-wrapper\" style=\"margin-bottom: 16px;\">\n                  <h2><div class=\"dp-token-token\" data-type=\"scout\"></div> ".concat(_('Scouted Cards'), "</h2>\n                </div>\n                <div id=\"dp-game-board-field-scout\"></div>");
    };
    return DogField;
}());
var DogWalkPark = /** @class */ (function () {
    function DogWalkPark(game) {
        this.game = game;
        this.walkerSpots = {};
        this.resourceSpots = {};
        this.possibleParkLocationIds = [];
        this.clickHandlers = [];
        this.element = $("dp-game-board-park");
    }
    DogWalkPark.prototype.setUp = function (gameData) {
        var _this = this;
        dojo.place("<div id=\"dp-walk-trail-start\" class=\"dp-walk-trail start\"></div>", this.element);
        dojo.place("<div id=\"dp-walk-trail-end\" class=\"dp-walk-trail end\"></div>", this.element);
        dojo.place("<div id=\"dp-walk-trail\" class=\"dp-walk-trail\"></div>", this.element);
        var trailWrapper = $('dp-walk-trail');
        for (var i = 1; i <= 10; i++) {
            dojo.place("<div id=\"park-column-".concat(i, "\" class=\"dp-park-column\"></div>"), trailWrapper);
        }
        this.walkerSpots[0] = new LineStock(this.game.dogWalkerManager, $("dp-walk-trail-start"), { direction: "column", wrap: "nowrap", gap: '0px' });
        for (var i = 1; i <= 15; i++) {
            this.createParkSpot(i, "park-column-".concat(DogWalkPark.spotColumnMap[i]));
            this.walkerSpots[i] = new LineStock(this.game.dogWalkerManager, $("dp-walker-spot-".concat(i)), { direction: "column", wrap: "nowrap", gap: '0px' });
            this.resourceSpots[i] = new LineStock(this.game.tokenManager, $("dp-resource-spot-".concat(i)), { direction: "column", wrap: "nowrap" });
        }
        for (var i = 91; i <= 94; i++) {
            this.createParkSpot(i, "dp-walk-trail-end");
            this.walkerSpots[i] = new LineStock(this.game.dogWalkerManager, $("dp-walker-spot-".concat(i)), { direction: "column", wrap: "nowrap", gap: '0px' });
        }
        this.moveWalkers(gameData.park.walkers);
        // Park Bonuses
        this.locationBonusCardPile = new AllVisibleDeck(this.game.locationBonusCardManager, $('dp-game-board-park-location-card-deck'), {});
        gameData.park.locationBonusCards.forEach(function (card) { return _this.addLocationBonusCard(card); });
        this.addExtraLocationBonuses(gameData.park.extraLocationBonuses);
    };
    DogWalkPark.prototype.addExtraLocationBonuses = function (extraLocationBonuses) {
        var _this = this;
        Object.values(this.resourceSpots).forEach(function (stock) { return stock.removeAll(); });
        extraLocationBonuses.forEach(function (locationBonus) { return _this.resourceSpots[locationBonus.locationId].addCard(_this.game.tokenManager.createToken(locationBonus.bonus)); });
    };
    DogWalkPark.prototype.moveWalkers = function (walkers) {
        var _this = this;
        return Promise.all(walkers.map(function (walker) { return _this.walkerSpots[walker.locationArg].addCard(walker); }));
    };
    DogWalkPark.prototype.enterWalkerSpotsSelection = function (possibleParkLocationIds, onClick) {
        var _this = this;
        this.possibleParkLocationIds = possibleParkLocationIds;
        this.possibleParkLocationIds.forEach(function (possibleParkLocationId) {
            var element = $("dp-walk-spot-".concat(possibleParkLocationId));
            _this.clickHandlers.push(dojo.connect(element, 'onclick', function () { onClick(possibleParkLocationId); }));
            element.classList.add('selectable');
        });
    };
    DogWalkPark.prototype.exitWalkerSpotsSelection = function () {
        this.possibleParkLocationIds.forEach(function (possibleParkLocationId) {
            var element = $("dp-walk-spot-".concat(possibleParkLocationId));
            element.classList.remove('selectable');
        });
        this.clickHandlers.forEach(function (clickHandler) { return dojo.disconnect(clickHandler); });
        this.clickHandlers = [];
    };
    DogWalkPark.prototype.addLocationBonusCard = function (card) {
        return this.locationBonusCardPile.addCard(card);
    };
    DogWalkPark.prototype.createParkSpot = function (id, parentElement) {
        dojo.place("<div id=\"dp-walk-spot-".concat(id, "\" class=\"dp-walk-spot\" data-spot-id=\"").concat(id, "\">\n                            <div id=\"dp-resource-spot-").concat(id, "\" class=\"dp-resource-spot\"></div>\n                            <div id=\"dp-walker-spot-").concat(id, "\" class=\"dp-walker-spot\"></div>\n                         </div>"), $(parentElement));
    };
    DogWalkPark.spotColumnMap = {
        '1': 1,
        '2': 2,
        '3': 3,
        '4': 4,
        '5': 5,
        '6': 5,
        '7': 6,
        '8': 6,
        '9': 7,
        '10': 7,
        '11': 8,
        '12': 8,
        '13': 9,
        '14': 9,
        '15': 10,
    };
    return DogWalkPark;
}());
var PlayerArea = /** @class */ (function () {
    function PlayerArea(game) {
        this.game = game;
        this.walkerStocks = {};
        this.kennelStocks = {};
        this.leadStocks = {};
        this.playerDials = {};
        this.playerObjective = {};
    }
    PlayerArea.prototype.setUp = function (gameData) {
        for (var playerId in gameData.players) {
            var player = gameData.players[playerId];
            this.createPlayerPanels(player);
            var playerArea = this.createPlayerArea(player);
            if (Number(player.id) === this.game.getPlayerId()) {
                dojo.place(playerArea, "dp-own-player-area");
            }
            else {
                dojo.place(playerArea, "dp-player-areas");
            }
        }
        for (var playerId in gameData.players) {
            var player = gameData.players[playerId];
            this.playerDials[Number(player.id)] = new DogOfferDial({
                elementId: "dp-game-board-offer-dial-".concat(player.id),
                parentId: "dp-player-token-wrapper-".concat(player.id),
                player: player,
                readOnly: true,
                initialValue: player.offerValue
            });
            var dogWalkerStockId = "dp-walker-rest-area-".concat(player.id);
            dojo.place("<div id=\"".concat(dogWalkerStockId, "\"></div>"), $("dp-player-token-wrapper-".concat(player.id)));
            this.walkerStocks[Number(player.id)] = new LineStock(this.game.dogWalkerManager, $(dogWalkerStockId), { center: false });
            this.moveWalkerToPlayer(Number(player.id), player.walker);
            var kennelStockId = "dp-player-area-".concat(player.id, "-kennel");
            this.kennelStocks[Number(player.id)] = new LineStock(this.game.dogCardManager, $(kennelStockId), { center: true });
            this.moveDogsToKennel(Number(player.id), player.kennelDogs);
            var leadStockId = "dp-player-area-".concat(player.id, "-lead");
            this.leadStocks[Number(player.id)] = new LineStock(this.game.dogCardManager, $(leadStockId), { center: false });
            this.moveDogsToLead(Number(player.id), player.leadDogs);
            var objectiveStockId = "dp-player-objective-card-".concat(player.id);
            this.playerObjective[Number(player.id)] = new LineStock(this.game.objectiveCardManager, $(objectiveStockId), {});
            this.moveObjectiveToPlayer(Number(player.id), player.chosenObjective);
            if (player.orderNo === 1) {
                dojo.place(this.createFirsPlayerMarker(), $("dp-player-token-wrapper-".concat(player.id)));
            }
        }
        for (var _i = 0, _a = gameData.autoWalkers; _i < _a.length; _i++) {
            var autoWalker = _a[_i];
            dojo.place("<div id=\"dp-player-area-".concat(autoWalker.id, "\" class=\"whiteblock dp-player-area\" style=\"background-color: #").concat(autoWalker.color, ";\">\n                            <div class=\"label-wrapper\">\n                                <h2 style=\"color: #").concat(autoWalker.color, ";\">").concat(autoWalker.name, "</h2>\n                            </div>\n                            <div class=\"dp-player-area-section-wrapper\">\n                                <div class=\"label-wrapper vertical\">\n                                    <h2 style=\"color: #").concat(autoWalker.color, ";\">").concat(_('Kennel'), "</h2>\n                                </div>\n                                <div id=\"dp-player-area-").concat(autoWalker.id, "-kennel\" class=\"dp-player-area-kennel\">\n                                </div>\n                            </div>\n                        </div>"), "dp-player-areas");
            var kennelStockId = "dp-player-area-".concat(autoWalker.id, "-kennel");
            this.kennelStocks[Number(autoWalker.id)] = new LineStock(this.game.dogCardManager, $(kennelStockId), { center: true });
            this.moveDogsToKennel(Number(autoWalker.id), autoWalker.kennelDogs);
        }
    };
    PlayerArea.prototype.moveObjectiveToPlayer = function (playerId, objectiveCard) {
        var _this = this;
        if (objectiveCard) {
            dojo.connect($('player-table-objective-button'), 'onclick', function (event) { return _this.game.helpDialogManager.showObjectiveHelpDialog(event, objectiveCard); });
            return this.playerObjective[playerId].addCard(objectiveCard);
        }
        return Promise.resolve(true);
    };
    PlayerArea.prototype.moveWalkerToPlayer = function (playerId, walker) {
        if (walker && walker.location == 'player') {
            return this.walkerStocks[playerId].addCard(walker);
        }
        return Promise.resolve(true);
    };
    PlayerArea.prototype.moveDogsToKennel = function (playerId, dogs) {
        return this.kennelStocks[playerId].addCards(dogs);
    };
    PlayerArea.prototype.moveDogsToLead = function (playerId, dogs) {
        return this.leadStocks[playerId].addCards(dogs);
    };
    PlayerArea.prototype.setPlayerOfferValue = function (playerId, offerValue) {
        this.playerDials[playerId].currentValue = offerValue;
    };
    PlayerArea.prototype.resetAllOfferValues = function () {
        for (var playerId in this.playerDials) {
            this.playerDials[playerId].currentValue = null;
        }
    };
    PlayerArea.prototype.setSelectionModeForKennel = function (selectionMode, playerId, selectableDogs, onSelect) {
        this.kennelStocks[playerId].setSelectionMode(selectionMode);
        if (selectionMode != 'none') {
            this.kennelStocks[playerId].setSelectableCards(selectableDogs);
            this.kennelStocks[playerId].onSelectionChange = onSelect;
        }
        else {
            this.kennelStocks[playerId].onSelectionChange = undefined;
        }
    };
    PlayerArea.prototype.getSelectedKennelDog = function (playerId) {
        var selection = this.kennelStocks[playerId].getSelection();
        if (selection.length > 0) {
            return selection[0];
        }
        return null;
    };
    PlayerArea.prototype.setNewFirstWalker = function (playerId) {
        var element = $('dp-first-player-marker');
        return this.game.animationManager.play(new BgaAttachWithAnimation({
            animation: new BgaSlideAnimation({ element: element, transitionTimingFunction: 'ease-out' }),
            attachElement: $("dp-player-token-wrapper-".concat(playerId))
        }));
    };
    PlayerArea.prototype.initAutoWalkers = function (autoWalker) {
        dojo.place("<div id=\"overall_auto_walker_".concat(autoWalker.id, "_board\" class=\"player-board\" style=\"height: auto;\">\n                                    <div class=\"player_board_inner\">\n                                        <div class=\"player-name\" id=\"player_name_").concat(autoWalker.id, "\" style=\"color: #").concat(autoWalker.color, "\">\n                                            ").concat(autoWalker.name, "                    \n                                        </div>\n                                        <div class=\"player_board_content\">\n                                            <div id=\"dp-player-token-wrapper-").concat(autoWalker.id, "\" class=\"dp-player-token-wrapper\"></div>\n                                            <div style=\"display: flex; justify-content: center\">\n                                                <div id=\"dp-autowalker-die-").concat(autoWalker.id, "\" class=\"dp-dice\" data-value=\"").concat(autoWalker.lastDieRoll, "\">\n                                                    <div class=\"side\" data-side=\"1\"></div>\n                                                    <div class=\"side\" data-side=\"2\"></div>\n                                                    <div class=\"side\" data-side=\"3\"></div>\n                                                    <div class=\"side\" data-side=\"4\"></div>\n                                                    <div class=\"side\" data-side=\"5\"></div>\n                                                    <div class=\"side\" data-side=\"6\"></div>\n                                                </div>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div>"), "player_boards");
        this.playerDials[Number(autoWalker.id)] = new DogOfferDial({
            elementId: "dp-game-board-offer-dial-".concat(autoWalker.id),
            parentId: "dp-player-token-wrapper-".concat(autoWalker.id),
            player: autoWalker,
            readOnly: true,
            initialValue: autoWalker.offerValue
        });
        var dogWalkerStockId = "dp-walker-rest-area-".concat(autoWalker.id);
        dojo.place("<div id=\"".concat(dogWalkerStockId, "\"></div>"), $("dp-player-token-wrapper-".concat(autoWalker.id)));
        this.walkerStocks[Number(autoWalker.id)] = new LineStock(this.game.dogWalkerManager, $(dogWalkerStockId), { center: false });
        this.moveWalkerToPlayer(autoWalker.id, autoWalker.walker);
    };
    PlayerArea.prototype.createPlayerArea = function (player) {
        return "<div id=\"player-table-".concat(player.id, "\" class=\"whiteblock dp-player-area\" style=\"background-color: #").concat(player.color, ";\">\n                    <div id=\"player-table-").concat(player.id, "-resources\" class=\"player-table-resources\">\n                        ").concat(['stick', 'ball', 'toy', 'treat'].map(function (resource) { return "<span><span class=\"dp-token-token small\" data-type=\"".concat(resource, "\"></span><span id=\"player-table-").concat(resource, "-counter-").concat(player.id, "\" style=\"vertical-align: middle; padding: 0 5px;\"></span></span>"); }).join(''), "\n                        ").concat(this.game.getPlayerId() == Number(player.id) ? "<a id=\"player-table-objective-button\" class=\"bgabutton bgabutton_gray\">".concat(_('Objective'), "</a>") : '', "\n                    </div>\n                    <div class=\"label-wrapper\">\n                        <h2 style=\"color: #").concat(player.color, ";\">").concat(player.name, "</h2>\n                    </div>\n                    <div class=\"dp-player-area-section-wrapper\">\n                        <div class=\"label-wrapper vertical\">\n                            <h2 style=\"color: #").concat(player.color, ";\">").concat(_('Lead'), "</h2>\n                        </div>\n                        <div class=\"dp-lead-board dp-board\" data-color=\"#").concat(player.color, "\">\n                            <div id=\"dp-player-area-").concat(player.id, "-lead\" class=\"dp-lead-board-lead\"></div>\n                        </div>\n                    </div>\n                    <div class=\"dp-player-area-section-wrapper\">\n                        <div class=\"label-wrapper vertical\">\n                            <h2 style=\"color: #").concat(player.color, ";\">").concat(_('Kennel'), "</h2>\n                        </div>\n                        <div id=\"dp-player-area-").concat(player.id, "-kennel\" class=\"dp-player-area-kennel\"> \n                        </div>\n                    </div>\n                </div>");
    };
    PlayerArea.prototype.createPlayerPanels = function (player) {
        dojo.place("<div id=\"dp-player-resources-".concat(player.id, "\" class=\"dp-player-resources\">\n                            <div id=\"dp-player-dummy-resources-").concat(player.id, "\" style=\"height: 0; width: 0; overflow: hidden;\"></div>\n                          </div>\n                          <div id=\"dp-player-token-wrapper-").concat(player.id, "\" class=\"dp-player-token-wrapper\"></div>\n                          <div id=\"dp-player-objective-card-").concat(player.id, "\"  class=\"dp-player-objective-card\"></div>"), "player_board_".concat(player.id));
    };
    PlayerArea.prototype.createFirsPlayerMarker = function () {
        return "<div id=\"dp-first-player-marker\" class=\"dp-token dp-first-player-marker\"></div>";
    };
    return PlayerArea;
}());
var PlayerResources = /** @class */ (function () {
    function PlayerResources(game) {
        this.game = game;
        this.playerResourceStocks = {};
    }
    PlayerResources.prototype.setUp = function (gameData) {
        for (var playerId in gameData.players) {
            var player = gameData.players[playerId];
            var resources = player.resources;
            this.playerResourceStocks[playerId] = {};
            var _loop_6 = function (resource) {
                this_3.playerResourceStocks[playerId][resource] = new CounterVoidStock(this_3.game, this_3.game.tokenManager, {
                    counter: new ebg.counter(),
                    targetElement: "dp-player-resources-".concat(player.id),
                    counterId: "dp-player-".concat(resource, "-counter-").concat(player.id),
                    initialCounterValue: resources[resource],
                    setupIcon: function (element) {
                        element.classList.add("dp-token-token");
                        element.classList.add("small");
                        element.dataset.type = resource;
                    }
                });
                this_3.playerResourceStocks[playerId][resource].counter.addTarget($("player-table-".concat(resource, "-counter-").concat(player.id)));
            };
            var this_3 = this;
            for (var resource in resources) {
                _loop_6(resource);
            }
        }
    };
    PlayerResources.prototype.payResourcesToDog = function (playerId, dog, resources) {
        return __awaiter(this, void 0, void 0, function () {
            var _i, resources_1, resource, token;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        resources.forEach(function (resource) { return _this.playerResourceStocks[playerId][resource].decValue(1); });
                        _i = 0, resources_1 = resources;
                        _a.label = 1;
                    case 1:
                        if (!(_i < resources_1.length)) return [3 /*break*/, 4];
                        resource = resources_1[_i];
                        token = this.game.tokenManager.createToken(resource);
                        return [4 /*yield*/, this.game.dogCardManager.cardTokenVoidStocks[dog.id].addCard(token, { fromStock: this.playerResourceStocks[playerId][resource] })];
                    case 2:
                        _a.sent();
                        _a.label = 3;
                    case 3:
                        _i++;
                        return [3 /*break*/, 1];
                    case 4: return [2 /*return*/];
                }
            });
        });
    };
    PlayerResources.prototype.gainResourcesFromDog = function (playerId, dog, resources) {
        return __awaiter(this, void 0, void 0, function () {
            var _i, resources_2, resource, token;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        resources.forEach(function (resource) { return _this.playerResourceStocks[playerId][resource].incValue(1); });
                        _i = 0, resources_2 = resources;
                        _a.label = 1;
                    case 1:
                        if (!(_i < resources_2.length)) return [3 /*break*/, 4];
                        resource = resources_2[_i];
                        token = this.game.tokenManager.createToken(resource);
                        return [4 /*yield*/, this.playerResourceStocks[playerId][resource].addCard(token, { fromStock: this.game.dogCardManager.cardTokenVoidStocks[dog.id] })];
                    case 2:
                        _a.sent();
                        _a.label = 3;
                    case 3:
                        _i++;
                        return [3 /*break*/, 1];
                    case 4: return [2 /*return*/];
                }
            });
        });
    };
    PlayerResources.prototype.gainResourcesFromForecastCard = function (playerId, foreCastCard, resources) {
        return __awaiter(this, void 0, void 0, function () {
            var _i, resources_3, resource, token;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        resources.forEach(function (resource) { return _this.playerResourceStocks[playerId][resource].incValue(1); });
                        _i = 0, resources_3 = resources;
                        _a.label = 1;
                    case 1:
                        if (!(_i < resources_3.length)) return [3 /*break*/, 4];
                        resource = resources_3[_i];
                        token = this.game.tokenManager.createToken(resource);
                        return [4 /*yield*/, this.playerResourceStocks[playerId][resource].addCard(token, { fromElement: this.game.forecastManager.getCardElement(foreCastCard) })];
                    case 2:
                        _a.sent();
                        _a.label = 3;
                    case 3:
                        _i++;
                        return [3 /*break*/, 1];
                    case 4: return [2 /*return*/];
                }
            });
        });
    };
    PlayerResources.prototype.gainResourceFromLocation = function (playerId, locationId, resource) {
        return __awaiter(this, void 0, void 0, function () {
            var stock, token;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        this.playerResourceStocks[playerId][resource].incValue(1);
                        stock = this.game.dogWalkPark.resourceSpots[locationId];
                        token = this.game.tokenManager.createToken(resource);
                        return [4 /*yield*/, this.playerResourceStocks[playerId][resource].addCard(token, { fromStock: stock })];
                    case 1:
                        _a.sent();
                        return [2 /*return*/];
                }
            });
        });
    };
    PlayerResources.prototype.gainResources = function (playerId, resources, fromElementId) {
        return __awaiter(this, void 0, void 0, function () {
            var _i, resources_4, resource, token;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        resources.forEach(function (resource) { return _this.playerResourceStocks[playerId][resource].incValue(1); });
                        _i = 0, resources_4 = resources;
                        _a.label = 1;
                    case 1:
                        if (!(_i < resources_4.length)) return [3 /*break*/, 4];
                        resource = resources_4[_i];
                        token = this.game.tokenManager.createToken(resource);
                        return [4 /*yield*/, this.playerResourceStocks[playerId][resource].addCard(token)];
                    case 2:
                        _a.sent();
                        _a.label = 3;
                    case 3:
                        _i++;
                        return [3 /*break*/, 1];
                    case 4: return [2 /*return*/];
                }
            });
        });
    };
    PlayerResources.prototype.payResources = function (playerId, resources) {
        return __awaiter(this, void 0, void 0, function () {
            var _this = this;
            return __generator(this, function (_a) {
                resources.forEach(function (resource) { return _this.playerResourceStocks[playerId][resource].decValue(1); });
                return [2 /*return*/];
            });
        });
    };
    return PlayerResources;
}());
var RoundTracker = /** @class */ (function () {
    function RoundTracker(game) {
        this.game = game;
    }
    RoundTracker.prototype.setUp = function (gameData) {
        this.updateRound(gameData.currentRound);
        this.updatePhase(gameData.currentPhase);
    };
    RoundTracker.prototype.updateRound = function (round) {
        $(RoundTracker.elementId).dataset.round = round;
    };
    RoundTracker.prototype.updatePhase = function (phase) {
        $(RoundTracker.elementId).dataset.phase = this.toPhaseId(phase);
        this.resetFocus();
        this.setFocus(phase);
    };
    RoundTracker.prototype.resetFocus = function () {
        $('dp-game-board-park-wrapper').style.order = 10;
        $('dp-game-board-field-wrapper').style.order = 11;
        $('dp-own-player-area').style.order = 12;
        $('dp-game-board-field-scout-wrapper').style.order = 13;
    };
    RoundTracker.prototype.setFocus = function (phase) {
        switch (phase) {
            case 'PHASE_RECRUITMENT_1':
            case 'PHASE_RECRUITMENT_2':
                $('dp-game-board-field-wrapper').style.order = 2;
                $('dp-own-player-area').style.order = 3;
                break;
            case 'PHASE_SELECTION':
                $('dp-own-player-area').style.order = 1;
                break;
            case 'PHASE_WALKING':
                $('dp-game-board-park-wrapper').style.order = 1;
                $('dp-own-player-area').style.order = 2;
                break;
        }
    };
    RoundTracker.prototype.setScoutFocus = function () {
        this.resetFocus();
        $('dp-game-board-field-scout-wrapper').style.order = 1;
        $('dp-game-board-field-wrapper').style.order = 2;
        $('dp-game-board-park-wrapper').style.order = 3;
        $('dp-own-player-area').style.order = 4;
    };
    RoundTracker.prototype.setSwapFocus = function () {
        this.resetFocus();
        $('dp-own-player-area').style.order = 1;
        $('dp-game-board-field-wrapper').style.order = 2;
        $('dp-game-board-park-wrapper').style.order = 3;
    };
    RoundTracker.prototype.unsetFocus = function () {
        this.resetFocus();
        this.setFocus(this.toPhase(Number($(RoundTracker.elementId).dataset.phase)));
    };
    RoundTracker.prototype.toPhaseId = function (phase) {
        switch (phase) {
            case 'PHASE_RECRUITMENT_1':
            case 'PHASE_RECRUITMENT_2':
                return 1;
            case 'PHASE_SELECTION':
                return 2;
            case 'PHASE_WALKING':
                return 3;
            case 'PHASE_HOME_TIME':
                return 4;
        }
    };
    RoundTracker.prototype.toPhase = function (phaseId) {
        switch (phaseId) {
            case 1:
                return 'PHASE_RECRUITMENT_1';
            case 2:
                return 'PHASE_SELECTION';
            case 3:
                return 'PHASE_WALKING';
            case 4:
                return 'PHASE_HOME_TIME';
        }
    };
    RoundTracker.elementId = 'dp-round-tracker';
    return RoundTracker;
}());
var ZOOM_LEVELS = [0.25, 0.375, 0.5, 0.625, 0.75, 0.875, 1];
var ANIMATION_MS = 800;
var TOOLTIP_DELAY = document.body.classList.contains('touch-device') ? 1500 : undefined;
var LOCAL_STORAGE_ZOOM_KEY = 'dogpark-zoom';
var DogPark = /** @class */ (function () {
    function DogPark() {
        var _this = this;
        ///////////////////////////////////////////////////
        //// Utility methods
        ///////////////////////////////////////////////////
        this.delay = function (ms) { return __awaiter(_this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!this.animationManager.animationsActive()) return [3 /*break*/, 2];
                        return [4 /*yield*/, new Promise(function (resolve) { return setTimeout(resolve, ms); })];
                    case 1:
                        _a.sent();
                        return [3 /*break*/, 4];
                    case 2: return [4 /*yield*/, Promise.resolve()];
                    case 3:
                        _a.sent();
                        _a.label = 4;
                    case 4: return [2 /*return*/];
                }
            });
        }); };
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
        this.finalScoringPad = new FinalScoringPad(this, 'dp-final-scoring-pad-wrapper');
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
    DogPark.prototype.setup = function (gamedatas) {
        this.setAlwaysFixTopActions();
        log("Starting game setup");
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
        this.zoomManager = new AutoZoomManager('dp-game', 'dp-zoom-level');
        this.animationManager = new AnimationManager(this, { duration: ANIMATION_MS });
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
        dojo.place('<div id="custom-actions"></div>', $('maintitlebar_content'), 'last');
        this.setupNotifications();
        log("Ending game setup");
    };
    ///////////////////////////////////////////////////
    //// Game & client states
    // onEnteringState: this method is called each time we are entering into a new game state.
    //                  You can use this method to perform some user interface changes at this moment.
    //
    DogPark.prototype.onEnteringState = function (stateName, args) {
        log('Entering state: ' + stateName, args.args);
        switch (stateName) {
            case 'chooseObjectives':
                this.enteringChooseObjectives();
                break;
            case 'recruitmentOffer':
                this.enteringRecruitmentOffer(args.args);
                break;
            case 'recruitmentTakeDog':
                this.enteringRecruitmentTakeDog();
                break;
            case 'selectionPlaceDogOnLead':
                this.enteringSelectionPlaceDogOnLead(args.args);
                break;
            case 'selectionPlaceDogOnLeadSelectResources':
                this.enteringSelectionPlaceDogOnLeadSelectResources(args.args);
                break;
            case 'walkingMoveWalker':
                this.enteringWalkingMoveWalker(args.args);
                break;
            case 'actionSwap':
                this.enteringActionSwap(args.args);
                break;
            case 'actionScout':
                this.enteringActionScout(args.args);
                break;
            case 'actionMoveAutoWalker':
                this.enteringActionMoveAutoWalker(args.args);
                break;
            case 'actionCrafty':
                this.enteringActionCrafty(args.args);
                break;
            case 'actionGainResourcesPrivate':
                this.enteringGainResources(args.args);
                break;
            case 'actionGainResources':
                this.enteringGainResources(args.args[this.getPlayerId()]);
                break;
            case 'actionGlobetrotter':
                this.enteringGlobetrotter(args.args);
                break;
        }
    };
    DogPark.prototype.enteringChooseObjectives = function () {
        if (this.isCurrentPlayerActive()) {
            this.currentPlayerChooseObjectives = new ChooseObjectives(this, "dp-choose-objectives");
            this.currentPlayerChooseObjectives.enter();
        }
    };
    DogPark.prototype.enteringRecruitmentOffer = function (args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            if (args.maxOfferValue > 0) {
                this.dogField.setDogSelectionModeField('single');
                this.currentPlayerOfferDial = new DogOfferDial({
                    elementId: 'dp-current-player-offer-dial',
                    parentId: 'custom-actions',
                    player: this.getPlayer(this.getPlayerId()),
                    initialValue: 1,
                    maxOfferValue: args.maxOfferValue,
                    readOnly: false,
                    onConfirm: function () {
                        _this.placeOfferOnDog();
                    }
                });
            }
            else {
                this.gamedatas.gamestate.descriptionmyturn = _(this.gamedatas.gamestate.descriptionmyturn) + '<br />' + _('Insufficient reputation to place an offer') + '<br />';
                this.updatePageTitle();
            }
        }
    };
    DogPark.prototype.enteringRecruitmentTakeDog = function () {
        if (this.isCurrentPlayerActive()) {
            this.dogField.setDogSelectionModeField('single');
        }
    };
    DogPark.prototype.enteringSelectionPlaceDogOnLead = function (args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            if (args.numberOfDogsOnlead < args.maxNumberOfDogs) {
                this.playerArea.setSelectionModeForKennel('single', this.getPlayerId(), Object.values(args.dogs), function (selection) {
                    if (selection.length === 1) {
                        _this.playerArea.setSelectionModeForKennel('none', _this.getPlayerId());
                        _this.takeNoLockAction("placeDogOnLead", { dogId: selection[0].id });
                    }
                });
            }
        }
    };
    DogPark.prototype.enteringSelectionPlaceDogOnLeadSelectResources = function (args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            new DogPayCosts("custom-actions", args.resources, args.dog, args.freeDogsOnLead > 0, args.nextDogCosts1Resource, function () {
                dojo.empty('custom-actions');
                _this.takeNoLockAction('placeDogOnLeadCancel');
            }, function (resources, isFreePlacement) {
                dojo.empty('custom-actions');
                _this.takeNoLockAction('placeDogOnLeadPayResources', { dogId: args.dog.id, isFreePlacement: isFreePlacement, resources: JSON.stringify(resources) });
            });
        }
    };
    DogPark.prototype.enteringWalkingMoveWalker = function (args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            this.dogWalkPark.enterWalkerSpotsSelection(args.possibleParkLocationIds, function (locationId) { _this.takeAction('moveWalker', { locationId: locationId }); });
        }
    };
    DogPark.prototype.enteringActionSwap = function (args) {
        if (this.isCurrentPlayerActive()) {
            this.playerArea.setSelectionModeForKennel("single", this.getPlayerId(), args.dogsInKennel);
            this.dogField.setDogSelectionModeField('single');
            this.roundTracker.setSwapFocus();
        }
    };
    DogPark.prototype.enteringActionScout = function (args) {
        this.dogField.addDogCardsToScoutedField(args.scoutedDogCards);
        if (this.isCurrentPlayerActive() && args.scoutedDogCards && args.scoutedDogCards.length > 0) {
            this.dogField.setDogSelectionModeField('single');
            this.dogField.setDogSelectionModeScout('single');
            this.roundTracker.setScoutFocus();
        }
    };
    DogPark.prototype.enteringActionMoveAutoWalker = function (args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            this.dogWalkPark.enterWalkerSpotsSelection(args.possibleParkLocationIds, function (locationId) { _this.takeAction('moveAutoWalker', { locationId: locationId }); });
        }
    };
    DogPark.prototype.enteringActionCrafty = function (args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            new ExchangeResources("custom-actions", args.resources, args.dog.craftyResource, function () {
                dojo.empty('custom-actions');
                _this.takeNoLockAction('cancelCrafty');
            }, function (resource) {
                dojo.empty('custom-actions');
                _this.takeNoLockAction('confirmCrafty', { resource: resource });
            });
        }
    };
    DogPark.prototype.enteringGainResources = function (args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            new GainResources('custom-actions', args.nrOfResourcesToGain, args.resourceOptions, args.canCancel, function () {
                dojo.empty('custom-actions');
                _this.takeNoLockAction('cancelGainResources');
            }, function (resources) {
                dojo.empty('custom-actions');
                _this.takeNoLockAction('confirmGainResources', { resources: JSON.stringify(resources) });
            });
        }
    };
    DogPark.prototype.enteringGlobetrotter = function (args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            this.dogWalkPark.enterWalkerSpotsSelection(args.possibleParkLocationIds, function (locationId) { _this.takeAction('confirmGlobetrotter', { locationId: locationId }); });
        }
    };
    DogPark.prototype.onLeavingState = function (stateName) {
        log('Leaving state: ' + stateName);
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
    };
    DogPark.prototype.leavingSelectionPlaceDogOnLead = function () {
        this.playerArea.setSelectionModeForKennel('none', this.getPlayerId());
    };
    DogPark.prototype.leavingWalkingMoveWalker = function () {
        if (this.isCurrentPlayerActive()) {
            this.dogWalkPark.exitWalkerSpotsSelection();
        }
    };
    DogPark.prototype.leavingActionGlobetrotter = function () {
        if (this.isCurrentPlayerActive()) {
            this.dogWalkPark.exitWalkerSpotsSelection();
        }
    };
    DogPark.prototype.leavingActionSwap = function () {
        if (this.isCurrentPlayerActive()) {
            this.playerArea.setSelectionModeForKennel('none', this.getPlayerId());
            this.dogField.setDogSelectionModeField('none');
            this.roundTracker.unsetFocus();
        }
    };
    DogPark.prototype.leavingActionScout = function () {
        this.dogCardManager.discardPile.addCards(this.dogField.scoutedDogStock.getCards());
        if (this.isCurrentPlayerActive()) {
            this.dogField.setDogSelectionModeScout('none');
            this.dogField.setDogSelectionModeField('none');
            this.roundTracker.unsetFocus();
        }
    };
    DogPark.prototype.leavingActionMoveAutoWalker = function () {
        if (this.isCurrentPlayerActive()) {
            this.dogWalkPark.exitWalkerSpotsSelection();
        }
    };
    // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
    //                        action status bar (ie: the HTML links in the status bar).
    //
    DogPark.prototype.onUpdateActionButtons = function (stateName, args) {
        var _this = this;
        if (this.isCurrentPlayerActive()) {
            switch (stateName) {
                case 'chooseObjectives':
                    this.addActionButton('confirmObjective', _("Confirm objective"), function () { return _this.confirmObjective(); });
                    break;
                case 'recruitmentOffer':
                    if (args.maxOfferValue == 0) {
                        this.addActionButton('skipPlaceOfferOnDog', _("Skip"), function () { return _this.skipPlaceOfferOnDog(); });
                    }
                    break;
                case 'recruitmentTakeDog':
                    this.addActionButton('takeDog', _("Confirm"), function () { return _this.recruitDog(); });
                    break;
                case 'selectionPlaceDogOnLead':
                    var selectionPlaceDogOnLeadArgs_1 = args;
                    this.addAdditionalActionButtons(args.additionalActions);
                    this.addActionButton('confirmSelection', _("Confirm Selection"), function () { return _this.confirmSelection(selectionPlaceDogOnLeadArgs_1); });
                    if ((selectionPlaceDogOnLeadArgs_1.numberOfDogsOnlead < 1 && Object.keys(selectionPlaceDogOnLeadArgs_1.dogs).length >= 1) || selectionPlaceDogOnLeadArgs_1.additionalActions.some(function (action) { return !action.optional; })) {
                        dojo.addClass('confirmSelection', 'disabled');
                    }
                    break;
                case 'walkingMoveWalkerAfter':
                    var walkingMoveWalkerAfterArgs = args;
                    this.addAdditionalActionButtons(args.additionalActions);
                    if (walkingMoveWalkerAfterArgs.additionalActions.every(function (action) { return action.optional; })) {
                        this.addActionButton('confirmWalking', _("Confirm Walking"), function () { return _this.confirmWalking(); });
                    }
                    break;
                case 'actionSwap':
                    this.addActionButton('confirmSwap', _("Confirm"), function () { return _this.confirmSwap(); });
                    this.addActionButton('skipSwap', _("Skip"), function () { return _this.skipSwap(); }, null, null, 'red');
                    break;
                case 'actionScout':
                    if (args.scoutedDogCards.length > 0) {
                        this.addActionButton('confirmScout', _("Replace Dog"), function () { return _this.confirmScout(); });
                    }
                    this.addActionButton('endScout', _("End Scout Action"), function () { return _this.endScout(); }, null, null, 'red');
                    break;
            }
            if (args === null || args === void 0 ? void 0 : args.canCancelMoves) {
                this.addActionButton('undoLast', _("Undo last"), function () { return _this.undoLast(); }, null, null, 'red');
                this.addActionButton('undoAll', _("Undo all"), function () { return _this.undoAll(); }, null, null, 'red');
            }
        }
        else {
            if (!this.isReadOnly()) {
                switch (stateName) {
                    case 'selectionActions':
                        this.addActionButton('changeSelection', _("Change Selection"), function () { return _this.changeSelection(); });
                        break;
                    case 'chooseObjectives':
                        this.addActionButton('changeObjective', _("Change Objective"), function () { return _this.changeObjective(); });
                        break;
                }
            }
        }
    };
    DogPark.prototype.addAdditionalActionButtons = function (additionalActions) {
        var _this = this;
        if (additionalActions && additionalActions.length > 0) {
            additionalActions.forEach(function (additionalAction) {
                var buttonColor = additionalAction.optional ? 'gray' : 'blue';
                switch (additionalAction.type) {
                    case 'WALKING_PAY_REPUTATION_ACCEPT':
                        _this.addActionButton("payReputationAccept", dojo.string.substitute(_('Pay ${resourceType} to unlock location bonus(es)'), { resourceType: _this.tokenIcon('reputation') }), function () { return _this.additionalAction(additionalAction); }, null, null, buttonColor);
                        break;
                    case 'WALKING_PAY_REPUTATION_DENY':
                        _this.addActionButton("payReputationDeny", _('Skip location bonuses'), function () { return _this.additionalAction(additionalAction); }, null, null, buttonColor);
                        break;
                    case 'WALKING_GAIN_LOCATION_BONUS':
                        _this.addActionButton("gainLocationBonus".concat(additionalAction.id), dojo.string.substitute(_('Gain location bonus ${resourceType}'), { resourceType: _this.tokenIcons(additionalAction.additionalArgs['bonusType'], additionalAction.additionalArgs['amount']) }), function () { return _this.additionalAction(additionalAction); }, null, null, buttonColor);
                        break;
                    case 'WALKING_GAIN_LEAVING_THE_PARK_BONUS':
                        _this.addActionButton("gainLeavingPark".concat(additionalAction.id), dojo.string.substitute(_('Gain leaving the park bonus ${resourceType}'), { resourceType: _this.tokenIcons(additionalAction.additionalArgs['bonusType'], additionalAction.additionalArgs['amount'], additionalAction.additionalArgs['swapIncludesWalkedToken'] ? ['walked'] : []) }), function () { return _this.additionalAction(additionalAction); }, null, null, buttonColor);
                        break;
                    case 'USE_DOG_ABILITY':
                        _this.addActionButton("useDogAbility".concat(additionalAction.id), dojo.string.substitute('<b>${dogName}</b>: <i>${abilityTitle}</i>', { dogName: _(additionalAction.additionalArgs['dogName']), abilityTitle: _(additionalAction.additionalArgs['abilityTitle']) }), function () { return _this.additionalAction(additionalAction); }, null, null, buttonColor);
                        break;
                    case 'USE_FORECAST_ABILITY':
                        _this.addActionButton("useForecastAbility".concat(additionalAction.id), _('Use Forecast Card'), function () { return _this.additionalAction(additionalAction); }, null, null, buttonColor);
                        break;
                }
            });
        }
    };
    DogPark.prototype.additionalAction = function (additionalAction) {
        var _this = this;
        if (additionalAction.canBeUndone) {
            this.takeNoLockAction('additionalAction', { actionId: additionalAction.id });
        }
        else {
            this.wrapInConfirm(function () { return _this.takeNoLockAction('additionalAction', { actionId: additionalAction.id }); });
        }
    };
    DogPark.prototype.confirmObjective = function () {
        var cardId = this.currentPlayerChooseObjectives.getSelectedObjectiveId();
        if (cardId) {
            this.currentPlayerChooseObjectives.exit();
            this.takeNoLockAction('chooseObjective', { cardId: cardId });
        }
        else {
            this.showMessage(_("You must select an objective first"), 'error');
        }
    };
    DogPark.prototype.changeObjective = function () {
        var _this = this;
        this.takeNoLockAction('changeObjective', null, function () { return _this.currentPlayerChooseObjectives.enter(); });
    };
    DogPark.prototype.recruitDog = function () {
        var selectedDog = this.dogField.getSelectedFieldDog();
        this.takeAction('recruitDog', { dogId: selectedDog === null || selectedDog === void 0 ? void 0 : selectedDog.id });
    };
    DogPark.prototype.skipPlaceOfferOnDog = function () {
        this.takeAction('skipPlaceOfferOnDog');
    };
    DogPark.prototype.placeOfferOnDog = function () {
        var selectedDog = this.dogField.getSelectedFieldDog();
        var offerValue = this.currentPlayerOfferDial.currentValue;
        this.takeAction('placeOfferOnDog', { dogId: selectedDog === null || selectedDog === void 0 ? void 0 : selectedDog.id, offerValue: offerValue });
    };
    DogPark.prototype.confirmSelection = function (args) {
        var _this = this;
        if (args.numberOfDogsOnlead < args.maxNumberOfDogs && Object.keys(args.dogs).length > 0) {
            this.wrapInConfirm(function () { return _this.takeNoLockAction('confirmSelection'); }, _('You can still place dogs on your lead, are you sure you want confirm your selection?'));
        }
        else {
            this.takeNoLockAction('confirmSelection');
        }
    };
    DogPark.prototype.changeSelection = function () {
        this.takeNoLockAction('changeSelection');
    };
    DogPark.prototype.confirmWalking = function () {
        this.takeAction('confirmWalking');
    };
    DogPark.prototype.confirmSwap = function () {
        var fieldDog = this.dogField.getSelectedFieldDog();
        var kennelDog = this.playerArea.getSelectedKennelDog(this.getPlayerId());
        if (!fieldDog) {
            this.showMessage(_("You must select 1 dog in the field"), 'error');
        }
        else if (!kennelDog) {
            this.showMessage(_("You must select 1 dog in your kennel"), 'error');
        }
        else {
            this.takeAction("confirmSwap", { fieldDogId: fieldDog.id, kennelDogId: kennelDog.id });
        }
    };
    DogPark.prototype.skipSwap = function () {
        this.takeAction('cancelSwap');
    };
    DogPark.prototype.confirmScout = function () {
        var fieldDog = this.dogField.getSelectedFieldDog();
        var scoutDog = this.dogField.getSelectedScoutDog();
        if (!fieldDog) {
            this.showMessage(_("You must select 1 dog in the field"), 'error');
        }
        else if (!scoutDog) {
            this.showMessage(_("You must select 1 dog from the scout area"), 'error');
        }
        else {
            this.takeAction("confirmScout", { fieldDogId: fieldDog.id, scoutDogId: scoutDog.id });
        }
    };
    DogPark.prototype.endScout = function () {
        this.takeAction("endScout");
    };
    DogPark.prototype.undoLast = function () {
        this.takeNoLockAction('undoLast');
    };
    DogPark.prototype.undoAll = function () {
        this.takeNoLockAction('undoAll');
    };
    DogPark.prototype.disableActionButtons = function () {
        var buttons = document.querySelectorAll('.action-button');
        buttons.forEach(function (button) {
            button.classList.add('disabled');
        });
    };
    DogPark.prototype.isReadOnly = function () {
        return this.isSpectator || typeof g_replayFrom != 'undefined' || g_archive_mode;
    };
    DogPark.prototype.getPlayerId = function () {
        return Number(this.player_id);
    };
    DogPark.prototype.getPlayer = function (playerId) {
        return Object.values(this.gamedatas.players).find(function (player) { return Number(player.id) == playerId; });
    };
    DogPark.prototype.takeAction = function (action, data, onComplete) {
        if (onComplete === void 0) { onComplete = function () { }; }
        data = data || {};
        data.lock = true;
        this.ajaxcall("/dogpark/dogpark/".concat(action, ".html"), data, this, onComplete);
    };
    DogPark.prototype.takeNoLockAction = function (action, data, onComplete) {
        if (onComplete === void 0) { onComplete = function () { }; }
        this.disableActionButtons();
        data = data || {};
        this.ajaxcall("/dogpark/dogpark/".concat(action, ".html"), data, this, onComplete);
    };
    DogPark.prototype.setTooltip = function (id, html) {
        this.addTooltipHtml(id, html, TOOLTIP_DELAY);
    };
    DogPark.prototype.setTooltipToClass = function (className, html) {
        this.addTooltipHtmlToClass(className, html, TOOLTIP_DELAY);
    };
    DogPark.prototype.setScore = function (playerId, score) {
        var _a;
        (_a = this.scoreCtrl[playerId]) === null || _a === void 0 ? void 0 : _a.toValue(score);
    };
    DogPark.prototype.isAskForConfirmation = function () {
        return true; // For now always ask for confirmation, might make this a preference later on.
    };
    DogPark.prototype.wrapInConfirm = function (runnable, message) {
        if (message === void 0) { message = _("This action can not be undone. Are you sure?"); }
        if (this.isAskForConfirmation()) {
            this.confirmationDialog(message, function () {
                runnable();
            });
        }
        else {
            runnable();
        }
    };
    DogPark.prototype.getOrderedPlayers = function (players) {
        var _this = this;
        var result = players.sort(function (a, b) { return Number(a.playerNo) - Number(b.playerNo); });
        var playerIndex = result.findIndex(function (player) { return Number(player.id) === Number(_this.getPlayerId()); });
        return playerIndex > 0 ? __spreadArray(__spreadArray([], players.slice(playerIndex), true), players.slice(0, playerIndex), true) : players;
    };
    DogPark.prototype.setAlwaysFixTopActions = function (alwaysFixed, maximum) {
        if (alwaysFixed === void 0) { alwaysFixed = true; }
        if (maximum === void 0) { maximum = 30; }
        this.alwaysFixTopActions = alwaysFixed;
        this.alwaysFixTopActionsMaximum = maximum;
        this.adaptStatusBar();
    };
    DogPark.prototype.adaptStatusBar = function () {
        this.inherited(arguments);
        if (this.alwaysFixTopActions) {
            var afterTitleElem = document.getElementById('after-page-title');
            var titleElem = document.getElementById('page-title');
            //@ts-ignore
            var zoom = getComputedStyle(titleElem).zoom;
            if (!zoom) {
                zoom = 1;
            }
            var titleRect = afterTitleElem.getBoundingClientRect();
            if (titleRect.top < 0 && (titleElem.offsetHeight < (window.innerHeight * this.alwaysFixTopActionsMaximum / 100))) {
                var afterTitleRect = afterTitleElem.getBoundingClientRect();
                titleElem.classList.add('fixed-page-title');
                titleElem.style.width = ((afterTitleRect.width - 10) / zoom) + 'px';
                afterTitleElem.style.height = titleRect.height + 'px';
            }
            else {
                titleElem.classList.remove('fixed-page-title');
                titleElem.style.width = 'auto';
                afterTitleElem.style.height = '0px';
            }
        }
    };
    ///////////////////////////////////////////////////
    //// Reaction to cometD notifications
    /*
        setupNotifications:

        In this method, you associate each of your game notifications with your local method to handle it.

        Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                your pylos.game.php file.

    */
    DogPark.prototype.setupNotifications = function () {
        var _this = this;
        log('notifications subscriptions setup');
        var notifs = [
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
        notifs.forEach(function (notif) {
            dojo.subscribe(notif[0], _this, function (notifDetails) {
                log("notif_".concat(notif[0]), notifDetails.args);
                var promise = _this["notif_".concat(notif[0])](notifDetails.args);
                // tell the UI notification ends
                promise === null || promise === void 0 ? void 0 : promise.then(function () { return _this.notifqueue.onSynchronousNotificationEnd(); });
            });
            // make all notif as synchronous
            _this.notifqueue.setSynchronous(notif[0], notif[1]);
        });
    };
    DogPark.prototype.notif_objectivesChosen = function (args) {
        var _this = this;
        return Promise.all(args.chosenObjectiveCards.map(function (_a) {
            var playerId = _a.playerId, cardId = _a.cardId;
            var objectives = _this.getPlayer(playerId).objectives;
            var chosenObjective = objectives.find(function (card) { return card.id === cardId; });
            return _this.playerArea.moveObjectiveToPlayer(playerId, chosenObjective);
        })).then(function () {
            var _a;
            (_a = _this.currentPlayerChooseObjectives) === null || _a === void 0 ? void 0 : _a.destroy();
            _this.currentPlayerChooseObjectives = null;
        });
    };
    DogPark.prototype.notif_dogRecruited = function (args) {
        var _this = this;
        this.setScore(args.playerId, args.score);
        this.playerArea.moveWalkerToPlayer(args.playerId, args.walker);
        return this.playerArea.moveDogsToKennel(args.playerId, [args.dog]).then(function () { return _this.breedExpertAwardManager.updateBreedExpertAwardStandings(); });
    };
    DogPark.prototype.notif_dogOfferPlaced = function (args) {
        if (Number(args.playerId) === Number(this.getPlayerId())) {
            this.playerArea.setPlayerOfferValue(this.getPlayerId(), this.currentPlayerOfferDial.currentValue);
        }
        return Promise.all(this.dogField.addWalkersToField([args.walker]));
    };
    DogPark.prototype.notif_offerValueRevealed = function (args) {
        this.playerArea.setPlayerOfferValue(args.playerId, args.offerValue);
    };
    DogPark.prototype.notif_resetAllOfferValues = function () {
        this.playerArea.resetAllOfferValues();
    };
    DogPark.prototype.notif_fieldRefilled = function (args) {
        return Promise.all(this.dogField.addDogCardsToField(args.dogs));
    };
    DogPark.prototype.notif_newPhase = function (args) {
        this.roundTracker.updateRound(args.round);
        this.roundTracker.updatePhase(args.newPhase);
    };
    DogPark.prototype.notif_dogPlacedOnLead = function (args) {
        var _this = this;
        var promise = this.playerArea.moveDogsToLead(args.playerId, [args.dog])
            .then(function () { return _this.playerResources.payResourcesToDog(args.playerId, args.dog, args.resources); })
            .then(function () { return _this.dogCardManager.addResourceToDog(args.dog.id, 'walked'); });
        if (args.playerId == this.getPlayerId()) {
            return promise;
        }
        return Promise.resolve();
    };
    DogPark.prototype.notif_undoDogPlacedOnLead = function (args) {
        this.playerResources.gainResourcesFromDog(args.playerId, args.dog, args.resources);
        this.playerArea.moveDogsToKennel(args.playerId, [args.dog]);
        this.dogCardManager.removeResourceFromDog(args.dog.id, 'walked');
    };
    DogPark.prototype.notif_playerGainsResources = function (args) {
        return this.playerResources.gainResources(args.playerId, args.resources);
    };
    DogPark.prototype.notif_playerGainsLocationBonusResource = function (args) {
        var _this = this;
        if (args.resources.includes('reputation')) {
            this.setScore(args.playerId, args.score);
        }
        else {
            return Promise.all(args.resources.filter(function (resource) { return ['ball', 'stick', 'treat', 'toy'].includes(resource); })
                .map(function (resource) { return _this.playerResources.gainResourceFromLocation(args.playerId, args.locationId, resource); }));
        }
        return Promise.resolve();
    };
    DogPark.prototype.notif_undoPlayerGainsLocationBonusResource = function (args) {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!args.resources.includes('reputation')) return [3 /*break*/, 1];
                        this.setScore(args.playerId, args.score);
                        return [3 /*break*/, 3];
                    case 1: return [4 /*yield*/, this.playerResources.payResources(args.playerId, args.resources.filter(function (resource) { return ['ball', 'stick', 'treat', 'toy'].includes(resource); }))];
                    case 2:
                        _a.sent();
                        _a.label = 3;
                    case 3: return [2 /*return*/];
                }
            });
        });
    };
    DogPark.prototype.notif_moveWalkers = function (args) {
        return this.dogWalkPark.moveWalkers(args.walkers);
    };
    DogPark.prototype.notif_moveWalker = function (args) {
        return this.dogWalkPark.moveWalkers([args.walker]);
    };
    DogPark.prototype.notif_playerPaysReputationForLocation = function (args) {
        this.setScore(args.playerId, args.score);
        return Promise.resolve();
    };
    DogPark.prototype.notif_playerLeavesThePark = function (args) {
        this.setScore(args.playerId, args.score);
        if (args.walker) {
            return this.dogWalkPark.moveWalkers([args.walker]);
        }
        return Promise.resolve();
    };
    DogPark.prototype.notif_playerGainsReputation = function (args) {
        this.setScore(args.playerId, args.score);
        return Promise.resolve();
    };
    DogPark.prototype.notif_playerLosesReputation = function (args) {
        this.setScore(args.playerId, args.score);
        return Promise.resolve();
    };
    DogPark.prototype.notif_moveDogsToKennel = function (args) {
        return this.playerArea.moveDogsToKennel(args.playerId, args.dogs);
    };
    DogPark.prototype.notif_moveWalkerBackToPlayer = function (args) {
        return this.playerArea.moveWalkerToPlayer(args.playerId, args.walker);
    };
    DogPark.prototype.notif_flipForecastCard = function (args) {
        this.forecastManager.flipCard(args.foreCastCard);
        return Promise.resolve();
    };
    DogPark.prototype.notif_newLocationBonusCardDrawn = function (args) {
        var _this = this;
        return this.dogWalkPark.addLocationBonusCard(args.locationBonusCard)
            .then(function () { return _this.dogWalkPark.addExtraLocationBonuses(args.locationBonuses); });
    };
    DogPark.prototype.notif_newFirstWalker = function (args) {
        return this.playerArea.setNewFirstWalker(args.playerId);
    };
    DogPark.prototype.notif_playerSwaps = function (args) {
        var _this = this;
        this.dogCardManager.removeAllResourcesFromDog(args.kennelDog.id);
        this.dogCardManager.addInitialResourcesToDog(args.fieldDog);
        this.dogField.addDogCardsToField([args.kennelDog]);
        return this.playerArea.moveDogsToKennel(args.playerId, [args.fieldDog]).then(function () { return _this.breedExpertAwardManager.updateBreedExpertAwardStandings(); });
    };
    DogPark.prototype.notif_playerScoutReplaces = function (args) {
        this.dogField.discardDogFromField(args.fieldDog);
        return Promise.all(this.dogField.addDogCardsToField([args.scoutDog]));
    };
    DogPark.prototype.notif_undoPlayerScoutReplaces = function (args) {
        this.dogField.addDogCardsToField([args.scoutDog]);
        return this.dogField.addDogCardsToScoutedField([args.fieldDog]);
    };
    DogPark.prototype.notif_activateDogAbility = function (args) {
        var promises = [];
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
    };
    DogPark.prototype.notif_activateForecastCard = function (args) {
        var promises = [];
        if (args.gainedResources) {
            promises.push(this.playerResources.gainResourcesFromForecastCard(args.playerId, args.forecastCard, args.gainedResources.filter(function (resource) { return resource !== 'reputation'; })));
        }
        if (args.lostResources) {
            promises.push(this.playerResources.payResources(args.playerId, args.lostResources.filter(function (resource) { return resource !== 'reputation'; })));
        }
        if (args.score || args.score === 0) {
            this.setScore(args.playerId, args.score);
        }
        if (args.playerId == this.getPlayerId()) {
            return Promise.all(promises);
        }
        return Promise.resolve();
    };
    DogPark.prototype.notif_playerAssignsResources = function (args) {
        return __awaiter(this, void 0, void 0, function () {
            var _i, _a, resource;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _i = 0, _a = args.resourcesAdded;
                        _b.label = 1;
                    case 1:
                        if (!(_i < _a.length)) return [3 /*break*/, 5];
                        resource = _a[_i];
                        return [4 /*yield*/, this.playerResources.payResourcesToDog(args.playerId, args.dog, [resource])];
                    case 2:
                        _b.sent();
                        return [4 /*yield*/, this.dogCardManager.addResourceToDog(args.dog.id, resource)];
                    case 3:
                        _b.sent();
                        _b.label = 4;
                    case 4:
                        _i++;
                        return [3 /*break*/, 1];
                    case 5: return [2 /*return*/];
                }
            });
        });
    };
    DogPark.prototype.notif_revealObjectiveCards = function (args) {
        var _this = this;
        return Promise.all(args.objectiveCards.map(function (objectiveCard) { return _this.objectiveCardManager.updateCardInformations(objectiveCard); }));
    };
    DogPark.prototype.notif_finalScoringRevealed = function (args) {
        var _this = this;
        return this.finalScoringPad.showPad(args.scoreBreakDown, true).then(function () {
            Object.keys(args.scoreBreakDown).forEach(function (playerId) { return _this.setScore(Number(playerId), args.scoreBreakDown[playerId]['score']); });
        });
    };
    DogPark.prototype.notif_autoWalkerDieRolled = function (args) {
        var dieElement = $("dp-autowalker-die-".concat(args.walkerId));
        if (dieElement) {
            dieElement.dataset['value'] = 7 - args.side;
            this.delay(500).then(function () { return dieElement.dataset['value'] = args.side; });
        }
    };
    DogPark.prototype.format_string_recursive = function (log, args) {
        try {
            if (log && args && !args.processed) {
                Object.keys(args).forEach(function (argKey) {
                });
            }
        }
        catch (e) {
            console.error(log, args, "Exception thrown", e.stack);
        }
        return this.inherited(arguments);
    };
    DogPark.prototype.tokenIcon = function (type) {
        return "<span class=\"dp-token-token dp-token-".concat(type, " small\" data-type=\"").concat(type, "\"></span>");
    };
    DogPark.prototype.tokenIcons = function (type, nrOfIcons, additionalIcons) {
        var _this = this;
        var tokens = [];
        for (var i = 0; i < nrOfIcons; i++) {
            tokens.push(this.tokenIcon(type));
        }
        if (additionalIcons && additionalIcons.length > 0) {
            additionalIcons.forEach(function (additionalIcon) { return tokens.push(_this.tokenIcon(additionalIcon)); });
        }
        return tokens.join(' ');
    };
    DogPark.prototype.walkerIcon = function (color) {
        return "<span class=\"dp-dog-walker\" data-color=\"#".concat(color, "\"></span>");
    };
    DogPark.prototype.breedIcon = function (breed) {
        return "<span>".concat(_(breed.charAt(0).toUpperCase() + breed.slice(1)), "</span>");
    };
    DogPark.prototype.updatePlayerOrdering = function () {
        var _this = this;
        this.inherited(arguments);
        this.gamedatas.autoWalkers.forEach(function (autoWalker) {
            _this.playerArea.initAutoWalkers(autoWalker);
        });
        if (Object.keys(this.gamedatas.players).length === 1) {
            dojo.place("<div id=\"dp-solo-ratings-panel\" class=\"player-board\" style=\"height: auto;\"></div>", "player_boards", 'first');
            new SoloRatings('dp-solo-ratings-panel');
        }
    };
    DogPark.prototype.formatWithIcons = function (description) {
        var _this = this;
        var tags = description.match(/<[^>]*?>/g);
        console.log(description);
        tags.forEach(function (originalTag) {
            if (originalTag.includes('icon-')) {
                var tag = '';
                tag = originalTag.replace('<', '');
                tag = tag.replace('>', '');
                var resultTag = _this.tokenIcon(tag.replace('icon-', ''));
                description = description.replace(originalTag, resultTag);
            }
            else if (originalTag.includes('breed-')) {
                var tag = '';
                tag = originalTag.replace('<', '');
                tag = tag.replace('>', '');
                var resultTag = _this.breedIcon(tag.replace('breed-', ''));
                description = description.replace(originalTag, resultTag);
            }
        });
        return description;
    };
    return DogPark;
}());
