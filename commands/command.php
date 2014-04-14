<?php
define("SMS_WP_DIRECTORY", ABSPATH . 'wp-content'.DIRECTORY_SEPARATOR . 'plugins' .DIRECTORY_SEPARATOR . 'sms-wp'. DIRECTORY_SEPARATOR);
define( 'SETTINGS_FILE', SMS_WP_DIRECTORY . 'views'. DIRECTORY_SEPARATOR. 'settings.php');
abstract class Command{
    abstract public function description();
    abstract public function format();
    abstract public function register_and_build_fields();
    public function get_settings_file(){
        //replace it with the constant SETTINGS_FILE
        //return 'C:\Users\almajeas\Documents\My Web Sites\WordPress\wp-content\plugins\sms-wp\views\settings.php';
      return SETTINGS_FILE;
      //return 'C:\wamp\www\wordpress\wp-content\plugins\sms-wp\views\settings.php';
    }
    public function send_sms($message)
      {
        global $wp_sms_plugin;
        $wp_sms_plugin->send_sms($message);
      }

      public function add_command($class, $command, $callback ) {
        global $wp_sms_plugin;
        $wp_sms_plugin->commands[$command] = array(__( $class, $class ), $callback);
        add_action('admin_init', array($class, 'register_and_build_fields'));
      }

}
?>