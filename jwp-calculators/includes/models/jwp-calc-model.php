<?php
 
/**
 * The Jazel Calculators Model
 *
 * @package Jazel
 * @subpackage jCalcModel
 * @since 1.0
 */
 
if( !class_exists( 'jCalcModel' ) ):

    class jCalcModel
    {

        private $title;
        private $type;
        private $inv_url; 

        public function __construct($args)
        {
            $this->title = $args['title'];
            $this->type = $args['type'];
            $this->inv_url = $args['inv_url'];
        }
 
        public function get_title()
        {
            return $this->title;
        }
        
        public function get_type()
        {
            return $this->type;
        }

        public function get_inv_url()
        {
            return $this->inv_url;
        }        
    }
endif;
?>