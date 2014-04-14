<?php

require_once('command.php');
if(!class_exists('CommentContents'))
{
    class CommentContents extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'comment_contents','comment_contents');
        }

        public function description(){
            return "retrieves the contents of the comment with id equal to comment_id";
        }

        public function format(){
            return "comment_contents comment_id";
        }

        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_comment_contents_enabled_send_sms', 'Comment reply enabled?',array(__CLASS__, 'comment_contents_enable_send_sms_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function comment_contents_enable_send_sms_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_comment_contents_enabled_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_comment_contents_enabled_send_sms]" value="1"'.checked(1, $options['sms_wp_command_comment_contents_enabled_send_sms'], false)  . ' />';
        }

        public static function comment_contents($sms){
            if(preg_match("/^\s+(\d+).*/", $sms, $parsed_sms)){
              if($parsed_sms[1]){
                $new_sms = get_comment_text( $parsed_sms[1] );
                if($new_sms){
                    // got something
                }else{
                    $new_sms = "comment contents failed";
                }
              }else{
                   $new_sms = "coudn't parse comment_contents id";
              }
            }else{
              $new_sms = "coudn't parse comment_contents";
            }
            $options = get_option('plugin_options');
            if($options['sms_wp_command_comment_contents_enabled_send_sms']){
                parent::send_sms($new_sms); 
            }
            return $new_sms;
        }
    }
    new CommentContents();
}
    
?>