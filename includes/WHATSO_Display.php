<?php

/**
 * This class is loaded on the front-end since its main job is 
 * to display the WhatsApp box.
 */    

class WHATSO_Display
{
    

    public function __construct()
    {
        
        

        add_action('wp_ajax_whatso_display_widget', array( $this, 'displayWidget' ));
        add_action('wp_ajax_nopriv_whatso_display_widget', array( $this, 'displayWidget' ));
       
        add_action('wp_ajax_whatso_display_buttons', array( $this, 'displayButtons' ));
        add_action('wp_ajax_nopriv_whatso_display_buttons', array( $this, 'displayButtons' ));
        if (is_admin() ) {
            return;
        }
        
        add_action('wp_footer', array( $this, 'outputHTML' ));
        add_action('wp_enqueue_scripts', array( $this, 'wpEnqueueScripts' ), 1);


    }
    
    public static function isBetweenTime( $from, $till, $input )
    {
        $f = DateTime::createFromFormat('!H:i', $from);
        $t = DateTime::createFromFormat('!H:i', $till);
        $i = DateTime::createFromFormat('!H:i', $input);
        if ($f > $t ) {
            $t->modify('+1 day');
        }
        return ( $f <= $i && $i <= $t ) || ( $f <= $i->modify('+1 day') && $i <= $t );
    }
    
