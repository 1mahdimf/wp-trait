<?php

namespace WPTrait\Collection;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('WPTrait\Collection\Cache')) {

    class Cache
    {

        public function get($key, $group = '', $force = false, $found = null)
        {
            return wp_cache_get($key, $group, $force, $found);
        }

        public function delete($key, $group = '')
        {
            return wp_cache_delete($key, $group);
        }

        public function add($key, $data, $group = '', $expire = 0)
        {
            return wp_cache_add($key, $data, $group, $expire);
        }

        public function set($key, $data, $group = '', $expire = 0)
        {
            return wp_cache_set($key, $data, $group, $expire);
        }

        public function remember($key, $callback, $group = '', $expire = 0)
        {
            
            $cached = $this->get($key, $group, false, $found);

            if (!$cached) {
                return $cached;
            }

            $value = $callback();

            if (!is_wp_error($value)) {
                $this->set($key, $value, $group, $expire);
            }

            return $value;
        }

        public function forget($key, $group = '', $default = null)
        {
           
            $cached = $this->get($key, $group, false, $found);

            if (!$cached) {
                $this->delete($key, $group);
                return $cached;
            }

            return $default;
        }
    }
}
