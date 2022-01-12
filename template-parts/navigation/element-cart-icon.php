<?php global $woocommerce; ?>
<a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>">
	<span uk-icon="icon: cart; ratio: 1.5" class="uk-icon-button">
		<span class="uk-badge uk-position-absolute"><?php echo $woocommerce->cart->cart_contents_count; ?></span>
	</span>
</a>
