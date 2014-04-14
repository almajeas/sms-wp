<?php

require_once('command.php');
if(!class_exists('Post'))
{
    class Post extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'post','post');
        }

        public function description(){
            return "adds the provided text as a new post";
        }
        public function format(){
            return "post text";
        }
        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_post_reply_send_sms', 'Get recent posts enabled?',array(__CLASS__, 'post_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function post_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_post_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_post_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_post_reply_send_sms'], false)  . ' />';
        }

        public static function post($sms){
          $post = array(
              'post_author' => 1,
              'post_name' => "New post from SMS",
              'post_content' => htmlspecialchars($sms),
              'post_status'   => 'publish',
              'post_type'   => 'post'
              );
          
          $new_id = wp_insert_post($post);
          
          set_post_format($new_id, 'status' );
          $new_sms = "posted id:[" . $new_id . ']'; 
         $options = get_option('plugin_options');
            if($options['sms_wp_command_post_reply_send_sms']){
                parent::send_sms($new_sms); 
            } 
        }

    }
    new Post();
}

    
?>