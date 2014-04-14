<?php

require_once('command.php');
if(!class_exists('PostNotification'))
{
    class PostNotification extends Command
    {
        public function __construct()
        {
            parent::add_command(__CLASS__,'post_notification','post_notification');
            add_action('publish_post',array(__CLASS__, 'post_notification'));
        }
        public function register_and_build_fields(){
            add_settings_field('sms_wp_notification_post_send_sms', 'Post reply enabled?',array(__CLASS__, 'post_notification_enable_send_sms_setting_call'), parent::get_settings_file(), 'notifications_section');
        }

        public function post_notification_enable_send_sms_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_notification_post_send_sms]" type="checkbox" name="plugin_options[sms_wp_notification_post_send_sms]" value="1"'.checked(1, $options['sms_wp_notification_post_send_sms'], false)  . ' />';
        }

        public function description(){
            return "notifies the user when a new post is made";
        }

        public function format(){
            return "N/A";
        }

        public static function post_notification($post_id){
            $post = get_post($post_id);
            $author = get_userdata($post->post_author);
            $format = "New post (ID %u) by %s (ID %d) (email %s) titled \"%s\"";
            $text = sprintf($format, $post->ID, $author->display_name, $author->ID, $author->user_email, $post->post_title);
            $options = get_option('plugin_options');
            if($options['sms_wp_notification_post_send_sms']){
                parent::send_sms($text);
            }
            return $new_sms;
        }
    }
    new PostNotification();
}

    
?>