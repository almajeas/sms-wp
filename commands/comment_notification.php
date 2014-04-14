<?php

require_once('command.php');
if(!class_exists('CommentNotification'))
{
    class CommentNotification extends Command
    {
        public function __construct()
        {
            parent::add_command(__CLASS__,'comment_notification','comment_notification');
            add_action('wp_insert_comment',array(__CLASS__, 'comment_notification'));
        }
        public function register_and_build_fields(){
            add_settings_field('sms_wp_notification_comment_send_sms', 'Comment reply enabled?',array(__CLASS__, 'comment_notification_enable_send_sms_setting_call'), parent::get_settings_file(), 'notifications_section');
        }

        public function comment_notification_enable_send_sms_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_notification_comment_send_sms]" type="checkbox" name="plugin_options[sms_wp_notification_comment_send_sms]" value="1"'.checked(1, $options['sms_wp_notification_comment_send_sms'], false)  . ' />';
        }

        public function description(){
            return "notifies the user when a new comment is made";
        }

        public function format(){
            return "N/A";
        }

        public static function comment_notification($comment_id, $comment_object){
            $comment = get_comment($comment_id);
            $post = get_post($comment->comment_post_ID);
            $format = "New comment (ID %u) by %s (email %s) on post (ID %u) titled \"%s\"";
            $new_sms = sprintf($format, $comment_id, $comment->comment_author, $comment->comment_author_email, $comment->comment_post_ID, $post->post_title, $comment->comment_parent, $comment->user_id);
            $options = get_option('plugin_options');
            if($options['sms_wp_notification_comment_send_sms']){
                parent::send_sms($new_sms);
            }
            return $new_sms;
        }
    }
    new CommentNotification();
}

    
?>