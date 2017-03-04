<?php

/**
 *
 */
class VHR_Helpers
{

  function __construct()
  {
    add_filter('wp_insert_post_data', array($this,'vhr_title_code'), '99', 2);
    add_filter('cmb2_show_on', array($this, 'vhr_exclude_from_new'), 10, 2);

  }

  function vhr_title_code($data, $postarr)
  {
      if ($data['post_type'] == 'ingresso') {
          $title = '#'.$postarr['ID'];
          $data['post_title'] = $title;
      }

      return $data;
  }


  public function register_new_page($new_page_title, $new_page_content, $new_page_template)
  {
      $new_page_id = null;

      $page_check = get_page_by_title($new_page_title);
      $new_page = array(
              'post_type' => 'page',
              'post_title' => $new_page_title,
              'post_content' => $new_page_content,
              'post_status' => 'publish',
              'post_author' => 1,
      );
      if (!isset($page_check->ID)) {
          $new_page_id = wp_insert_post($new_page);
          if (!empty($new_page_template)) {
              update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
          }
      }

      return $new_page_id;
  }

  function vhr_exclude_from_new($display, $meta_box)
  {
      if (!isset($meta_box['show_on']['alt_key'], $meta_box['show_on']['alt_value'])) {
          return $display;
      }

      global $pagenow;

    // Force to be an array
    $to_exclude = !is_array($meta_box['show_on']['alt_value'])
    ? array($meta_box['show_on']['alt_value'])
    : $meta_box['show_on']['alt_value'];

      $is_new_post = 'post-new.php' == $pagenow && in_array('post-new.php', $to_exclude);

      return !$is_new_post;
  }

}

new VHR_Helpers;
