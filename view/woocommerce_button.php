<h1 style="font-size:40px;"><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/images/whatsoLogoNew_black2.webp" height="35px" width="35px" style="margin-top:-10px;" alt="">
Whatso Click to Chat</h1>
<ul class="breadcrumb">
  <li><a href="admin.php?page=whatso_floating_menu_setup">Whatso</a></li>
  <li><a href="admin.php?page=whatso_floating_ctc_setup">Click to Chat</a></li>
 <li><?php esc_html_e('WooCommerce Settings','whatso');?></li>
 
</ul>
<div class="wrap">
    <h1><?php esc_html_e('WooCommerce Settings', 'whatso'); ?></h1>
    <?php settings_errors(); ?>
    <form action="" method="post" novalidate="novalidate">
        <p><?php esc_html_e('Display Chat button along with your products on WooCommmerce Product page using below form.', 'whatso'); ?></p>
        <table class="form-table whatso-account-item">
        <caption><?php esc_html_e('Woocommerce Button Settings','whatso');?></caption>
            <tbody>
                <tr>
                    <th scope="row"><label for="wc_button_position"><?php esc_html_e('Button position', 'whatso'); ?></label></th>
                    <td>
                        <select name="wc_button_position" id="wc_button_position">
                            <option value="after_short_description" <?php selected('after_short_description', WHATSO_Utils::getSetting('wc_button_position'), true); ?>><?php esc_html_e('After short description', 'whatso'); ?></option>
                            <option value="after_long_description" <?php selected('after_long_description', WHATSO_Utils::getSetting('wc_button_position'), true); ?>><?php esc_html_e('After long description', 'whatso'); ?></option>
                            <option value="before_atc" <?php selected('before_atc', WHATSO_Utils::getSetting('wc_button_position'), true); ?>><?php esc_html_e('Before Add to Cart button', 'whatso'); ?></option>
                            <option value="after_atc" <?php selected('after_atc', WHATSO_Utils::getSetting('wc_button_position'), true); ?>><?php esc_html_e('After Add to Cart button', 'whatso'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wc_randomize_accounts_order"><?php esc_html_e('Randomize Accounts Order', 'whatso'); ?></label></th>
                    <td>
                        <p><input type="checkbox" name="wc_randomize_accounts_order" value="on" id="wc_randomize_accounts_order" <?php checked('on', WHATSO_Utils::getSetting('wc_randomize_accounts_order'), true); ?> /> <label for="wc_randomize_accounts_order"><?php esc_html_e('Yes, randomize the order of accounts', 'whatso'); ?></label></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wc_total_accounts_shown"><?php esc_html_e('Total accounts shown', 'whatso'); ?></label></th>
                    <td>
                        <p><input type="number" min="0" max="100" name="wc_total_accounts_shown" value="<?php echo filter_var(WHATSO_Utils::getSetting('wc_total_accounts_shown'), FILTER_SANITIZE_NUMBER_INT); ?>" id="wc_total_accounts_shown" /> </p>
                        <p class="description"><?php esc_html_e('If the value is zero (0), then all the selected accounts will be displayed.', 'whatso');?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="selected_accounts"><?php esc_html_e('Select accounts to display', 'whatso'); ?></label></th>
                    <td><?php WHATSO_Templates::displaySelectedAccounts('selected_accounts_for_woocommerce'); ?></td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field('whatso_woocommerce_button_form', 'whatso_woocommerce_button_form_nonce'); ?>
        <input type="hidden" name="whatso_woocommerce_button" value="submit" />
        <input type="hidden" name="submit" value="submit" />
        <p class="submit"><input type="submit" id="submit" class="btn btn-theme" value="<?php esc_attr_e('Save WooCommerce Settings', 'whatso'); ?>"></p>
    </form>
</div>
