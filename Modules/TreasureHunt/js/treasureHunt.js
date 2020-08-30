$(function() {
    naja.initialize();

    initChallengeForm();

    initSummernote();

    function initChallengeForm() {
        let $challengeFormContainer = $('.challenge-form-container');
        let $description = $challengeFormContainer.find('[name="description"]');

        $challengeFormContainer.on('click', '.flip-page', function() {
            let $notebook = $(this).closest('.challenge-form-container').find('.notebook');

            $notebook.toggleClass('page-left');
            $notebook.toggleClass('page-right');
        })
    }

    function initSummernote() {

        $('textarea.wysiwyg').each(function() {
            let $el = $(this);
            let summernoteOptions = $el.data('summernoteOptions');

            $el.summernote(summernoteOptions);
        })
    }

    $('body').on('change', '[data-ajax-on-change]', (e) => {
        let url = e.target.dataset['ajaxOnChange'];
        url = url.replace('__value__', e.target.value)
        naja.makeRequest('GET', url)
    });
});
