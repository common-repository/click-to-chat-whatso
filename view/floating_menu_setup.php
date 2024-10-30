<?php
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body>
    <div class="pr-5 container">
        <div class="row">
            <div class="col-auto">
                <?php $img_url = plugin_dir_url(__DIR__);
                $logo = $img_url . 'assets/images/whatsoLogoNew_black2.webp';
                $notificationvector = $img_url . 'assets/images/notificationiconvector.jpeg';
                $bucket = $img_url . 'assets/images/bucket.jpeg';
                $vector = $img_url . 'assets/images/messagevector.jpeg';
                $bullhorn = $img_url . 'assets/images/bullhorn-icon.png';
                $notificationIcon = $img_url . 'assets/images/notification-icon.png';

                ?>
                <img src="<?php echo esc_url($logo); ?>" style="width: 35px;" class="imgclass" alt="">
            </div>
            <div class="col p-0">
                <h5 class="head-title my-3"><?php esc_html_e('Whatso Widgets', 'whatso'); ?></h5>
            </div>
        </div>
        <?php
        $whatsoVersion = get_option('whatso_version_detail');
        $data = json_decode($whatsoVersion);
        if ($data->IsCompulsoryUpdate) {
        ?>
            <div>
                <a href="<?php echo esc_attr(get_site_url()); ?>/wp-admin/plugins.php" class="d-inline-block"><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/images/upgradeplugin.png" class="img-fluid" alt="" /></a>
            </div>
        <?php
        }
        ?>
        <div class="row">
            <div class="col-md-4 col-lg-4 d-flex align-items-stretch">
                <a href="admin.php?page=whatso_notifications_setup" class="text-black text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h2 class="card-title"><?php esc_html_e('Order Notification', 'whatso'); ?></h2>
                                </div>
                                <div class="col-auto">
                                    <img class="img" src="<?php echo esc_attr($notificationvector); ?>" alt-="">
                                </div>
                            </div>
                            <div class="row">
                                <p class="card-text2"><?php esc_html_e('Website owner & customer gets a WhatsApp for all successful orders.', 'whatso'); ?></p>
                            </div>
                            <div class="row mt-4">
                                <a href="admin.php?page=whatso_notifications_setup" class="settings">Manage <i class="fa fa-chevron-circle-right"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-4 d-flex align-items-stretch">
                <a href="admin.php?page=whatso_abandoned_cart" class="text-black text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h2 class="card-title"><?php esc_html_e('Abandoned Cart', 'whatso'); ?></h2>
                                </div>
                                <div class="col-auto">
                                    <img class="img" src="<?php echo esc_attr($bucket); ?>" alt="">
                                </div>
                            </div>
                            <div class="row">
                                <p class="card-text2"><?php esc_html_e('Recover failed orders in real cash by sending multiple WhatsApp messages.', 'whatso'); ?>
                                </p>
                            </div>
                            <div class="row mt-4">
                                <a href="admin.php?page=whatso_abandoned_cart" class="settings">Manage <i class="fa fa-chevron-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-lg-4 d-flex align-items-stretch">
                <a href="admin.php?page=whatso_floating_ctc_setup" class="text-black text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h2 class="card-title"><?php esc_html_e('Click to Chat', 'whatso'); ?></h2>
                                </div>
                                <div class="col-auto">
                                    <img class="img" src="<?php echo esc_attr($vector); ?>" alt="">
                                </div>
                            </div>
                            <div class="row">
                                <p class="card-text2"><?php esc_html_e('Click to Chat feature allows you to begin a WhatsApp chat with visitors on your website.', 'whatso'); ?></p>
                            </div>
                            <div class="row mt-4">
                                <a href="admin.php?page=whatso_floating_ctc_setup" class="settings">Manage <i class="fa fa-chevron-circle-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        </div>

        <div class="row">

            <div class="col-md-4 col-lg-4 d-flex align-items-stretch">
                <a href="admin.php?page=whatso_message_notification_cf7" class="text-black text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h2 class="card-title"><?php esc_html_e('Message notification for contact form 7', 'whatso'); ?></h2>
                                </div>
                                <div class="col-auto">
                                    <img class="img" src="<?php echo esc_attr($notificationIcon); ?>" alt="">
                                </div>
                            </div>
                            <div class="row">
                                <p class="card-text2"><?php esc_html_e('Admin can receive notification message of contact form 7.', 'whatso'); ?></p>
                            </div>
                            <div class="row mt-4">
                                <a href="admin.php?page=whatso_message_notification_cf7" class="settings">Manage <i class="fa fa-chevron-circle-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-4 d-flex align-items-stretch">
                <a href="admin.php?page=whatso_broadcast_message" class="text-black text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h2 class="card-title"><?php esc_html_e('Broadcast Messages', 'whatso'); ?></h2>
                                </div>
                                <div class="col-auto">
                                    <img class="img" src="<?php echo esc_attr($bullhorn); ?>" alt="">
                                </div>
                            </div>
                            <div class="row">
                                <p class="card-text2"><?php esc_html_e('Admin can send any custom messages to the customers’ WhatsApp.', 'whatso'); ?></p>
                            </div>
                            <div class="row mt-4">
                                <a href="admin.php?page=whatso_broadcast_message" class="settings">Manage <i class="fa fa-chevron-circle-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="row card-body">
                        <h5 class="dashboardmsg">
                            <b><?php esc_html_e('Are you facing any issue in setup?', 'whatso'); ?> </b><br>
                            <h6><a href="https://www.whatso.net/" target="_blank">Click here</a> for online chat support or Email us at <a href="mailto: hi@whatso.net" target="_blank">hi@whatso.net</a>.
                                You can also WhatsApp us on <a href="https://api.whatsapp.com/send?phone=+919099913506&text=hi" target="_blank">919106393472</a>.</h6>
                        </h5>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>