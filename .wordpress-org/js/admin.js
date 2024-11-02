jQuery(function($) {
	
	'use strict';
	
	/**
	 * Save the main building block of DOM elements; for the 
	 * sake of succinctness
	 **********************************************************************/
	var DOM = (function ( dom ) {
		
		var dom = dom || {};
		
		dom.body = $( 'body:eq(0)' );
		
		return dom;
		
	} ( DOM ) );
	
	/**
	* I use MiniColor library for the color picker. It should be use on 
	* all inputs with a .minicolors class name.
	**********************************************************************/
	(function () {
		
		if( $.minicolors ) {
			DOM.body.find( '.minicolors' ).minicolors({
				opacity: true,
				format: 'rgb'
			});
		}
		
	}());
	
	/**
	* 
	**********************************************************************/
	(function () {
		
		var handleMediaPicker = function () {
			if( $('table')) {
                $(".post-type-whatso_accounts #posts-filter").after("<a style='margin-top:20px;position: absolute' target='_blank' href='https://www.whatso.net/aqwertyzxcvdsoiptrdghjitrd?waid=192794'><img src='https://www.whatso.net/images/click-to-chat.png'/></a>");
                $(".whatso_page_whatso_woocommerce_button .wrap").after("<a style='margin-top:20px;' target='_blsank' href='https://www.whatso.net/aqwertyzxcvdsoiptrdghjitrd?waid=192794'><img src='https://www.whatso.net/images/click-to-chat.png'/></a>");
			}
			
			DOM.body.find( '.media-picker' ).each(function () {
				var el = $( this ),
					container,
					button,
					buttonText = el.data( 'button-text' ),
					preview
					;
				
				el.wrap( '<div class="whatso-picker-container"></div>' );
				container = el.parents( '.whatso-picker-container' );
				if ( ! container.find( '.whatso-clicker' ).length ) {
					container.append( '<span class="whatso-clicker">' + buttonText + '</span>' );
					container.append( '<div class="whatso-picker-preview"></div>' );
				}
				button = container.find( 'span.whatso-clicker' );
				preview = container.find( '.whatso-picker-preview' );
				
				el.css({
					paddingRight: Math.ceil( parseInt( button.outerWidth() ) ) + 3
				});
				
				container.off().on( 'click', '.whatso-close-preview', function () {
					el.val( '' );
					preview.html( '' );
				} );
				
				if ( '' !== el.val() ) {
					preview.addClass( 'show' ).html( '<span class="whatso-image-container"><img src="' + el.val() + '" /><span class="whatso-close-preview"></span></span>' );
				}
				
			});
		};
		
		handleMediaPicker();
		
		/* When the button is clicked, open the media library */
		DOM.body.on( 'click', '.whatso-picker-container .whatso-clicker', function ( e ) {
			e.preventDefault();
			
			var el = $( this ),
				inputField = el.prev( 'input' ),
				preview = el.next( '.whatso-picker-preview' ),
				insertImage = wp.media.controller.Library.extend({
					defaults :  _.defaults({
							id: 'insert-image',
							title: 'Insert Image Url',
							allowLocalEdits: true,
							displaySettings: true,
							displayUserSettings: true,
							multiple : true,
							type : 'image' /* audio, video, application/pdf, ... etc */
					  }, wp.media.controller.Library.prototype.defaults)
				});
			
			/* Setup media frame */
			var frame = wp.media({
				button : { text : 'Select' },
				state : 'insert-image',
				states : [
					new insertImage()
				]
			});
			
			frame.on('select',function() {
				
				var state = frame.state('insert-image')
					, selection = state.get('selection')
					;
				
				if (!selection) {
					return;
				}
				var imgSrc = ''
					, attachmentIds = []
					, isImage = true;
					;
				selection.each(function(attachment) {
					var display = state.display(attachment).toJSON()
						, obj_attachment = attachment.toJSON()
						;
					
					display = wp.media.string.props(display, obj_attachment);
					imgSrc = display['src'] || display['linkUrl'];
					
					if ( ! display['src'] ) {
						isImage = false;
					}
					
					/* What is being returned? */
					attachmentIds.push(attachment.id);
				});
				
				inputField.val( imgSrc );
				
				/* 	If the selected file is an image, set the value into an <img> and 
					show the preview. Otherwise, hide the preview. */
				if ( isImage ) {
					preview.addClass('show').html( '<span class="whatso-image-container"><img src="' + imgSrc + '" /><span class="whatso-close-preview"></span></span>' );
				}
				else {
					preview.removeClass('show').html( '' );
				}
				
			});

			/* reset selection in popup, when open the popup */
			frame.on('open',function() {
				var selection = frame.state('insert-image').get('selection');
				
				/* remove all the selection first */
				selection.each(function(image) {
					var attachment = wp.media.attachment( image.attributes.id );
					attachment.fetch();
					selection.remove(attachment ? [attachment] : []);
				});				
			});

			/* now open the popup */
			frame.open();
			
		} );
		
		/*	Add new contact. */
		var template = DOM.body.find( 'template#account-item' ).html(),
			currentId = parseInt( DOM.body.find( '.whatso-account-item' ).length ),
			accountContainer = DOM.body.find( '.whatso-account-items' )
			;
		
		DOM.body.find( '.whatso-add-account' ).on( 'click', function (e) {
			e.preventDefault();
			accountContainer.append( template.replace( /#id#/g, ++currentId ) );
			//$( this ).parents( '.form-table' ).before( template.replace( /#id#/g, ++currentId ) );
			handleMediaPicker();
		} );
		
		/*	Remove contact. */
		DOM.body.on( 'click', '.whatso-remove-account', function (e) {
			e.preventDefault();
			$( this ).parents( '.whatso-account-item' ).remove();
		} );
		
	}());
	
	/**
	* Search posts to exclude
	**********************************************************************/
	(function () {
		
		DOM.body.find( '.whatso-search-posts input' ).on( 'keydown keyup focus blur', function ( e ) {
			
			var el = $( this )
				, parent = el.parents( 'td' )
				, searchContainer = parent.find( '.whatso-search-posts' )
				, searchList = searchContainer.find( 'ul' )
				, searchInput = searchContainer.find( 'input' )
				, nonce = searchInput.data( 'nonce' )
				, searching
				, xhr = false
				;
			
			/* Do nothing when Enter key is pressed */
			if( e.keyCode == 13 ) {
				e.preventDefault();
			}
			
			if ( e.type === 'focus' ) {
				searchList.addClass( 'whatso-show' );
				return true;
			}
			
			if ( e.type === 'blur' ) {
				searchList.removeClass( 'whatso-show' );
				return true;
			}
			
			if ( e.type === 'keyup' ) {
				
				clearTimeout( searching );
				if ( xhr ) {
					xhr.abort();
					searchContainer.removeClass( 'whatso-show-loader' );
				}
				
				searching = setTimeout( function () {
					var data = {
						action: 'whatso_search_posts',
						security: nonce,
						title: el.val()
					};
					
					if ( '' === data.title ) {
						searchList.removeClass( 'whatso-show' ).html( '' );
						return;
					}
					
					searchContainer.addClass( 'whatso-show-loader' );
					xhr = $.post( whatso_ajax_object.ajax_url, data, function( response ) {
						
						if ( 'no-result' !== response ) {
							searchList.addClass( 'whatso-show' ).html( response );
						}
						else {
							searchList.removeClass( 'whatso-show' ).html( '' );
						}
						
						searchContainer.removeClass( 'whatso-show-loader' );
						
					} );
					
				}, 250 );
				
			}
			
		} );
		
		DOM.body.find( '.whatso-search-posts ul' ).on( 'click', 'li', function () {
			
			var el = $( this )
				, inclusion = el.parents( 'td' ).find( '.whatso-inclusion' )
				, id = el.data( 'id' )
				, permalink = el.find( '.whatso-permalink' ).text()
				, title = el.find( '.whatso-title' ).text()
				, deleteLabel = inclusion.data( 'delete-label' )
				, arrayName = inclusion.is( '.whatso-included-posts' ) ? 'whatso_included' : 'whatso_excluded'
				;
			
			$(  '<li id="whatso-excluded-' + id + '">' +
					'<p class="whatso-title">' + title + '</p> ' +
					'<p class="whatso-permalink"><a href="' + permalink + '" target="_blank">' + permalink + '</a></p> ' +
					'<span class="dashicons dashicons-no"></span>' +
					'<input type="hidden" name="' + arrayName + '[]" value="' + id + '"/>' +
				'</li>' ).appendTo( inclusion );
			
		} );
		
		DOM.body.find( '.whatso-inclusion' ).on( 'click', '.dashicons', function () {
			$( this ).parent( 'li' ).remove();
		} );
		
	}());
	
	/**
	* Move an account up or down
	**********************************************************************/
	(function () {
		
		DOM.body.on( 'click', '.whatso-queue-buttons span', function () {
			
			var el = $( this ),
				direction = el.is( '.whatso-move-up' ) ? 'up' : 'down',
				table = el.parents( 'table' )
				;
			
			if ( el.is( '.whatso-move-up' ) ) {
				table.insertBefore( table.prev( 'table' ) );
			}
			else {
				table.insertAfter( table.next( 'table' ) );
			}
			
		} );
		
	}());
	
	/**
	* Executed on 'product' post type.
	**********************************************************************/
	(function () {
		
		var cbRemoveButton = DOM.body.find( 'input#whatso_remove_button' ),
			settingsTable = DOM.body.find( '#whatso-custom-wc-button-settings' ),
			toggleSettings = function () {
				if ( cbRemoveButton.is( ':checked' ) ) {
					settingsTable.hide();
				}
				else {
					settingsTable.show();
				}
			}
			;
			
		toggleSettings();
		
		cbRemoveButton.change(function () {
			toggleSettings();
		});
		
	}());
	
	/**
	* Search accounts
	**********************************************************************/
	(function () {
		
		var accountResult = DOM.body.find( '.whatso-account-result .whatso-account-list' );
		
		DOM.body.find( '.whatso-account-search input' ).on( 'keydown keyup focus blur', function ( e ) {
			
			var el = $( this )
				, searchContainer = el.parents( '.whatso-account-search' )
				, searchList = searchContainer.find( '.whatso-account-list' )
				, searchInput = searchContainer.find( 'input' )
				, nonce = searchInput.data( 'nonce' )
				, searching
				, xhr = false
				;
			
			/* Do nothing when Enter key is pressed */
			if( e.keyCode == 13 ) {
				e.preventDefault();
			}
			
			if ( e.type === 'focus' ) {
				searchList.addClass( 'whatso-show' );
				return true;
			}
			
			if ( e.type === 'blur' ) {
				setTimeout( function () {
					searchList.removeClass( 'whatso-show' );
				}, 150 );
				return true;
			}
			
			if ( e.type === 'keyup' ) {
				
				clearTimeout( searching );
				if ( xhr ) {
					xhr.abort();
					searchContainer.removeClass( 'whatso-show-loader' );
				}
				
				searching = setTimeout( function () {
					var data = {
						action: 'whatso_search_accounts',
						security: nonce,
						title: el.val()
					};
					
					if ( '' === data.title ) {
						searchList.removeClass( 'whatso-show' ).html( '' );
						return;
					}
					
					searchContainer.addClass( 'whatso-show-loader' );
					xhr = $.post( whatso_ajax_object.ajax_url, data, function( response ) {
						
						if ( 'no-result' !== response ) {
							searchList.addClass( 'whatso-show' ).html( response );
						}
						else {
							searchList.removeClass( 'whatso-show' ).html( '' );
						}
						
						searchContainer.removeClass( 'whatso-show-loader' );
						
					} );
					
				}, 250 );
				
			}
			
		} );
		
		DOM.body.find( '.whatso-account-search' ).on( 'click', '.whatso-item', function () {
			
			var el = $( this )
				, id = el.data( 'id' )
				, nameTitle = el.data( 'name-title' )
				, removeLabel = el.data( 'remove-label' )
				, imageURL = el.find( '.whatso-avatar img' ).attr( 'src' )
				, title = el.find( '.whatso-title' ).text()
				;
			
			$( '<div class="whatso-item whatso-clearfix" id="whatso-item-' + id + '">' +
					'<div class="whatso-avatar"><img src="' + imageURL + '" alt=""/></div>' +
					'<div class="whatso-info whatso-clearfix">' +
						'<a href="post.php?post=' + id + '&action=edit" target="_blank" class="whatso-title">' + title + '</a>' +
						'<div class="whatso-meta">' +
							nameTitle + ' <br/>' +
							'<span class="whatso-remove-account">' + removeLabel + '</span>' +
						'</div>' +
					'</div>' +
					'<div class="whatso-updown"><span class="whatso-up dashicons dashicons-arrow-up-alt2"></span><span class="whatso-down dashicons dashicons-arrow-down-alt2"></span></div>' +
					'<input type="hidden" name="whatso_selected_account[]" value="' + id + '"/>' +
				'</div>' ).appendTo( accountResult );
			
		} );
		
		accountResult.on( 'click', '.whatso-updown span', function () {
			
			var el = $( this )
				, item = el.parents( '.whatso-item' )
				;
			
			if ( el.is( '.whatso-up' ) ) {
				item.insertBefore( item.prev( '.whatso-item' ) );
			}
			else {
				item.insertAfter( item.next( '.whatso-item' ) );
			}
			
		});
		
		accountResult.on( 'click', '.whatso-remove-account', function () {
			$( this ).parents( '.whatso-item' ).remove();
		} );
		
	}());
	
	/**
	* 
	**********************************************************************/
	(function () {
		
		
		
	}());
	
});
