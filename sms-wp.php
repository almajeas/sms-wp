<?php
/**
 * Plugin Name: WP SMS Plugin
 * Plugin URI: http://www.codeawhile.com
 * Description: Plugin capable of handling commands sent by SMS services and sending texts
 * Version: 1.0
 * Author: Team Wordpress
 * Author URI: http://www.codeawhile.com
 * License: A "Slug" license name e.g. GPL2
 */

/*  Copyright 2013  Team Wordpress  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if(!class_exists('WP_SMS_Plugin'))
{

    class WP_SMS_Plugin
    {
      
      var $commands;
      const API_KEY = 'API_Key';
      const DEFAULT_API_KEY = 'Test default value';

        /**
         * Construct the plugin object
         */
      public function __construct()
      {
         // add_action('wp_footer', array(&$this, 'send_sms'));
          add_action('init', array(&$this, 'receive_sms'));
          $this->commands   = array();
          require_once(plugin_dir_path( __FILE__ ) . 'views/settings.php');
      }

      public function initialize_commands(){
          define("ABS_PATH", dirname(__FILE__));
          define('COMMANDS_PATH', DIRECTORY_SEPARATOR . 'commands');
          $commands_path = ABS_PATH . COMMANDS_PATH .DIRECTORY_SEPARATOR;

          require_once( $commands_path .'add_user.php');
          require_once( $commands_path .'post.php');
          require_once( $commands_path .'get.php');
          require_once( $commands_path .'comment.php');
          require_once( $commands_path .'approve_comment.php');
          require_once( $commands_path .'unapprove_comment.php');
          require_once( $commands_path .'delete_post.php');
          require_once( $commands_path .'unapproved_comments.php');
          require_once( $commands_path .'get_post_comments.php');
          require_once( $commands_path .'append_to_post.php');
          require_once( $commands_path .'comment_notification.php');
          require_once( $commands_path .'post_notification.php');
          require_once( $commands_path .'comment_contents.php');
          require_once( $commands_path .'delete_comment.php');
          require_once( $commands_path .'spam_comment.php');
          
          
      }

      public function execute_command($parsed_sms){
        $command = $parsed_sms[1];
        if ( isset( $this->commands[$command] ) ) {
          return call_user_func( $this->commands[$command], $parsed_sms[2] );
        } else {
         return $this->send_sms("unrecognized command");
        }
      }

      

      public static function get_api_key(){
          $options = get_option('plugin_options'); 

        return $options['sms_wp_api_key'];
      }

      public function send_sms($message)
      {
        $body = array( 
          'number' => '9187607543',
          'sms' => $message,
          'api_key' => $this->get_api_key()
        );   
        $url = 'http://ec2-54-200-114-147.us-west-2.compute.amazonaws.com:8080/reciever';
        $request = new WP_Http;
        $result = $request->request( $url, array('method' => 'POST', 'body' => $body));
        echo $result;
      }
    
      public function receive_sms()
      {
        if(isset( $_POST["api_key"]) && $_POST["api_key"] == $this->get_api_key()){
          if( isset( $_POST["sms_text"])){
            $parsed_sms = $this->parse_sms($_POST["sms_text"]);
            if($parsed_sms[0] == 'valid'){
              $this->execute_command($parsed_sms);
            }else{
              $this->send_sms("undefined command");
            }
          }
          // Prevent WordPress from simply outputing the homepage
          die();
        }else if(isset($_GET["commandlist"])){
          header("Access-Control-Allow-Origin: *");
          ksort($this->commands);
          foreach ($this->commands as $key => $value){
            $name = $key;
            $format = call_user_func(array($value[0],'format'));
            $description = call_user_func(array($value[0],'description'));
            echo '<tr><td>'.$name.'</td><td>'.$format.'</td><td>'.$description.'</td></tr>';
          }
          die();
        }
      }

      public function parse_sms($sms_text)
      {
        if(preg_match("/^(\w+)(.*)/", $sms_text, $matches)){
          $matches[0] = "valid";
          $matches[1] = strtolower($matches[1]);
        }else{
          $matches[0] = "invalid";
        }
        return $matches;
      }

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        } // END public static function activate

        /**
         * Deactivate the plugin
         */     
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate
    } // END class SMS_WP_Plugin
} // END if(!class_exists('SMS_WP_Plugin'))
if(class_exists('WP_SMS_Plugin'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('WP_SMS_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('WP_SMS_Plugin', 'deactivate'));

    // instantiate the plugin class
    global $wp_sms_plugin;
    $wp_sms_plugin = new WP_SMS_Plugin();
    
    $wp_sms_plugin->initialize_commands();

    
}

?>
