<?php
$box_position = '' === WHATSO_Utils::getSetting('box_position') ? 'right' : WHATSO_Utils::getSetting('box_position');

?>
<div class="wrap">
    
    <?php require_once 'floating_widget_header.php'; ?>
    
    <form action="" method="post" novalidate="novalidate">
        
        <p><?php esc_html_e('Change text and style of widget.', 'whatso'); ?></p>
        
        <table class="form-table">
        <caption>Widget Settings</caption>
            <tbody>
                <tr>
                    <th scope="row"><label for="toggle_text"><?php esc_html_e('Widget Text', 'whatso'); ?></label></th>
                    <td>
                        <input name="toggle_text" type="text" id="toggle_text" class="regular-text" value="<?php echo esc_attr(WHATSO_Utils::getSetting('toggle_text')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="toggle_text_color"><?php esc_html_e('Widget Text Color', 'whatso'); ?></label></th>
                    <td>
                        <input name="toggle_text_color" type="text" id="toggle_text_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('toggle_text_color')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="toggle_background_color"><?php esc_html_e('Widget Background Color', 'whatso'); ?></label></th>
                    <td>
                        <input name="toggle_background_color" type="text" id="toggle_background_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('toggle_background_color')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Widget Type by Device', 'whatso'); ?></label></th>
                    <td>
                        <p><input name="toggle_round_on_desktop" type="checkbox" id="toggle_round_on_desktop" value="on" <?php echo 'on' === WHATSO_Utils::getSetting('toggle_round_on_desktop') ? 'checked' : ''; ?>> <label for="toggle_round_on_desktop"><?php esc_html_e('Show rounded widget on desktop', 'whatso'); ?></label></p>
                        <p><input name="toggle_round_on_mobile" type="checkbox" id="toggle_round_on_mobile" value="on" <?php echo 'on' === WHATSO_Utils::getSetting('toggle_round_on_mobile') ? 'checked' : ''; ?>> <label for="toggle_round_on_mobile"><?php esc_html_e('Show rounded widget on mobile', 'whatso'); ?></label></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="description"><?php esc_html_e('Description', 'whatso'); ?></label></th>
                    <td>
                        <?php 
                        wp_editor(
                            WHATSO_Utils::getSetting('description'), 'description', array(
                            'media_buttons' => false,
                            'textarea_name' => 'description',
                            'textarea_rows' => 3,
                            'teeny' => true,
                            'quicktags' => false
                            ) 
                        ); 
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="container_text_color"><?php esc_html_e('Container Text Color', 'whatso'); ?></label></th>
                    <td>
                        <input name="container_text_color" type="text" id="container_text_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('container_text_color')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="container_background_color"><?php esc_html_e('Container Background Color', 'whatso'); ?></label></th>
                    <td>
                        <input name="container_background_color" type="text" id="container_background_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('container_background_color')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="account_hover_background_color"><?php esc_html_e('Account Item Background Color on Hover', 'whatso'); ?></label></th>
                    <td>
                        <input name="account_hover_background_color" type="text" id="account_hover_background_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('account_hover_background_color')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="account_hover_text_color"><?php esc_html_e('Account Item Text Color on Hover', 'whatso'); ?></label></th>
                    <td>
                        <input name="account_hover_text_color" type="text" id="account_hover_text_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('account_hover_text_color')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="border_color_between_accounts"><?php esc_html_e('Border Color Between Accounts', 'whatso'); ?></label></th>
                    <td>
                        <input name="border_color_between_accounts" type="text" id="border_color_between_accounts" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('border_color_between_accounts')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="box_position"><?php esc_html_e('Box Position', 'whatso'); ?></label></th>
                    <td>
                        <p><input type="radio" name="box_position" value="left" id="box_position_left" <?php echo esc_html('left') === esc_attr($box_position) ? 'checked' : ''; ?> /> <label for="box_position_left"><?php esc_html_e('Bottom Left', 'whatso'); ?></label></p>
                        <p><input type="radio" name="box_position" value="right" id="box_position_right" <?php echo esc_html('right') === esc_attr($box_position) ? 'checked' : ''; ?> /> <label for="box_position_right"><?php esc_html_e('Bottom Right', 'whatso'); ?></label></p>
                        
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="toggle_center_on_mobile"><?php esc_html_e('Center Widget on Small Screen', 'whatso'); ?></label></th>
                    <td>
                        <p><input type="checkbox" name="toggle_center_on_mobile" value="on" id="toggle_center_on_mobile" <?php checked('on', WHATSO_Utils::getSetting('toggle_center_on_mobile'), true); ?> /> <label for="toggle_center_on_mobile"><?php esc_html_e('Yes, put the toggle at the bottom center on small screen', 'whatso'); ?></label></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="randomize_accounts_order"><?php esc_html_e('Randomize Accounts Order', 'whatso'); ?></label></th>
                    <td>
                        <p><input type="checkbox" name="randomize_accounts_order" value="on" id="randomize_accounts_order" <?php checked('on', WHATSO_Utils::getSetting('randomize_accounts_order'), true); ?> /> <label for="randomize_accounts_order"><?php esc_html_e('Yes, randomize the order of accounts', 'whatso'); ?></label></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="total_accounts_shown"><?php esc_html_e('Total accounts shown', 'whatso'); ?></label></th>
                    <td>
                        <p><input type="number" min="0" max="100" name="total_accounts_shown" value="<?php echo filter_var(WHATSO_Utils::getSetting('total_accounts_shown'), FILTER_SANITIZE_NUMBER_INT); ?>" id="total_accounts_shown" /> </p>
                        <p class="description"><?php esc_html_e('If the value is zero (0), then all the selected accounts will be displayed.', 'whatso');?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cache_time"><?php esc_html_e('Cache time', 'whatso'); ?></label></th>
                    <td>
                        <p><input type="number" min="0" max="60" name="cache_time" value="<?php echo filter_var(WHATSO_Utils::getSetting('cache_time'), FILTER_SANITIZE_NUMBER_INT); ?>" id="cache_time" /> <?php esc_html_e('minute(s)', 'whatso'); ?> </p>
                        <p class="description"><?php esc_html_e("This is useful to decrease server load but the accuracy of `Availability` feature will be decreased the higher the cache time set. A recommended value for cache time is 5 to 10 minutes, which means that the accounts could still be online 5 to 10 minutes after they're supposed to be offline.", 'whatso');?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php wp_nonce_field('whatso_display_settings_form', 'whatso_display_settings_form_nonce'); ?>
        <input type="hidden" name="whatso_display_settings" value="submit" />
        <input type="hidden" name="submit" value="submit" />
        <p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Display Settings', 'whatso'); ?>"></p>
        
    </form>
</div>
