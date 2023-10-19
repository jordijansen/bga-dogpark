const determineBoardWidth = () => {
    return 1000;
}

const determineMaxZoomLevel = () => {
    const bodycoords = dojo.marginBox("zoom-overall");
    const contentWidth = bodycoords.w;
    const rowWidth = determineBoardWidth();

    return contentWidth / rowWidth;
}

const getZoomLevels = (maxZoomLevels: number) => {
    let zoomLevels = [];
    if (maxZoomLevels > 1) {
        const maxZoomLevelsAbove1 = maxZoomLevels - 1;
        const increments = (maxZoomLevelsAbove1 / 6)
        zoomLevels = [ (increments) + 1, (increments * 2) + 1, (increments * 3) + 1, (increments * 4) + 1, (increments * 5) + 1, (increments * 6) + 1]
    }
    zoomLevels = [...zoomLevels, 1, 0.8, 0.6];
    return zoomLevels.sort();
}

class AutoZoomManager extends ZoomManager {

    constructor(elementId: string, localStorageKey: string) {
        const storedZoomLevel = localStorage.getItem(localStorageKey);
        const maxZoomLevel = determineMaxZoomLevel();
        if (storedZoomLevel && Number(storedZoomLevel) > maxZoomLevel) {
            localStorage.removeItem(localStorageKey);
        }

        const zoomLevels = getZoomLevels(determineMaxZoomLevel());
        super({
            element: document.getElementById(elementId),
            smooth: false,
            zoomLevels: zoomLevels,
            defaultZoom: 1,
            localStorageZoomKey: localStorageKey,
            zoomControls: {
                color: 'black',
                position: 'top-right'
            },
            onZoomChange: zoom => {
                const sideBoard = $('dp-game-board-side-zoom-wrapper');
                if (sideBoard && zoom > 0) {
                    sideBoard.style.transform = `scale(${zoom - 0.2})`
                }
            }
        });
    }
}