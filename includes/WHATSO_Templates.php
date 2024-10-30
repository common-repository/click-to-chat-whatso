<?php

class WHATSO_Templates
{
    
    static public function displaySelectedAccounts( $category, $product_id = 0 )
    {
        
        ?>
        <div class="whatso-account-search">
            <div class="whatso-account-list"></div>
        </div>
        <div class="whatso-account-result">
            <h4><?php esc_html_e('Selected Accounts:', 'whatso'); ?></h4>
            <div class="whatso-account-list">    
        <?php
        global $wpdb;
        $selected_accounts = json_decode(WHATSO_Utils::getSetting($category, ''), true);
        if ('selected_accounts_for_product' === $category ) {
            $selected_accounts = json_decode(get_post_meta($product_id, 'whatso_selected_accounts', true));
            
        }
        $selected_accounts = is_array($selected_accounts) ? $selected_accounts : array();
        $selected_accounts = count($selected_accounts) < 1? array( 0 ) : $selected_accounts;
        global $post;
        $the_accounts = get_posts(
            array(
            'posts_per_page' => -1,
            'post_type' => 'whatso_accounts',
            'orderby' => 'post__in'
            ) 
        );

        foreach ( $the_accounts as $post ) {
            setup_postdata($post);
            $name = sanitize_text_field(get_post_meta($post->ID, 'whatso_name', true));
            $account_title = sanitize_text_field(get_post_meta($post->ID, 'whatso_title', true));
            $account_number = sanitize_text_field(get_post_meta($post->ID, 'whatso_number', true));
            $whatso_visibility= get_post_meta($post->ID, 'whatso_visibility', true);
            $account_id=  sanitize_text_field(get_post_meta($post->ID, 'post_id', true));
                
                
            if(in_array(get_the_ID(), $selected_accounts) ) {    
                $checked = 'checked';
            }    
            else
            {
                $checked = '';
                    
    
            }
                
            ?>
                
                    <div class="whatso-item whatso-clearfix" data-id="<?php echo esc_attr(get_the_ID()); ?>" data-name-title="<?php esc_attr($name . ' / ' . $account_title); ?>" >
                    <label><input type="checkbox" name="whatso_selected_account[]"  value="<?php echo esc_attr(get_the_ID()); ?>" <?php echo esc_html($checked); ?> > <?php echo esc_attr($name .' / '.$account_number); ?> </label>
                    </div>
            <?php	                    
        }
            
            wp_reset_postdata();
        ?>
        
            
        </div>
        </div>
        <?php
    }
    
}

?>
