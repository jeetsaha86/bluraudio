jQuery(document).ready(function ($) {
    $('body').on('click', '.__track_buy', function (e) {
        // block default behavior and stop propagation
        e.preventDefault();
        e.stopPropagation();

        // store default and elements
        var productID = $(this).attr('data-id'),
            postData  = {
                'id': productID,
                'action': 'add_variation_to_cart',
            };

        // run the AJAX query
        $.post(pinkFader.ajaxURL, postData, function (response) {
            $('#add-to-cart-popup .add-to-cart-popup-content').html(response);
            UIkit.modal('#add-to-cart-popup').show();
        });
    });
});