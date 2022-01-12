<?php $product_id = get_the_ID(); ?>
<?php $user_favorites = get_user_favorites(); ?>
<?php $extra_class = in_array( $product_id, $user_favorites ) ? '__track_fav' : ''; ?>
<?php $extra_class_active = in_array( $product_id, $user_favorites ) ? '__active' : ''; ?>

<div class="__track_item <?php echo $extra_class; ?> __playable" data-product-id="<?php echo $product_id; ?>">
	<div class="uk-grid uk-flex uk-flex-middle" uk-grid>
		<div class="uk-width-auto">
			<div class="uk-width-auto uk-position-relative">
				<img alt="<?php the_title(); ?>" data-src="<?php the_post_thumbnail_url( 'thumbnail' ); ?>" height="50" uk-img width="50">
				<div class="uk-overlay-primary uk-position-cover __track_icon">
					<div class="uk-position-center">
						<img alt src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icon-anim-equalizer.svg" uk-svg>
					</div>
				</div>
			</div>
		</div>
		<div class="uk-width-expand">
			<div class="uk-text-bold __track_title"><?php the_title(); ?></div>
			<!--
			<div class="uk-text-meta uk-text-lowercase">
				<?php $tags = wc_get_product_terms( $product_id, 'pa_mood' ); ?>
				<?php if ( ! is_wp_error( $tags ) && ! empty( $tags ) ) : ?>
					<?php $tags = wp_list_pluck( $tags, 'name' ); ?>
					<?php echo '#' . implode( ' #', $tags ); ?>
				<?php endif; ?>
			</div>
			-->
		</div>
		<div class="uk-width-auto">
			<a class="__track_favorite uk-margin-small-right <?php echo $extra_class_active; ?>" href="#" data-id="<?php echo $product_id; ?>">
				<span class="uk-icon-button uk-button-default" uk-icon="icon: heart"></span>
			</a>
			<a class="__track_buy" href="#" data-id="<?php echo $product_id; ?>">
				<span class="uk-icon-button uk-button-secondary" uk-icon="icon: cart"></span>
			</a>
		</div>
	</div>
</div>