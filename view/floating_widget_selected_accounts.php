<!DOCTYPE html>
<html lang="en">
<head>
 <title>whatso</title>

</head>

<body>




<div class="wrap">
    <?php require_once 'floating_widget_header.php'; ?>
    <form action="" method="post" novalidate="novalidate">
    <!--    <p><?php esc_html_e('Select accounts to display on your website.', 'whatso'); ?></p>-->
        <?php WHATSO_Templates::displaySelectedAccounts('selected_accounts_for_widget'); ?>
        <?php wp_nonce_field('whatso_selected_accounts_form', 'whatso_selected_accounts_form_nonce'); ?>
        
        <input type="hidden" name="whatso_selected_accounts" value="submit" />
        
        <input type="hidden" name="submit" value="submit" />
        <p class="submit"><input type="submit" id="submit" class="btn btn-theme" value="<?php esc_attr_e('Save Selected Accounts', 'whatso'); ?>"></p>
    </form>
</div>
</html>
