/**
 * Original File Created by Automattic for Underscore (_s) theme
 * https://github.com/Automattic/_s/
 *
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function($) {

	// Update the site title in real time...
	wp.customize('blogname', function(value) {
		value.bind(function(newval) {
			$('.navbar-brand').html(newval);
		});
	});

	//Update the site description in real time...
	wp.customize('blogdescription', function(value) {
		value.bind(function(newval) {
			$('.site-description').html(newval);
		});
	});

	//Update site background color...
	wp.customize('background_color', function(value) {
		value.bind(function(newval) {
			$('body').css('background-color', newval );
		});
	});

}(jQuery));
