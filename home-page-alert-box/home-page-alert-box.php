<?php
/*
Plugin Name: Home Page Alert Box
Description: This plugin adds the ability to create an alert box on the homepage
Plugin URI: http://www.beforesite.com/documentation/
Author: Andrew @ Geeenville Web Design
Author URI: http://www.beforesite.com
Version: 1.0
License: GPL2
Text Domain: hpab_lang
*/

/*
    Copyright (C) 2015  Rew Rixom  (email : rew@rixom.org)

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
if ( ! defined( 'ABSPATH' ) ) exit;

// load scripts
add_action( 'admin_enqueue_scripts', 'homepage_alert_box_admin_enqueue' );

function homepage_alert_box_admin_enqueue( $hook_suffix ) {

  $page  = get_current_screen()->base;
  if( $page == "dashboard" )
  {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'home-page-alert-box', plugins_url('home-page-alert-box.js', __FILE__ ), array( 'wp-color-picker' ), false, true );    
  }

}


function homepage_alert_box_frontpage_styles_enqueue() {
  wp_enqueue_style( 'hpab-css', plugins_url( 'hpab-wysiwyg-css.css', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'homepage_alert_box_frontpage_styles_enqueue' );

function plugin_mce_css( $mce_css ) {
  if ( ! empty( $mce_css ) )
    $mce_css .= ',';

  $mce_css .= plugins_url( 'hpab-wysiwyg-css.css', __FILE__ );

  return $mce_css;
}
add_filter( 'mce_css', 'plugin_mce_css' );

// Dash widget
add_action( 'wp_dashboard_setup', 'homepage_alert_box_add_dashboard_widgets' );

function homepage_alert_box_add_dashboard_widgets() {
 if ( !current_user_can( 'activate_plugins' ) ) return;
  wp_add_dashboard_widget(
    'homepage_alert_box_dashboard_widget',         // Widget slug.
    __('Home Page Alert Box','hpab_lang'),         // Title.
    'homepage_alert_box_dashboard_widget_function' // Display function.
  );
}

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function homepage_alert_box_dashboard_widget_function() {

  $hpab_is_active   = get_option( 'hpab_is_active', 'yes');
  $hpab_bg_color    = get_option( 'hpab_bg_color', '#E0491B' );
  $hpab_text_color  = get_option( 'hpab_text_color', '#FFFFFF' );
  $hpab_body_val    = get_option( 'hpab_body_val', '' );
  
  if( $hpab_is_active == 'yes' ) {
    $hpab_radio = '<input type="radio" name="hpab_is_active" value="yes" checked>Yes <input type="radio" name="hpab_is_active" value="no">No';
  }else{
    $hpab_radio = '<input type="radio" name="hpab_is_active" value="yes">Yes <input type="radio" name="hpab_is_active" value="no" checked>No';
  }

  echo('<div id="hpab-description-wrap" class="textarea-wrap">');  

  $hpab_wysiwyg_options = array(
      'media_buttons' => 0,
      'teeny'         => 0,
      'editor_height' => '250',
      'editor_class'  => 'hpab_wysiwyg',
      // 'editor_css'    => '<style>body#tinymce.wp-editor{background:#000!important;}</style>'
    );

  $hpab_wysiwyg_options = apply_filters('hpab_wysiwyg_options_filter', $hpab_wysiwyg_options );
  $hpab_wysiwyg_options['textarea_name'] = 'hpab_content';
  $hpab_content  = $hpab_body_val;
  $hpab_wysiwyg_contents = ($hpab_content!= "") ? stripslashes( $hpab_content ) : '' ;
  wp_editor(
    $hpab_wysiwyg_contents,
    'hpab_content',
    $hpab_wysiwyg_options
    );

  $hpab_lang_radio_label      = __('Activate Alert Box','hpab_lang');
  $hpab_lang_bgcolor_label    = __('Background Color','hpab_lang');
  $hpab_lang_textcolor_label  = __('Text Color','hpab_lang');
  $hpab_lang_save_bnt         = __('Save Alert Box','hpab_lang');



  echo('<p><label for="hpab_active">'.$hpab_lang_radio_label.':</label><br>'.$hpab_radio.'</p>');
  echo('</div><br class="clear">');
  echo('<div class="input-color-wrap">'.$hpab_lang_bgcolor_label.':<br> <input id="hpab_bg_color" name="hpab_bg_color" type="text" value="'.$hpab_bg_color.'" class="hpab-color-field" data-default-color="#E0491B"></div>');
  echo('<div class="input-color-wrap">'.$hpab_lang_textcolor_label.':<br> <input id="hpab_text_color" name="hpab_text_color" type="text" value="'.$hpab_text_color.'" class="hpab-color-field" data-default-color="#FFFFFF"></div>');
  echo('<p class="submit"><button type="button" class="button button-primary" id="save-hpab" >'.$hpab_lang_save_bnt.'</button></p><br class="clear">');
  
}

// Save via AJAX
// 
add_action( 'wp_ajax_homepage_alert_box_save',  'homepage_alert_box_save' );

function homepage_alert_box_save() {
  update_option( 'hpab_is_active',  $_REQUEST['hpab_is_active']  );
  update_option( 'hpab_bg_color',   $_REQUEST['hpab_bg_color']   );
  update_option( 'hpab_text_color', $_REQUEST['hpab_text_color'] );
  update_option( 'hpab_body_val',   $_REQUEST['hpab_body_val']   );
  homepage_alert_box_write_css();
  die('updated');
}

function homepage_alert_box_write_css()
{
  $file = plugin_dir_path( __FILE__ ).'hpab-wysiwyg-css.css';
  
  $hpab_bg_color     =  $_REQUEST['hpab_bg_color'];
  $hpab_text_color   =  $_REQUEST['hpab_text_color'];
  $hpab_body_val     =  $_REQUEST['hpab_body_val'];
 
  $css = "body#tinymce.hpab_content{
            background:$hpab_bg_color;
            background-color: $hpab_bg_color;
            color:$hpab_text_color;
          }";
  $css .= "div#hp_alert{
            background:$hpab_bg_color;
            background-color: $hpab_bg_color;
            color:$hpab_text_color;
            padding:20px;
            margin:10px 0;
          }";
  $css .= "div#hp_alert p,
          div#hp_alert div,
          div#hp_alert blockquote
          {
            color:$hpab_text_color !important;
          }";


  file_put_contents( $file, $css, LOCK_EX );
}


function homepage_alert_box_function($content) {
  if(!is_front_page()) {
    return $content;
  }

  $hpab_is_active   = get_option( 'hpab_is_active', 'yes');
  $hpab_body_val    = get_option( 'hpab_body_val', '' );

  if( $hpab_is_active == 'yes' && $hpab_body_val !== '' && $hpab_body_val !== false ) {
    
    $content = "<div id='hp_alert'>
                  $hpab_body_val
                </div>
                {$content}";
  }
  return $content;

}

add_filter( 'the_content', 'homepage_alert_box_function', 20 );

//EOF