<?php

/*
 * Plugin Name: iHumbak Redirect Post To Category Post
 * Version: 1.0
 *
 */
defined( 'ABSPATH' ) || exit;
include_once plugin_dir_path(__FILE__).'updater.php';

add_action('template_redirect', 'ihrptcp_redirect');
function ihrptcp_redirect() {
  $slug = str_replace('/','',$_SERVER['REQUEST_URI']);
  $permalink_structure= '/%category%/%postname%/';
  $args = [
    'posts_per_page' => 1,
    'name'=>$slug,
    'post_type' => 'post',
  ];
  $posts = get_posts($args);
  if (!empty($slug) && !empty($posts) && get_option('permalink_structure') == $permalink_structure){

    if (wp_redirect(get_permalink($posts[0]), 301)) {
      exit;
    }
  }
}