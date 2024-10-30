
<div class="wrap">
    
    <?php require_once 'floating_widget_header.php'; ?>
    
    <form action="" method="post" novalidate="novalidate">
        
        <p><?php esc_html_e('Below are messages to show to visitors for GDPR compliance. Use only if you need to comply with GDPR.', 'whatso'); ?></p>
        
        <table class="form-table">
        <caption>Consent settings</caption>
            <tbody>
                <tr>
                    <th scope="row"><label for="consent_description"><?php esc_html_e('Consent Description', 'whatso'); ?></label></th>
                    <td>
                        <?php 
                        wp_editor(
                            sanitize_text_field(WHATSO_Utils::getSetting('consent_description')), 'consent_description', array(
                            'media_buttons' => false,
                            'textarea_name' => 'consent_description',
                            'textarea_rows' => 3,
                            'teeny' => true,
                            'quicktags' => false
                            ) 
                        ); 
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="consent_checkbox_text_label"><?php esc_html_e('Consent Checkbox Text Label', 'whatso'); ?></label></th>
                    <td>
                        <?php 
                        wp_editor(
                            sanitize_text_field(WHATSO_Utils::getSetting('consent_checkbox_text_label')), 'consent_checkbox_text_label', array(
                            'media_buttons' => false,
                            'textarea_name' => 'consent_checkbox_text_label',
                            'textarea_rows' => 3,
                            'teeny' => true,
                            'quicktags' => false
                            ) 
                        ); 
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="consent_alert_background_color"><?php esc_html_e('Alert Background Color', 'whatso'); ?></label></th>
                    <td>
                        <input name="consent_alert_background_color" type="text" id="consent_alert_background_color" class="minicolors" value="<?php echo esc_attr(WHATSO_Utils::getSetting('consent_alert_background_color')); ?>">
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php wp_nonce_field('whatso_consent_confirmation_form', 'whatso_consent_confirmation_form_nonce'); ?>
        <input type="hidden" name="whatso_consent_confirmation" value="submit" />
        <input type="hidden" name="submit" value="submit" />
        <p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Consent Confirmation', 'whatso'); ?>"></p>
        
    </form>
</div>
