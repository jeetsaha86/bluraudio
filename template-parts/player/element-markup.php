<div class="__audio_player">
	<div class="__listing_overlay uk-section-default">
		<div class="uk-grid uk-grid-collapse uk-height-1-1" uk-grid>
			<div class="uk-width-medium@m __track_filters_wrap uk-background-secondary uk-light">
				<div class="__track_filters uk-grid uk-grid-medium uk-child-width-1-1" uk-grid>
					<div class="uk-visible@m">
						<div class="__track_filter_search">
							<div class="uk-grid uk-grid-collapse" uk-grid>
								<div class="uk-width-small uk-width-1-1@m uk-hidden@m">
									<div class="__track_filter_title uk-text-bold uk-text-emphasis"><?php _e( 'Search Track', 'yootheme' ) ?></div>
								</div>
								<div class="uk-width-expand">
									<?php echo facetwp_display( 'facet', 'track_search' ); ?>
								</div>
							</div>
						</div>
					</div>
					<div>
						<div class="uk-grid uk-grid-collapse" uk-grid>
							<div class="uk-width-small uk-width-1-1@m">
								<div class="uk-grid uk-grid-small uk-child-width-auto" uk-grid>
									<div class="__track_filter_title uk-text-bold">
										<?php _e( 'Filter by Mood', 'yootheme' ) ?>
									</div>
									<div>
										<a onclick="FWP.reset()" class="facet-reset uk-link uk-text-small">Reset</a>
									</div>
								</div>
							</div>
							<div class="uk-width-expand">
								<div class="uk-visible@m">
									<?php echo facetwp_display( 'facet', 'track_mood' ); ?>
								</div>
								<div class="uk-hidden@m uk-dark">
									<?php echo facetwp_display( 'facet', 'track_mood_mobile' ); ?>
								</div>
							</div>
						</div>
					</div>
					<div>
						<div class="uk-grid uk-grid-collapse" uk-grid>
							<div class="uk-width-small uk-width-auto@m">
								<div class="__track_filter_title uk-text-bold uk-text-emphasis"><?php _e( 'Favorites Only', 'yootheme' ) ?></div>
							</div>
							<div class="uk-width-expand uk-flex uk-flex-middle uk-flex-right">
								<div class="__fav_checkbox">
									<?php $checked = isset( $_COOKIE['__fav_cookie'] ) && $_COOKIE['__fav_cookie'] ? 'checked' : ''; ?>
									<input id="__fav_only" type="checkbox" <?php echo $checked; ?>>
									<label for="__fav_only"></label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="uk-width-expand@m __track_items_wrap">
				<div class="uk-hidden@m __track_filter_search_mobile">
					<div class="__track_filter_search">
						<div class="uk-grid uk-grid-collapse" uk-grid>
							<div class="uk-width-expand">
								<?php echo facetwp_display( 'facet', 'track_search_mobile' ); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="__track_items">
					<?php echo facetwp_display( 'template', 'track_listing' ); ?>
					<div class="__track_pager">
						<?php echo facetwp_display( 'facet', 'track_pager' ); ?>
					</div>
				</div>
			</div>
			<div class="uk-width-large@m __track_preview_wrap">
				<a href="#" class="__close_playlist __track_preview_close">
					<span uk-icon="icon:close"></span>
				</a>
				<div class="__track_preview uk-padding">
					<div class="__track_preview_artwork">
						<img alt="" class="uk-box-shadow-large uk-width-large" src="https://placeholder.pics/svg/300/F4F4F4/3D3B4E-EFEFEF/Placeholder">
					</div>
					<h4 class="__track_preview_title uk-margin-remove-bottom"></h4>
					<div class="__track_preview_meta">
						<ul class="uk-subnav uk-subnav-divider uk-text-small uk-text-muted">
							<!--<li class="__track_preview_artist"></li>-->
							<li class="__track_preview_duration"></li>
						</ul>
					</div>
					<div class="__track_preview_tags uk-margin-bottom"></div>
					<div class="uk-margin-bottom">
						<a class="__track_buy uk-button uk-button-danger uk-button-small">
							<span class="uk-margin-small-right" uk-icon="icon : cart; ratio: 1.2"></span>
							<span>License Track</span>
						</a>
						<a class="__track_favorite uk-button uk-button-default uk-button-small">
							<span uk-icon="icon : heart; ratio: 1.2"></span>
						</a>
					</div>
					<div class="__track_preview_content uk-text-small"></div>
					<div>
						<a href="<?php echo wc_get_checkout_url();?>" class="uk-button uk-button-secondary uk-button-small uk-width-1-1">Checkout</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="__audio_controls uk-section-default">
		<div class="__player_progress">
			<div class="__player_current_progress"></div>
			<input value="0" class="__player_track_bar uk-range" type="range" min="0" max="100" step="0.01">
		</div>
		<div class="uk-grid uk-flex uk-flex-middle" uk-grid>
			<div class="uk-width-large@m uk-width-auto">
				<div class="uk-grid uk-grid-medium uk-flex uk-flex-middle" uk-grid>
					<div class="uk-width-auto">
						<a class="__track_artwork uk-cover-container" href="#">
							<img src="" alt="" uk-cover>
							<div class="uk-overlay-primary uk-position-cover">
								<div class="uk-position-center uk-text-center">
									<span style="display: block;font-size: 80%;">Expand</span>
									<span uk-icon="icon: list; ratio: 1.5;"></span>
								</div>
							</div>
						</a>
					</div>
					<div>
						<div class="__track_title uk-text-bold"></div>
						<div class="__track_tags uk-text-meta uk-visible@m"></div>
					</div>
				</div>
			</div>
			<div class="uk-width-expand">
				<div class="__player_actions uk-flex uk-flex-middle uk-flex-center@m uk-flex-right">
					<div>
						<a class="__player_action_repeat uk-visible@m">
							<img alt src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/player-buttons/repeat.svg" uk-svg>
						</a>
					</div>
					<div>
						<a class="__player_action_previous">
							<img alt src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/player-buttons/backward.svg" uk-svg>
						</a>
					</div>
					<div class="__player_action_play_pause">
						<div class="__player_spinner">
							<div uk-spinner="ratio: 5"></div>
						</div>
						<a class="__player_action_play">
							<img alt src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/player-buttons/play.svg" uk-svg>
						</a>
						<a class="__player_action_pause">
							<img alt src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/player-buttons/pause.svg" uk-svg>
						</a>
					</div>
					<div>
						<a class="__player_action_next">
							<img alt src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/player-buttons/forward.svg" uk-svg>
						</a>
					</div>
					<div>
						<a class="__player_action_shuffle uk-visible@m">
							<img alt src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/player-buttons/shuffle.svg" uk-svg>
						</a>
					</div>
				</div>
			</div>
			<div class="uk-width-large@m uk-visible@m uk-text-right@m">
				<a class="__track_buy uk-button uk-button-danger uk-button-small uk-margin-right" href="#">
					<span class="uk-margin-small-right" uk-icon="icon : cart; ratio: 1.2"></span>
					<span>License Track</span>
				</a>
			</div>
		</div>
		<div class="uk-hidden">
			<audio class="__player_audio"></audio>
		</div>
	</div>
</div>





