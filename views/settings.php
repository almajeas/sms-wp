<?php
add_action( 'admin_menu',  'create_plugin_options_page' );
add_action('admin_init',  'register_and_build_fields'); 
   function create_plugin_options_page() {
        add_options_page( __( 'WP SMS Plugin', 'WP_SMS_Plugin' ), __( 'WP SMS', 'WP_SMS_Plugin' ), 'manage_options', __FILE__, 'build_options_page'  );
      }

      function build_options_page() {?>
            <div id="plugin-options-wrap" class="wrap">
                    <h2><?php _e( 'WP SMS', 'WP_SMS_Plugin' ); ?></h2>
                    <form method="post" action="options.php" enctype="multipart/form-data">
                        <?php settings_fields('plugin_options'); ?>
                        <?php do_settings_sections(__FILE__); ?>
                        <p class="submit">
                                <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
                        </p>
                    </form>
            </div>
<?php
      }
      function register_and_build_fields() {
         register_setting('plugin_options', 'plugin_options', 'validate_setting');
         add_settings_section('api_key_section', 'API Key Settings', 'api_key_section_call', __FILE__);
         add_settings_field('sms_wp_api_key', 'API Key:', 'api_key_setting_call', __FILE__, 'api_key_section');
         add_settings_section('commands_section', 'Commands Settings', 'commands_section_call', __FILE__);
         add_settings_section('notifications_section', 'Notifications Settings', 'notifications_section_call', __FILE__);
         
      }
      function validate_setting($plugin_options) {
          return $plugin_options;
      }
      function api_key_section_call() {}

      function api_key_setting_call() {  
          $options = get_option('plugin_options');
          echo "<input name='plugin_options[sms_wp_api_key]' type='text' value='{$options['sms_wp_api_key']}' />";
      }
      function commands_section_call(){}
      function notifications_section_call(){}

?>




