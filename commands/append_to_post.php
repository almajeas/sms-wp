<?php

require_once('command.php');
if(!class_exists('AppendToPost'))
{
    class AppendToPost extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'append_to_post','append_to_post');
        }

        public function description(){
            return "appends the provided text to the post with id equal to post_id";
        }

        public function format(){
            return "append_to_post post_id text";
        }
        
        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_append_to_post_reply_send_sms', 'append to post reply enabled?',array(__CLASS__, 'append_to_post_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function append_to_post_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_append_to_post_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_append_to_post_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_append_to_post_reply_send_sms'], false)  . ' />';
        }

        public static function append_to_post($sms){
        if(preg_match("/^\s+(\d+)\s(.*)/", $sms, $parsed_sms)){
          $post = get_post($parsed_sms[1]);
          if ($post) {
            $updated_post = array(
                  'ID' => $parsed_sms[1],
                  'post_content' => $post->post_content . htmlspecialchars($parsed_sms[2]),
                  );
            $post_id = wp_update_post($updated_post);
            if($post_id == 0){
              $new_sms = "failed to append to post";
            } else {
              $new_sms = "successfully appended to post";
            }
          } else {
            $new_sms = "invalid post id for append_to_post command";
          }
        }else{
          $new_sms = "couldn't parse append_to_post arguments";
        }
        $options = get_option('plugin_options');
        if($options['sms_wp_command_append_to_post_reply_send_sms']){
          parent::send_sms($new_sms);
        }
        return $new_sms;
      }

    }
    new AppendToPost();
}

    
?>