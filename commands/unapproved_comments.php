<?php

require_once('command.php');
if(!class_exists('UnapproveComments'))
{
    class UnapproveComments extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'get_unapproved_comments','get_unapproved_comments');
        }

        public function description(){
            return "retrieves the ids and contents of all unapproved comments made on the post with id equal to post_id";
        }

        public function format(){
            return "get_unapproved_comments post_id";
        }

        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_unapproved_comments_reply_send_sms', 'approve comment reply enabled?',array(__CLASS__, 'unapproved_comments_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function unapproved_comments_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_unapproved_comments_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_unapproved_comments_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_unapproved_comments_reply_send_sms'], false)  . ' />';
        }

        public static function get_unapproved_comments($sms){
            if(preg_match("/^\s*(\d*)(.*)/", $sms, $parsed_sms)){
                $args = array(
                'status' => 'hold',
                );
                if($parsed_sms[1]){
                    $args['post_id'] = $parsed_sms[1]; 
                }
                $unapproved_commnets = get_comments($args);
                $new_sms = "unapproved comments:\n";
                foreach($unapproved_commnets as $unapproved_commnet){
                $new_sms = $new_sms . '[' . $unapproved_commnet->comment_ID . ']' . '[' . $unapproved_commnet->comment_content . "]\n";
                }
            }else{
                $new_sms = "couldn't parse get_unapproved_comments arguments";
            }
            $options = get_option('plugin_options');
            if($options['sms_wp_command_unapproved_comments_reply_send_sms']){
                parent::send_sms($new_sms); 
            }
      }

    }
    new UnapproveComments();
}

    
?>