jQuery(document).ready(function ($) {
    // Initialize the audio player
    $('.__audio_player').audio();

    // add grid to facet wp template
    $('.__track_items .facetwp-template').addClass('uk-grid-collapse uk-child-width-1-2@xl').attr('uk-grid', '');

    // add theme select

    // open/close player
    $('body').on('click', 'a.__open_playlist, .__open_playlist > a', function (e) {
        // prevent default action of following links
        e.preventDefault();

        // open player listings
        $('.__audio_player').data('audio').open_listing();
    }).on('click', '.__close_playlist', function (e) {
        // prevent default action of following links
        e.preventDefault();

        // open player listings
        $('.__audio_player').data('audio').close_listing();
    });

    // get all product grids
    $('.product-grid').each(function (index, element) {
        var gridItemWrap = $(this).find(' > div'),
            gridItems    = gridItemWrap.find(' > div');

        // add class to wrap
        gridItemWrap.addClass('__track_items');

        // iterate over each item
        gridItems.each(function (index, element) {
            var itemID      = $(this).find('.__track_buy').attr('data-id'),
                itemOverlay = $(this).find('.uk-overlay-primary');

            // add class and attribute
            $(this).addClass('__track_item').attr('data-product-id', itemID);

            // add play button to overlay
            itemOverlay.html('<span class="uk-position-medium uk-position-top-left" uk-icon="icon: play-circle; ratio: 2"></span>');
        });
    });

    /* Contact form 7 Submission Alert */
    document.addEventListener('wpcf7mailsent', function (event) {
        UIkit.notification({
            'pos': 'bottom-right',
            'message': 'Thank you for your message. It has been sent.',
            'status': 'primary',
            'timeout': 5000,
        });
    }, false);

    // add icons to dashboard
    $('.woocommerce-MyAccount-navigation > ul').addClass('uk-subnav uk-subnav-divider');

    // display mood reset button
    $(document).on('facetwp-loaded', function () {
        var qs = FWP.build_query_string();
        if (qs.indexOf('_track_mood') !== -1) {
            $('.facet-reset').show();
        } else {
            $('.facet-reset').hide();
        }
    });
});

