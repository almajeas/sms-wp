<?php

require_once('command.php');
if(!class_exists('GetPostComments'))
{
    class GetPostComments extends Command
    {
        public function __construct()
        {
            parent::add_command(__CLASS__,'get_post_comments','get_post_comments');
        }

        public function description(){
            return "retrieves the comments made on the post with id equal to post_id";
        }

        public function format(){
            return "get_post_comments post_id";
        }

        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_get_post_comments_enabled_send_sms', 'Get post comments enabled?',array(__CLASS__, 'get_post_comments_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function get_post_comments_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_get_post_comments_enabled_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_get_post_comments_enabled_send_sms]" value="1"'.checked(1, $options['sms_wp_command_get_post_comments_enabled_send_sms'], false)  . ' />';
        }

        public static function get_post_comments($sms){
            if(preg_match("/^\s+(\d+)/", $sms, $parsed_sms)){
              $args;
              if($parsed_sms[1]){
                  $args['post_id'] = $parsed_sms[1]; 
                  $commnets = get_comments($args);
                  $sms = "comments:\n[id][status][body]\n";
                  foreach($commnets as $commnet){
                    $new_sms = $new_sms . '[' . $commnet->comment_ID . ']' . '[' . $commnet->comment_approved . ']'. '[' . $commnet->comment_content . "]\n";
                  }
              }else{
                $new_sms = "couldn't parse id for get_post+comments command";
              }
            }else{
              $new_sms = "couldn't parse get_post_comments command";
            }
            $options = get_option('plugin_options');
            if($options['sms_wp_command_get_enabled_send_sms']){
                parent::send_sms($new_sms); 
            }
            return $new_sms;
      }

    }
    new GetPostComments();
}

    
?>