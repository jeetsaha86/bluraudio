<?php if ( is_user_logged_in() ) : ?>
	<a class="uk-margin-small-right" href="<?php echo wc_get_account_endpoint_url( 'orders' ); ?>"><span uk-icon="icon: user; ratio: 1.5" class="uk-icon-button"></span></a>
<?php else : ?>
	<a class="uk-margin-small-right" href="<?php echo wc_get_account_endpoint_url(''); ?>"><span uk-icon="icon: user; ratio: 1.5" class="uk-icon-button"></span></a>
<?php endif; ?>
