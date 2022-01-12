jQuery(document).ready(function ($) {
    $('body').on('click', '.__track_favorite', function (e) {
        // block default behavior and stop propagation
        e.preventDefault();
        e.stopPropagation();

        // element
        var $element = $(this);

        // type
        var setType = $(this).hasClass('__active') ? 'remove' : 'add';

        // store default and elements
        var productID = $(this).attr('data-id'),
            postData  = {
                'id': productID,
                'type': setType,
                'action': 'add_user_favorite',
            };

        // run the AJAX query
        $.post(pinkFader.ajaxURL, postData, function (response) {
            if (response) {
                $('.__track_favorite[data-id="' + productID + '"]').toggleClass('__active');
                //$element.toggleClass('__active');

                if (setType === 'add') {
                    UIkit.notification({
                        message: '<div class="__show_fav">Track has been added to favorites! Click here to show favorite tracks.</div>',
                        status: 'primary',
                        pos: 'bottom-right',
                        timeout: 5000
                    });
                }
            }
        });
    }).on('click', '.__show_fav', function (e) {
        $('#__fav_only').prop("checked", true).trigger('change');
    });

    // toggle favorites
    $('#__fav_only').change(function () {
        var currentStatus = Cookies.get('__fav_cookie'),
            postData      = {
                'status': currentStatus,
                'action': 'toggle_favorites_only',
            };

        // run the AJAX query
        $.post(pinkFader.ajaxURL, postData, function (response) {
            window.location = window.location.href.split("?")[0] + '?expand_player=1';
        });
    });
});