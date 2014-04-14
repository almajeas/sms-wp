<?php

require_once('command.php');
if(!class_exists('Get'))
{
    class Get extends Command
    {
        public function __construct()
        {
            parent::add_command(__CLASS__,'get','get_recent_posts');
        }

        public function description(){
            return "retrieves the contents and ids of the last n posts made";
        }

        public function format(){
            return "get n";
        }
        
        public function register_and_build_fields(){
            add_settings_field('sms_wp_command_get_enabled_send_sms', 'Get recent posts enabled?',array(__CLASS__, 'get_enable_send_sms_reply_setting_call'), parent::get_settings_file(), 'commands_section');
        }

        public function get_enable_send_sms_reply_setting_call(){
          $options = get_option('plugin_options');
          echo  '<input name="plugin_options[sms_wp_command_get_enabled_send_sms]" type="checkbox" name="plugin_options[sms_wp_command_get_enabled_send_sms]" value="1"'.checked(1, $options['sms_wp_command_get_enabled_send_sms'], false)  . ' />';
        }

        public static function get_recent_posts($sms){
            if(preg_match("/^\s+(\d+)(.*)/", $sms, $matches)){
              $args = array(
                  'numberposts' => $matches[1],
                  'offset' => 0,
                  'category' => 0,
                  'orderby' => 'post_date',
                  'order' => 'DESC',
                  'include' => '',
                  'exclude' => '',
                  'meta_key' => '',
                  'meta_value' => '',
                  'post_type' => 'post',
                  'post_status' => 'draft, publish, future, pending, private',
                  'suppress_filters' => true );
              $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
              $new_sms = "";
              foreach($recent_posts as $post){
                $new_sms = $new_sms . '[' . $post['ID'] . ']' . $post['post_title'] . " : " . $post['post_content'] . "\n";
              }
            }else{
              $new_sms = "coudn't parse the number of requested posts";
            }
            $options = get_option('plugin_options');
            if($options['sms_wp_command_get_enabled_send_sms']){
                parent::send_sms($new_sms); 
            } 
            return $new_sms;
        }
    }

    new Get();
}

    
?>