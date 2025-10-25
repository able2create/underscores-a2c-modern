/* global wp */
/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @package _s
 */

'use strict';

/**
 * Initialize customizer preview
 */
( function() {
	// Wait for wp.customize to be available
	if ( typeof wp === 'undefined' || typeof wp.customize === 'undefined' ) {
		return;
	}

	// Site title
	wp.customize( 'blogname', ( value ) => {
		value.bind( ( to ) => {
			const titleElement = document.querySelector( '.site-title a' );
			if ( titleElement ) {
				titleElement.textContent = to;
			}
		} );
	} );

	// Site description
	wp.customize( 'blogdescription', ( value ) => {
		value.bind( ( to ) => {
			const descriptionElement = document.querySelector( '.site-description' );
			if ( descriptionElement ) {
				descriptionElement.textContent = to;
			}
		} );
	} );

	// Header text color
	wp.customize( 'header_textcolor', ( value ) => {
		value.bind( ( to ) => {
			const titleElements = document.querySelectorAll( '.site-title, .site-description' );
			const linkElements = document.querySelectorAll( '.site-title a, .site-description' );

			if ( to === 'blank' ) {
				// Hide text
				titleElements.forEach( ( element ) => {
					element.style.clip = 'rect(1px, 1px, 1px, 1px)';
					element.style.position = 'absolute';
				} );
			} else {
				// Show text with color
				titleElements.forEach( ( element ) => {
					element.style.clip = 'auto';
					element.style.position = 'relative';
				} );

				linkElements.forEach( ( element ) => {
					element.style.color = to;
				} );
			}
		} );
	} );
}() );
