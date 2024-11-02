jQuery(function($) {
	
	'use strict';
	
	
	/**
	 * jQuery Tiny Pub/Sub
	 * https://github.com/cowboy/jquery-tiny-pubsub
	 *
	 * Copyright (c) 2013 "Cowboy" Ben Alman
	 * Licensed under the MIT license.
	 **********************************************************************/
	var o = $({});
	$.subscribe = function() {o.on.apply(o, arguments);};
	$.unsubscribe = function() {o.off.apply(o, arguments);};
	$.publish = function() {o.trigger.apply(o, arguments);};
	
	/**
	 * Save the main building block of DOM elements; for the 
	 * sake of succinctness
	 **********************************************************************/
	var DOM = (function ( dom ) {
		
		var dom = dom || {}
			, whatsoFlag = $( '.whatso-flag' ) 
			;
		
		dom.body = $('body:eq(0)');
		dom.isSmallScreen = ( window.getComputedStyle( whatsoFlag.get(0), ':after' ).content == '"small"' || window.getComputedStyle( whatsoFlag.get(0), ':after' ).content == 'small' ) ? true : false;
		dom.isMobileScreen = ( window.getComputedStyle( whatsoFlag.get(0), ':after' ).content == '"mobile"' || window.getComputedStyle( whatsoFlag.get(0), ':after' ).content == 'mobile' ) ? true : false;
		dom.isLargeScreen = ( window.getComputedStyle( whatsoFlag.get(0), ':after' ).content == '"desktop"' || window.getComputedStyle( whatsoFlag.get(0), ':after' ).content == 'desktop' ) ? true : false;
		
		return dom;
		
	}( DOM ) );
	
	/**
	 * Simple cookie utilities
	 **********************************************************************/
	var COOKIES = (function ( cookies ) {
		
		var cookies = cookies || {};
		
		cookies.setItem = function ( name, value, durationInMinutes ) {
			var d = new Date();
			d.setTime( d.getTime() + ( durationInMinutes * 60 * 1000 ) );
			var expires = 'expires=' + d.toUTCString();
			document.cookie = name + '=' + value + ';' + expires + ';path=/';
		};
		
		cookies.getItem = function ( name ) {
			var name = name + '=',
				decodedCookie = decodeURIComponent( document.cookie ),
				ca = decodedCookie.split(';');
				
			for ( var i = 0; i < ca.length; i++ ) {
				
				var c = ca[ i ];
				
				while ( c.charAt( 0 ) == ' ' ) {
					c = c.substring(1);
				}
				
				if ( c.indexOf( name ) == 0 ) {
					return c.substring( name.length, c.length );
				}
			}
			return null;
		}
		
		cookies.deleteItem = function ( name ) {
			document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
		};
		
		return cookies;
		
	}( COOKIES ) );
	
	/**
	* Set widget height on mobile
	**********************************************************************/
	(function () {
		
		$.subscribe('whatso-widget-ready', function () {
			var container = $( '.whatso-container' )
				, box = container.find( '.whatso-box' )
				, description = container.find( '.whatso-description' )
				, toggle = container.find( '.whatso-toggle' )
				, peopleList = container.find( '.whatso-people' )
				;
			
			peopleList.css({
				maxHeight: $( window ).height() - ( description.outerHeight() + parseInt( box.css( 'bottom' ) ) + 20 )
			});
			
		});
		
	}());
	
	/**
	* Centralize the process of hide/show of the box.
	**********************************************************************/
	(function () {
		var neverBeenResized = true;
		$.subscribe('whatso-widget-ready', function () {
			var whatso = DOM.body.find( '.whatso-container' ),
				toggleBox = function ( e ) {
					whatso.toggleClass( 'whatso-show' );
					
					if ( ! COOKIES.getItem( 'whatso' ) ) {
						COOKIES.setItem( 'whatso', 'toggled', 1440 );
					}
				};
			$.subscribe('whatso-toggle-box', toggleBox);
		});
		
	}());
	
	/**
	* Show and hide the box.
	**********************************************************************/
	(function () {
		
		$.subscribe('whatso-widget-ready', function () {
			
			var whatsoFlag = DOM.body.find( '.whatso-flag' ),
				whatso = DOM.body.find( '.whatso-container' ),
				delayTime = parseInt( whatso.data( 'delay-time' ) ),
				inactiveTime = parseInt( whatso.data( 'inactive-time' ) ),
				scrollLength = parseInt( whatso.data( 'scroll-length' ) ),
				autoDisplayOnMobile = whatso.data( 'auto-display-on-mobile' ),
				box = whatso.find( '.whatso-box' ),
				toggle = whatso.find( '.whatso-toggle' ),
				close = whatso.find( '.whatso-close' ),
				bottomClose = whatso.find( '.whatso-mobile-close' ),
				autoShow
				;
			
			if ( ! whatso.length || ! whatsoFlag.length ) {
				return;
			}
			
			/* Set the box's display to block. Its visibility still hidden though. */
			box.addClass( 'whatso-js-ready' );
			
			/* Toggle box on toggle's (or close's) click */
			toggle.add( close ).add( bottomClose ).on( 'click', function () {
				$.publish('whatso-toggle-box');
			} );
			
			/* We're done here if the screen is small. */
			if ( DOM.isSmallScreen && whatso.is( '.whatso-disable-auto-display-on-small-screen' ) ) {
				return;
			}
			
			/* 	Show box after a delay time on page load and only if it has 
				not been shown before.
				*/
			if ( delayTime > 0 ) {
				autoShow = setTimeout( function () {
					if ( ! box.is( '.whatso-show' ) && ! COOKIES.getItem( 'whatso' ) ) {
						$.publish('whatso-toggle-box');
					}
				}, delayTime * 1000 );
			}
			
			/* 	Trigger after inactivity and only if it has not been shown 
				before.
				*/
			var cb,
				executed = false,
				events = 'mousemove mousedown mouseup onkeydown onkeyup focus scroll',
				showAfterInactivity = function () {
					clearTimeout( cb );
					if ( ! executed ) {
						cb = setTimeout(function () {
							if ( ! COOKIES.getItem( 'whatso' ) && ! box.is( '.whatso-show' ) ) {
								$.publish('whatso-toggle-box');
							}
							$( document ).off( events, showAfterInactivity );
						}, inactiveTime * 1000 );
					}
				};
			
			if ( inactiveTime > 0 ) {
				$( document ).on( events, showAfterInactivity );
			}
			
			/* 	Trigger after scrolling.
				Accessing DOM on-scroll is a bad idea. Let's execute the function 
				every half a second during/post scroll instead.
				*/
			var percentage = Math.abs( scrollLength ) / 100,
				scrolling,
				timing = true,
				scrollHandler = function() {
					
					scrolling = true;
					
					if ( timing ) {
						
						setTimeout(function () {
							if ( $( window ).scrollTop() >= ( $( document ).height() - $( window ).height() ) * percentage ){
								if ( ! COOKIES.getItem( 'whatso' ) && ! box.is( '.whatso-show' ) ) {
									$.publish('whatso-toggle-box');
								}
								$( window ).off( 'scroll', scrollHandler );
							}
							timing = true;
							scrolling = false;
						}, 500 );
						
						if ( scrolling ) {
							timing = false;
						}
					}
				}
				;
			
			if ( scrollLength > 0 ) {
				$( window ).on( 'scroll', scrollHandler );
			}
		});
		
	}());
	
	/**
	* If avatar is not provided or provided but error, add a hint to 
	* .whatso-face so we can show a default image.
	**********************************************************************/
	(function () {
		
		$.subscribe('whatso-widget-ready', function () {
			DOM.body.find( '.whatso-container .whatso-face' ).each(function () {
				var el = $( this ),
					img = el.find( 'img' ),
					noImage = true
					;
				
				if ( img.length ) {
					var url = img.attr( 'src' ),
						tester = new Image();
					tester.src = url;
					
					tester.onerror = function () {
						el.addClass( 'no-image' );
					};
					
				}
				else {
					el.addClass( 'no-image' );
				}
				
			});
		});
		
	}());
	
	/**
	* If we're on desktop, use web.whatsapp.com instead. But if not,
	* remove the target attribute because it will simply open the app.
	**********************************************************************/
	(function () {
		
		var alterURL = function () {
			var whatsoAccounts = DOM.body.find( 'a.whatso-account' ),
				whatsoFlag = DOM.body.find( '.whatso-flag' )
				;
			
			if ( ! whatsoFlag.length ) {
				return;
			}
			
			/* Change URL to web.whatsapp.com if the user is using a desktop. */
			if ( DOM.isLargeScreen ) {
				
				whatsoAccounts.each(function () {
					var el = $( this ),
						number = el.data( 'number' ),
						text = el.data( 'auto-text' )
						;
					
					if ( '' === number ) {
						return true;
					}
					el.attr( 'href', 'https://web.whatsapp.com/send?phone=' + number + '&text=' + text );
				});
				
			}
			else {
				if ( window.location === window.parent.location ) {
				  whatsoAccounts.removeAttr( 'target' );
				}
			}
		};
		
		alterURL();
		
		$.subscribe('whatso-widget-ready', function () {
			alterURL();
		});
		
		$.subscribe('whatso-woo-ready', function () {
			alterURL();
		});
		
		$.subscribe('whatso-button-ready', function () {
			alterURL();
		});
		
	}());
	
	/**
	* Get widget via AJAX
	**********************************************************************/
	(function () {
		
		var whatsosw = DOM.body.find( '#whatso-config' );
		
		if ( whatsosw.length < 1 ) {
			return;
		}

		/*	whatsosw.length will always renurn true for now, which is why
			we need to check the `data-ids` value */
		if ( whatsosw.data( 'ids' ) == '' ) {
			return;
		}

		/**
		 * In order to prevent heavy load on the server, we use the cache
		 * stored from the last ajax request.
		 */

		var cookieName = 'whatso-widget-cache-' + whatsosw.data( 'ids' );
		
		if ( whatsosw.data( 'cache-time' ) > 0 
			&& COOKIES.getItem( cookieName ) 
			&& sessionStorage.getItem( cookieName ) ) {

			$( sessionStorage.getItem( cookieName ) ).appendTo( DOM.body );
			setTimeout( function () {
				$.publish( 'whatso-widget-ready' );
			}, 100 );
			return;
		}
		
		var data = {
			'action': 'whatso_display_widget',
			'when': Date.now(),
			'current-language': whatsosw.data( 'current-language' ),
			'ids': whatsosw.data( 'ids' ),
			'page-title': whatsosw.data( 'page-title' ),
			'page-url': whatsosw.data( 'page-url' )
		};
		
		$.post( whatso_ajax_object.ajax_url, data, function( response ) {
			
			if ( 'no-show' === response ) {
				return;
			}
			
			var hideOnLargeScreen = true
				, hideOnSmallScreen = true
				;
				
			$( response ).find( '.whatso-account' ).each(function () {
				var el = $( this );
				
				if ( DOM.isLargeScreen && ! el.is( '.whatso-hide-on-large-screen' ) ) {
					hideOnLargeScreen = false;
				}
				
				if ( ( DOM.isSmallScreen || DOM.isMobileScreen ) && ! el.is( '.whatso-hide-on-small-screen' ) ) {
					hideOnSmallScreen = false;
				}
			});
			
			if ( ( DOM.isLargeScreen && ! hideOnLargeScreen ) || ( ( DOM.isMobileScreen || DOM.isSmallScreen ) && ! hideOnSmallScreen ) ) {
				$( response ).appendTo( DOM.body );

				/* Cache the widget for future use to prevent frequent ajax calls. */

				COOKIES.setItem( cookieName, 'loaded', 1 );
				sessionStorage.setItem( cookieName, response );
			}
			
			setTimeout( function () {
				$.publish( 'whatso-widget-ready' );
			}, 100 );
		});

	}());	
	
	/**
	* Button uses ajax to fetch data so we can have an accurate display
	* even when the page is cached.
	**********************************************************************/
	(function () {
		
		var whatsosw = DOM.body.find( '#whatso-config' )
			, ids = []
			, pageURL = ''
			, pageTitle = ''
			, buttonJSONString = ''
			, fetchNew = false
			;
		
		if ( whatsosw.length < 1 ) {
			return;
		}

		DOM.body.find( '.whatso-button-container' ).each(function () {
			var el = $( this )
				, id = el.data( 'target-id' )
				;
			
			pageURL = el.data( 'page-url' );
			pageTitle = el.data( 'page-title' );
				
			ids.push( id );
		});
		
		if ( ids.length < 1 ) {
			return;
		}

		/* Remove duplicates. Without this, we'll be facing a bug ahead. */
		ids = ids.filter( function (value, index, self) {
			return self.indexOf(value) === index;
		} );

		var displayButtons = function ( data ) {
			var data = JSON.parse( data )
			for ( var key in data ) {
				if ( data.hasOwnProperty( key ) ) {
					var val = data[ key ];
					DOM.body.find( '.whatso-button-container#whatso-button-' + val.id ).append( val.content );
					
					/* 	Remove this item from the ids. Those which are not removed means 
						that it doesn't have the button to show. */
					var index = ids.indexOf( val.id );
					if ( index > -1 ) {
						ids.splice( index, 1 );
					}
				}
			}
			
			/* 	If there's an offline account which doesn't have an offline description, 
				then remove the container. */
			for ( var i = 0, limit = ids.length; i < limit; i++ ) {
				DOM.body.find( '.whatso-button-container#whatso-button-' + ids[i] ).remove();
			}
			
			setTimeout( function () {
				$.publish('whatso-button-ready');
			}, 100 );
		};

		/**
		 * In order to prevent heavy load on the server, we use the cache
		 * stored from the last ajax request.
		 */

		/* It's cached and there's no cookie */
		if ( whatsosw.data( 'cache-time' ) > 0 && ! COOKIES.getItem( 'whatso-buttons-cache' ) ) {
			fetchNew = true;
		}

		/* It's cached and there's no storage */
		if ( whatsosw.data( 'cache-time' ) > 0 && ! sessionStorage.getItem('whatso-buttons-cache') ) {
			fetchNew = true;
		}

		/* It's cached and there's a storage but some ids are missing */
		if ( whatsosw.data( 'cache-time' ) > 0 && sessionStorage.getItem('whatso-buttons-cache') ) {
			var jsoned = JSON.parse( sessionStorage.getItem('whatso-buttons-cache') )
				, currentIds = []
				;

			for ( var key in jsoned ) {
				if ( jsoned.hasOwnProperty( key ) ) {
					var val = jsoned[ key ];
					currentIds.push( val.id )
				}
			}
			
			var arrIds = ids.toString().split( ',' );
			for ( var i = 0, lim = arrIds.length; i < lim; i++ ) {
				if ( ! currentIds.includes( parseInt( arrIds[ i ] ) ) ) {
					fetchNew = true;
				}
			}
		}

		/* It's cached, there's a storage and nothing's wrong */
		if ( whatsosw.data( 'cache-time' ) > 0 
			&& ! fetchNew 
			&& sessionStorage.getItem('whatso-buttons-cache')) {

			displayButtons( sessionStorage.getItem('whatso-buttons-cache') );
		}

		/* It's not cached of there's something wrong with the cache */
		if ( whatsosw.data( 'cache-time' ) < 1 || fetchNew ) {
			var data = {
				'action': 'whatso_display_buttons',
				'when': Date.now(),
				'ids': ids.join( ',' ),
				'page-title': pageTitle,
				'page-url': pageURL
			};
			
			$.post( whatso_ajax_object.ajax_url, data, function( response ) {
				
				if ( response === 'none' ) {
					return false;
				}

				/* Cache the widget for future use to prevent frequent ajax calls. */
				COOKIES.setItem( 'whatso-buttons-cache', 'loaded', 1 );
				sessionStorage.setItem('whatso-buttons-cache', response );
				displayButtons( response );
				
			});
		}
		
	}());
	
	/**
	* WooCommerce buttons are loaded using AJAX and treated differently 
	* from the other buttons because it has randomize and limit features.
	**********************************************************************/
	(function () {
		
		var whatsosw = DOM.body.find( '#whatso-config' )
			, ids = ''
			, pageURL = ''
			, pageTitle = ''
			, buttonJSONString = ''
			, fetchNew = false
			;

		if ( whatsosw.length < 1 ) {
			return;
		}
		
		DOM.body.find( '.whatso-wc-buttons-container' ).each(function () {
			var el = $( this );
			
			pageURL = el.data( 'page-url' );
			pageTitle = el.data( 'page-title' );
			ids = el.data( 'ids' );
		});
		
		if ( ids.length < 1 ) {
			return;
		}

		var displayWCButtons = function ( data ) {

			var data = JSON.parse( data );
				
			for ( var key in data ) {
				if ( data.hasOwnProperty( key ) ) {
					var val = data[ key ];
					DOM.body.find( '.whatso-wc-buttons-container' ).append( $( '<p>' + val.content + '</p>' ) );
				}
			}
			
			setTimeout( function () {
				$.publish('whatso-woo-ready');
			}, 100 );

		};

		/**
		 * In order to prevent heavy load on the server, we use the cache
		 * stored from the last ajax request.
		 */

		var cookieName = 'whatso-wc-buttons-cache-' + ids.toString().replace( /,/g, '-' );
		
		if ( whatsosw.data( 'cache-time' ) > 0 
			&& COOKIES.getItem( cookieName ) 
			&& sessionStorage.getItem( cookieName ) ) {

			displayWCButtons( sessionStorage.getItem( cookieName ) );
			return;
		}

		var data = {
			'action': 'whatso_display_buttons',
			'when': Date.now(),
			'ids': ids,
			'type' : 'woocommerce_button',
			'page-title': pageTitle,
			'page-url': pageURL
		};
		
		$.post( whatso_ajax_object.ajax_url, data, function( response ) {
			
			if ( response === 'none' ) {
				return false;
			}
			
			/* Cache the widget for future use to prevent frequent ajax calls. */
			COOKIES.setItem( cookieName, 'loaded', 1 );
			sessionStorage.setItem( cookieName, response );
			displayWCButtons( response );
			
		});

	}());

	/**
	* Send event to Google Analytics
	**********************************************************************/
	(function () {
		
		/* Remove gdpr container if user already gave her consent. */
		$.subscribe('whatso-widget-ready', function () {
			if ( COOKIES.getItem( 'consent' ) ) {
				DOM.body.find( '.whatso-gdpr' ).remove();
			}
		});
		
		DOM.body.on( 'click', 'a.whatso-account', function ( e ) {
			
			var el = $( this )
				, number = parseInt( el.data( 'number' ) )
				, gaLabel = el.data( 'ga-label' )
				, parent = el.parents( '.whatso-people' )
				, gdpr = parent.find( '.whatso-gdpr' )
				, confirmation = gdpr.find( '.whatso-confirmation' )
				, consent = gdpr.find( '#whatso-consent' )
				, to
				;
			
			/* If consent needed and is not given yet. */
			if ( consent.length && ! consent.is( ':checked' ) ) {
				confirmation.addClass( 'whatso-blink' );
				
				confirmation.one( 'animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd', function () {
					confirmation.removeClass( 'whatso-blink' );
				} );
				
				e.preventDefault();
				return false;
			}
			
			/* If the script reaches here, the user gave her consent. */
			if ( el.closest( '.whatso-container' ).length ) {
				COOKIES.setItem( 'consent', 'acquired', 1440 );
				gdpr.remove();
			}
			
			/* Click analytics. Try to send data to Google or Facebook. */
			try {
				gtag( 'event', 'Button Clicked', {
					'event_category': 'WhatsApp Click to Chat',
					'event_label': 'WhatsApp Account: ' + gaLabel
				} );	
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
			try {
				ga( 'send', 'event', 'WhatsApp Click to Chat', 'Button Clicked', 'WhatsApp Account: ' + gaLabel );
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
			try {
				_gaq.push([ '_trackEvent', 'WhatsApp Click to Chat', 'Button Clicked', 'WhatsApp Account: ' + gaLabel ]);
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
			try {
				dataLayer.push({
					'event': 'customEvent',
					'eventCategory': 'WhatsApp Click to Chat',
					'eventAction': 'Button Clicked',
					'eventLabel': 'WhatsApp Account: ' + gaLabel
				});
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
			var pixeled = false;
			try {
				/* Facebook pixel yields warning when we initiate fbq multiple times. */
				if ( ! pixeled ) {
					fbq( 'trackCustom', 'ClickToChat', {
						event: 'Chat started',
						account: gaLabel
					});
					pixeled = true;
				}
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
		} );
		
	}());
	
	/**
	* 
	**********************************************************************/
	(function () {
		

		
	}());
	
});