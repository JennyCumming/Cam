(function ($, Drupal, once) {

    Drupal.behaviors.cambridgeTeasers = {
        attach: function (context, settings) {
            const view_rows = once('campl-row', '.view-content > .campl-row', context);
            if (view_rows.length) {
                $(view_rows).each(function () {
                    var $teasers = $('.campl-teaser-border', this);

                    if (Modernizr.mq('only screen and (min-width: 768px)')) {
                        $teasers.matchHeight(false);
                    }

                    $(window).resize(function () {
                        if (Modernizr.mq('only screen and (max-width: 767px)')) {
                            $teasers.height('auto');
                        } else {
                            $teasers.matchHeight(false);
                        }
                    });
                });
            }
        }
    };

})(jQuery, Drupal, once);
