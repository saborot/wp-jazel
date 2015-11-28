<?php
/**
    Plugin Name: JWP Piwik Widgets
    Description: Adds additional Piwik widgets to the dashboard.
    Version:     1.0
    Author:      Sherwin Aborot - Jazel, LLC.
 */

function enqueue_jwp_piwik_widgets() {
    
    add_action('load-dashboard_page_wp-piwik_stats', 'load_jwp_piwik_widgets');
    
    function load_jwp_piwik_widgets() {

        if ( class_exists('WP_Piwik\Widget') ) {
            
            require_once( 'widgets\VisitorsInterest.php');
            require_once( 'widgets\VisitsByServerTime.php');
            $settings = new WP_Piwik\Settings($GLOBALS ['wp-piwik']);
            new JWP_Piwik\VisitorsInterest ( $GLOBALS ['wp-piwik'], $settings, 'dashboard_page_wp-piwik_stats' );
            new JWP_Piwik\VisitsServerTime ( $GLOBALS ['wp-piwik'], $settings, 'dashboard_page_wp-piwik_stats' );
        }
    }
}

function jwp_piwik_enqueue_scripts() {
    
    wp_enqueue_style( 'jwp_piwik_styles', plugins_url() . '/jwp-piwik-widgets/css/jwp-piwik.css' );
    wp_enqueue_style('wp-piwik', $GLOBALS ['wp-piwik']->getPluginURL().'css/wp-piwik.css',array(),$GLOBALS ['wp-piwik']->getPluginVersion());    
    wp_enqueue_script('wp-piwik', $GLOBALS ['wp-piwik']->getPluginURL().'js/wp-piwik.js', array(), $GLOBALS ['wp-piwik']->getPluginVersion(), true);
}

add_action( 'admin_enqueue_scripts', 'jwp_piwik_enqueue_scripts' );
add_action('plugins_loaded','enqueue_jwp_piwik_widgets');
?>