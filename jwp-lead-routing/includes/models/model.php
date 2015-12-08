<?php
 
/**
 * The Jazel Lead Route Model
 *
 * @since 1.0
 */
 
if( !class_exists( 'jwpLeadRouteModel' ) ):

    class jwpLeadRouteModel
    {

        private $vehicle_fields = array(
            'id' => '',
            'year' => '',
            'make' => '',
            'model' => '',
            'vin' => '',
            'stock' => '',
            'trim' => '',
            'doors' => '',
            'bodystyle' => '',
            'transmission' => '',
            'odometer' => '',
            'condition' => '',
            'colorcombination' => array(
                'interiorcolor' => '',
                'exteriorcolor' => '',
                'preference' => '',
            ),
            'imagetag' => '',
            'price' => '',
            'pricecomments' => '',
            'option' => array(
                'optionname' => '',
                'manufacturercode' => '',
                'stock' => '',
                'weighting' => '',
                'price' => '',
            ),
            'finance' => array(
                'method' => '',
                'amount' => '',
                'balance' => '',
            ),
            'comments' => ''
        );
        
        private $customer_fields = array(
            'id' => '',
            'contact' => array(
                'name' => array(
                    'first' => '',
                    'last' => '', 
                ),
                'email address' => '',
                'day phone' => '',
                'address' => array(
                    'street address' => '',
                    'apartment' => '',
                    'city' => '',
                    'regioncode' => '',
                    'zip / postal code' => '',
                    'country' => '',
                ),
            ),
            'timeframe' => array(
                'description' => '',
                'earliestdate' => '',
                'latestdate' => '',
            ),
            'comments' => '',
        );
        
        private $vendor_fields = array(
            'adf_vendor_name' => '',
        );        

        private $provider_fields = array(
            'adf_provider_fullname' => '',
            'adf_provider_url' => '',
        );
        
        private $piwik_fields = array(
            'totalVisitDurationPretty' => '',
            'referalSummary' => '',
        );
        
        private $jazel_lead_info_fields = array(
            'adf_contact_primary' => false,
            'adf_customer_id' => '',
            'adf_customer_id_sequence' => '',
            'adf_customer_id_source' => '',
            'adf_request_date' => '',
        );

        public function __construct($formData, $formFields, $piwikData) {
            
            $this->vehicle_fields = $this->map_data($this->vehicle_fields, $formFields);
            $this->customer_fields = $this->map_data($this->customer_fields, $formFields);
            $this->vendor_fields = $this->map_data($this->vendor_fields, $formData);
            $this->provider_fields = $this->map_data($this->provider_fields, $formData);
            $this->piwik_fields = $this->map_data($this->piwik_fields, $piwikData);
        }
        
        private function map_data($category, $info) {
            
            //var_dump($info);
            foreach( $category as $key => $value ) {

                // Check for Array 1st level
                if ( is_array($category[$key])) {
                    
                    foreach($category[$key] as $key2 => $value2) {

                        // Check for Array 2nd level
                        if ( is_array($category[$key][$key2])) {
                            
                            foreach($category[$key][$key2] as $key3 => $value3) {
                                
                                if ( isset($info[$key3]) ) {

                                    $category[$key][$key2][$key3] = $info[$key3];
                                }                        
                            }
                        } else if ( isset($info[$key2]) ) {

                            $category[$key][$key2] = $info[$key2];
                        }                        
                    }
                } else if ( isset($info[$key]) ) {

                    $category[$key] = $info[$key];
                }
            }
            
            return $category;
        }
        
        public function get_data($category) {
            
            switch($category) {
                case 'vehicle':
                    return $this->vehicle_fields;
                case 'customer':
                    return $this->customer_fields;
                case 'vendor':
                    return $this->vendor_fields;
                case 'provider':
                    return $this->provider_fields;                    
                case 'piwik':
                    return $this->piwik_fields;
            }
        }
    }
endif;
?>