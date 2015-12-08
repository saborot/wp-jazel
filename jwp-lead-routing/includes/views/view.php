<?php
 
/**
 * The Jazel Lead Route View
 *
 * @since 1.0
 */
  
if( !class_exists( 'jwpLeadRouteView' ) ):
 
    class jwpLeadRouteView {

        private static function render_adf($lead) {
            
            //create the xml document            
            $xmlDoc = new DOMDocument('1.0', 'utf-8');
            $adfImplementation = new DOMImplementation();
            $doc = $adfImplementation->createDocument(NULL,"ADF");
            $pi = $xmlDoc->createProcessingInstruction('ADF', 'version="1.0"');
            $xmlDoc->appendChild($pi);
            $xmlDoc->formatOutput = true;
            
            //create the root element
            $root = $xmlDoc->appendChild($xmlDoc->createElement("adf"));
            
            $prospect = $xmlDoc->createElement("prospect");
            $root->appendChild($prospect);

            // ID
            $id = $xmlDoc->createElement("id");
            $id->setAttribute('sequence', 'uniqueLeadId');
            $id->setAttribute('source', get_bloginfo('name'));
            $prospect->appendChild($id);
            
            // Request Date
            $reqDate = $xmlDoc->createElement("requestdate");
            $reqDate->appendChild($xmlDoc->createTextNode(date(DATE_ATOM)));
            $prospect->appendChild($reqDate);

            // Vehicle
            $data_vehicle = $lead->get_data('vehicle');
            $vehicle = $xmlDoc->createElement("vehicle");
            $prospect->appendChild($vehicle);
            static::fill_section($data_vehicle, $vehicle, $xmlDoc);
            
            // Customer
            $data_customer = $lead->get_data('customer');
            $customer = $xmlDoc->createElement("customer");
            $prospect->appendChild($customer);
            static::fill_section($data_customer, $customer, $xmlDoc);

            // Vendor
            $data_vendor = $lead->get_data('vendor');
            $vendor = $xmlDoc->createElement("vendor");
            $prospect->appendChild($vendor);
            static::fill_section($data_vendor, $vendor, $xmlDoc);

            // Provider
            $data_provider = $lead->get_data('provider');
            $provider = $xmlDoc->createElement("provider");
            $prospect->appendChild($provider);
            static::fill_section($data_provider, $provider, $xmlDoc);

            $xpath = new DOMXPath($xmlDoc);

            while (($node_list = $xpath->query('//*[not(*) and not(@*) and not(text()[normalize-space()])]')) && $node_list->length) {
                foreach ($node_list as $node) {
                    $node->parentNode->removeChild($node);
                }
            }            
            
            return $xmlDoc->saveXML();
            /*
            <?ADF VERSION "1.0"?>
            <?XML VERSION “1.0”?>
            <adf>
            <prospect>
            <requestdate>2000-03-30T15:30:20-08:00</requestdate>
            <vehicle>
            <year>1999</year>
            <make>Chevrolet</make>
            <model>Blazer</model>
            </vehicle>
            <customer>
             <contact>
             <name part="full">John Doe</name>
             <phone>393-999-3922</phone>
             </contact>
             </customer>
            <vendor>
             <contact>
             <name part="full">Acura of Bellevue</name>
             </contact>
            </vendor>
            </prospect>
            </adf>            
            */
        }
        
        private static function render_plain($lead) {

            return "Hello,\nThis is a text email, the text/plain version.\n\nRegards,\nYour Name";
        }
        
        private static function render_html($lead) {

            return "<h1>Hello,\nThis is a good email, the HTML version.</h1>\n\nRegards,\n<i>Your Name</i>";
        }
        
        public static function compose($lead, $boundary) {
            
            $division = "\r\n\r\n--" . $boundary . "\r\n";
            $closure = "\r\n\r\n--" . $boundary . "--";
            
            // Plain Text
            $message = $division .
                "Content-Type: text/plain\r\n".
                "Content-Disposition: inline\r\n\r\n";
            $message .= static::render_plain($lead);
            
            // HTML
            $message .= $division.
                "Content-Type: text/html\r\n".
                "Content-Disposition: inline\r\n\r\n";
            $message .= static::render_html($lead);
            
            //ADF
            $message .= $division.
                "Content-Type: application/xml\r\n".
                'Content-Disposition: attachment;filename=leads.adf';
                
            $message .= "\r\n\r\n";
                
            $message .= static::render_adf($lead);
                
            $message .= $closure;
            
            return $message;
        }
        
        private static function fill_section($data, $section, $xmlDoc) {
            
            foreach ($data as $key => $value) {
                    
                $safeTag = static::parseADFTag($key);
                if ( is_array($value) ) {

                    $sub_section = $section->appendChild($xmlDoc->createElement($safeTag));
                    foreach ($value as $key2 => $value2) {

                        $safeTag = static::parseADFTag($key2);
                        if ( is_array($value2) ) {
                            
                            $sub_section2 = $sub_section->appendChild($xmlDoc->createElement($safeTag));
                            foreach ($value2 as $key3 => $value3) {
                                
                                if ( !isset($value3) || strlen($value3) <= 0 ) continue;
                                $safeTag = static::parseADFTag($key3);
                                $sub_section2->appendChild($xmlDoc->createElement($safeTag))->appendChild($xmlDoc->createTextNode($value3));
                            }
                        } else {

                            if ( !isset($value2) || strlen($value2) <= 0 ) continue;
                            $sub_section->appendChild($xmlDoc->createElement($safeTag))->appendChild($xmlDoc->createTextNode($value2));
                        }
                    }
                } else {

                    if ( !isset($value) || strlen($value) <= 0 ) continue;
                    $section->appendChild($xmlDoc->createElement($safeTag))->appendChild($xmlDoc->createTextNode($value));
                }
            } 
        }
        
        private static function parseADFTag($tagName) {
            
            $knownNonADFTags = array(
                'zip / postal code' => 'postalcode',
                'street address' => 'street',
                'email address' => 'email',
                'day phone' => 'phone',
            );
            
            if ( array_key_exists($tagName, $knownNonADFTags) ) {
                $tagName = $knownNonADFTags[$tagName];
            }
            
            return $tagName;
        }
    }
endif;
?>