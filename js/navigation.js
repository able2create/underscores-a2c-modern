/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 *
 * @package _s
 */

'use strict';

/**
 * Initialize navigation
 */
function initNavigation() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation doesn't exist.
	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.querySelector( 'button' );

	// Return early if the button doesn't exist.
	if ( ! button ) {
		return;
	}

	const menu = siteNavigation.querySelector( 'ul' );

	// Hide menu toggle button if menu is empty and return early.
	if ( ! menu ) {
		button.style.display = 'none';
		return;
	}

	// Add nav-menu class if not present
	menu.classList.add( 'nav-menu' );

	// Toggle menu on button click
	button.addEventListener( 'click', () => {
		const isExpanded = button.getAttribute( 'aria-expanded' ) === 'true';
		siteNavigation.classList.toggle( 'toggled' );
		button.setAttribute( 'aria-expanded', String( ! isExpanded ) );
	} );

	// Close menu when clicking outside
	document.addEventListener( 'click', ( event ) => {
		if ( ! siteNavigation.contains( event.target ) ) {
			siteNavigation.classList.remove( 'toggled' );
			button.setAttribute( 'aria-expanded', 'false' );
		}
	} );

	// Handle keyboard navigation
	const links = menu.querySelectorAll( 'a' );
	const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	// Add focus/blur handlers for keyboard navigation
	links.forEach( ( link ) => {
		link.addEventListener( 'focus', handleFocus, true );
		link.addEventListener( 'blur', handleFocus, true );
	} );

	// Add touch handlers for mobile
	linksWithChildren.forEach( ( link ) => {
		link.addEventListener( 'touchstart', handleTouch, { passive: false } );
	} );

	/**
	 * Handle focus events for keyboard navigation
	 *
	 * @param {Event} event - The focus or blur event
	 */
	function handleFocus( event ) {
		if ( event.type !== 'focus' && event.type !== 'blur' ) {
			return;
		}

		let element = event.target;

		// Move up through ancestors until we hit .nav-menu
		while ( element && ! element.classList.contains( 'nav-menu' ) ) {
			if ( element.tagName === 'LI' ) {
				element.classList.toggle( 'focus' );
			}
			element = element.parentElement;
		}
	}

	/**
	 * Handle touch events for mobile navigation
	 *
	 * @param {Event} event - The touchstart event
	 */
	function handleTouch( event ) {
		const menuItem = event.target.parentElement;

		if ( ! menuItem ) {
			return;
		}

		event.preventDefault();

		// Remove focus from siblings
		Array.from( menuItem.parentElement.children ).forEach( ( sibling ) => {
			if ( sibling !== menuItem ) {
				sibling.classList.remove( 'focus' );
			}
		} );

		// Toggle focus on current item
		menuItem.classList.toggle( 'focus' );
	}
}

// Initialize when DOM is ready
if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initNavigation );
} else {
	initNavigation();
}
