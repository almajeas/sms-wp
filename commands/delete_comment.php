<?php

require_once('command.php');
if(!class_exists('DeleteComment'))
{
    class DeleteComment extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'delete_comment','delete_comment');
        }

        public function description(){
            return "deletes the comment with id equal to comment_id";
        }

        public function format(){
            return "delete_comment comment_id";
        }
        
        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_delete_comment_reply_send_sms', 'delete comment reply enabled?',array(__CLASS__, 'delete_comment_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function delete_comment_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_delete_comment_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_delete_comment_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_delete_comment_reply_send_sms'], false)  . ' />';
        }

        public static function delete_comment($sms){
            if(preg_match("/^\s+(\d+)(.*)/", $sms, $parsed_sms)){
              if($parsed_sms[1]){
                if(wp_delete_comment( $parsed_sms[1] )){
                    $new_sms = '[' . $parsed_sms[1] . ']' . ' deleted';
                }else{
                    $new_sms = "deleting comment failed";   
                }
              }else{
                  $new_sms = "coudn't parse delete_comment id";
              }
            }else{
              $new_sms = "coudn't parse delete_comment";
            }
            $options = get_option('plugin_options');
            if($options['sms_wp_command_delete_comment_reply_send_sms']){
                parent::send_sms($new_sms); 
            }
      }


    }
    new DeleteComment();
}

    
?>