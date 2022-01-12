/*
 *  jquery-audio - v1.0.0
 *  A jQuery plugin developed for PinkFader site's music player
 *  http://wecodify.co
 *
 *  Made by We Codify Co.
 */
// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ($) {

    // here we go!
    $.audio = function (element) {

        // to avoid confusions, use "plugin" to reference the
        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('audio').settings.propertyName from outside the plugin,
        // where "element" is the element the plugin is attached to;
        plugin.settings = {
            'data': {},
            'queue': [],
            'elements': {},
        }

        // reference to the jQuery version of DOM element
        var $element = $(element);

        /**
         * Returns a random integer between min (inclusive) and max (inclusive).
         * The value is no lower than min (or the next integer greater than min
         * if min isn't an integer) and no greater than max (or the next integer
         * lower than max if max isn't an integer).
         * Using Math.round() will give you a non-uniform distribution!
         */
        var getRandomInt = function (min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        // the "constructor" method that gets called when the object is created
        plugin.init = function () {

            // set playing status
            plugin.settings.data.playing = false;

            // set shuffle status
            plugin.settings.data.shuffle = false;

            // set repeat status
            plugin.settings.data.repeat = false;

            // set repeat status
            plugin.settings.data.expanded = false;

            // if we need to reopen the player
            plugin.set_expanded_state();

            // run the method to store elements for use
            plugin.elements();

            // get initial track list
            plugin.queue_track_ids();

            // bind all events for the player
            plugin.bind_events();

            // load first track data
            plugin.load_track_data();
        }

        // store all elements inside the player for global use
        plugin.elements = function () {
            plugin.settings.elements.listOverlay              = $element.find('.__listing_overlay');
            plugin.settings.elements.listItems                = $element.find('.__track_items');
            plugin.settings.elements.playerControls           = $element.find('.__audio_controls');
            plugin.settings.elements.playerAudio              = $element.find('.__player_audio');
            plugin.settings.elements.playerLoader             = $element.find('.__player_spinner');
            plugin.settings.elements.playerProgressSlider     = $element.find('.__player_track_bar');
            plugin.settings.elements.playerCurrentProgressBar = $element.find('.__player_current_progress');
            plugin.settings.elements.trackRepeat              = $element.find('.__player_action_repeat');
            plugin.settings.elements.trackPrevious            = $element.find('.__player_action_previous');
            plugin.settings.elements.trackPlay                = $element.find('.__player_action_play');
            plugin.settings.elements.trackPause               = $element.find('.__player_action_pause');
            plugin.settings.elements.trackNext                = $element.find('.__player_action_next');
            plugin.settings.elements.trackShuffle             = $element.find('.__player_action_shuffle');
            plugin.settings.elements.trackBuy                 = plugin.settings.elements.playerControls.find('.__track_buy');
            plugin.settings.elements.trackTitle               = plugin.settings.elements.playerControls.find('.__track_title');
            plugin.settings.elements.trackTags                = plugin.settings.elements.playerControls.find('.__track_tags');
            plugin.settings.elements.trackArtwork             = plugin.settings.elements.playerControls.find('.__track_artwork');


            plugin.settings.elements.trackPreview          = $element.find('.__track_preview');
            plugin.settings.elements.trackPreviewBuy       = plugin.settings.elements.trackPreview.find('.__track_buy');
            plugin.settings.elements.trackPreviewFav       = plugin.settings.elements.trackPreview.find('.__track_favorite');
            plugin.settings.elements.trackPreviewTags      = plugin.settings.elements.trackPreview.find('.__track_preview_tags');
            plugin.settings.elements.trackPreviewTitle     = plugin.settings.elements.trackPreview.find('.__track_preview_title');
            //plugin.settings.elements.trackPreviewArtist    = plugin.settings.elements.trackPreview.find('.__track_preview_artist');
            plugin.settings.elements.trackPreviewContent   = plugin.settings.elements.trackPreview.find('.__track_preview_content');
            plugin.settings.elements.trackPreviewDuration  = plugin.settings.elements.trackPreview.find('.__track_preview_duration');
            //plugin.settings.elements.trackPreviewMetronome = plugin.settings.elements.trackPreview.find('.__track_preview_metronome');
            plugin.settings.elements.trackPreviewArtwork   = plugin.settings.elements.trackPreview.find('.__track_preview_artwork');
        }

        /* Toggle the listings */
        plugin.toggle_listing = function () {
            if (plugin.settings.data.expanded === false) {
                plugin.open_listing();
            } else {
                plugin.close_listing();
            }
        }

        /* Open the listings */
        plugin.open_listing = function () {
            plugin.settings.data.expanded = true;

            // add classes
            $element.addClass('open');
            $('html').addClass('__player_expanded');
        }

        /* Close the listings */
        plugin.close_listing = function () {
            // set state to closed
            plugin.settings.data.expanded = false;

            // remove classes
            $element.removeClass('open');
            $('html').removeClass('__player_expanded');
        }

        /* Check if we need to re open the player */
        plugin.set_expanded_state = function () {
            var current_url = new URL(window.location.href),
                will_expand = current_url.searchParams.get('expand_player');

            if (will_expand) {
                plugin.open_listing();
            }
        }

        /* Return the state of the player expanded status */
        plugin.get_expanded_state = function () {
            return plugin.settings.data.expanded;
        }

        /* Audio player is busy */
        plugin.working = function () {
            plugin.settings.elements.playerControls.addClass('__loading');
        }

        /* Audio player is no more busy */
        plugin.completed = function () {
            plugin.settings.elements.playerControls.removeClass('__loading');
        }

        /* Return audio player playing status */
        plugin.is_playing = function () {
            return plugin.settings.data.playing;
        }

        /* Destroy all data, remove source and reset everything */
        plugin.destroy = function () {
            // set the source to none
            plugin.settings.elements.playerAudio.attr('src', '');

            // set the track bar progress to 0
            plugin.settings.elements.playerCurrentProgressBar.css('width', '0%');

            // set the track slider to 0
            plugin.settings.elements.playerProgressSlider.val(0);

            // remove the playing class to the controls div
            plugin.settings.elements.playerControls.removeClass('__playing');
        }

        /* On startup or item click anywhere on the site find */
        /* all siblings and queue all product ids from the found items */
        plugin.queue_track_ids = function (clicked = null) {
            // empty queue
            plugin.settings.queue = [];

            // get all item divs
            var $track_items = clicked != null ? $(clicked).parents('.__track_items').find('.__track_item') : plugin.settings.elements.listItems.find('.__track_item');

            // get the data id from each items in the list
            $track_items.each(function (index, element) {
                plugin.settings.queue[index] = parseInt($(this).attr('data-product-id'));
            });

            // set current clicked index
            // this is the index point where we start playing
            plugin.settings.data.currentIndex = clicked != null ? $(clicked).index() : 0;
        }

        /* AJAX Load track data from WordPress and trigger a loaded event */
        plugin.load_track_data = function () {
            // get the id of the track to be played
            var load_track_id = plugin.settings.queue[plugin.settings.data.currentIndex];

            // params for ajax call
            var params = {
                'action': 'get_track_data',
                'productID': load_track_id
            };

            // stop and reset all
            plugin.destroy();

            // enable loading state
            plugin.working();

            // ajax call to acquire track data from WordPress
            $.post(pinkFader.ajaxURL, params, function (response) {
                // set track data
                plugin.settings.data.trackData = response;

                // set artwork
                plugin.settings.elements.trackArtwork.find('img').attr('src', response.artwork.thumb);
                plugin.settings.elements.trackPreviewArtwork.find('img').attr('src', response.artwork.large);

                // set title
                var decodedTitle = plugin.htmlDecode(response.title);
                plugin.settings.elements.trackTitle.text(decodedTitle);
                plugin.settings.elements.trackPreviewTitle.text(decodedTitle);

                // set content
                plugin.settings.elements.trackPreviewContent.html(response.content);

                // set tags
                // plugin.settings.elements.trackTags.html(response.tags);
                plugin.settings.elements.trackPreviewTags.html(response.tags);

                // set artist
                //plugin.settings.elements.trackPreviewArtist.text('By ' + response.track.artist);

                // set duration
                plugin.settings.elements.trackPreviewDuration.text(response.track.length + ' Minutes');

                // set bpm
                // plugin.settings.elements.trackPreviewMetronome.text(response.track.bpm + ' BPM');

                // set buy button
                plugin.settings.elements.trackBuy.attr('data-id', response.id);
                plugin.settings.elements.trackPreviewBuy.attr('data-id', response.id);

                // set favorite
                plugin.settings.elements.trackPreviewFav.attr('data-id', response.id);

                // remove active classes
                plugin.settings.elements.trackPreviewFav.removeClass('__active');

                // set active class if necessary
                if (response.favorite === true) {
                    plugin.settings.elements.trackPreviewFav.addClass('__active');
                }

                // set audio track
                plugin.settings.elements.playerAudio.attr('src', response.track.url).trigger('added');
            });
        }

        plugin.set_index = function (increment = 0, force = false) {
            // get queue length
            var queue_length  = plugin.settings.queue.length - 1,
                current_index = plugin.settings.data.currentIndex;

            // if repeat mode is not on
            if (!plugin.settings.data.repeat || force) {
                // if shuffle mode is on
                if (plugin.settings.data.shuffle) {
                    plugin.settings.data.currentIndex = Math.floor(Math.random() * (queue_length + 1));
                } else {
                    var next_track_index = current_index + increment;
                    if (next_track_index < 0) {
                        plugin.settings.data.currentIndex = queue_length;
                    } else if (next_track_index > queue_length) {
                        plugin.settings.data.currentIndex = 0;
                    } else {
                        plugin.settings.data.currentIndex = next_track_index;
                    }
                }
            }
        }

        plugin.bind_events = function () {
            // toggle listing bar on clicking the artwork box
            plugin.settings.elements.trackArtwork.on('click', function (e) {
                // prevent default behavior
                e.preventDefault();

                // toggle the listing bar
                plugin.toggle_listing();
            });

            // display the listing overlay when clicked on artwork
            $('body').on('click', '.__track_item', function (e) {
                // prevent default behavior
                e.preventDefault();

                // set track to autoplay on when clicked
                plugin.settings.data.playing = true;

                // get the track ids to tbe played
                plugin.queue_track_ids($(this));

                // load the track data from WordPress
                plugin.load_track_data();
            });

            plugin.settings.elements.trackPause.on('click', function () {
                // enable loading state
                plugin.working();

                // set the playing status
                plugin.settings.data.playing = false;

                // trigger pause
                plugin.settings.elements.playerAudio.trigger('pause');
            });

            plugin.settings.elements.trackPlay.on('click', function () {
                // enable loading state
                plugin.working();

                // set the playing status
                plugin.settings.data.playing = true;

                // trigger pause
                plugin.settings.elements.playerAudio.trigger('play');
            });

            plugin.settings.elements.trackRepeat.on('click', function () {
                // set the repeat status
                plugin.settings.data.repeat = plugin.settings.data.repeat !== true;

                // toggle pause
                plugin.settings.elements.trackRepeat.toggleClass('__active');
            });

            plugin.settings.elements.trackShuffle.on('click', function () {
                // set the repeat status
                plugin.settings.data.shuffle = plugin.settings.data.shuffle !== true;

                // toggle pause
                plugin.settings.elements.trackShuffle.toggleClass('__active');
            });

            plugin.settings.elements.trackPrevious.on('click', function (e) {
                // prevent default behavior
                e.preventDefault();

                // increase the index
                plugin.set_index(-1, true);

                // load the track data from WordPress
                plugin.load_track_data();
            });

            plugin.settings.elements.trackNext.on('click', function (e) {
                // prevent default behavior
                e.preventDefault();

                // increase the index
                plugin.set_index(1, true);

                // load the track data from WordPress
                plugin.load_track_data();
            });

            // on audio track play
            plugin.settings.elements.playerAudio.on('added', function () {
                // set active item
                $('body').find('.__track_item').removeClass('__item_active').end().find('.__track_item[data-product-id="' + plugin.settings.data.trackData.id + '"]').addClass('__item_active');

                // trigger load
                plugin.settings.elements.playerAudio.trigger('load');
            });

            // Run when enough data is available
            plugin.settings.elements.playerAudio.on('canplay', function () {
                // remove loading state
                plugin.completed();

                if (plugin.is_playing()) {
                    // trigger the play method for audio
                    plugin.settings.elements.playerAudio.trigger('play');
                }

                // get and store the duration of the track
                plugin.settings.data.trackDuration = plugin.settings.elements.playerAudio.get(0).duration;
            });

            // on audio track play
            plugin.settings.elements.playerAudio.on('play', function () {
                // remove loading state
                plugin.completed();

                // add the playing class to the controls div
                plugin.settings.elements.playerControls.addClass('__playing');
            });

            // on audio track pause
            plugin.settings.elements.playerAudio.on('pause', function () {
                // remove loading state
                plugin.completed();

                // remove the playing class to the controls div
                plugin.settings.elements.playerControls.removeClass('__playing');
            });

            // on audio track end
            plugin.settings.elements.playerAudio.on('ended', function () {
                // remove loading state
                plugin.completed();

                // increase the index
                plugin.set_index(1);

                // remove the playing class to the controls div
                plugin.settings.elements.playerControls.removeClass('__playing');

                // load the track data from WordPress
                plugin.load_track_data();
            });

            // on audio track progress
            plugin.settings.elements.playerAudio.on('timeupdate', function () {
                // get and store the current time of the track playing
                plugin.settings.data.trackCurrentTime = plugin.settings.elements.playerAudio.get(0).currentTime;

                // calculate the current progress and store the value
                plugin.settings.data.trackCurrentProgress = (100 * plugin.settings.data.trackCurrentTime) / plugin.settings.data.trackDuration;
                plugin.settings.data.trackCurrentProgress = plugin.settings.data.trackCurrentProgress.toFixed(2);

                // set the progress tp trackbar and slider
                //plugin.settings.elements.playerProgressSlider.val(plugin.settings.data.trackCurrentProgress);
                plugin.settings.elements.playerCurrentProgressBar.css('width', plugin.settings.data.trackCurrentProgress + '%');
            });

            // on slider drag start
            plugin.settings.elements.playerProgressSlider.on('mousedown keydown touchstart input', function () {
                // pause when seeking
                plugin.settings.elements.playerAudio.trigger('pause');

                // get slider value
                var seek_to_percent = plugin.settings.elements.playerProgressSlider.val();

                // calculate the current progress and store the value
                plugin.settings.data.trackCurrentProgress = (plugin.settings.data.trackDuration * seek_to_percent) / 100;
                plugin.settings.data.trackCurrentProgress = plugin.settings.data.trackCurrentProgress.toFixed(2);

                // seek to the calculated current time
                plugin.settings.elements.playerCurrentProgressBar.css('width', seek_to_percent + '%');
            });

            plugin.settings.elements.playerProgressSlider.on('mouseup keyup touchend', function () {
                // get slider value
                var seek_to_percent = plugin.settings.elements.playerProgressSlider.val();

                // calculate the current progress and store the value
                plugin.settings.data.trackCurrentProgress = (plugin.settings.data.trackDuration * seek_to_percent) / 100;
                plugin.settings.data.trackCurrentProgress = plugin.settings.data.trackCurrentProgress.toFixed(2);

                // seek to the calculated current time
                plugin.settings.elements.playerAudio.get(0).currentTime = plugin.settings.data.trackCurrentProgress;
                plugin.settings.elements.playerCurrentProgressBar.css('width', seek_to_percent + '%');
            });
        }

        plugin.htmlDecode = function (input) {
            var e       = document.createElement('textarea');
            e.innerHTML = input;
            // handle case of empty input
            return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
        }

        // fire up the plugin!
        // call the "constructor" method
        plugin.init();
    }

    // add the plugin to the jQuery.fn object
    $.fn.audio = function () {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            // noinspection EqualityComparisonWithCoercionJS
            if (undefined == $(this).data('audio')) {

                // create a new instance of the plugin
                // pass the DOM element
                var plugin = new $.audio(this);

                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('audio').publicMethod(arg1, arg2, ... argn)
                $(this).data('audio', plugin);
            }
        });
    }
})(jQuery);