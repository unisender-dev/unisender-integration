<?php
/*
Plugin Name: UniSender
Plugin URI: http://www.unisender.com/
Description: Integrate the blog with UniSender newsletter delivery service
Version: 1.7.1
Author: UniSender
Author URI: http://www.unisender.com/
License: GPL2

Copyright (c) 2010. UniSender Software Ltd.  (email : plugins@unisender.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

require_once 'unisender_plugin.php';

/*init i18n*/
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'unisender', 'wp-content/plugins/' . $plugin_dir.'/i18n', $plugin_dir.'/i18n');

$usPlugin = new UnisenderPlugin();
$usPlugin->addOptions();

$usPlugin->initAdminMenu();

$usPlugin->processForm();

function widget_unisenderSubscribeForm() {

    global $unisender_subscribe_form_errors;
    if(!empty($unisender_subscribe_form_errors))
    {

        if(is_array($unisender_subscribe_form_errors))
        {
            $errors = implode("<br/>",$unisender_subscribe_form_errors);
        }
        elseif(is_string($unisender_subscribe_form_errors))
        {
            $errors = $unisender_subscribe_form_errors;
        }
	    echo "<script type='text/javascript'>jQuery(document).ready(function(){alert('".htmlentities($errors)."');});</script>";
    }
    echo get_option('unisender_subscribe_form');
}

function unisenderSubscribeForm_init()
{
	if ( function_exists('wp_register_sidebar_widget') ) {
		wp_register_sidebar_widget('%d1%84%d0%be%d1%80%d0%bc%d0%b0-%d0%bf%d0%be%d0%b4%d0%bf%d0%b8%d1%81%d0%ba%d0%b8-unisender', __('Unisender subscribe form',"unisender"), 'widget_unisenderSubscribeForm');
	} else {
		register_sidebar_widget(__('Unisender subscribe form',"unisender"), 'widget_unisenderSubscribeForm');
	}
}

add_action("plugins_loaded", "unisenderSubscribeForm_init");



?>
