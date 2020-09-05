(function () {
    'use strict';

    /**
     *
     * @param {MapSpecification} map
     * @constructor
     */
    function DragAndDropMap(map) {
        /**
         * @type {Object<string, {canvas: HTMLCanvasElement, context: CanvasRenderingContext2D}>}
         */
        this.layers = {};
        /** @type {?MapSpecification} */
        this.map = null;
        /** @type {Image[]} */
        this.images = [];
        /** @type {?RenderData} */
        this.renderData = null;

        this.io = {
            /** @type {?number} */
            selectedTile: null,
        };

        const whenReady = this.initialize(map)
            .catch((error) => console.error(error));


        /**
         * @returns {Promise<void>}
         */
        this.onReady = function () {
            return whenReady;
        };
    }

    /**
     * @private
     * @param {MapSpecification} map
     * @returns {Promise<void>}
     */
    DragAndDropMap.prototype.initialize = async function (map) {
        this.map = map;
        this.images = await loadImages(map.imageParts);
        let imageOrder = [];
        this.images.forEach((image, i) => {
            imageOrder.push(i);
        });

        this.imageOrder = imageOrder;
    };

    /**
     * @param {HTMLDivElement} puzzleWrapper
     */
    DragAndDropMap.prototype.bind = function (puzzleWrapper) {
        if (this.wrapperElement) {
            throw new Error("This instance is already bound to another element");
        }

        this.wrapperElement = puzzleWrapper;
        this.initDomStructure();
        this.initClickEvents();
    };

    /** @private */
    DragAndDropMap.prototype.initDomStructure = function () {
        this.wrapperElement.classList.add('drag-and-drop-puzzle');

        let imageCanvas = document.createElement('canvas');
        let imageContext = imageCanvas.getContext('2d');
        this.layers.image = {
            canvas: imageCanvas,
            context: imageContext,
        };
        this.wrapperElement.appendChild(imageCanvas);

        let draggingCanvas = document.createElement('canvas');
        let draggingContext = draggingCanvas.getContext('2d');
        this.layers.dragging = {
            canvas: draggingCanvas,
            context: draggingContext,
        };

        this.wrapperElement.appendChild(draggingCanvas);

        let updateCanvasSize = (parentElement) => {
            imageCanvas.width = draggingCanvas.width = parentElement.clientWidth;
            imageCanvas.height = draggingCanvas.height = parentElement.clientHeight;
            this.renderData = null;
            this.render();
        }

        window.addEventListener('resize', () => updateCanvasSize(this.wrapperElement));
        updateCanvasSize(this.wrapperElement);
    };
    /** @private */
    DragAndDropMap.prototype.initClickEvents = function () {
        this.wrapperElement.addEventListener('click', (/**MouseEvent*/ e) => {
            if (!this.renderData) {
                console.error("renderData not initialized");
                return;
            }
            const tile = {
                x: Math.floor((e.offsetX - this.renderData.offset.x) / this.renderData.tileSize.width),
                y: Math.floor((e.offsetY - this.renderData.offset.y) / this.renderData.tileSize.height),
            };

            let tileSelection;
            if (0 > tile.x || tile.x >= this.map.tiling.x
                || 0 > tile.y || tile.y >= this.map.tiling.y) {
                tileSelection = null;
            } else {
                tileSelection = tile.y * this.map.tiling.x + tile.x;
            }

            if (this.io.selectedTile !== null) {
                if (this.io.selectedTile !== tileSelection) {
                    this.swapTiles(this.io.selectedTile, tileSelection);
                }
                tileSelection = null;
            }

            this.io.selectedTile = tileSelection;
            this.render();
        })
    }

    /**
     * @param {number} a
     * @param {number} b
     */
    DragAndDropMap.prototype.swapTiles = function (a, b) {
        if (0 > a || a >= this.imageOrder.length) {
            throw new Error(a + ' is out of bounds');
        }
        if (0 > b || b >= this.imageOrder.length) {
            throw new Error(b + ' is out of bounds');
        }

        let temp = this.imageOrder[a];
        this.imageOrder[a] = this.imageOrder[b];
        this.imageOrder[b] = temp;
    }

    DragAndDropMap.prototype.render = function () {
        this.renderImageTiling(this.layers.image.canvas, this.layers.image.context);
    };

    /**
     * @private
     * @param {HTMLCanvasElement} canvas
     * @param {CanvasRenderingContext2D} context
     */
    DragAndDropMap.prototype.renderImageTiling = function (canvas, context) {
        context.clearRect(0, 0, canvas.width, canvas.height);

        /** @type {RenderData} */
        let rd;
        if (!this.renderData) {
            const padding = 20;
            rd = this.renderData = calculateRenderData(this.map.dimensions, {
                width: canvas.width - 2 * padding,
                height: canvas.height - 2 * padding
            }, this.map.tiling);
            rd.offset.x += padding;
            rd.offset.y += padding;
        } else {
            rd = this.renderData;
        }

        let i = 0;
        for (let y = 0; y < this.map.tiling.y; y++) {
            for (let x = 0; x < this.map.tiling.x; x++) {
                let image = this.images[this.imageOrder[i++]];
                context.drawImage(image,
                    rd.offset.x + x * rd.tileSize.width, rd.offset.y + y * rd.tileSize.height,
                    rd.tileSize.width, rd.tileSize.height);
            }
        }

        context.strokeStyle = 'black';
        context.beginPath();
        for (let row = 0; row <= this.map.tiling.y; row++) {
            let y = rd.offset.y + row * rd.tileSize.height;
            context.moveTo(rd.offset.x, y);
            context.lineTo(rd.offset.x + rd.targetSize.width, y);
        }
        for (let col = 0; col <= this.map.tiling.x; col++) {
            let x = rd.offset.x + col * rd.tileSize.width;
            context.moveTo(x, rd.offset.y);
            context.lineTo(x, rd.offset.y + rd.targetSize.height);
        }
        context.stroke();

        if (this.io.selectedTile !== null) {
            let highlight = {
                x: this.io.selectedTile % this.map.tiling.x,
                y: Math.floor(this.io.selectedTile / this.map.tiling.x),
            };
            context.strokeStyle = 'lime';
            context.strokeRect(rd.offset.x + highlight.x * rd.tileSize.width,
                rd.offset.y + highlight.y * rd.tileSize.height,
                rd.tileSize.width, rd.tileSize.height);
        }
    }

    DragAndDropMap.prototype.download = function(fileName) {
        var link = document.createElement('a');
        link.download = fileName || 'image.png';
        link.href = this.layers.image.canvas.toDataURL()
        link.click();
    };

    /**
     *
     * @param {Dimensions2D} source
     * @param {Dimensions2D} destination
     * @param {Point2D} tiling
     *
     * @returns {RenderData}
     */
    function calculateRenderData(source, destination, tiling) {
        const renderData = {};
        renderData.scale = getResizeScale(source, destination);
        renderData.targetSize = {
            width: source.width * renderData.scale,
            height: source.height * renderData.scale,
        };
        renderData.offset = {
            x: (destination.width - renderData.targetSize.width) / 2,
            y: (destination.height - renderData.targetSize.height) / 2,
        };
        renderData.tileSize = {
            width: renderData.targetSize.width / tiling.x,
            height: renderData.targetSize.height / tiling.y,
        };

        return renderData;
    }

    /**
     * @param {Dimensions2D} source
     * @param {Dimensions2D} destination
     * @returns number
     */
    function getResizeScale(source, destination) {
        const matchWidthScale = destination.width / source.width;

        if (matchWidthScale * source.height > destination.height) {
            return destination.height / source.height; // matchHeightScale
        }

        return matchWidthScale;
    }

    /**
     * @private
     * @param {string[]} imageFileNames
     * @returns {Promise<Image[]>}
     */
    async function loadImages(imageFileNames) {
        let imagesPromise = imageFileNames.map((imgUrl) => {
            return new Promise((resolve) => {
                let image = new Image();
                image.src = imgUrl;
                image.onload = () => resolve(image);
            });
        });

        return Promise.all(imagesPromise);
    }

    window.DragAndDropMap = DragAndDropMap;
})();

/**
 * @typedef {Object} MapSpecification
 *
 * @property {Point2D} tiling
 * @property {Dimensions2D} dimensions
 * @property {string[]} imageParts
 */

/**
 * @typedef {Object} RenderData
 *
 * @property {number} scale
 * @property {Point2D} offset
 * @property {Dimensions2D} tileSize
 * @property {Dimensions2D} targetSize
 */

/**
 * @typedef {Object} Point2D
 *
 * @property {number} x
 * @property {number} y
 */

/**
 * @typedef {Object} Dimensions2D
 *
 * @property {number} width
 * @property {number} height
 */
