<?php
 
/**
 * The Jazel Calculators View
 *
 * @package Jazel
 * @subpackage jCalcView
 * @since 1.0
 */
 
if( !class_exists( jCalcView) ):
 
    class jCalcView
    {

        public static function render($model)
        {
            $title = $model->get_title();
            $type = $model->get_type();
            $inv_url = $model->get_inv_url();
            
            $first_field_label = 'Price';
            $first_field_name = 'price';
            $term_label = ' / Mo.';
            
            if ( $type != 1 ) {
                $first_field_label = 'Monthly Payment';
                $first_field_name = 'monthlypayment';
                $term_label = '';
            }
            
            ob_start();
            echo 
                '<article class="jazel-calc clearfix" style="max-width:187px;">' . 
                    '<h1>' . $title . '</h1>' .
                    '<div class="fields">' .
                        '<div class="container">' .
                            '<label class="column one required">' . $first_field_label . '*</label>' .
                            '<input type="text" name="' . $first_field_name .'" data-format="monetary" class="column one req" placeholder="$" />' .
                        '</div>' .
                        '<div class="container">' .
                            '<label class="column one">Trade-In Value</label>' .
                            '<input type="text" name="tradein" data-format="monetary" class="column one" placeholder="$" />' .
                        '</div>' .
                        '<div class="container">' .
                            '<label class="column one">Payoff on Trade-In</label>' .
                            '<input type="text" name="payoff" data-format="monetary" class="column one" placeholder="$" />' .
                        '</div>' .
                        '<div class="container">' .
                            '<label class="column one">Downpayment</label>' .
                            '<input type="text" name="downpayment" data-format="monetary" class="column one" placeholder="$" />' .
                        '</div>' .
                        '<div class="container">' .
                            '<label class="column one required">Finance Rate (APR)*</label>' .
                            '<input type="text" name="apr" data-format="percentage" class="column one req" placeholder="%" />' .
                        '</div>' .
                        '<div class="container">' .
                            '<label class="column one required">Term (Months)*</label>' .
                            '<input type="text" name="term" class="column one req" />' .
                        '</div>' .
                        '<div class="container">' .
                            '<label class="column one-second legend">* Requred</label>' .
                            '<button class="column one-second button-calculate">Calculate</button>' .
                        '</div>' .                        
                    '</div>' .
                    '<div class="results">' .
                        '<div class="estimate"><span>&nbsp;</span>' . $term_label . '</div>' .
                    '</div>' .
                    '<div class="container disclaimer">' .
                        '<label class="column one">This is only an estimate</label>' .
                        '<span>View Details<i class="icon-right-open"></i></span>' .
                        '<div class="disclaimer-text"><strong>Calculator Disclaimer</strong><p>Excludes taxes, title, license and insurance.</p></div>' .
                    '</div>' .
                    '<input type="submit" value="Find Vehicles" class="column one view-inventory" data-url="' . $inv_url . '" />' .
                '</article>';
            return ob_get_clean();
        }
    }
endif;
?>