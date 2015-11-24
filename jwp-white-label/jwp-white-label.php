<?php

/*
Plugin Name: JWP White Label
Description: Implements white labelling for a more user-friendly CMS.
Author: Sherwin Aborot - Jazel, LLC.
Version: 1.0
*/





/*
 * Add Favicon
**/
function jazel_admin_favicon() {
    
    echo '<link href="' . plugins_url('../images/icon.gif', __FILE__) .'" rel="icon" type="image/x-icon">';
}
add_action('admin_head', 'jazel_admin_favicon');





/*
 *  Hide Update Nags
**/
function hide_update_notice_to_all_but_admin_users()
{
    if (!current_user_can('update_core')) {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}
add_action( 'admin_head', 'hide_update_notice_to_all_but_admin_users', 1 );





/*
 * Login
**/ 

function change_wp_login_url() {
    return 'http://www.jazelmotors.com';
}
add_filter('login_headerurl', 'change_wp_login_url');
 
function change_wp_login_title() {

    return 'Jazel Motors: Reinventing Car Shopping';
}
add_filter('login_headertitle', 'change_wp_login_title');





/*
 * Include Stylesheets
**/ 
function jazel_admin_theme_style() {
    
    wp_enqueue_style('jwp_white_label', plugins_url('/css/jwp-admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'jazel_admin_theme_style');
add_action('login_enqueue_scripts', 'jazel_admin_theme_style');
add_action('get_header', 'jazel_admin_theme_style'); /* For front-end sticky header for logged-in users */

/* registering my script with Gravity Forms so that it gets enqueued when running on no-conflict mode */
function register_safe_script( $scripts ){

    $scripts[] = "jwp_white_label";
    return $scripts;
}
add_filter('gform_noconflict_styles', 'register_safe_script' );





/*
 * Remove unneeded dashboard widgets
**/ 
function jazel_admin_dashboard()
{
    // Globalize the metaboxes array, this holds all the widgets for wp-admin
    global $wp_meta_boxes;
     
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'jazel_admin_dashboard' );






/*
 *  Add Welcome Message to dashboard
**/
function jazel_custom_dashboard_widgets() {
    
    wp_add_dashboard_widget('custom_help_widget', 'Welcome to JAZEL', 'jazel_dashboard_help');
}
add_action('wp_dashboard_setup', 'jazel_custom_dashboard_widgets');

function jazel_dashboard_help() {
    
    echo '<h2>We are here to assist you.</h2>'.
        '<p>Please call us if you ever have any questions. We are eager to show you how to use our software and to discuss ideas on how to improve your website. Thank you for using Jazel.</p>'.
        '<aside class="jazel-support">'.
            '<h3>JAZEL SUPPORT TEAM<span>Contact us with any questions</span></h3>'.
            '<ul>'.
                '<li>Email — <a href="mailto:support@jazel.com">support@jazel.com</a></li>'.
                '<li>Phone — (866) 529-3555</li>'.
            '</ul>'.
        '</aside>';
}





/*
 *  Update admin menu
**/
function jazel_admin_bar_menu( $wp_admin_bar ) {

	$wp_admin_bar->remove_node( 'about' );  // About Wordpress
    $wp_admin_bar->remove_node( 'wporg' );  // Link to WordPress.org
    $wp_admin_bar->remove_node( 'documentation' );  // Link to WordPress Codex
    $wp_admin_bar->remove_node( 'support-forums' );  // Link to WordPress Forums
    $wp_admin_bar->remove_node( 'feedback' );  // Link to WordPress Feedback
    
    // Change link on logo
	$args = array(
		'id'    => 'wp-logo',
		'href'  => 'http://www.jazelmotors.com',
		'meta'  => array(
            'class' => 'jazel',
            'target' => '_black',
            )
	);    
    $wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'jazel_admin_bar_menu', 999 );

function jazel_admin_menu() {
    
    global $submenu;
    unset($submenu['index.php'][10]); // Removes 'Updates'.
}
add_action('admin_menu', 'jazel_admin_menu');





/*
 * Modify Footer Text (Left & Right)
**/ 
function jazel_admin_footer () 
{
    return 'Thank you for creating with <a href="http://www.jazelmotors.com">Jazel Motors</a>.';
}
add_filter('admin_footer_text', 'jazel_admin_footer');

function right_admin_footer_text_output($text) {
    
    return 'Jazel Motors: Reinventing Car Shopping';
}
add_filter('update_footer', 'right_admin_footer_text_output', 11);

?>