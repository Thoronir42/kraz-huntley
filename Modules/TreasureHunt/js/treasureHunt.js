$(function() {
    initChallengeForm();

    function initChallengeForm() {
        let $challengeFormContainer = $('.challenge-form-container');
        let $description = $challengeFormContainer.find('[name="description"]');

        $description.summernote({
            airMode: true,
            placeholder: "Text výzvy..."
        });

        $challengeFormContainer.on('click', '.flip-page', function() {
            let $notebook = $(this).closest('.challenge-form-container').find('.notebook');

            $notebook.toggleClass('page-left');
            $notebook.toggleClass('page-right');
        })
    }
})

console.log('asd');
