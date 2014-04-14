<?php

require_once('command.php');
if(!class_exists('AddUser'))
{
    class AddUser extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'add_user','add_user');
        }

        public function description(){
            return "adds a new user to the wordpress site with the username and user_email specified and emails them their password. Optionally, a role can be specified for the user.";
        }
        public function format(){
            return "add_user username user_email [role]";
        }
        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_add_user_reply_send_sms', 'Add user reply enabled?',array(__CLASS__, 'add_user_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function add_user_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_add_user_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_add_user_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_add_user_reply_send_sms'], false)  . ' />';
        }

        public static function add_user($sms){
          if(preg_match("/^\s+(\S+)\s+(\S+)\s+(\S+).*/", $sms, $parsed_sms)){
            $username = $parsed_sms[1];
            $user_email = $parsed_sms[2];
            $role = $parsed_sms[3];
            if (!username_exists($username)){
              $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
              $user_info = array(
                'user_login' => $username,
                'user_pass' => $random_password,
                'user_email' => $user_email,
                'role' => $role);
              $user_id = wp_insert_user($user_info);
              if( is_wp_error($user_id)){
                $new_sms = 'Error: ' . $user_id->get_error_message();
              } else {
                wp_new_user_notification( $user_id, $random_password);
                $new_sms = $role . ' created successfully. Password (' . $random_password . ') sent to registered email.';
              }
            }else {
              $new_sms = 'User creation unsuccessful. Username already exists.';
            }
          } else if(preg_match("/^\s+(\S+)\s+(\S+).*/", $sms, $parsed_sms2)){
            $username = $parsed_sms2[1];
            $user_email = $parsed_sms2[2];
            if ( !username_exists($username) ) {
              $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
              $user_id = wp_create_user( $username, $random_password, $user_email );
              if( is_wp_error($user_id)){
                $new_sms = 'Error: ' . $user_id->get_error_message();
              } else {
                wp_new_user_notification( $user_id, $random_password);
                $new_sms = 'User created successfully. Password (' . $random_password . ') sent to registered email.';
              }
            } else {
              $new_sms = 'User creation unsuccessful. Username already exists.';
            }
          } else {
            $new_sms = 'Unable to parse new user information';
          }
          $options = get_option('plugin_options');
          if($options['sms_wp_command_add_user_reply_send_sms']){
            parent::send_sms($new_sms);
          }
        }
    }
    new AddUser();
}

    
?>