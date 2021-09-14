<?php

namespace WPTrait\Hook;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!trait_exists('AdminInit')) {

    trait AdminInit
    {

        public function bootAdminInit($arg = array())
        {
            $defaults = array(
                'method' => 'admin_init',
                'priority' => 10,
            );
            $args = wp_parse_args($arg, $defaults);

            $this->add_action('admin_init', $args['method'], $args['priority']);
        }

        public function admin_init()
        {
        }
    }

}