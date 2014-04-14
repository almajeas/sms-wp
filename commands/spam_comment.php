<?php

require_once('command.php');
if(!class_exists('SpamComment'))
{
    class SpamComment extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'spam_comment','spam_comment');
        }

        public function description(){
            return "marks the comment with id equal to comment_id as spam";
        }

        public function format(){
            return "spam_comment comment_id";
        }
        
        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_spam_comment_reply_send_sms', 'spam comment reply enabled?',array(__CLASS__, 'spam_comment_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function spam_comment_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_spam_comment_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_spam_comment_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_spam_comment_reply_send_sms'], false)  . ' />';
        }

        public static function spam_comment($sms){
            if(preg_match("/^\s+(\d+)(.*)/", $sms, $parsed_sms)){
              if($parsed_sms[1]){
                if(wp_spam_comment( $parsed_sms[1] )){
                    $new_sms = '[' . $parsed_sms[1] . ']' . ' set as spam';
                }else{
                    $new_sms = "spamming comment failed";   
                }
              }else{
                  $new_sms = "coudn't parse spam_comment id";
              }
            }else{
              $new_sms = "coudn't parse spam_comment";
            }
            $options = get_option('plugin_options');
            if($options['sms_wp_command_spam_comment_reply_send_sms']){
                parent::send_sms($new_sms); 
            }
            return $new_sms;
      }


    }
    new SpamComment();
}

    
?>