    public function displayButtons()
    {
        
        $ids = isset($_POST['ids']) ? explode(',', sanitize_text_field(wp_unslash($_POST['ids']))) : array();
        $page_title = isset($_POST['page-title']) ? sanitize_text_field(wp_unslash($_POST['page-title'])) : '';
        $page_url = isset($_POST['page-url']) ?  sanitize_text_field(wp_unslash($_POST['page-url'])) : '';
        $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';
        
        $account = get_posts(
            array(
            'posts_per_page' => -1,
            'post__in' => $ids,
            'post_type' => 'whatso_accounts'
            ) 
        );
        
        if (count($account) < 1 ) {
            echo esc_html('none');
            wp_die();
        }
        
        $pinned_acc = array();
        $online_acc = array();
        $offline_acc = array();
        $item = array();
        $i = 0;
        foreach ( $account as $post ) {
            setup_postdata($post);
            
            $classes = array( 'whatso-button', 'whatso-account', 'whatso-clearfix' );
            
            $from = sanitize_text_field(get_post_meta($post->ID, 'whatso_hour_start', true)) . ':' . sanitize_text_field(get_post_meta($post->ID, 'whatso_minute_start', true));
            $till = sanitize_text_field(get_post_meta($post->ID, 'whatso_hour_end', true)) . ':' . sanitize_text_field(get_post_meta($post->ID, 'whatso_minute_end', true));
            
            $offline_text = sanitize_text_field(get_post_meta($post->ID, 'whatso_offline_text', true));
            
            $current_day = strtolower(gmdate('l'));
            $availability = json_decode(get_post_meta($post->ID, 'whatso_availability', true), true);
            $availability = is_array($availability) ? $availability : array();
            
            /* Time and day availability */
            
            if (isset($availability[ $current_day ])  
                && isset($availability[ $current_day ][ 'hour_start' ])  
                && isset($availability[ $current_day ][ 'minute_start' ])  
                && isset($availability[ $current_day ][ 'hour_end' ])  
                && isset($availability[ $current_day ][ 'minute_end' ])
            ) {
                
                $from = $availability[ $current_day ][ 'hour_start' ] . ':' . $availability[ $current_day ][ 'minute_start' ];
                $till = $availability[ $current_day ][ 'hour_end' ] . ':' . $availability[ $current_day ][ 'minute_end' ];
                
                /* Ignore if time is unavailable */
                if (! self::isBetweenTime($from, $till, current_time('H:i')) ) {
                    if ('' === trim($offline_text) ) {
                        continue;
                    }
                    else {
                        $classes[] = 'whatso-offline';
                    }
                }
                
            }
            else {
                continue;
            }
            
            $item = '';
            
            $number = preg_replace('/[^0-9]/', '', get_post_meta($post->ID, 'whatso_number', true));
            $name = sanitize_text_field(get_post_meta($post->ID, 'whatso_name', true));
            $title = sanitize_text_field(get_post_meta($post->ID, 'whatso_title', true));
            $title = '' !== $title ? ' &nbsp;/&nbsp; ' . $title : '';
            $button_label = sanitize_text_field(get_post_meta($post->ID, 'whatso_button_label', true));
            $button_label = '' !== $button_label ? $button_label : WHATSO_Utils::getSetting('button_label', esc_html__('Need help? Chat via WhatsApp', 'whatso'));
            $predefined_text = sanitize_text_field(get_post_meta($post->ID, 'whatso_predefined_text', true));
            $predefined_text = str_ireplace('[whatso_page_title]', $page_title, $predefined_text);
            $predefined_text = str_ireplace('[whatso_page_url]', $page_url, $predefined_text);
            $predefined_text = str_ireplace("\r\n", rawurlencode("\r\n"), $predefined_text);
            
            $post_title = sanitize_text_field(get_the_title($post));
            
        
            if (has_post_thumbnail($post) ) {
                $avatar = '<img src="' . get_the_post_thumbnail_url($post) . '" alt="' . $name . '"/>';
            }
            else {
                $avatar = '<svg class="WhatsApp" width="40px" height="40px" viewBox="0 0 92 92"><use xlink:href="#whatso-logo"></svg>';
            }
            
            
            
            if ('on' === get_post_meta($post->ID, 'whatso_hide_on_large_screen', true) ) {
                $classes[] = 'whatso-hide-on-large-screen';
            }
            
            if ('on' === get_post_meta($post->ID, 'whatso_hide_on_small_screen', true) ) {
                $classes[] = 'whatso-hide-on-small-screen';
            }
            
            if ('round' === strtolower(WHATSO_Utils::getSetting('button_style')) ) {
                $classes['whatso-round'] = 'whatso-round';
            }
            
            $href = 'https://api.whatsapp.com/send?phone=' . $number . ( '' !== $predefined_text ? '&text=' . $predefined_text : '' );
            if (strpos(get_post_meta($post->ID, 'whatso_number', true), 'chat.whatsapp.com') !== false ) {
                $number = '';
                $href = sanitize_text_field(get_post_meta($post->ID, 'whatso_number', true));
                $classes[] = 'whatso-group';
            }
            
            $background_color = sanitize_text_field(get_post_meta($post->ID, 'whatso_background_color', true));
            $background_color_on_hover = sanitize_text_field(get_post_meta($post->ID, 'whatso_background_color_on_hover', true));
            $text_color = sanitize_text_field(get_post_meta($post->ID, 'whatso_text_color', true));
            $text_color_on_hover = sanitize_text_field(get_post_meta($post->ID, 'whatso_text_color_on_hover', true));
            
            if ('' !== trim($background_color) 
                || '' !== trim($background_color_on_hover)
                || '' !== trim($text_color) 
                || '' !== trim($text_color_on_hover)
            ) {
                
                $item.= '<style type="text/css" scoped>';
                $item.= '#whatso-button-' . $post->ID . ' > * {';
                $item.= ( '' !== trim($background_color) ) ? 'background-color:' . esc_html($background_color) . ' !important;' : '';
                $item.= ( '' !== trim($text_color) ) ? 'color:' . esc_html($text_color) . ' !important;' : '';
                $item.= '}';
                $item.= '#whatso-button-' . $post->ID . ' > *:hover {';
                $item.= ( '' !== trim($background_color_on_hover) ) ? 'background-color:' . esc_html($background_color_on_hover) . ' !important;' : '';
                $item.= ( '' !== trim($text_color_on_hover) ) ? 'color:' . esc_html($text_color_on_hover) . ' !important;' : '';
                $item.= '}';
                $item.= '</style>';
                
            }
            
            if (in_array('whatso-offline', $classes) ) {
                $item.= '<span class="' . implode(' ', $classes) . '" >';
                $item.= '<span class="whatso-avatar">' . $avatar . '</span><span class="whatso-text"><span class="whatso-profile">' . esc_html($name) . esc_html($title) . '</span><span class="whatso-copy">' . esc_html($button_label) . '</span><span class="whatso-offline-text">' . esc_html($offline_text) . '</span></span>';
                $item.= '</span>';
            }
            else {
                $item.= '<a href="' .esc_html($href) . '" class="' . implode(' ', $classes) . '" data-number="' .esc_html($number) . '" data-auto-text="' . esc_html($predefined_text) . '" data-ga-label="' . esc_html($post_title) . '" target="_blank">';
                $item.= '<span class="whatso-avatar">' .$avatar . '</span><span class="whatso-text"><span class="whatso-profile">' .esc_html($name) . esc_html($title) . '</span><span class="whatso-copy">' . esc_html($button_label) . '</span></span>';
                $item.= '</a>';
            }
            
            
            if (in_array('whatso-offline', $classes) ) {
                $offline_acc[ $i ] = array(
                'id' => $post->ID,
                'content' => $item
                );
            }
            else {
                $is_pinned = sanitize_text_field(get_post_meta($post->ID, 'whatso_pin_account', true)) == 'on' ? true : false;
                if ($is_pinned ) {
                    $pinned_acc[ $i ] = array(
                    'id' => $post->ID,
                    'content' => $item
                    );
                }
                else {
                    $online_acc[ $i ] = array(
                    'id' => $post->ID,
                    'content' => $item
                    );
                }
            }
            
            
            $i++;
                        
        }
        
        wp_reset_postdata();
        
        ksort($pinned_acc);
        ksort($online_acc);
        ksort($offline_acc);
        
        if ('woocommerce_button' === $type && 'on' === WHATSO_Utils::getSetting('wc_randomize_accounts_order') ) {
            shuffle($online_acc);
        }
        
        $html = array_merge($pinned_acc, $online_acc, $offline_acc);
        
        /* Limit the items shown if limit parameter is set. */            
        $total_accounts_shown = ( int ) WHATSO_Utils::getSetting('wc_total_accounts_shown');
        if ('woocommerce_button' === $type && $total_accounts_shown > 0 ) {
            $i = 1;
            foreach ( $html as $k => $v ) {
                if ($i > $total_accounts_shown ) {
                    unset($html[ $k ]);
                }
                else {
                    $html[ $k ] = $v;
                }
                $i++;
            }
        }
        
        echo wp_json_encode($html,JSON_UNESCAPED_SLASHES);
        
        wp_die();
        
    }
    
