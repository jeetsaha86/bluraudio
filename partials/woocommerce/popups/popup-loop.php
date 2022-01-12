<?php $variation_data = $variation_data ? $variation_data : []; ?>
<div>
	<div class="uk-tile uk-tile-secondary uk-tile-small">
		<div class="uk-h4 uk-text-emphasis uk-margin-remove-bottom"> <?php echo $variation_data['title']; ?></div>
		<div class="uk-h1 uk-text-primary uk-margin-remove-top"> <?php echo wc_price( $variation_data['price'] ); ?></div>
		<div class="uk-h5 uk-text-success uk-margin-remove-top"><?php echo $variation_data['name']; ?></div>
		<div class="uk-text-bold uk-margin-bottom"> <?php echo $variation_data['tagline']; ?></div>
		<?php $list_points = explode( "\n", $variation_data['list_points'] ); ?>
		<ul class="uk-list uk-text-small uk-visible@m">
			<?php foreach ( $list_points as $list_point ) : ?>
				<li><span class="uk-text-primary" uk-icon="icon: triangle-right"></span><?php echo $list_point; ?></li>
			<?php endforeach; ?>
		</ul>
		<?php if ( $variation_data['in_cart'] ) : ?>
			<div>
				<a class="uk-button uk-button-danger uk-border-rounded uk-width-1-1" href="<?php echo wc_get_cart_url(); ?>">Already In Cart</a>
			</div>
		<?php else: ?>
			<div>
				<a class="uk-button uk-button-danger uk-border-rounded uk-width-1-1" href="<?php echo $variation_data['add_tp_cart_url']; ?>">Add To Cart</a>
			</div>
		<?php endif; ?>
	</div>
</div>