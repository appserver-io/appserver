;(function(){

	if (window.jQuery) {

		// jQuery version
		$(document).ready(function(){

			// Add a 'js' class to the html tag
			// If you're using modernizr or similar, you
			// won't need to do this
			$('html').addClass('js');

			// Fade in videos
			var $fade_in_videos = $('.video-bg video');
			$fade_in_videos.each(function(){
				if( $(this)[0].currentTime > 0 ) {
					// It's already started playing
					$(this).addClass('is-playing');
				} else {
					// It hasn't started yet, wait for the playing event
					$(this).on('playing', function(){
						$(this).addClass('is-playing');
					});
				}
			});

			// Scrap videos on iOS because it won't autoplay,
			// it adds it's own play icon and opens the
			// media player when clicked
			var iOS = /iPad|iPhone|iPod/.test(navigator.platform) || /iPad|iPhone|iPod/.test(navigator.userAgent);
			if( iOS ) {
				$('.video-bg video').remove();
			}

		});

	} else {

		// Vanilla JS version

		// Add a 'js' class to the html tag
		// If you're using modernizr or similar, you
		// won't need to do this
		document.documentElement.className += " js";

		// Fade in videos
		var fade_in_videos = document.querySelectorAll('.video-bg video');
		for( i=0; i<fade_in_videos.length; i++ ) {
			if( fade_in_videos[i].currentTime > 0 ) {
				// It's already started playing
				fade_in_videos[i].className += ' is-playing';
			} else {
				// It hasn't started yet, wait for the playing event
				fade_in_videos[i].addEventListener("playing", function(){
					if(this.className.indexOf('is-playing') < 0) {
						this.className += ' is-playing';
					}
				});
			}
		} 

		// Scrap videos on iOS because it won't autoplay,
		// it adds it's own play icon and opens the
		// media player when clicked
		var iOS = /iPad|iPhone|iPod/.test(navigator.platform);
		if( iOS ) {
			var background_videos = document.querySelectorAll('.video-bg video');
			for( i=0; i<background_videos.length; i++ ) {
				background_videos[i].parentNode.removeChild(background_videos[i]);
			}
		}

	}

})();
