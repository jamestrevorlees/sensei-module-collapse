<?php
/*
 * Plugin Name: Sensei Module Collapse
 * Version: 1.0.0
 * Plugin URI: http://www.advantagelearn.co.za
 * Description: Add collapsable modules to your sensei courses
 * Author: Pango
 * Author URI: http://www.advantagelearn.co.za
 * Requires at least: 3.5
 * Tested up to: 3.8
 *
 * @package WordPress
 * @author Pango
 * @since 1.0.0
 */

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Functions used by plugins
 */
if ( ! class_exists('WooThemes_Sensei_Dependencies')) {
    require_once 'woo-includes/class-woothemes-sensei-dependencies.php';
}
/**
     * Sensei Detection
     */
if ( ! function_exists('is_sensei_active')) {
    function is_sensei_active() {
    return WooThemes_Sensei_Dependencies::sensei_active_check();
    }
}
/**
     * Include plugin class
     */
if (is_sensei_active()) {
    require_once('classes/class-sensei-module-collapse.php');

    global $sensei_module_collapse;
    $sensei_module_collapse = new Sensei_Module_Collapse(__FILE__);
}