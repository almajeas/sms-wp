<?php

require_once('command.php');
if(!class_exists('Comment'))
{
    class Comment extends Command
    {

        public function __construct()
        {
            parent::add_command(__CLASS__,'comment','comment');
        }

        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_comment_reply_send_sms', 'Comment reply enabled?',array(__CLASS__, 'comment_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function comment_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_comment_reply_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_comment_reply_send_sms]" value="1"'.checked(1, $options['sms_wp_command_comment_reply_send_sms'], false)  . ' />';
        }

        public function description(){
            return "adds the provided text as a comment to the post with id equal to post_id";
        }

        public function format(){
            return "comment post_id text";
        }

        public static function comment($sms){
            
            print("in");
            if(preg_match("/^\s+(\d+)(.*)/", $sms, $parsed_sms)){
                if(get_post($parsed_sms[1])){
                    $time = current_time('mysql');
                    $data = array(
                    'comment_post_ID' => $parsed_sms[1],
                    'comment_author' => 'admin',
                    'comment_content' => $parsed_sms[2],
                    'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
                    'comment_date' => $time,
                    'comment_approved' => 1,
                );
                wp_insert_comment($data);
                $new_sms = "comment posted";
                }else{
                    $new_sms = "couldn't parse post id for command comment";
                }
            }else{
                $new_sms = "couldn't parse command comment";
            }
            
            $options = get_option('plugin_options');
            if($options['sms_wp_command_comment_reply_send_sms']){
                parent::send_sms($new_sms); 
            }
            return $new_sms;
      }

    }
    new Comment();
}

    
?>