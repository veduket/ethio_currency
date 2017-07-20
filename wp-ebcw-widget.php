<?php
  /*
  Plugin Name: ET Buna Currency Widget
  Plugin URI: http://
  Description: Get daily exchange rates for ETB and calculate currency conversion and price per pound calculation at your finger tips
  Version: 1.1
  Author: Yared Getachew
  Author URI: http://veduket.wordpress.com
  */
  /*
  Copyright 2017
  Yared Getachew (email : veduket@gmail.com)
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301
  USA  */
  // Call function when plugin is activated
  register_activation_hook(__FILE__,'ebcw_install');
  // Action hook to initialize the plugin
  add_action('admin_init', 'ebcw_init');
  // Action hook to register our option settings
  add_action( 'admin_init', 'ebcw_register_settings' );
  // Action hook to add the post products menu item
  add_action('admin_menu', 'ebcw_menu');
  // Action hook to save the meta box data when the post is saved
  add_action('save_post','ebcw_save_meta_box');
  // Action hook to create the post products shortcode
  add_shortcode('pp', 'ebcw_shortcode');
  // Action hook to create plugin widget
  add_action( 'widgets_init', 'ebcw_register_widgets');
  //register styles and js files
  add_action('init', 'register_scripts');
  add_action('wp_enqueue_scripts', 'enqueue_scripts');
  function register_scripts(){
    //CSS
    wp_register_style("patternfly",plugin_dir_url(__FILE__)."assets/css/patternfly.min.css",false);
    wp_register_style("patternfly-additions",plugin_dir_url(__FILE__)."assets/css/patternfly-additions.min.css",false);
    wp_register_style("bootstrap_select",plugin_dir_url(__FILE__)."assets/css/bootstrap-select.min.css",false);
    wp_register_style("fontawesome",plugin_dir_url(__FILE__)."assets/css/font-awesome.min.css",false);
    //JS
    wp_register_script('jquery',plugin_dir_url(__FILE__)."assets/js/jquery.min.js",false);
    wp_register_script('bootstrapjs',plugin_dir_url(__FILE__)."assets/js/bootstrap.min.js",false);
    wp_register_script('patternflyjs',plugin_dir_url(__FILE__)."assets/js/patternfly.min.js",false);
    wp_register_script('dateformat',plugin_dir_url(__FILE__)."assets/js/date.format.js",false);
    // wp_register_script('numberformat',plugin_dir_url(__FILE__)."assets/js/jquery.number.min.js",false,array('jquery'));

  }

  //enque scripts
  function enqueue_scripts(){
    wp_enqueue_style("patternfly");
    wp_enqueue_style("patternfly-additions");
    wp_enqueue_style("bootstrap_select");
    wp_enqueue_style("fontawesome");

    wp_enqueue_script("jquery");
    wp_enqueue_script("bootstrapjs");
    wp_enqueue_script("patternflyjs");
    wp_enqueue_script("dateformat");
    // wp_enqueue_script("numberformat");
  }

  function ebcw_register_settings(){
    //register our array of settings
    register_setting( 'ebcw-settings-group', 'ebcw_options' );
  }
  function ebcw_settings_page(){  }

  function ebcw_install() {
    //
  }
  //create the post products sub-menu
  function ebcw_menu() {
    //add_options_page(__('Post Products Settings Page','ebcw-plugin'),__('Post Products Settings','ebcw-plugin'), 'administrator',__FILE__, 'ebcw_settings_page');
  }
  //create post meta box
  function ebcw_init() {
    // create our custom meta box
    add_meta_box('ebcw-meta',__('Easy to use Currency exchange rate and conversion widget','ebcw-plugin'), 'ebcw_meta_box','post','side','default');
  }
  //create shortcode
  function ebcw_shortcode($atts, $content = null) {
    //
  }
  //build post product meta box
  function ebcw_meta_box($post,$box) { }

  //save meta box data
  function ebcw_save_meta_box($post_id,$post) {}
  //register our widget
  function ebcw_register_widgets() {
    register_widget( 'ebcw_widget' );
  }
  //ebcw_widget class
  class ebcw_widget extends WP_Widget {
    function ebcw_widget() {
      $defaults = array( 'title' => __('Products','pp-plugin'),  'number_products' => '' );
      $instance = wp_parse_args( (array) $instance, $defaults );
      $title = strip_tags($instance['title']);
      $widget_ops = array('classname' => 'ebcw_widget','description' => __('Easily calculate currency and price per pound','ebcw-plugin') );
      $this->WP_Widget('ebcw_widget', __('ET Buna Currency Tool','ebcw-plugin'),  $widget_ops);
    }
    //build our widget settings form
    function form($instance) {}
    //save our widget settings
    function update($new_instance, $old_instance) {
      //
    }
    //display our widget
    function widget($args, $instance) {
      global $post;
      extract($args);
      echo $before_widget;
      $title = apply_filters('widget_title', $instance['title'] );
      $number_products = empty($instance['number_products']) ?   '&nbsp;' : apply_filters('widget_number_products', $instance['number_products']);
      if (!empty( $title ) ) { echo $before_title. $title. $after_title; };
      require_once(__DIR__."/_ui_widget.php");
      echo $after_widget;
    }
  } ?>
