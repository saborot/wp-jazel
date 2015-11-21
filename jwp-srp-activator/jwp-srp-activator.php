<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* Plugin Name: JWP SRP Activator
 * Description: Allows any page to act as a vehicle search result page.
 * Version: 1.0.0
 * Author: Sherwin Aborot - Jazel, LLC.
**/

/* Register Meta Box for enabling SRP */
    function add_jazel_srp_switch() {
        
        add_meta_box('jazel-srp-switch',__( 'SRP Page Setting', 'jazel-srp-react' ), 'render_jazel_srp_switch', 'page', 'side');        
    }
    add_action( 'add_meta_boxes', 'add_jazel_srp_switch' );
    
    function render_jazel_srp_switch($post) {
        
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'jazel_srp_save_meta_box_data', 'jazel_srp_meta_box_nonce' );

        // Get current value from database
        $value = get_post_meta( $post->ID, '_jazel_srp_switch_meta_key', true );

        // Set default value
        if ( empty($value) )
            $value = 'false';
        
        echo '<div><input type="text" id="jazel-srp-switch" name="jazel-srp-switch" value="' . $value . '"/></div>';
        echo '<label for="jazel_srp_switch">' . _e('Show SRP on this page ( true/false )') . '</label>';
    }
    
    function save_jazel_srp_switch($post_id) {
        
        // Check for nonce
        if ( ! isset( $_POST['jazel_srp_meta_box_nonce'] ) ) {
            return;
        }
        
        // Verify that the nonce is valid
        if ( ! wp_verify_nonce( $_POST['jazel_srp_meta_box_nonce'], 'jazel_srp_save_meta_box_data' ) ) {
            return;
        }
        
        // Check for autosave activity
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        // Check the user's permissions
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
        
        // Check if field is set
        if ( ! isset( $_POST['jazel-srp-switch'] ) ) {
            return;
        }        
        
        // Sanitize user input
        $my_data = sanitize_text_field( $_POST['jazel-srp-switch'] );
        
        if ( $my_data != 'true' && $my_data != 'false' ) {
            $my_data = 'false';
        }
        
        // Update database
        update_post_meta( $post_id, '_jazel_srp_switch_meta_key', $my_data );
    }
    add_action( 'save_post', 'save_jazel_srp_switch' );
    
    
/* Enqueue SRP JS & CSS bundles */
    add_action('wp_enqueue_scripts','enqueue_scripts');
    function enqueue_scripts() {
        
        global $post;
        
        $useSRP = get_post_meta( $post->ID, '_jazel_srp_switch_meta_key', true );

        if ( $useSRP === 'true' ) {
            add_filter( 'the_content', 'render_jazel_srp_container' );
            add_filter( 'body_class', 'add_jazel_srp_class' );
         
            wp_enqueue_script('page-init', plugins_url('/js/jwp-srp-activator.js', __FILE__), array('jazel-srp-script'), false, true);         
            wp_enqueue_script('jazel-srp-script','http://auto5-web.s3-website-us-west-2.amazonaws.com/SRP/scripts/bundle.min.js',array(),null,true);
            wp_enqueue_style('jazel-srp-styles','http://auto5-web.s3-website-us-west-2.amazonaws.com/SRP/css/bundle.css');
        }
    }


    // The React SRP script searches for this particular element ID so we'll add it automatically to a page's content area.
    function render_jazel_srp_container($content) {
        
        $content .= '<div id="srp-app"></div>';

        return $content;
    }

    // Add class to body so we can style the layout to our needs
    function add_jazel_srp_class( $classes ) {

        $classes[] = 'is-srp';
        return $classes;
    }

?>