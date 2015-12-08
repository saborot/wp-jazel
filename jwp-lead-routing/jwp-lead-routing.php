<?php
/**
    Plugin Name: JWP Lead Routing
    Description: Reroute leads gathered using Gravity forms.
    Depends: WP-Piwik
    Version:     1.0
    Author:      Sherwin Aborot - Jazel, LLC.
 */

require_once( 'includes/controllers/main-controller.php' );

/*
function enhance_lead_info($lead) {
    
    var_dump($lead);
    
    $form = GFFormsModel::get_form_meta( $lead['form_id'] );

    $values= array();

    foreach( $form['fields'] as $field ) {

        if ( isset($lead[ $field['id'] ]) ) {
            $values[$field['id']] = array(
                'id'    => $field['id'],
                'label' => $field['label'],
                'value' => $lead[ $field['id'] ],
            );
        }
    }
    
    var_dump($values);
        
    // Find visitor info
    
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
                'idSite' => $id_site, 'visitorId' => $visitor_id
        ) );

        $profile = $piwik_instance->request ( $profile_request );

    // Add Jazel Endpoints
    
    // Compose ADF
    
    // Compose HTML
    
    // Send to 3rd Party
    $recipients = 'saborot@gmail.com';
    $subject = 'Lead Routing';
    
    $boundary = uniqid('np');
    
    $headers = array(
        'MIME-Version: 1.0',
        'From: Sherwin Sena Aborot',
        'To: '. $recipients,
        'Content-Type: multipart/mixed; boundary=' . $boundary,
        'Content-Disposition: attachment; filename=Jazel.Integration.ADF',
        );

    $message = "\r\n\r\n--" . $boundary . "\r\n".
        "Content-Type: text/plain\r\n".
        "Content-Disposition: inline\n\r\n";
        
    //Plain text body
    $message .= "Hello,\nThis is a text email, the text/plain version.\n\nRegards,\nYour Name";

    // HTML Body
    $message .= "\r\n\r\n--" . $boundary . "\r\n".
        "Content-Type: text/html\r\n".
        "Content-Disposition: inline\r\n\r\n";
    
    $message .= "<h1>This is an HTML Header content.</h1>";
    
    //ADF body
    $message .= "\r\n\r\n--" . $boundary . "\r\n".
        "Content-Type: application/xml\r\n".
        "Content-Disposition: attachment; filename=Leads.ADF;\r\n\r\n";
        
    $message .= '<?xml version="1.0" encoding="UTF-8"?>
      <?ADF VERSION="1.0"?>
        <adf>
          <prospect>
            <id sequence="uniqueLeadId" source="sitename"></id>
            <requestdate></requestdate>
            <vehicle interest="buy" status="used">
              <vin></vin>
              <year></year>
              <make></make>
              <model></model>
              <stock></stock>
            </vehicle>
            <customer>
              <contact>
              <name part="first" type="individual"></name>
              <name part="last" type="individual"></name>
              <email></email>
              <phone type="home"></phone>
              <phone type="work"></phone>
              <phone type="mobile"></phone>
            </contact>
            <comments></comments>
          </customer>
          <vendor>
            <contact>
                <name part="full"></name>
                <email></email>
                <phone type="business"></phone>
            </contact>
          </vendor>
        </prospect>
    </adf>';
    $message .= "\r\n\r\n--" . $boundary . "--";
    wp_mail( $recipients, $subject, $message, $headers );
}

add_action( 'gform_after_submission', 'enhance_lead_info' );
*/
?>