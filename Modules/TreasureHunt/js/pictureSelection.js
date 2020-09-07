(function () {
    'use strict';

    function PictureSelection($selectionEl) {

        if ($selectionEl.length !== 1) {
            throw new Error("Invalid selection");
        }

        const whenReady = this.initialize($selectionEl)
            .catch((error) => console.error(error));

        /**
         * @returns {Promise<void>}
         */
        this.onReady = function () {
            return whenReady;
        };
    }

    PictureSelection.prototype.initialize = async function ($selectionEl) {
        this.$el = $selectionEl;
        this.$buttonsWrapper = $('<div class="buttons-wrapper"></div>');
        this.$el.append(this.$buttonsWrapper);
        this.$modal = createPickerModal();
        this.$modal.$body = this.$modal.find('.modal-body');
        $(document.body).append(this.$modal);

        this.$el.find('select').each((i, select) => {
            let selected = select.options[select.selectedIndex];
            console.log(selected);
            let $select = $(select);

            let $btn = $('<button><img src="' + selected.text + '"></button>');

            $btn.appendTo(this.$buttonsWrapper);
            $select.hide();
        });

        this.$el.on('change', 'select', (e) => {
            let $select = $(e.target);
            let i = $select.index();
            let option = e.target.options[e.target.selectedIndex];

            let $img = this.$buttonsWrapper.find('button:nth-child(' + (i + 1) + ') img');
            $img.attr('src', option.text);
        });

        this.$el.on('click', '.buttons-wrapper button', (e) => {
            e.preventDefault();
            let $btn = $(e.currentTarget);
            let i = $btn.index();

            this.openSelection(i)
                .then((value) => {
                    let $select = this.$el.find('select:nth-child(' + (i + 1) + ')');
                    $select.val(value);
                    $select.change();
                })
                .catch((err) => {
                    if (err) {
                        console.error(err);
                    }
                });
        });


        return Promise.resolve(1);
    };

    PictureSelection.prototype.openSelection = function (i) {
        let select = this.$el.find('select')[i];

        let options = [];

        for (let option of select.options) {
            options.push({
                value: option.value,
                imgSrc: option.text,
            });
        }

        this.$modal.$body.text('');
        options.forEach((option) => {
            let $img = $('<img src="' + option.imgSrc + '"/>');
            this.$modal.$body.append($img);
        });

        return new Promise(((resolve, reject) => {
            let resolved = false;
            this.$modal.modal();

            this.$modal.on('click', 'img', (e) => {
                if (resolved) {
                    return;
                }
                let option = options[$(e.currentTarget).index()];
                resolve(option.value);
                this.$modal.modal('hide');
                this.$modal.off();
            });

            this.$modal.on('hidden.bs.modal', (e) => {
                if (resolved) {
                    return;
                }

                reject();
                this.$modal.off();
            });
        }));
    };

    function createPickerModal() {
        return $('<div class="modal modal-picture-selection" tabindex="-1" role="dialog">\n' +
            '  <div class="modal-dialog" role="document">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <h5 class="modal-title">Výběr</h5>\n' +
            '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
            '          <span aria-hidden="true">&times;</span>\n' +
            '        </button>\n' +
            '      </div>\n' +
            '      <div class="modal-body">\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div>');
    }

    window.PictureSelection = PictureSelection;
})()
