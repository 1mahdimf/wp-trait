<?php

namespace WPTrait;

use WPTrait\Collection\Hooks;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('Plugin')) {

    abstract class Plugin
    {
        use Hooks;

        public $plugin;

        public function __construct($slug, $args = array())
        {
            // Set Plugin Slug
            $this->plugin = new \stdClass();
            $this->plugin->slug = $slug;

            // Check Custom argument
            $default = array(
                'main_file' => WP_PLUGIN_DIR . '/' . $slug . '/' . $slug . '.php',
                'global' => $this->sanitize_plugin_slug($slug),
                'prefix' => $this->sanitize_plugin_slug($slug)
            );
            $arg = wp_parse_args($args, $default);

            // Set Main File
            $this->plugin->main_file = $arg['main_file'];

            // Set Prefix
            $this->plugin->prefix = $arg['prefix'];

            // Define Variable
            $this->define_constants();

            // include PHP files
            if (method_exists($this, 'includes')) {
                $this->includes();
            }

            // init Wordpress hook
            $this->init_hooks();

            // Set Global Function
            if (!empty($arg['global']) and !is_null($arg['global'])) {
                $GLOBALS[$arg['global']] = $this;

                // Create global function for backwards compatibility.
                $function = 'function ' . $arg['global'] . '() { return $GLOBALS[\'' . $arg['global'] . '\']; }';
                eval($function);
            }

            // Instantiate Object Class
            $this->instantiate();

            // Plugin Loaded Action
            do_action($this->plugin->prefix . '_loaded');
        }

        public function __get($name)
        {
            return $this->$name;
        }

        protected function define_constants()
        {
            if (!function_exists('get_plugin_data')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }

            $this->plugin = (object)array_merge((array)$this->plugin, (array)array_change_key_case(get_plugin_data($this->plugin->main_file), CASE_LOWER));
            $this->plugin->url = plugins_url('', $this->plugin->main_file);
            $this->plugin->path = plugin_dir_path($this->plugin->main_file);
        }

        protected function includes()
        {
        }

        protected function instantiate()
        {
        }

        protected function init_hooks()
        {
            // Load Text Domain
            if (isset($this->plugin->textdomain) and !empty($this->plugin->textdomain)) {
                load_plugin_textdomain($this->plugin->textdomain, false, wp_normalize_path($this->plugin->path . '/languages'));
            }

            // register_activation_hook
            if (method_exists($this, 'register_activation_hook')) {
                register_activation_hook($this->plugin->main_file, array($this, 'register_activation_hook'));
            }

            // register_deactivation_hook
            if (method_exists($this, 'register_deactivation_hook')) {
                register_deactivation_hook($this->plugin->main_file, array($this, 'register_deactivation_hook'));
            }

            // register_uninstall_hook
            if (method_exists($this, 'register_uninstall_hook')) {
                register_uninstall_hook($this->plugin->main_file, array(__CLASS__, 'register_uninstall_hook'));
            }
        }

        protected function register_activation_hook()
        {
        }

        protected function register_deactivation_hook()
        {
        }

        protected static function register_uninstall_hook()
        {
        }

        protected function sanitize_plugin_slug($slug)
        {
            return str_replace("-", "_", trim($slug));
        }
    }

}
