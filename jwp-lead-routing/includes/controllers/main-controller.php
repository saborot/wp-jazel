<?php
 
/**
 * Jazel Lead Routing Main Controller
 *
 * @since 1.0
 */
 
require_once( plugin_dir_path(__FILE__) . '../models/model.php' );
require_once( plugin_dir_path(__FILE__) . '../views/view.php' );

class jwpLeadRouteController
{
     
    public function __construct() {   
    
        // Form Settings
        add_filter('gform_form_settings', array( $this, 'add_lead_settings_ui' ), 10, 2);
        add_filter('gform_pre_form_settings_save', array( $this, 'save_lead_setting_fields' ), 10, 1);
    
        // Form Submit
        add_action( 'gform_after_submission', array( $this, 'route_lead'), 10, 2 );
    }
    
    function add_lead_settings_ui( $form_settings, $form ) {
        
		$adf_contact_primary = '';
		if ( rgar( $form, 'adf_contact_primary' ) ) {
			$adf_contact_primary = 'checked="checked"';
		}        
		$tr_customer = '
        <tr><th>'. __( "Lead Information", "gravityforms") .'</th></tr>
        <tr>
            <td class="gf_sub_settings_cell" colspan="2">
                <div class="gf_animate_sub_settings">
                    <table>
                        <tr class="child_setting_row">
                            <th>Contact Primary</th>
                            <td>
                                <input type="checkbox" id="adf_contact_primary" name="adf_contact_primary" value="1" ' . $adf_contact_primary . '/>
                            </td>
                        </tr>                       
                        <tr class="child_setting_row">
                            <th>Customer ID</th>
                            <td>
                                <input type="text" id="adf_customer_id" name="adf_customer_id" value="'. esc_html( rgar( $form, "adf_customer_id" ) ) .'" />
                            </td>
                        </tr>                
                        <tr class="child_setting_row">
                            <th>Customer ID Sequence</th>
                            <td>
                                <input type="text" id="adf_customer_id_sequence" name="adf_customer_id_sequence" value="'. esc_html( rgar( $form, "adf_customer_id_sequence" ) ) .'" />
                            </td>
                        </tr>
                        <tr class="child_setting_row">
                            <th>Customer ID Source</th>
                            <td>
                                <input type="text" id="adf_customer_id_source" name="adf_customer_id_source" value="'. esc_html( rgar( $form, "adf_customer_id_source" ) ) .'" />
                            </td>
                        </tr>
                        <tr class="child_setting_row">
                            <th>Request Date</th>
                            <td>
                                <input type="text" id="adf_request_date" name="adf_request_date" value="'. esc_html( rgar( $form, "adf_request_date" ) ) .'" />
                            </td>
                        </tr>                        
                    </table>
                </div>
            </td>          
        </tr>';         
        
		$tr_email = '
        <tr><th>'. __( "Email", "gravityforms") .'</th></tr>
        <tr>
            <td class="gf_sub_settings_cell" colspan="2">
                <div class="gf_animate_sub_settings">
                    <table>                    
                        <tr class="child_setting_row">
                            <th>From</th>
                            <td>
                                <input type="text" id="adf_email_from" name="adf_email_from" value="'. esc_html( rgar( $form, "adf_email_from" ) ) .'" />
                            </td>
                        </tr>                
                        <tr class="child_setting_row">
                            <th>Subject</th>
                            <td>
                                <input type="text" id="adf_email_subject" name="adf_email_subject" value="'. esc_html( rgar( $form, "adf_email_subject" ) ) .'" />                        
                            </td>
                        </tr>
                        <tr class="child_setting_row">
                            <th>Recipients</th>
                            <td>
                                <textarea id="adf_email_recipients" name="adf_email_recipients" class="fieldwidth-3 fieldheight-2">' . esc_html( rgar( $form, 'adf_email_recipients' ) ) . '</textarea>
                            </td>
                        </tr>                         
                    </table>
                </div>
            </td>          
        </tr>';        
        
		$tr_provider = '
        <tr><th>'. __( "Provider", "gravityforms") .'</th></tr>
        <tr>
            <td class="gf_sub_settings_cell" colspan="2">
                <div class="gf_animate_sub_settings">
                    <table>
                        <tr class="child_setting_row">
                            <th>FullName</th>
                            <td>
                                <input type="text" id="adf_provider_fullname" name="adf_provider_fullname" value="'. esc_html( rgar( $form, "adf_provider_fullname" ) ) .'" />
                            </td>
                        </tr>                
                        <tr class="child_setting_row">
                            <th>URL</th>
                            <td>
                                <input type="text" id="adf_provider_url" name="adf_provider_url" value="'. esc_html( rgar( $form, "adf_provider_url" ) ) .'" />                        
                            </td>
                        </tr>                    
                    </table>
                </div>
            </td>          
        </tr>';
        
		$tr_vendor = '
        <tr><th>'. __( "Vendor", "gravityforms") .'</th></tr>
        <tr>
            <td class="gf_sub_settings_cell" colspan="2">
                <div class="gf_animate_sub_settings">
                    <table>
                        <tr class="child_setting_row">
                            <th>Name</th>
                            <td>
                                <input type="text" id="adf_vendor_name" name="adf_vendor_name" value="'. esc_html( rgar( $form, "adf_vendor_name" ) ) .'" />
                            </td>
                        </tr>                     
                    </table>                
                </div>
            </td>
        </tr>';        
                 
        
        $form_lead = array(
            'adf_email' => $tr_email,
            'adf_customer' => $tr_customer,
            'adf_provider' => $tr_provider,
            'adf_vendor' => $tr_vendor,
        );
        
        $form_settings[__( 'Form Lead', 'gravityforms' )] = $form_lead;
        
        return $form_settings;
    }
    
