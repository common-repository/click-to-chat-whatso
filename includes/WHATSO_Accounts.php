<?php


class   WHATSO_Accounts
{



    /**
     * Initialize constructor.
     */
    public function __construct()
    {
        add_action('init', array( $this, 'accountsPostType' ));
        if (! is_admin() ) {
            return;
        }

        add_filter('manage_whatso_accounts_posts_columns', array( $this, 'accountTabulationHeader' ));
        add_action('manage_whatso_accounts_posts_custom_column', array( $this, 'accountTabulationData' ), 10, 2);
        add_action('manage_edit-whatso_accounts_sortable_columns', array( $this, 'visibiltyWebsite' ));
        add_filter('manage_edit-whatso_accounts_sortable_columns', array( $this, 'accountTabulationSorting' ));
        add_action('add_meta_boxes', array( $this, 'addMetaBoxes' ));
        add_action('save_post', array( $this, 'saveMetaBoxes' ));
        //allow redirection, even if my theme starts to send output to the browser
        add_action('init', 'do_output_buffer');
        function do_output_buffer()
        {
            ob_start();
        }

    }
    /**
     * Create menu in wp-admin
     */


    public function accountsPostType()
    {
        $labels = array(
            ''               => _x('Whatso Accounts', 'post type general name', 'whatso'),
            'singular_name'      => _x('Whatso Account', 'post type singular name', 'whatso'),
            'menu_name'          => _x('Accounts', 'admin menu', 'whatso'),
            'name_admin_bar'     => _x('Account', 'add new on admin bar', 'whatso'),
            'add_new'            => _x('Add New', 'book', 'whatso'),
            'add_new_item'       => __('Add New WhatsApp Account', 'whatso'),
            'new_item'           => __('New Account', 'whatso'),
            'edit_item'          => __('Edit Account', 'whatso'),
            'view_item'          => __('View Account', 'whatso'),
            'all_items'          => __('Accounts', 'whatso'),
            'search_items'       => __('Search Accounts', 'whatso'),
            'parent_item_colon'  => __('Parent Accounts:', 'whatso'),
            'not_fonameund'          => __('No accounts found.', 'whatso'),
            'not_found_in_trash' => __('No accounts found in Trash.', 'whatso')
        );

        $args = array(
            'labels'              => $labels,
            'description'         => __('Whatso Accounts', 'whatso'),
            'public'              => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'query_var'           => false,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'hierarchical'          => true,
            'menu_position'       => 5,
            'supports'            => array( 'title', 'thumbnail' )
        );

        register_post_type('whatso_accounts', $args);

        add_action('admin_head', 'replace_default_featured_image_meta_box', 100);

        function replace_default_featured_image_meta_box()
        {
            remove_meta_box('postimagediv', 'whatso_accounts', 'side');
            add_meta_box('postimagediv', __('Upload photo of the user'), 'post_thumbnail_meta_box', 'whatso_accounts', 'side', 'high');
        }
        /**
         * Function for change title
         */
        function wpb_change_title_text( $title )
        {
            $screen = get_current_screen();
            if  ('whatso_accounts' == $screen->post_type ) {
                $title = 'Add Your Name (This will not be displayed on Plugin)';
            }
            return esc_html($title);
        }
        add_filter('enter_title_here', 'wpb_change_title_text');

        /**
 * message box
**/

    }


    public function accountTabulationHeader( $defaults )
    {
        unset($defaults['title']);
        unset($defaults['date']);
        $defaults['title']  = esc_html__('Account Title', 'whatso');
        $defaults['picture']  = esc_html__('Picture', 'whatso');
        $defaults['number']  = esc_html__('Number', 'whatso');
        $defaults['role']  = esc_html__('Role Title', 'whatso');
        $defaults['pinned']  = esc_html__('Pin Account', 'whatso');
        $defaults['visibilty']  = esc_html__('Visible On Website', 'whatso');
        return $defaults;
    }



