<?php

require_once('command.php');
if(!class_exists('DeletePost'))
{
    class DeletePost extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'delete_post','delete_post');
        }


        public function description(){
            return "deletes the post with id equal to post_id";
        }

        public function format(){
            return "delete_post post_id";
        }
        
        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_delete_post_reply_send_sms', 'delete post reply enabled?',array(__CLASS__, 'delete_post_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function delete_post_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_delete_post_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_delete_post_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_delete_post_reply_send_sms'], false)  . ' />';
        }

        public static function delete_post($sms){
            if(preg_match("/^\s+(\d+)/", $sms, $parsed_sms)){
              if(get_post($parsed_sms[1])){
                  if(wp_delete_post($parsed_sms[1])) {
                    $new_sms = "deleted post";
                  } else {
                    $new_sms = "failed to delete post";
                  }
              }else{
                $new_sms = "invalid post id for delete_post command";
              }
            }else{
              $new_sms = "couldn't parse delete_post command";
            }
            $options = get_option('plugin_options');
            if($options['sms_wp_command_delete_post_reply_send_sms']){
                parent::send_sms($new_sms); 
            }
            return $new_sms;
      }

    }
    new DeletePost();
}

    
?>