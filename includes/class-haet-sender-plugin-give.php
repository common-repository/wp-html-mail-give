<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
*   detect the origin of an email
*
**/
class Haet_Sender_Plugin_Give extends Haet_Sender_Plugin {
    public function __construct($mail) {
        if( !array_key_exists('give', $_POST) && !array_key_exists('give-form-id', $_POST) && !array_key_exists('give_action', $_GET) )
            throw new Haet_Different_Plugin_Exception();
    }



    /**
    *   modify_content()
    *   mofify the email content before applying the template
    **/
    public function modify_content($content){
        $content = wpautop($content);
        return $content;
    }

    /**
    *   modify_template()
    *   mofify the email template before the content is added
    **/
    public function modify_template($template){
        // $css = file_get_contents( HAET_MAIL_WPFORMS_PATH.'views/give/template/style.css' );
        // $template = str_replace('/**** ADD CSS HERE ****/', '/**** ADD CSS HERE ****/' . $css, $template);
        
        return $template;
    } 

    /**
    *   modify_styled_mail()
    *   mofify the email body after the content has been added to the template
    **/
    public function modify_styled_mail($message){
        return $message;
    }  


    public static function set_available_email_templates( $templates ){ 
        $templates['wphtmlmail'] = 'WP HTML Mail';
        return $templates;
    }


    // public static function set_selected_email_template( $template ){
    //     return 'wphtmlmail';
    // }


    public static function is_wphtmlmail_template_selected(){
        $template = give_get_option( 'email_template' );

        if( isset( $_POST['give-form-id'] ) && is_plugin_active( 'give-per-form-emails/give-per-form-emails.php' ) ){
            $form_id = $_POST['give-form-id'];

            $customized_option = get_post_meta( $form_id, '_gpfe_form_email_settings', true );

            if ( $customized_option == 'enabled' ) 
                $template = get_post_meta( $form_id, '_gpfe_email_template', true );
        }

        return 'wphtmlmail' == $template;
    }


    public static function remove_give_email_settings( $settings ){
        if( self::is_wphtmlmail_template_selected() ){
            for ($i=0; $i < count($settings); $i++) { 
                if( in_array( $settings[$i]['id'], array( 'email_logo') ) )
                    unset( $settings[$i] );
            }
        }
        return $settings;
    }


    public static function set_email_template_path( $file_paths ){
        $file_paths[2] = HAET_MAIL_GIVE_PATH . 'views/templates/';
        return $file_paths;
    }


    public static function apply_template_for_preview( $message ){
        if ( !empty( $_GET['give_action'] ) && 'preview_email' == $_GET['give_action'] ) {
            if( self::is_wphtmlmail_template_selected() ){
                $email = array(
                    'to'        => '', 
                    'subject'   => '', 
                    'message'   => $message, 
                    'headers'   => '', 
                    'attachments' => array()
                );

                $email = Haet_Mail()->style_mail( $email );
                $message = $email['message'];
            }
        }
        return $message;
    }


    public function use_template(){
        $plugin_options = $this->get_plugin_options();
        if(array_key_exists($this->current_plugin['name'], $plugin_options)){
            $use_template = $plugin_options[ $this->current_plugin['name'] ]['template'];
            if( $use_template )
                return self::is_wphtmlmail_template_selected();
        }else
            return true;
    }


    public static function plugin_actions_and_filters(){
        add_filter( 'give_email_templates', 'Haet_Sender_Plugin_Give::set_available_email_templates' );
        //add_filter( 'give_email_template', 'Haet_Sender_Plugin_Give::set_selected_email_template' );
        add_filter( 'give_get_settings_emails', 'Haet_Sender_Plugin_Give::remove_give_email_settings' );

        

        add_filter( 'give_template_paths', 'Haet_Sender_Plugin_Give::set_email_template_path' );

        add_filter( 'give_email_message', 'Haet_Sender_Plugin_Give::apply_template_for_preview' );
    }

}