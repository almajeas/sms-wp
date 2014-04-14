<?php

require_once('command.php');
if(!class_exists('UnapproveComment'))
{
    class UnapproveComment extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'unapprove_comment','unapprove_comment');
        }

        public function description(){
            return "marks the comment with id equal to comment_id as unnaproved";
        }

        public function format(){
            return "unapprove_comment comment_id";
        }

        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_unapprove_comment_reply_send_sms', 'unapprove comment reply enabled?',array(__CLASS__, 'unapprove_comment_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function unapprove_comment_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_unapprove_comment_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_unapprove_comment_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_unapprove_comment_reply_send_sms'], false)  . ' />';
        }

       public static function unapprove_comment($sms){
            if(preg_match("/^\s+(\d+).*/", $sms, $parsed_sms)){
              if($parsed_sms[1]){
                if(wp_set_comment_status( $parsed_sms[1] , 'hold', true )){
                    $new_sms = '[' . $parsed_sms[1] . ']' . ' unapproved';
                }else{
                    $new_sms = "unapproving comment failed";   
                }
              }else{
                   $new_sms = "coudn't parse unapprove_comment id";
              }
            }else{
              $new_sms = "coudn't parse unapprove_comment";
            }
            $options = get_option('plugin_options');
            if($options['sms_wp_command_unapprove_comment_reply_send_sms']){
                parent::send_sms($new_sms); 
            }
      }


    }
    new UnapproveComment();
}

    
?>