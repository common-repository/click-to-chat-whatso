<div class="wrap">
    
    <?php require_once 'floating_widget_header.php'; ?>
    
    <form action="" method="post" novalidate="novalidate">
        <ul class="eg-ul">
        <li><?php esc_html_e('Delay Time: Time taken to expand Plugin after page load. Eg. You can set 15 to expand chat widget after 15 second of page load.', 'whatso'); ?></li>
        <li><?php esc_html_e('Inactivity Time: Plugin will be expanded only when visitor is inactive for X seconds. You can set value of X below. Eg. 10 Seconds.', 'whatso'); ?></li>
        <li><?php esc_html_e('Scroll Length: Plugin will be displayed when visitor scroll X % of screen. Eg. 20%', 'whatso'); ?></li>
        <li><?php esc_html_e('Note: If you are not sure about what values to set, avoid using the below feature', 'whatso'); ?></li>
        </ul>
        <table class="form-table">
        <caption>Whatso Display Settings</caption>
            <tbody>
                <tr>
                    <th scope="row"><label for="delay_time"><?php esc_html_e('Delay Time', 'whatso'); ?></label></th>
                    <td>
                        <input name="delay_time" type="number" min="0" max="999" id="delay_time" value="<?php echo filter_var(WHATSO_Utils::getSetting('delay_time'), FILTER_SANITIZE_NUMBER_INT); ?>"> <?php esc_html_e('second(s)', 'whatso'); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="inactivity_time"><?php esc_html_e('Inactivity Time', 'whatso'); ?></label></th>
                    <td>
                        <input name="inactivity_time" type="number" min="0" max="999" id="inactivity_time" value="<?php echo filter_var(WHATSO_Utils::getSetting('inactivity_time'), FILTER_SANITIZE_NUMBER_INT); ?>"> <?php esc_html_e('second(s)', 'whatso'); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="scroll_length"><?php esc_html_e('Scroll Length', 'whatso'); ?></label></th>
                    <td>
                        <input name="scroll_length" type="number" min="0" max="100" id="scroll_length" value="<?php echo filter_var(WHATSO_Utils::getSetting('scroll_length'), FILTER_SANITIZE_NUMBER_INT); ?>">  <?php esc_html_e('%', 'whatso'); ?>
                    </td>
                </tr>
                
                <tr>
                <tr>
                    <th scope="row"><label for="disable_auto_display_on_small_screen"><?php esc_html_e('Disable on mobile', 'whatso'); ?></label></th>
                    <td>
                        <input name="disable_auto_display_on_small_screen" type="checkbox" id="disable_auto_display_on_small_screen" value="on" <?php echo esc_html('on' === WHATSO_Utils::getSetting('disable_auto_display_on_small_screen') ? 'checked' : ''); ?>>  <label for="disable_auto_display_on_small_screen"><?php esc_html_e('Yes, disable auto display on small screen.', 'whatso'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="disable_auto_display_when_no_one_online"><?php esc_html_e('Disable when no one is online', 'whatso'); ?></label></th>
                    <td>
                        <input name="disable_auto_display_when_no_one_online" type="checkbox" id="disable_auto_display_when_no_one_online" value="on" <?php echo esc_html('on' === WHATSO_Utils::getSetting('disable_auto_display_when_no_one_online') ? 'checked' : ''); ?>>  <label for="disable_auto_display_when_no_one_online"><?php esc_html_e('Yes, disable auto display when no one is online.', 'whatso'); ?></label>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php wp_nonce_field('whatso_auto_display_form', 'whatso_auto_display_form_nonce'); ?>
        
        <input type="hidden" name="whatso_auto_display" value="submit" />
        <input type="hidden" name="submit" value="submit" />
        <p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Auto Display', 'whatso'); ?>"></p>
        
    
    </form>
</div>
