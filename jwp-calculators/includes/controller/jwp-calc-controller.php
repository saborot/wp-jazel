<?php
 
/**
 * Jazel Calculators Main Controller
 *
 * @package Jazel
 * @subpackage jCalcController
 * @since 1.0
 */
 
require_once( plugin_dir_path(__FILE__) . '../models/jwp-calc-model.php' );
require_once( plugin_dir_path(__FILE__) . '../views/jwp-calc-view.php' );

class jCalcController
{
     
    public function __construct()
    {   
        global $post;
        if( is_page()  && has_shortcode( $post->post_content, 'jazel-calculator') ) {
            
            $this->setup_shortcode();    
            add_action('wp_enqueue_scripts',[$this,'enqueue_resources']);
        }
    }

    function enqueue_resources() {
        wp_enqueue_style('jcalc-styles', plugins_url('../assets/css/jwp-calc.css', __FILE__) );
        wp_enqueue_script('jcalc-script', plugins_url('../assets/js/jwp-calc.js', __FILE__), 'jQuery', array(), '1.0.0', true );
    }
    
    function setup_shortcode() {
        
        function jazel_calc_register_shortcode() {

            add_shortcode( 'jazel-calculator', 'render_shortcode' );
        }        
        add_action( 'init', 'jazel_calc_register_shortcode' );

        function render_shortcode( $atts) {
            
            $a = shortcode_atts( array(
                'title' => 'Estimated Monthly Payment',
                'type' => '1',
                'inv_url' => '/index.php/inventory'
            ), $atts );

            $jCalcModel = new jCalcModel($a);

            return jCalcView::render($jCalcModel);
        }        
    }
}
 
$jCalc = new jCalcController;
?>