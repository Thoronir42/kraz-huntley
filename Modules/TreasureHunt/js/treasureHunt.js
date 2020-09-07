$(function () {
    naja.initialize();

    initChallengeForm();

    initSummernote();

    function initChallengeForm() {
        let $challengeFormContainer = $('.challenge-form-container');
        let $description = $challengeFormContainer.find('[name="description"]');

        $challengeFormContainer.on('click', '.flip-page', function (e) {
            let $notebook = $('.challenge-form-container').find('.notebook');

            $notebook.toggleClass('page-left');
            $notebook.toggleClass('page-right');
        });
    }

    function initSummernote() {

        $('textarea.wysiwyg').each(function () {
            let $el = $(this);
            let summernoteOptions = $el.data('summernoteOptions');

            $el.summernote(summernoteOptions);
        })
    }

    (function initPictureSelection() {
        $('.picture-selection').each((i, el) => {
            let pictureSelection = new PictureSelection($(el));

            pictureSelection.onReady()
                .then((i) => console.log("Picture selection ready", i));
        });
    })();

    $('body').on('change', '[data-ajax-on-change]', (e) => {
        let url = e.target.dataset['ajaxOnChange'];
        url = url.replace('__value__', e.target.value)
        naja.makeRequest('GET', url)
    });

    $('[data-toggle="popover"]').each(function () {
        let $el = $(this);
        let popoverOptions = {};

        let contentElSelector = $el.data('contentEl');
        if (contentElSelector) {
            let $content = $(contentElSelector);
            $content.detach();
            $content.removeClass('d-none');

            popoverOptions.content = $content;
            popoverOptions.html = true;
        }

        if ($el.data('templateName') === 'popover-large') {
            popoverOptions.template = '<div class="popover popover-large" role="tooltip">' +
                '<div class="arrow"></div>' +
                '<h3 class="popover-header"></h3>' +
                '<div class="popover-body"></div>' +
                '</div>'
        }

        $el.popover(popoverOptions);
    });
});
