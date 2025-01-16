(function ($, Drupal, once) {

    Drupal.behaviors.cambridgeQuestionsAndAnswers = {
        attach: function (context, settings) {
            const questions = once('campl-questions', '.campl-questions-question', context);
            if (questions.length) {
                $(questions).each(function () {
                    $(this).bind('click', function () {
                        $(this).next('.campl-questions-answer').toggleClass('campl-questions-answer-revealed');
                        $(window).trigger('resize'); // force column heights to be recalculated
                    });
                });
            }
        }
    };
})(jQuery, Drupal, once);