    function save_lead_setting_fields($form) {
        
        $form['adf_contact_primary'] = rgpost( 'adf_contact_primary' );
        $form['adf_request_date'] = rgpost( 'adf_request_date' );
        $form['adf_customer_id'] = rgpost( 'adf_customer_id' );
        $form['adf_customer_id_sequence'] = rgpost( 'adf_customer_id_sequence' );
        $form['adf_customer_id_source'] = rgpost( 'adf_customer_id_source' );
        $form['adf_email_from'] = rgpost( 'adf_email_from' );
        $form['adf_email_subject'] = rgpost( 'adf_email_subject' );
        $form['adf_email_recipients'] = rgpost( 'adf_email_recipients' );
        $form['adf_provider_fullname'] = rgpost( 'adf_provider_fullname' );
        $form['adf_provider_url'] = rgpost( 'adf_provider_url' );
        $form['adf_vendor_name'] = rgpost( 'adf_vendor_name' );
        
        return $form;
    }
    
    // $lead is a parameter of 'gform_after_submission' hook passed here
    function route_lead($lead, $form) {

        $fields = $this->parse_form_data($lead, $form);

        // Prepare piwik profile
        $profile = $this->get_visitor_profile();
        
        // Prepare Jazel endpoints

        // Create Lead
        $lead_object = new jwpLeadRouteModel($form, $fields, $profile);
        
        // Reroute Lead
        $this->send_email($lead_object, $form);
    }
    
    function parse_form_data($lead, $form) {
        
        $field_values = array();

        foreach( $form['fields'] as $field ) {

            // Check if multipart field
            if ( isset($field['inputs']) ) {
                
                // loop input IDS
                foreach( $field['inputs'] as $part ) {
                    
                    if ( isset($lead[ $part['id'] ]) ) {
                                    
                        $field_values[ strtolower($part['label']) ] = $lead[ $part['id'] ];
                    }
                }
            } else if ( isset($lead[ $field['id'] ]) ) {
                
                $field_values[ strtolower($field['label']) ] = $lead[ $field['id'] ];
            }
        }
        
        return $field_values;
    }
    
    function get_visitor_profile() {
        
        // Get Site ID
        $piwik_instance = $GLOBALS ['wp-piwik'];
        $id_site = $piwik_instance->getPiwikSiteId();
        $ip_site = $_SERVER['REMOTE_ADDR'];
        $ip_range_min = substr($ip_site,0,strlen($ip_site) -1) . 0;
        $ip_range_max = substr($ip_site,0,strlen($ip_site) -1) . 255;
        
        // Get Visitor ID based on IP
        $visit_details_request = WP_Piwik\Request::register ( 'Live.getLastVisitsDetails', array (
            'idSite' => $id_site,
            'period' => 'day',
            'date' => 'today',
            'daysSinceLastVisit' => 0,
            'segment' => '&visitIp>' . urlencode($ip_range_min) . ';visitIp<' . urlencode($ip_range_max),
        ) );
        
        $visit_details = $piwik_instance->request ( $visit_details_request );
        
        // Get Profile
        $visitor_id = $visit_details[0]['visitorId'];

        $profile_request = WP_Piwik\Request::register ( 'Live.getVisitorProfile', array (
                'idSite' => $id_site, 'visitorId' => $visitor_id, 'flat' => 1,
        ) );

        return $piwik_instance->request ( $profile_request );        
    }

    function send_email( $lead, $form ) {
        
        $recipients = str_replace("\r\n", ",", $form['adf_email_recipients']);

        $subject = $form['adf_email_subject'];
        $boundary = uniqid('np');        
        $headers = array(
            'MIME-Version: 1.0',
            'From: ' . $form['adf_email_from'],
            'To: '. $recipients,
            'Content-Type: multipart/mixed; boundary=' . $boundary,
            'Content-Disposition: attachment; filename=Jazel.Integration.ADF',
            );

        $message = jwpLeadRouteView::compose($lead, $boundary);
        
        wp_mail( $recipients, $subject, $message, $headers );
    }
    
    function add_settings() {
        
        
    }
}
 
$jwpLead = new jwpLeadRouteController;
?>