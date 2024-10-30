<?php

/**
 * Controller: settings.php
 *
 * @package settings
 */

/* Stop immediately if accessed directly. */
if (! defined('ABSPATH') ) {
    die();
}
?>
<h1><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/images/whatsoLogoNew_black2.webp" height="35px" width="35px" style="margin-top:-10px;" alt="">
Whatso Click to Chat</h1>
<ul class="breadcrumb">
  <li><a href="admin.php?page=whatso_floating_menu_setup"><strong>Whatso</strong></a></li>
  <li><a href="admin.php?page=whatso_floating_ctc_setup"><strong>Click to Chat</strong></a></li>
 <li>Shortcode Settings</li>
</ul>
<div class="wrap">
    <h1><?php esc_html_e('Shortcode Settings', 'whatso'); ?></h1>
    
    <?php settings_errors(); ?>
    
    <form action="" method="post" novalidate="novalidate">
        <p><?php esc_html_e("Below form helps you set default style for buttons set via shortcode. Individual button style can also be set when creating or editing the account.", 'whatso'); ?></p>
        <table id="whatso-default-settings" class="form-table">
        <caption>Whatso Button Settings</caption>
            <tbody>
                <tr>
                    <th scope="row"><label for="button_label"><?php esc_html_e('Button Label', 'whatso'); ?></label></th>
                    <td>
                        <input name="button_label" type="text" id="button_label" class="regular-text" value="<?php echo esc_attr(WHATSO_Utils::getSetting('button_label')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_style"><?php esc_html_e('Button Style', 'whatso'); ?></label></th>
                    <td>
                        <select name="button_style" id="button_style">
                            <option value="boxed" <?php selected('boxed', WHATSO_Utils::getSetting('button_style'), true); ?>><?php esc_html_e('Boxed', 'whatso');?></option>
                            <option value="round" <?php selected('round', WHATSO_Utils::getSetting('button_style'), true); ?>><?php esc_html_e('Round', 'whatso');?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_background_color"><?php esc_html_e('Button Background Color', 'whatso'); ?></label></th>
                    <td>
                        <input name="button_background_color" type="text" id="button_background_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('button_background_color')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_text_color"><?php esc_html_e('Button Text Color', 'whatso'); ?></label></th>
                    <td>
                        <input name="button_text_color" type="text" id="button_text_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('button_text_color')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_background_color_on_hover"><?php esc_html_e('Button Background Color on Hover', 'whatso'); ?></label></th>
                    <td>
                        <input name="button_background_color_on_hover" type="text" id="button_background_color_on_hover" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('button_background_color_on_hover')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_text_color_on_hover"><?php esc_html_e('Button Text Color on Hover', 'whatso'); ?></label></th>
                    <td>
                        <input name="button_text_color_on_hover" type="text" id="button_text_color_on_hover" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('button_text_color_on_hover')); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><label for="button_background_color_offline"><?php esc_html_e('Button Background Color When Offline', 'whatso'); ?></label></th>
                    <td>
                        <input name="button_background_color_offline" type="text" id="button_background_color_offline" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('button_background_color_offline')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_text_color_offline"><?php esc_html_e('Button Text Color When Offline', 'whatso'); ?></label></th>
                    <td>
                        <input name="button_text_color_offline" type="text" id="button_text_color_offline" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('button_text_color_offline')); ?>">
                    </td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field('whatso_settings_form', 'whatso_settings_form_nonce'); ?>
        <input type="hidden" name="whatso_settings" value="submit" />
        <input type="hidden" name="submit" value="submit" />
        <p class="submit"><input type="submit" id="submit" class="btn btn-theme" value="<?php esc_attr_e('Save Changes', 'whatso'); ?>"></p>
    </form>
</div>
