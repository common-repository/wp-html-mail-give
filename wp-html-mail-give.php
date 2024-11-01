<?php
/*
Plugin Name: Give Donation - Email Template
Plugin URI: https://wordpress.org/plugins/wp-html-mail-give/
Description: Beautiful responsive mails for Give
Version: 1.1
Text Domain: wp-html-mail-give
Author: Hannes Etzelstorfer
Author URI: http://etzelstorfer.com
License: GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

/*  Copyright 2019 Hannes Etzelstorfer (email : hannes@etzelstorfer.com) */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 

function wphtmlmail_give_core_notice() {
    ?>
    <div class="updated">
        <p><?php printf( 
                    __( '<strong>Notice:</strong> To use the WP HTML Mail - Give integration please install the free WP HTML Mail plugin first. <a href="%s">Install Plugin</a>', 'wp-html-mail-give' ), 
                    wp_nonce_url( network_admin_url( 'update.php?action=install-plugin&plugin=wp-html-mail' ), 'install-plugin_wp-html-mail' )
            ); ?></p>
    </div>
    <?php
}

function wphtmlmail_give_init(){
    if(!is_plugin_active( 'wp-html-mail/wp-html-mail.php' )){
        add_action( 'admin_notices', 'wphtmlmail_give_core_notice' );
    }else{

        define( 'HAET_MAIL_GIVE_PATH', plugin_dir_path(__FILE__) );
        define( 'HAET_MAIL_GIVE_URL', plugin_dir_url(__FILE__) );


        require HAET_MAIL_GIVE_PATH . 'includes/class-haet-sender-plugin-give.php';
    }
}
add_action( 'plugins_loaded', 'wphtmlmail_give_init', 20 );



function haet_mail_register_plugin_give($plugins){

    $plugins['give']   =  array(
        'name'      =>  'give',
        'files'      => array( 'give/give.php', 'give/give.php' ),
        'class'     =>  'Haet_Sender_Plugin_Give',
        'display_name' => 'Give',
    );
    return $plugins;
}




function wphtmlmail_give_load() {
    load_plugin_textdomain('wp-html-mail-give', false, dirname( plugin_basename( __FILE__ ) ) . '/translations' );

    add_filter( 'haet_mail_available_plugins', 'haet_mail_register_plugin_give');
} 
add_action('plugins_loaded', 'wphtmlmail_give_load');