    public function displayWidget()
    {
        
        $idField = isset($_POST['ids']) ? sanitize_text_field(wp_unslash($_POST['ids'])) : '';
        $titleField = isset($_POST['page-title']) ? sanitize_text_field(wp_unslash($_POST['page-title'])) : '';
        $urlField = isset($_POST['page-url']) ? sanitize_text_field(wp_unslash($_POST['page-url'])) : '';
        
        $ids = isset($idField) ? explode('-', $idField) : array();
        $page_title = isset($titleField) ? $titleField : '';
        $page_url = isset($urlField) ? $urlField : '';
        
        
        if (count($ids) < 1 ) {
            wp_die();
        }
        
        $the_accounts = get_posts(
            array(
            'posts_per_page' => -1,
            'post__in' => $ids,
            'post_type' => 'whatso_accounts',
            'orderby' => 'post__in'
            ) 
        );
        
        $pinned_acc = array();
        $online_acc = array();
        $offline_acc = array();
        $i = 0;
        $someone_is_online = false;
        foreach ( $the_accounts as $post ) {
            setup_postdata($post);
            
            $classes = array( 'whatso-account', 'whatso-clearfix' );
            
            $from = sanitize_text_field(get_post_meta($post->ID, 'whatso_hour_start', true)) . ':' . sanitize_text_field(get_post_meta($post->ID, 'whatso_minute_start', true));
            $till = sanitize_text_field(get_post_meta($post->ID, 'whatso_hour_end', true)) . ':' . sanitize_text_field(get_post_meta($post->ID, 'whatso_minute_end', true));
            
            $offline_text = sanitize_text_field(get_post_meta($post->ID, 'whatso_offline_text', true));
            
            $current_day = strtolower(gmdate('l'));
            $availability = json_decode(get_post_meta($post->ID, 'whatso_availability', true), true);
            $availability = is_array($availability) ? $availability : array();
            
            /* Time and day availability */
            
            if (isset($availability[ $current_day ])  
                && isset($availability[ $current_day ][ 'hour_start' ])  
                && isset($availability[ $current_day ][ 'minute_start' ])  
                && isset($availability[ $current_day ][ 'hour_end' ])  
                && isset($availability[ $current_day ][ 'minute_end' ])
            ) {
                
                $from = $availability[ $current_day ][ 'hour_start' ] . ':' . $availability[ $current_day ][ 'minute_start' ];
                $till = $availability[ $current_day ][ 'hour_end' ] . ':' . $availability[ $current_day ][ 'minute_end' ];
                
                /* Ignore if time is unavailable */
                if (! self::isBetweenTime($from, $till, current_time('H:i')) ) {
                    if ('' === trim($offline_text) ) {
                           continue;
                    }
                    else {
                        $classes[] = 'whatso-offline';
                    }
                }
                
            }
            else {
                continue;
            }
            
            $number = preg_replace('/[^0-9]/', '', get_post_meta($post->ID, 'whatso_number', true));
            $name = sanitize_text_field(get_post_meta($post->ID, 'whatso_name', true));
            $title = sanitize_text_field(get_post_meta($post->ID, 'whatso_title', true));
            $predefined_text = sanitize_text_field(get_post_meta($post->ID, 'whatso_predefined_text', true));
            $predefined_text = str_ireplace('[whatso_page_title]', $page_title, $predefined_text);
            $predefined_text = str_ireplace('[whatso_page_url]', $page_url, $predefined_text);
            $predefined_text = str_ireplace("\r\n", rawurlencode("\r\n"), $predefined_text);
            
            $post_title = get_the_title($post);
            
            
            /* Filter by WPML languages */
            if (isset($_POST['current-language']) ) {
                $current_language = sanitize_text_field(wp_unslash($_POST['current-language']));
                $compatible_languages = json_decode(get_post_meta($post->ID, 'whatso_target_languages', true), true);
                $compatible_languages = is_array($compatible_languages) ? $compatible_languages : array();
                if (count($compatible_languages) > 0 && ! in_array(strtolower($current_language), $compatible_languages) ) {
                    continue;
                }
            }
            
            $avatar_url = '';
            if (has_post_thumbnail($post) ) {
                $avatar_url = get_the_post_thumbnail_url($post);
            }
            else {
                $classes[] = 'whatso-no-image';
            }
            
            if ('on' === get_post_meta($post->ID, 'whatso_hide_on_large_screen', true) ) {
                $classes[] = 'whatso-hide-on-large-screen';
            }
            
            if ('on' === get_post_meta($post->ID, 'whatso_hide_on_small_screen', true) ) {
                $classes[] = 'whatso-hide-on-small-screen';
            }
            
            $href = 'https://api.whatsapp.com/send?phone=' . $number . ( '' !== $predefined_text ? '&text=' . $predefined_text : '' );
            if (strpos(get_post_meta($post->ID, 'whatso_number', true), 'chat.whatsapp.com') !== false ) {
                $number = '';
                $href = sanitize_text_field(get_post_meta($post->ID, 'whatso_number', true));
                $classes[] = 'whatso-group';
            }
            
            if (in_array('whatso-offline', $classes) ) {
                $offline_acc[ ++$i ] = '	<span class="' . implode(' ', $classes) . '">
								<div class="whatso-face"><img src="' . esc_url($avatar_url) . '" onerror="this.style.display=\'none\'"></div>
								<div class="whatso-info">
									<span class="whatso-title">' . esc_html($title) . '</span>
									<span class="whatso-name">' . esc_html($name) . '</span>
									<span class="whatso-offline-text">' . esc_html($offline_text) . '</span>
								</div>
							</span>';
            }
            else {
                $is_pinned = sanitize_text_field(get_post_meta($post->ID, 'whatso_pin_account', true)) == 'on' ? true : false;
                if ($is_pinned ) {
                    $pinned_acc[ ++$i ] = '<a href="' . $href . '" data-number="' . $number . '" class="' . implode(' ', $classes) . '" data-auto-text="' . esc_attr($predefined_text) . '" data-ga-label="' . esc_attr($post_title) . '" target="_blank">
								<div class="whatso-face"><img src="' . esc_url($avatar_url) . '" onerror="this.style.display=\'none\'"></div>
								<div class="whatso-info">
									<span class="whatso-title">' . esc_html($title) . '</span>
									<span class="whatso-name">' . esc_html($name) . '</span>
								</div>
							</a>';
                }
                else {
                    $online_acc[ ++$i ] = '<a href="' . $href . '" data-number="' . $number . '" class="' . implode(' ', $classes) . '" data-auto-text="' . esc_attr($predefined_text) . '" data-ga-label="' . esc_attr($post_title) . '" target="_blank">
								<div class="whatso-face"><img src="' . esc_url($avatar_url) . '" onerror="this.style.display=\'none\'"></div>
								<div class="whatso-info">
									<span class="whatso-title">' . esc_html($title) . '</span>
									<span class="whatso-name">' . esc_html($name) . '</span>
								</div>
							</a>';
                }
                
                $someone_is_online = true;
            }
            
        }
        wp_reset_postdata();
        
        if (!empty($pinned_acc) || !empty($online_acc) || !empty($offline_acc)) {
            
            ksort($pinned_acc);
            ksort($online_acc);
            ksort($offline_acc);
            
            if ('on' === WHATSO_Utils::getSetting('randomize_accounts_order') ) {
                shuffle($online_acc);
            }
            
            $html = array_merge($pinned_acc, $online_acc, $offline_acc);
            
            /* Limit the items shown if limit parameter is set. */            
            $total_accounts_shown = ( int ) esc_html(WHATSO_Utils::getSetting('total_accounts_shown'));
            if ($total_accounts_shown > 0 ) {
                $i = 1;
                foreach ( $html as $k => $v ) {
                    if ($i > $total_accounts_shown ) {
                        $html[ $k ] = '';
                    }
                    else {
                        $html[ $k ] = $v;
                    }
                    $i++;
                }
            }
        
            if (isset($_POST['current-language']) ) {
                do_action('wpml_switch_language', sanitize_text_field(wp_unslash($_POST['current-language'])));
                
            }
            
            $toggle_text = esc_html(WHATSO_Utils::getSetting('toggle_text'));
            $description = wp_kses_post(WHATSO_Utils::getSetting('description'));
            
            if (has_filter('wpml_translate_single_string') ) {
                $toggle_text = apply_filters('wpml_translate_single_string', $toggle_text, 'WhatsApp Click to Chat', 'Toggle Text');
                $description = apply_filters('wpml_translate_single_string', $description, 'WhatsApp Click to Chat', 'Description');
            }
            
            $delay_time = filter_var(WHATSO_Utils::getSetting('delay_time'), FILTER_SANITIZE_NUMBER_INT);
            $inactivity_time = filter_var(WHATSO_Utils::getSetting('inactivity_time'), FILTER_SANITIZE_NUMBER_INT);
            $scroll_length = filter_var(WHATSO_Utils::getSetting('scroll_length'), FILTER_SANITIZE_NUMBER_INT);
            
            $classes = array( 'whatso-container' );
            if ('left' === esc_attr(WHATSO_Utils::getSetting('box_position')) ) {
                $classes[] = 'whatso-left-side';
            }
            
            
            if ('' === $toggle_text ) {
                $classes[] = 'circled-handler';
            }
            
            if ('on' === esc_attr(WHATSO_Utils::getSetting('toggle_round_on_desktop')) ) {
                $classes[] = 'whatso-round-toggle-on-desktop';
            }
            
            if ('on' === esc_attr(WHATSO_Utils::getSetting('toggle_round_on_mobile')) ) {
                $classes[] = 'whatso-round-toggle-on-mobile';
            }
            
            if ('on' === esc_attr(WHATSO_Utils::getSetting('toggle_center_on_mobile')) ) {
                $classes[] = 'whatso-mobile-center';
            }
            
            if ('on' === esc_attr(WHATSO_Utils::getSetting('disable_auto_display_on_small_screen')) ) {
                $classes[] = 'whatso-disable-auto-display-on-small-screen';
            }
            
            /* If we should disable auto-display when no one is online. */
            if (! $someone_is_online && 'on' === esc_attr(WHATSO_Utils::getSetting('disable_auto_display_when_no_one_online')) ) {
                $delay_time = 0;
                $inactivity_time = 0;
                $scroll_length = 0;
            }
            
            /* GDPR HTML */
            $gdpr_html = '';
            $consent_description = '' !== trim(WHATSO_Utils::getSetting('consent_description'))
            ? wpautop(trim(WHATSO_Utils::getSetting('consent_description')))
            : ''
            ;
            $consent_checkbox_text_label = '' !== trim(WHATSO_Utils::getSetting('consent_checkbox_text_label'))
            ? wpautop(trim(WHATSO_Utils::getSetting('consent_checkbox_text_label')))
            : ''
            ;
            
            if (has_filter('wpml_translate_single_string') ) {
                $consent_description = wpautop(apply_filters('wpml_translate_single_string', $consent_description, 'WhatsApp Click to Chat', 'Consent Description'));
                $consent_checkbox_text_label = wpautop(apply_filters('wpml_translate_single_string', $consent_checkbox_text_label, 'WhatsApp Click to Chat', 'Consent Checkbox Text Label'));
            }
            
            $consent_checkbox_text_label = '' !== $consent_checkbox_text_label
            ? '<div class="whatso-confirmation"><label><input type="checkbox" name="whatso-consent" id="whatso-consent" /></label><div>' . $consent_checkbox_text_label . '</div></div>'
            : ''
            ;
            
            if ('' !== $consent_description || '' !== $consent_checkbox_text_label ) {
                $gdpr_html = '<div class="whatso-gdpr">' . $consent_description . $consent_checkbox_text_label . '</div>';
            }
            
            echo '<div class="' . esc_html(implode(' ', $classes)) . '" data-delay-time="' .esc_html($delay_time) . '" data-inactive-time="' . esc_html($inactivity_time) . '" data-scroll-length="' . esc_html($scroll_length) . '">
					<div class="whatso-box">
						<div class="whatso-description">
							' . esc_attr($description) . '
						</div>
						<span class="whatso-close"></span>
						<div class="whatso-people">
							' . esc_html($gdpr_html) . wp_kses_post(implode('', $html)) . '
						</div>
					</div>
					<div class="whatso-toggle"><svg class="WhatsApp" width="20px" height="20px" viewBox="0 0 90 90"><use xlink:href="#whatso-logo"></svg> <span class="whatso-text">' . esc_html($toggle_text) . '</span></div>
					<div class="whatso-mobile-close"><span>' . esc_html(WHATSO_Utils::getSetting('mobile_close_button_text', esc_html__('Go back to page', 'whatso'))) . '</span></div>
				</div>';
            
        }
        
        wp_die();
    }
    