    public function accountTabulationData( $column_name, $post_id)
    {

        $category='selected_accounts_for_widget';
        $selected_accounts = json_decode(WHATSO_Utils::getSetting($category, ''), true);

        if ($column_name == 'picture' && has_post_thumbnail($post_id) ) {

            echo '<img src="' . esc_url(get_the_post_thumbnail_url()) . '" style="max-width: 40px;"/>';

        }
        if ($column_name == 'number' ) {
            echo esc_attr(get_post_meta($post_id, 'whatso_number', true));
        }
        if ($column_name == 'role' ) {
            echo esc_attr(get_post_meta($post_id, 'whatso_title', true));
        }
        if ($column_name == 'pinned' ) {
            echo esc_attr(get_post_meta($post_id, 'whatso_pin_account', true)) === 'on' ? esc_html__('Yes', 'whatso') : esc_html__('No', 'whatso');
        }
        if ($column_name == 'visibilty' ) {
            if ((is_array($selected_accounts) && in_array(get_the_ID(), $selected_accounts)) || get_post_meta($post_id, 'whatso_visibilty', 1)) {

                echo wp_kses_post('<a href="admin.php?page=whatso_floating_widget"><font color="green">Yes</font></a>');
            }
            else
            {
                echo wp_kses_post('<a href="admin.php?page=whatso_floating_widget"><font color="red">No</font></a>');


            }
        }

    }
    public function visibiltyWebsite($post_id)
    {
        global $wpdb;
        $category='selected_accounts_for_widget';
        $selected_accounts = json_decode(WHATSO_Utils::getSetting($category, ''), true);
        $active_post_id    = $wpdb->get_results(" SELECT ID  FROM $wpdb->posts WHERE post_status = 'publish' &&  post_type='whatso_accounts' ");
        $active_array = json_decode(json_encode($active_post_id), true);
        $p_id=0;


        foreach($active_array as $arr2){

            foreach($arr2 as $id=>$p_id){

                $p_id  ;


                foreach($selected_accounts as $arr2){

                    $arr2;
                }


                if(!in_array($p_id, $selected_accounts) || !in_array(null, $selected_accounts)) {
                    $checked="checked";
                }
                else{

                    $checked;
                }

            }
        }
        if(isset($checked)) {
            echo wp_kses_post('<br><p style="text-align: center; border:2px #008000 ridge; width: 40%; margin: 0 auto;  padding: 3px; color: rgb(65, 175, 50); font-weight: bolder;
			 ">Please select accounts from <a href="admin.php?page=whatso_floating_widget">Display Settings</a> to display widget.</p></br>');
        }
        else
        {
            echo "";
        }



    }



    public function accountTabulationSorting( $columns )
    {
        $columns['number'] = 'number';
        $columns['time'] = 'time';
        return $columns;

    }

    public function addMetaBoxes()
    {

        $screen = get_current_screen();

        if('add' !== $screen->action ) {



            add_meta_box(
                'whatso-copy-shortcode',
                esc_html__('Shortcode for this account', 'whatso'),
                array( $this, 'copyShortcode' ),
                array( 'whatso_accounts' ),
                'side'
            );

        }

        add_meta_box(
            'whatso-account-information',
            esc_html__('Whatso Account Information', 'whatso'),
            array( $this, 'accountInformation' ),
            array( 'whatso_accounts' ),
            'normal'
        );
        add_meta_box(
            'whatso-page-targeting',
            esc_html__('Page Targeting', 'whatso'),
            array( $this, 'pageTargeting' ),
            array( 'whatso_accounts' ),
            'normal'
        );

        add_meta_box(
            'whatso-button-style',
            esc_html__('Button Style', 'whatso'),
            array( $this, 'buttonStyle' ),
            array( 'whatso_accounts' ),
            'normal'
        );


    }


    public function accountInformation( $post )
    {

        global $pagenow;
        $new = 'post-new.php' === $pagenow ? true : false;
        $number = sanitize_text_field(get_post_meta($post->ID, 'whatso_number', true));
        $name = sanitize_text_field(get_post_meta($post->ID, 'whatso_name', true));
        $title = sanitize_text_field(get_post_meta($post->ID, 'whatso_title', true));
        $predefined_text = sanitize_text_field(get_post_meta($post->ID, 'whatso_predefined_text', true));
        if (function_exists('sanitize_textarea_field') ) {
            $predefined_text = sanitize_textarea_field(get_post_meta($post->ID, 'whatso_predefined_text', true));
        }
        $button_label = sanitize_text_field(get_post_meta($post->ID, 'whatso_button_label', true));
        $hide_on_large_screen = ! $new ? sanitize_text_field(get_post_meta($post->ID, 'whatso_hide_on_large_screen', true)) : 'off';
        $hide_on_small_screen = ! $new ? sanitize_text_field(get_post_meta($post->ID, 'whatso_hide_on_small_screen', true)) : 'off';
        $pin_account = ! $new ? sanitize_text_field(get_post_meta($post->ID, 'whatso_pin_account', true)) : 'off';
        $offline_text = sanitize_text_field(get_post_meta($post->ID, 'whatso_offline_text', true));

        $availability = array(
            'sunday' => array(
                'label'        => esc_html__('Sunday', 'whatso'),
                'hour_start' => 0,
                'minute_start' => 0,
                'hour_end' => 23,
                'minute_end' => 59
            )
        ,
            'monday' => array(
                'label' => esc_html__('Monday', 'whatso'),
                'hour_start' => 0,
                'minute_start' => 0,
                'hour_end' => 23,
                'minute_end' => 59
            )
        ,
            'tuesday' => array(
                'label' => esc_html__('Tuesday', 'whatso'),
                'hour_start' => 0,
                'minute_start' => 0,
                'hour_end' => 23,
                'minute_end' => 59
            )
        ,
            'wednesday' => array(
                'label' => esc_html__('Wednesday', 'whatso'),
                'hour_start' => 0,
                'minute_start' => 0,
                'hour_end' => 23,
                'minute_end' => 59
            )
        ,
            'thursday' => array(
                'label' => esc_html__('Thursday', 'whatso'),
                'hour_start' => 0,
                'minute_start' => 0,
                'hour_end' => 23,
                'minute_end' => 59
            )
        ,
            'friday' => array(
                'label' => esc_html__('Friday', 'whatso'),
                'hour_start' => 0,
                'minute_start' => 0,
                'hour_end' => 23,
                'minute_end' => 59
            )
        ,
            'saturday' => array(
                'label' => esc_html__('Saturday', 'whatso'),
                'hour_start' => 0,
                'minute_start' => 0,
                'hour_end' => 23,
                'minute_end' => 59
            )
        );

        $existing_availability = json_decode(sanitize_text_field(get_post_meta($post->ID, 'whatso_availability', true)), true);
        $existing_availability = is_array($existing_availability) ? $existing_availability : array();
        foreach ( $existing_availability as $k => $v ) {
            if (isset($availability[ $k ]) 
                && isset($availability[ $k ][ 'hour_start' ]) 
                && isset($availability[ $k ][ 'minute_start' ]) 
                && isset($availability[ $k ][ 'hour_end' ]) 
                && isset($availability[ $k ][ 'minute_end' ])
            ) {

                $availability[ $k ][ 'hour_start' ] = $v[ 'hour_start' ];
                $availability[ $k ][ 'minute_start' ] = $v[ 'minute_start' ];
                $availability[ $k ][ 'hour_end' ] = $v[ 'hour_end' ];
                $availability[ $k ][ 'minute_end' ] = $v[ 'minute_end' ];

            }
        }

        ?>

        <table class="form-table" id="whatso-custom-wc-button-settings">
            <caption> Whatso Create Account</caption>
            <tbody>
            <tr>
                <th scope="row"><label for="whatso_number"><?php esc_html_e('Mobile Number with country code', 'whatso'); ?></label></th>
                <td>
                    <p>
                        <input type="text" class="widefat" id="whatso_number" name="whatso_number" placeholder="Mobile Number with country code Eg. +919876543210" value="<?php echo esc_attr($number); ?>" />

                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="whatso_name"><?php esc_html_e('Name', 'whatso'); ?></label></th>
                <td>
                    <input type="text" id="whatso_name" name="whatso_name" value="<?php echo esc_attr($name); ?>" placeholder="Type name of the person E.g. James Anderson" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="whatso_title"><?php esc_html_e('Title', 'whatso'); ?></label></th>
                <td>
                    <input type="text" id="whatso_title" name="whatso_title" value="<?php echo esc_attr($title); ?>" placeholder="Type Designation E.g. Customer Support Executive" class="widefat" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="whatso_predefined_text"><?php esc_html_e('Predefined Text', 'whatso'); ?></label></th>
                <td>
                    <textarea name="whatso_predefined_text" id="whatso_predefined_text" rows="3" placeholder="type a text here." class="widefat"><?php echo esc_textarea($predefined_text); ?></textarea>
                    <p class="description"><?php esc_html_e('Use [whatso_page_title] and [whatso_page_url] shortcodes to output the page\'s title and URL respectively. ', 'whatso'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="whatso_button_label"><?php esc_html_e('Button Label', 'whatso'); ?></label></th>
                <td>
                    <input type="text" id="whatso_button_label" name="whatso_button_label" value="<?php echo esc_attr($button_label); ?>" placeholder="<?php echo esc_attr(WHATSO_Utils::getSetting('button_label', esc_html__('Need help? Chat via WhatsApp', 'whatso'))); ?>" class="widefat" />
                    <p class="description"><?php esc_html_e('This text applies only on shortcode button. Leave empty to use the default label.', 'whatso'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="whatso_availability"><?php esc_html_e('Availability', 'whatso'); ?></label></th>
                <td>
                    <?php foreach ( $availability as $k => $v ) : ?>

                        <p>
                            <strong><?php echo esc_html($v['label']); ?></strong><br/>


                            <select name="whatso_availability[<?php echo esc_html($k); ?>][hour_start]">
                                <?php $this->displayAvailabilityOptions('hour', $v['hour_start']); ?>
                            </select> :
                            <select name="whatso_availability[<?php echo esc_html($k); ?>][minute_start]">
                                <?php $this->displayAvailabilityOptions('minute', $v['minute_start']); ?>
                            </select> <?php esc_html_e('to', 'whatso'); ?>
                            <select name="whatso_availability[<?php echo esc_html($k); ?>][hour_end]">
                                <?php $this->displayAvailabilityOptions('hour', $v['hour_end']); ?>
                            </select> :
                            <select name="whatso_availability[<?php echo esc_html($k); ?>][minute_end]">
                                <?php $this->displayAvailabilityOptions('minute', $v['minute_end']); ?>
                            </select>
                        </p><br/>

                    <?php endforeach; ?>

                    <?php if ('' === trim(get_option('timezone_string')) && '' === get_option('gmt_offset') ) : ?>

                        <p><a href="options-general.php"><?php esc_html_e('Please set your time zone first so we can have an accurate time availability.', 'whatso'); ?></a></p>

                    <?php else : ?>

                        <p class="description"><?php printf(esc_html__('Note that the timezone currently in use is %s', 'whatso'), '<a href="options-general.php#timezone_string" target="_blank">' . ( '' !== get_option('timezone_string') ? get_option('timezone_string') : esc_attr(get_option('gmt_offset')) ) . '</a>'); ?></p>

                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for=""><?php esc_html_e('Pin this account', 'whatso'); ?></label></th>
                <td>
                    <p><input type="checkbox" name="whatso_pin_account" value="on" id="whatso_pin_account" <?php checked('on', $pin_account); ?> /> <label for="whatso_pin_account"><?php esc_html_e('Yes, pin this account.', 'whatso'); ?></label></p>
                    <p class="description"><?php esc_html_e('If checked, this account will always be placed on top even when the list is randomized.', 'whatso'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for=""><?php esc_html_e('Display based on screen width', 'whatso'); ?></label></th>
                <td>
                    <p><input type="checkbox" name="whatso_hide_on_large_screen" value="on" id="whatso_hide_on_large_screen" <?php checked('on', $hide_on_large_screen); ?> /> <label for="whatso_hide_on_large_screen"><?php esc_html_e('Hide on large screen (wider than 782px)', 'whatso'); ?></label></p>
                    <p><input type="checkbox" name="whatso_hide_on_small_screen" value="on" id="whatso_hide_on_small_screen" <?php checked('on', $hide_on_small_screen); ?> /> <label for="whatso_hide_on_small_screen"><?php esc_html_e('Hide on small screen (narrower than 783px)', 'whatso'); ?></label></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="whatso_offline_text"><?php esc_html_e('Description text when offline', 'whatso'); ?></label></th>
                <td>
                    <input type="text" id="whatso_offline_text" name="whatso_offline_text" value="<?php echo esc_attr($offline_text); ?>" class="widefat" />
                    <p class="description"><?php esc_html_e('If this field is left blank, the account will be hidden when not available.', 'whatso'); ?></p>
                </td>
            </tr>
            </tbody>
        </table>

        <?php

        wp_nonce_field('whatso_account_meta_box', 'whatso_account_meta_box_nonce');
    }

    public function displayAvailabilityOptions( $time, $value )
    {
        $limit = 'hour' === $time ? 23 : 59;

        for ( $i = 0; $i <= $limit; $i++ ) {
            $text_number = strlen($i) < 2 ? '0' . $i : $i;
            $selected = intval($value) === $i ? 'selected' : '';
            echo '<option value="' . esc_attr($text_number) . '" ' . esc_attr($selected) . '>' . esc_attr($text_number) . '</option>';
        }

    }

    public function getInclusion( $ids, $category )
    {

        $ids = is_array($ids) ? $ids : array();
        $html = '';
        $category = 'included' === strtolower($category) ? esc_html('included') : esc_html('excluded');

        foreach ( $ids as $k => $v ) {
            if (filter_var($v, FILTER_VALIDATE_URL) !== false ) {
                $the_url =  esc_url($v);
                $html.= '
				 <li id="whatso-included-url-' .esc_html($k) . '">
					 <p class="whatso-permalink"><a href="' . $the_url . '" target="_blank">' . $the_url . '</a></p>
					 <span class="dashicons dashicons-no"></span>
					 <input type="hidden" name="whatso_' . $category . '[]" value="' . $the_url . '"/>
				 </li>';
                unset($ids[ $k ]);
            }
        }

        if (!empty($ids)) {
            global $post;
            $included_posts = get_posts(
                array(
                'posts_per_page' => -1,
                'post__in' => $ids,
                'post_type' => 'any'
                ) 
            );

            foreach ( $included_posts as $post ) {

                setup_postdata($post);

                $html.= '
				 <li id="whatso-included-' . sanitize_text_field(get_the_ID()) . '">
					 <p class="whatso-title">' . sanitize_text_field(get_the_title()) . '</p>
					 <p class="whatso-permalink"><a href="' . esc_url(get_the_permalink()) . '" target="_blank">' .esc_url(get_the_permalink()) . '</a></p>
					 <span class="dashicons dashicons-no"></span>
					 <input type="hidden" name="whatso_' . $category . '[]" value="' . sanitize_text_field(get_the_ID()) . '"/>
				 </li>';

            }
            wp_reset_postdata();
        }
        return wp_kses_post($html);
    }

    public function pageTargeting( $post )
    {

        global $pagenow;

        $new = 'post-new.php' === $pagenow ? true : false;

        if ($new ) {
            $target = array( 'home', 'blog', 'archive', 'page', 'post' );
        }
        else {
            $target = json_decode(get_post_meta($post->ID, 'whatso_target', true));
            $target = is_array($target) ? $target : array();
        }

        /* Include and exclude ids */

        $included_html = $this->getInclusion(json_decode(get_post_meta($post->ID, 'whatso_included_ids', true)), 'included');
        $excluded_html = $this->getInclusion(json_decode(get_post_meta($post->ID, 'whatso_excluded_ids', true)), 'excluded');


        /* WPML languages */

        $current_target_languages = json_decode(get_post_meta($post->ID, 'whatso_target_languages', true));

        $current_target_languages = is_array($current_target_languages) ? $current_target_languages : array();


        $languages = apply_filters('wpml_active_languages', null, 'orderby=id&order=desc');




        ?>
        <p class="description"><?php esc_html_e('Page targeting applies only to accounts inside the floating widget. It will be ignored on shortcode buttons. Make sure to clear the cache after saving this post if you use a caching plugin.', 'whatso'); ?></p>

        <table class="form-table" id="whatso-custom-wc-button-settings">
            <caption>Select below checkboxes to display Whatso click to chat button</caption>
            <tbody>
            <tr>
                <th scope="row"><label for=""><?php esc_html_e('Show on these post types', 'whatso'); ?></label></th>
                <td>
                    <p>
                        <input type="checkbox" name="whatso_target[home]" id="whatso_target[home]" value="home" <?php echo esc_html(in_array('home', $target) ? 'checked' : '')?> />
                        <label for="whatso_target[home]"><?php esc_html_e('Homepage', 'whatso'); ?></label>
                    </p>
                    <p>
                        <input type="checkbox" name="whatso_target[blog]" id="whatso_target[blog]" value="blog" <?php echo esc_html(in_array('blog', $target) ? 'checked' : '')?> />
                        <label for="whatso_target[blog]"><?php esc_html_e('Blog Index', 'whatso'); ?></label>
                    </p>
                    <p>
                        <input type="checkbox" name="whatso_target[archive]" id="whatso_target[archive]" value="archive" <?php echo esc_html(in_array('archive', $target) ? 'checked' : '') ?> />
                        <label for="whatso_target[archive]"><?php esc_html_e('Archives', 'whatso'); ?></label>
                    </p>
                    <p>
                        <input type="checkbox" name="whatso_target[page]" id="whatso_target[page]" value="page" <?php echo esc_html(in_array('page', $target) ? 'checked' : '') ?> />
                        <label for="whatso_target[page]"><?php esc_html_e('Pages', 'whatso'); ?></label>
                    </p>
                    <p>
                        <input type="checkbox" name="whatso_target[post]" id="whatso_target[post]" value="post" <?php echo esc_html(in_array('post', $target) ? 'checked' : '') ?> />
                        <label for="whatso_target[post]"><?php esc_html_e('Blog posts', 'whatso'); ?></label>
                    </p>
                    <?php foreach ( get_post_types(array( '_builtin' => false ), 'objects') as $post_type ) : ?>
                        <p>
                            <input type="checkbox" name="whatso_target[<?php echo esc_html($post_type->name); ?>]" id="whatso_target[<?php echo esc_html($post_type->name); ?>]" value="<?php echo esc_html($post_type->name); ?>" <?php echo esc_html(in_array($post_type->name, $target) ? 'checked' : '')?>/>
                            <label for="whatso_target[<?php echo esc_html($post_type->name); ?>]"><?php echo esc_html($post_type->label); ?></label>
                        </p>

                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Include Pages', 'whatso'); ?></th>
                <td>
                    <div class="whatso-search-posts">
                        <input type="text" class="regular-text" placeholder="<?php esc_html_e('Type the title of page/post to include', 'whatso'); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('whatso-search-nonce')); ?>" />
                        <div class="whatso-search-result">
                            <ul></ul>
                        </div>
                    </div>
                    <p class="whatso-listing-info"><span><?php esc_html_e('Included pages:', 'whatso'); ?></span></p>

                    <ul class="whatso-inclusion whatso-included-posts" data-delete-label="<?php esc_attr_e('Delete', 'whatso'); ?>">
                        <?php echo wp_kses_post($included_html); ?>
                        <li class="whatso-placeholder"><?php esc_html_e('No specific page is included.', 'whatso'); ?></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Exclude Pages', 'whatso'); ?></th>
                <td>
                    <div class="whatso-search-posts">
                        <input type="text" class="regular-text" placeholder="<?php esc_attr_e('Type the title of page/post to exclude', 'whatso'); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('whatso-search-nonce')); ?>" />
                        <div class="whatso-search-result">
                            <ul></ul>
                        </div>
                    </div>
                    <p class="whatso-listing-info"><span><?php esc_html_e('Excluded pages:', 'whatso'); ?></span></p>

                    <ul class="whatso-inclusion whatso-excluded-posts" data-delete-label="<?php esc_attr_e('Delete', 'whatso'); ?>">
                        <?php echo wp_kses_post($excluded_html); ?>
                        <li class="whatso-placeholder"><?php esc_html_e('None. All pages from checked post types above are included.', 'whatso'); ?></li>
                    </ul>
                </td>
            </tr>

            <?php if (is_array($languages) ) : ?>
                <tr>
                    <th scope="row"><?php esc_html_e('WPML Languages', 'whatso'); ?></th>
                    <td>
                        <?php foreach ( $languages as $v ) : ?>
                            <p>
                                <input type="checkbox" name="whatso_target_languages[<?php echo esc_html($v['code']); ?>]" id="whatso_target_languages[<?php echo esc_html($v['code']); ?>]" value="<?php echo esc_html($v['code']); ?>" <?php echo in_array(esc_html($v['code']), esc_attr($current_target_languages)) ? 'checked' : '' ?>/>
                                <label for="whatso_target_languages[<?php echo esc_html($v['code']); ?>]"><?php echo esc_html($v['translated_name']); ?></label>
                            </p>
                        <?php endforeach;?>
                        <p class="description"><span><?php esc_html_e('If none are selected, then the account will be displayed on all languages.', 'whatso'); ?></span></p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php

    }

    public function buttonStyle( $post )
    {

        global $pagenow;



        $background_color = sanitize_text_field(get_post_meta($post->ID, 'whatso_background_color', true));
        $background_color_on_hover = sanitize_text_field(get_post_meta($post->ID, 'whatso_background_color_on_hover', true));
        $text_color = sanitize_text_field(get_post_meta($post->ID, 'whatso_text_color', true));
        $text_color_on_hover = sanitize_text_field(get_post_meta($post->ID, 'whatso_text_color_on_hover', true));

        ?>
        <p class="description"><?php printf(esc_html__('Use the below settings if you want to modify style for shortcode button. This settings will not change your appearance of the click to chat widget.', 'whatso'), ''); ?></p>
        <table class="form-table" id="whatso-custom-wc-button-settings">
            <caption> Select Color for Whatso button</caption>
            <tbody>
            <tr>
                <th scope="row"><label for="whatso_background_color"><?php esc_html_e('Button Background Color', 'whatso'); ?></label></th>
                <td><input name="whatso_background_color" type="text" id="whatso_background_color" class="minicolors" value="<?php echo esc_attr($background_color); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="whatso_text_color"><?php esc_html_e('Button Text Color', 'whatso'); ?></label></th>
                <td><input name="whatso_text_color" type="text" id="whatso_text_color" class="minicolors" value="<?php echo esc_attr($text_color); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="whatso_background_color_on_hover"><?php esc_html_e('Button Background Color on Hover', 'whatso'); ?></label></th>
                <td><input name="whatso_background_color_on_hover" type="text" id="whatso_background_color_on_hover" class="minicolors" value="<?php echo  esc_attr($background_color_on_hover); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="whatso_text_color_on_hover"><?php esc_html_e('Button Text Color on Hover', 'whatso'); ?></label></th>
                <td><input name="whatso_text_color_on_hover" type="text" id="whatso_text_color_on_hover" class="minicolors" value="<?php echo  esc_attr($text_color_on_hover); ?>"></td>
            </tr>
            </tbody>
        </table>

        <?php
    }

    public function copyShortcode()
    {

        ?>

        <p><?php esc_html_e('Copy the shortcode below and paste it into the editor to display the button.', 'whatso'); ?></p>
        <p><input type="text" value='[whatsapp_button id="<?php echo esc_attr(get_the_ID()); ?>"]' class="widefat" onkeypress="return event.keyCode != 13;" readonly /></p>
        <?php

    }




    public function saveMetaBoxes( $post_id )
    {



        /* Check if our nonce is set. */
        if (! isset($_POST['whatso_account_meta_box_nonce']) ) {
            return;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['whatso_account_meta_box_nonce']));

        /* Verify that the nonce is valid. */
        if (! wp_verify_nonce($nonce, 'whatso_account_meta_box') ) {
            return;
        }

        /* WhatsApp Account Information */


        $number = isset($_POST['whatso_number']) ? sanitize_text_field(wp_unslash($_POST['whatso_number'])) : '';

        $name = isset($_POST['whatso_name']) ? sanitize_text_field(wp_unslash($_POST['whatso_name'])) : '';
        $title = isset($_POST['whatso_title']) ? sanitize_text_field(wp_unslash($_POST['whatso_title'])) : '';
        $predefined_text = isset($_POST['whatso_predefined_text']) ? sanitize_text_field(wp_unslash($_POST['whatso_predefined_text'])) : '';
        if (function_exists('sanitize_textarea_field') ) {
            $predefined_text = isset($_POST['whatso_predefined_text']) ? sanitize_textarea_field(wp_unslash($_POST['whatso_predefined_text'])) : '';
        }

        $button_label = isset($_POST['whatso_button_label']) ? sanitize_text_field(wp_unslash($_POST['whatso_button_label'])) : '';

        $availability = isset($_POST['whatso_availability']) ? wp_json_encode(sanitize_post(wp_unslash($_POST['whatso_availability'])))    : wp_json_encode(array());



        $offline_text = isset($_POST['whatso_offline_text']) ? sanitize_text_field(wp_unslash($_POST['whatso_offline_text'])) : '';
        $whatso_hide_on_large_screen = isset($_POST['whatso_hide_on_large_screen']) ? sanitize_text_field(wp_unslash($_POST['whatso_hide_on_large_screen'])) : '';
        $whatso_hide_on_small_screen = isset($_POST['whatso_hide_on_small_screen']) ? sanitize_text_field(wp_unslash($_POST['whatso_hide_on_small_screen'])) : '';
        $whatso_pin_account = isset($_POST['whatso_pin_account']) ? sanitize_text_field(wp_unslash($_POST['whatso_pin_account'])) : '';
        $hide_on_large_screen = isset($whatso_hide_on_large_screen) ? 'on' : 'off';
        $hide_on_small_screen = isset($whatso_hide_on_small_screen) ? 'on' : 'off';

        $pin_account = isset($whatso_pin_account) ? 'on' : 'off';

        $num=substr($number, 0, 1);
        if($num != "+") {
            $num2 = "+";
            $number=$num2.$number;

        }


        update_post_meta($post_id, 'whatso_number', $number);
        update_post_meta($post_id, 'whatso_name', $name);
        update_post_meta($post_id, 'whatso_title', $title);
        update_post_meta($post_id, 'whatso_predefined_text', $predefined_text);
        update_post_meta($post_id, 'whatso_button_label', $button_label);
        update_post_meta($post_id, 'whatso_availability', $availability);
        update_post_meta($post_id, 'whatso_offline_text', $offline_text);

        update_post_meta($post_id, 'whatso_hide_on_large_screen', $hide_on_large_screen);
        update_post_meta($post_id, 'whatso_hide_on_small_screen', $hide_on_small_screen);

        update_post_meta($post_id, 'whatso_pin_account', $pin_account);


        /* Button Style */

        $background_color = isset($_POST['whatso_background_color']) ? sanitize_text_field(wp_unslash($_POST['whatso_background_color'])) : '';
        $background_color_on_hover = isset($_POST['whatso_background_color_on_hover']) ? sanitize_text_field(wp_unslash($_POST['whatso_background_color_on_hover'])) : '';
        $text_color = isset($_POST['whatso_text_color']) ? sanitize_text_field(wp_unslash($_POST['whatso_text_color'])) : '';
        $text_color_on_hover = isset($_POST['whatso_text_color_on_hover']) ? sanitize_text_field(wp_unslash($_POST['whatso_text_color_on_hover'])) : '';
        $whatso_target = isset($_POST['whatso_target']) ? sanitize_text_field(wp_unslash($_POST['whatso_target'])) : '';
        $whatso_included = isset($_POST['whatso_included']) ? sanitize_text_field(wp_unslash($_POST['whatso_included'])) : '';
        $whatso_excluded = isset($_POST['whatso_excluded']) ? sanitize_text_field(wp_unslash($_POST['whatso_excluded'])) : '';
        $whatso_target_languages =  isset($_POST['whatso_target_languages']) ? sanitize_text_field(wp_unslash($_POST['whatso_target_languages'])) : '';
        update_post_meta($post_id, 'whatso_background_color', $background_color);
        update_post_meta($post_id, 'whatso_background_color_on_hover', $background_color_on_hover);
        update_post_meta($post_id, 'whatso_text_color', $text_color);
        update_post_meta($post_id, 'whatso_text_color_on_hover', $text_color_on_hover);

        /* Page Targeting */

        if (isset($whatso_target) ) {
            $t = array();
            foreach ( wp_unslash($_POST['whatso_target']) as $value )  {
                $t[] = sanitize_text_field($value);
            }
            update_post_meta($post_id, 'whatso_target', wp_json_encode($t));
        }
        else {
            update_post_meta($post_id, 'whatso_target', wp_json_encode(array()));
        }

        /* Included pages */
        if (isset($whatso_included) ) {
            $in_ids = array();
            foreach ( wp_unslash($_POST['whatso_included']) as $value ) {
                $in_ids[] =  sanitize_text_field($value);
            }
            update_post_meta($post_id, 'whatso_included_ids', wp_json_encode($in_ids));
        }
        else {
            update_post_meta($post_id, 'whatso_included_ids', wp_json_encode(array()));
        }

        /* Excluded pages */
        if (isset($whatso_excluded) ) {
            $ex_ids = array();
            $whatso_excluded = sanitize_text_field(wp_unslash($_POST['whatso_excluded']));
            foreach ( $whatso_excluded as $value ) {
                $ex_ids[] =  sanitize_text_field($value);
            }
            update_post_meta($post_id, 'whatso_excluded_ids', wp_json_encode($ex_ids));
        }
        else {
            update_post_meta($post_id, 'whatso_excluded_ids', wp_json_encode(array()));
        }

        if (isset($whatso_target_languages)) {
            $t = array();
            $whatso_target_languages = sanitize_text_field(wp_unslash($_POST['whatso_target_languages']));
            foreach ( $whatso_target_languages as $value ) {
                $t[] = sanitize_text_field($value);
            }
            update_post_meta($post_id, 'whatso_target_languages', wp_json_encode($t));
        }
        else {
            update_post_meta($post_id, 'whatso_target_languages', wp_json_encode(array()));
        }

    }

}