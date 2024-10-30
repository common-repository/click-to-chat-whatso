<?php

/**
 * This class is meant to bundle miscellaneous functionalities
 */
class WHATSO_Utils
{
    
    private static $stateOptionName = WHATSO_SETTINGS_NAME;
    private static $view;

    /**
     * Setting a vew file to use. This method is used in 
     * controller files.
     */
    public static function setView( $view )
    {
        self::$view = $view;
    }
    
    /**
     * Getting the view file. Used in WHATSO_Menu_Link().
     */
    public static function getView()
    {
        
        $view = self::$view;
        
        $path_to_view = WHATSO_PLUGIN_DIR . 'view/' . $view . '.php';
        
        if (file_exists($path_to_view) ) {
            include_once $path_to_view;
        }
        else {
            if (! self::$view ) {
                
                echo '<p style="color: red;">' . esc_html__('Something is wrong: The view is not set yet. Please contact the developer.', 'whatso') . '</p>';
            }
            else {
                echo '<p style="color: red;">' . esc_html__('Something is wrong: The view not found. Please contact the developer.', 'whatso') . '</p>';
            }
        }
        
    }
    
    /**
     * Used only once during plugin activation. Making sure that 
     * we have the option.
     */
    public static function prepeareSettings()
    {
        add_option(self::$stateOptionName);
    }
    
    public static function updateSetting( $key, $value )
    {
        
        
        $option = get_option(self::$stateOptionName);
        
        $data = array();
        
        if ($option ) {
            $data = json_decode($option, true);
        }
        $data[ $key ] = $value;
        
        
        update_option(self::$stateOptionName, wp_json_encode($data), true);
        
        
    }
    
    public static function getSetting( $key, $default = '' )
    {
        
        $option = get_option(self::$stateOptionName);
        $data = json_decode($option, true);
        if ($data && isset($data[ $key ]) ) {
            return stripslashes($data[ $key ]);
            
        }
        return $default;
    }

    public static function generateCustomCSS()
    {
        $css = '
.whatso-container .whatso-toggle,
.whatso-container .whatso-mobile-close,
.whatso-container .whatso-description,
.whatso-container .whatso-description a {
	background-color: ' . WHATSO_Utils::getSetting('toggle_background_color', '#34aa91') . ';
	color: ' . WHATSO_Utils::getSetting('toggle_text_color', '#ffffff') . ';
}
.whatso-container .whatso-description p {
	color: ' . WHATSO_Utils::getSetting('toggle_text_color', '#ffffff') . ';
}
.whatso-container .whatso-toggle svg {
	fill: ' . WHATSO_Utils::getSetting('toggle_text_color', '#ffffff') . ';
}
.whatso-container .whatso-box {
	background-color: ' . WHATSO_Utils::getSetting('container_background_color', '#ffffff') . ';
}
.whatso-container .whatso-gdpr,
.whatso-container .whatso-account {
	color: ' . WHATSO_Utils::getSetting('container_text_color', '#555555') . ';
}
.whatso-container .whatso-account:hover {
	background-color: ' . WHATSO_Utils::getSetting('account_hover_background_color', '#f5f5f5') . ';
	border-color: ' . WHATSO_Utils::getSetting('account_hover_background_color', '#f5f5f5') . ';
	color: ' . WHATSO_Utils::getSetting('account_hover_text_color', '#555555') . ';
}
.whatso-box .whatso-account,
.whatso-container .whatso-account.whatso-offline:hover {
	border-color: ' . WHATSO_Utils::getSetting('border_color_between_accounts', '#f5f5f5') . ';
}
.whatso-container .whatso-account.whatso-offline:hover {
	border-radius: 0;
}

.whatso-container .whatso-box:before,
.whatso-container .whatso-box:after {
	background-color: ' . WHATSO_Utils::getSetting('container_background_color', '#ffffff') . ';
	border-color: ' . WHATSO_Utils::getSetting('container_background_color', '#ffffff') . ';
}
.whatso-container .whatso-close:before,
.whatso-container .whatso-close:after {
	background-color: ' . WHATSO_Utils::getSetting('toggle_text_color', '#ffffff') . ';
}

.whatso-button {
	background-color: ' . WHATSO_Utils::getSetting('button_background_color') . ' !important;
	color: ' . WHATSO_Utils::getSetting('button_text_color') . ' !important;
}
.whatso-button:hover {
	background-color: ' . WHATSO_Utils::getSetting('button_background_color_on_hover') . ' !important;
	color: ' . WHATSO_Utils::getSetting('button_text_color_on_hover') . ' !important;
}

.whatso-button.whatso-offline,
.whatso-button.whatso-offline:hover {
	background-color: ' . WHATSO_Utils::getSetting('button_background_color_offline') . ' !important;
	color: ' . WHATSO_Utils::getSetting('button_text_color_offline') . ' !important;
}

@keyframes toast {
	from {
		background: ' . WHATSO_Utils::getSetting('consent_alert_background_color', '#ff0000') . ';
		}
	
	to {
		background: ' . WHATSO_Utils::getSetting('container_background_color', '#ffffff') . ';
		}
}
	';
    
        $css_file = wp_upload_dir(null, true, false)['basedir'].'/whatso/auto-generated-whatso.css';
    
        file_put_contents($css_file, trim($css));
    
    }
    
}

?>
