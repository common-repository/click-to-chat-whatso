<?php

/**
 * WhatsApp ajax
 */
class WHATSO_Ajax
{
    
    /**
     * Initialize constructor
     */
    public function __construct()
    {
        
        add_action('wp_ajax_whatso_search_posts', array( $this, 'searchPost' ));
        add_action('wp_ajax_whatso_search_accounts', array( $this, 'searchAccounts' ));
        
    }
    
    /**
     * Function for post search
     */
    public function searchPost()
    {
        
        check_ajax_referer('whatso-search-nonce', 'security');
        $title = '';
        if (isset($_POST['title']) ) {
            $title = sanitize_text_field(wp_unslash($_POST['title']));    
        }
        
        $html = '';
        
        if (filter_var($_POST['title'], FILTER_VALIDATE_URL) !== false ) {
            $the_url = '';
            if (isset($_POST['title']) ) {
                $the_url = sanitize_text_field(wp_unslash($_POST['title']));    
            }

            $html.= '<li data-id="' . $the_url . '">
					<span class="whatso-title">' . $the_url . '</span>
				</li>';
        }
        else {
            global $post;
            $args = array(
            'posts_per_page' => 50,
            's' => $title,
            'post_type' => 'any'
            );
            
            $result = get_posts($args);
            
            foreach ( $result as $post ) {
                setup_postdata(sanitize_post($post));
                
                $post_title = '' !== get_the_title() ? sanitize_text_field(get_the_title()) : sprintf(esc_html__('[No title with ID: %s]', 'whatso'), sanitize_text_field(get_the_ID()));
                $html.= '<li data-id="' . get_the_ID() . '">
					<span class="whatso-title">' . esc_html($post_title) . '</span>
					<span class="whatso-permalink">' .  esc_url(get_the_permalink()) . '</span>
				</li>';
            }
            wp_reset_postdata();
        }
        
        if ('' === $html ) {
            $html.= '<li data-id="">' . esc_html__('No Result', 'whatso') . '</li>';
        }
        echo wp_kses_post($html);
        wp_die();
    }
    
    /**
     * Function for search accounts
     */
    public function searchAccounts(  )
    {
        
        check_ajax_referer('whatso-search-nonce', 'security');
        $title = '';
        if (isset($_POST['title']) ) {
            $title = sanitize_text_field(wp_unslash($_POST['title']));    
        }
        
        global $post;
        $args = array(
        'posts_per_page' => 50,
        's' => $title,
        'post_type' => 'whatso_accounts'
        );
                
        $result = get_posts($args);
        $html = '';
        
        foreach ( $result as $post ) {
            setup_postdata($post);
            
            $name = sanitize_text_field(get_post_meta($post->ID, 'whatso_name', true));
            $account_title = sanitize_text_field(get_post_meta($post->ID, 'whatso_title', true));
            $avatar = get_the_post_thumbnail_url(sanitize_text_field($post->ID))
            ? get_the_post_thumbnail_url(sanitize_text_field($post->ID))
            : WHATSO_PLUGIN_URL . 'assets/images/logo-green-small.png';
            
                
            
            $post_title = '' !== get_the_title() ? esc_html(get_the_title()) : sprintf(esc_html__('[No title with ID: %s]', 'whatso'), sanitize_text_field(get_the_ID()));
            
            $html.= '<div class="whatso-item whatso-clearfix" data-id="' . sanitize_text_field(get_the_ID()) . '" data-name-title="' . esc_attr($name . ' / ' . $account_title) . '" data-remove-label="' . esc_attr__('Remove', 'whatso') . '">
						<div class="whatso-avatar"><img src="' . $avatar . '" alt=""/></div>
						<div class="whatso-info whatso-clearfix">
							<div class="whatso-title">' . esc_html($post_title) . '</div>
							<div class="whatso-meta">
								' .esc_html($name) . ' / ' .esc_html($account_title). '
							</div>
						</div>
					</div>';
        }
        wp_reset_postdata();
        if ('' === $html ) {
            $html.= '<div class="whatso-item whatso-clearfix">' . esc_html__('No Result', 'whatso') . '</div>';
        }
        echo esc_html($html);
        wp_die();
    }
}
?>