    public function outputHTML()
    {
        
        echo '
			<span class="whatso-flag"></span>
			<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
				<symbol id="whatso-logo">
					<path id="WhatsApp" d="M90,43.841c0,24.213-19.779,43.841-44.182,43.841c-7.747,0-15.025-1.98-21.357-5.455L0,90l7.975-23.522   c-4.023-6.606-6.34-14.354-6.34-22.637C1.635,19.628,21.416,0,45.818,0C70.223,0,90,19.628,90,43.841z M45.818,6.982   c-20.484,0-37.146,16.535-37.146,36.859c0,8.065,2.629,15.534,7.076,21.61L11.107,79.14l14.275-4.537   c5.865,3.851,12.891,6.097,20.437,6.097c20.481,0,37.146-16.533,37.146-36.857S66.301,6.982,45.818,6.982z M68.129,53.938   c-0.273-0.447-0.994-0.717-2.076-1.254c-1.084-0.537-6.41-3.138-7.4-3.495c-0.993-0.358-1.717-0.538-2.438,0.537   c-0.721,1.076-2.797,3.495-3.43,4.212c-0.632,0.719-1.263,0.809-2.347,0.271c-1.082-0.537-4.571-1.673-8.708-5.333   c-3.219-2.848-5.393-6.364-6.025-7.441c-0.631-1.075-0.066-1.656,0.475-2.191c0.488-0.482,1.084-1.255,1.625-1.882   c0.543-0.628,0.723-1.075,1.082-1.793c0.363-0.717,0.182-1.344-0.09-1.883c-0.27-0.537-2.438-5.825-3.34-7.977   c-0.902-2.15-1.803-1.792-2.436-1.792c-0.631,0-1.354-0.09-2.076-0.09c-0.722,0-1.896,0.269-2.889,1.344   c-0.992,1.076-3.789,3.676-3.789,8.963c0,5.288,3.879,10.397,4.422,11.113c0.541,0.716,7.49,11.92,18.5,16.223   C58.2,65.771,58.2,64.336,60.186,64.156c1.984-0.179,6.406-2.599,7.312-5.107C68.398,56.537,68.398,54.386,68.129,53.938z"/>
				</symbol>
			</svg>
			';
        
        global $post;
        
        $current_post_type = get_post_type();
        $current_post_id = get_the_ID();
        $http_host = isset($_POST['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_POST['HTTP_HOST'])) : '';
        $request_uri = isset($_POST['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_POST['REQUEST_URI'])) : '';
        $current_url = $http_host . $request_uri;
        
        $cache_time = (int) json_decode(WHATSO_Utils::getSetting('cache_time'), true);

        $selected_accounts = json_decode(WHATSO_Utils::getSetting('selected_accounts_for_widget', '[]'), true);
        $selected_accounts = count($selected_accounts) < 1? array( 0 ) : $selected_accounts;
        $the_accounts = get_posts(
            array(
            'posts_per_page' => -1,
            'post__in' => $selected_accounts,
            'post_type' => 'whatso_accounts',
            'orderby' => 'post__in'
            ) 
        );
        
        $displayedIds = array();
        
        foreach ( $the_accounts as $post ) {
            setup_postdata($post);
            
            $_target = json_decode(get_post_meta($post->ID, 'whatso_target', true));
            $target = is_array($_target) ? $_target : array();
            
            $_included_ids = json_decode(get_post_meta($post->ID, 'whatso_included_ids', true), true);
            $included_ids = is_array($_included_ids) ? $_included_ids : array();
            
            $_excluded_ids = json_decode(get_post_meta($post->ID, 'whatso_excluded_ids', true), true);
            $excluded_ids = is_array($_excluded_ids) ? $_excluded_ids : array();
            
            /* Page targeting. */
            
            /* Included Posts & URLs */
            $is_included = false;
            foreach ( $included_ids as $v ) {
                if (filter_var($v, FILTER_VALIDATE_URL) !== false ) {
                    $parsed = wp_parse_url($v);
                    $_current_url = $parsed['scheme'] . '://' . $current_url;
                    if ($v == $_current_url ) {
                        $is_included = true;
                        break;
                    }
                }
                if (! filter_var($v, FILTER_VALIDATE_URL) && is_singular() && $current_post_id == $v ) {
                    $is_included = true;
                    break;
                }
            }
            
            if ($is_included ) {
                $displayedIds[] = $post->ID;
                continue;
            }
            
            
            /* Excluded Posts & URLs */
            $is_excluded = false;
            foreach ( $excluded_ids as $v ) {
                if (filter_var($v, FILTER_VALIDATE_URL) !== false && $v == $current_url ) {
                    $parsed = wp_parse_url($v);
                    $_current_url = $parsed['scheme'] . '://' . $current_url;
                    if ($v == $_current_url ) {
                        $is_excluded = true;
                        break;
                    }
                }
                if (! filter_var($v, FILTER_VALIDATE_URL) && is_singular() && $current_post_id == $v ) {
                    $is_excluded = true;
                    break;
                }
            }
            if ($is_excluded ) {
                continue;
            }
            
            /* Default homepage */
            if (( is_front_page() && is_home() ) && ! in_array('home', $target) ) {
                continue;
            }
            
            /* Static homepage */
            if (is_front_page() && ! in_array('home', $target) ) {
                continue;
            }
            
            /* Blog page */
            if (is_home() && ! in_array('blog', $target)) {
                continue;
            }
            
            if (( is_search() || is_archive() ) && ! in_array('archive', $target) ) {
                continue;
            }
            
            if (! ( is_front_page() && is_home() ) && ! is_front_page() && is_singular('page') && ! in_array('page', $target) ) {
                continue;
            }
            
            
            if (is_singular('post') && ! in_array('post', $target) ) {
                continue;
            }
            
            $existing_post_types = get_post_types(sanitize_text_field(array( '_builtin' => false )));
            if (in_array($current_post_type, $existing_post_types) && ! in_array($current_post_type, $target) ) {
                
                continue;
                
            }
            
            $displayedIds[] = $post->ID;
            
        }
        wp_reset_postdata();
        
        /* Get current WPML lang and attach the ids to show */
        
        $ids = implode('-', $displayedIds);
        $current_lang = apply_filters('wpml_current_language', null);
        echo '<span id="whatso-config" data-current-language="' . esc_attr($current_lang) . '" data-ids="' . esc_attr($ids) . '" data-page-title="' . esc_attr(get_the_title()) . '" data-page-url="' .esc_url(get_permalink()) . '" data-cache-time="' .esc_attr($cache_time) . '"></span>';
    
        
    }
    
    public function wpEnqueueScripts()
    {
        
        $plugin_data = get_file_data(WHATSO_PLUGIN_BOOTSTRAP_FILE, array( 'version' ));
        $plugin_version = isset($plugin_data[0]) ? $plugin_data[0] : false;
        wp_enqueue_style('whatso-public', WHATSO_PLUGIN_URL . 'assets/css/public.css', array(), $plugin_version);
        $css_file = wp_upload_dir(null, true, false)['basedir'].'/whatso/auto-generated-whatso.css';
        if (file_exists($css_file) ) {
            $last_modified = filemtime($css_file);
            wp_enqueue_style('whatso-generated', wp_upload_dir(null, true, false)['baseurl'].'/whatso/auto-generated-whatso.css', array(), $last_modified);
        }
        
        wp_enqueue_script('whatso-public', WHATSO_PLUGIN_URL . 'assets/js/public.js', array( 'jquery' ), $plugin_version, true);
        wp_localize_script('whatso-public', 'whatso_ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ));
    }
    

}
