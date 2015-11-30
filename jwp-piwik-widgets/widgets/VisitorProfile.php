<?php

	namespace JWP_Piwik;

	class VisitorProfile extends \WP_Piwik\Widget {
	
		public $className = __CLASS__;
        private $profileInfo = null;

        public function __construct($piwik, $settings, $pageId, $info) {
            
            $this->profileInfo = $info;
            parent::__construct($piwik, $settings, $pageId);
        }
        
		protected function configure($prefix = '', $params = array()) {
            
			$timeSettings = $this->getTimeSettings();			
			$this->parameter = array(
				'idSite' => self::$wpPiwik->getPiwikSiteId($this->blogId),
				'period' => isset($params['period'])?$params['period']:$timeSettings['period'],
				'date'  => 'today',
                'visitorId' => $this->profileInfo['visitorId'],
                'segment' => 'visitorId==' . $this->profileInfo['visitorId'],
			);
			$this->method = array('Live.getVisitorProfile','Live.getLastVisitsDetails');
		}
		
        // To comply with widget interface.
        public function show() {}
        
		public function show_popup() {
            
			$response = array();
			$success = true;
			foreach ($this->method as $method) {
				$response[$method] = self::$wpPiwik->request($this->apiID[$method]);
				if (!empty($response[$method]['result']) && $response[$method]['result'] ='error')
					$success = false;
			}
                       
			if ($success) {
				$data = array();
                $visit_details = $this->profileInfo;
                $visitor_profile = $response['Live.getVisitorProfile'];
                $visits = $response['Live.getLastVisitsDetails'];
            
                // Visit Misc Information
                $visit_ip = $visit_details['visitIp'];
                $visitor_id = $visit_details['visitorId'];
                $visit_duration = $visitor_profile['totalVisitDurationPretty'];
                $scrn_reso = $visit_details['resolution'];
                $avg_gtime = $visitor_profile['averagePageGenerationTime'];
                
                // Page viewed
                $page_views = $visitor_profile['totalActions'];
                $page_views .= ' ' . ngettext('page', 'pages', $page_views);
                
                // Visit Count
                $total_visits = $visitor_profile['totalVisits'];
                $total_visits .= ' ' . ngettext('visit', 'visits', $total_visits);
                
                // Goal Convertion
                $goal_conversion = $visitor_profile['totalGoalConversions'];
                $goal_conversion .= ' ' . ngettext('goal', 'goals', $goal_conversion);
                
                // First Visit Info
                $first_visit_date = $visitor_profile['firstVisit']['prettyDate'];
                $first_visit_ellapse = $visitor_profile['firstVisit']['daysAgo'];
                $first_visit_ellapse .= ' ' . ngettext('day', 'days', $first_visit_ellapse);
                $first_visit_referral = $visitor_profile['firstVisit']['referralSummary'];
                
                // Browser Info
                $browser = $visit_details['browser'];
                $browser_info = $visit_details['browser'] . ', plugins: '.$visit_details['plugins'];
                $browser_icon = plugins_url('../../analytics/piwik/') . $visit_details['browserIcon'];
                
                $os = $visit_details['operatingSystem'];
                $os_icon = plugins_url('../../analytics/piwik/') . $visit_details['operatingSystemIcon'];
             
                ?>
                <table class="visitor-profile">
                    <tr>
                        <td>
                            <h2>Visitor Profile</h2>
                            <section>
                                <ul>
                                    <li>IP: <?= $visit_ip ?></li>
                                    <li>ID: <?= $visitor_id ?></li>
                                    <li><img src="<?= $browser_icon?>" title="<?=$browser_info?>" /><?=$browser?><img src="<?= $os_icon ?>" /><?= $os?></li>
                                    <li>Resolution <?= $scrn_reso ?></li>
                                </ul>
                            </section>
                            <hr>
                            <section>
                                <h3>Summary</h3>
                                <p>Spent a total of <?= $visit_duration ?> on the website, and viewed <?= $page_views ?> in 1 visit.</p>
                                <p>Converted <?= $goal_conversion ?>.</p>
                                <p>Each page took on average <?= $avg_gtime ?>'s to load for this visitor.</p>
                            </section>
                            <hr>
                            <section>
                                <h3>First Visit</h3>
                                <p><?= $first_visit_date ?> - <?= $first_visit_ellapse ?> ago.<p>
                                <p>from <?= $first_visit_referral ?></p>
                            </section>
                            <hr>
                            <section>
                                <h3>Location</h3>
                                <?php foreach ($visitor_profile['countries'] as $country) {
                                    
                                    $visit = $country['nb_visits'];
                                    $visit .= ' ' . ngettext('visit', 'visits', $visit);
                                    $country_name = $country['prettyName'];
                                    $country_flag = plugins_url('../../analytics/piwik/') . $country['flag'];
                                    ?>
                                    <?= $visit ?> from <?= $country_name ?><img src="<?= $country_flag ?>" />
                                <?php } ?>
                            </section>
                        </td>
                        <td class='visits'>
                            <h2>Visited Pages</h2>
                            <?php
                            $ctr = 0;
                            foreach ($visits as $visit) {
                                $ctr++;
                                $curr_visit_duration = $visit['visitDurationPretty'];
                                $curr_visit_date = $visit['serverDatePrettyFirstAction'];
                                $curr_visit_time = $visit['serverTimePrettyFirstAction'];
                                ?>
                                    <section>
                                    <h4><em>Visit #<?= $ctr ?></em> - ( <?= $curr_visit_duration ?>) <span><?= $curr_visit_date ?> <?= $curr_visit_time ?></span></h4>
                                    <ul>
                                        <?php
                                        $actions = $visit['actionDetails'];
                                        $actionCtr = 0;
                                        foreach ($actions as $action) {
                                            
                                            $page_title = $action['pageTitle'];
                                            $page_url = $action['url'];
                                            ?>
                                            <li><?= $page_title ?><p><a href="<?= $page_url ?>"><?= $page_url ?></a></p></li>
                                        <?php } ?>
                                    </ul>
                                    </section>
                            <?php } ?>
                            <p class='footer'>There are no more visits for this visitor.</p>
                        </td>
                    </tr>
                </table><?php
			}
		}
	}
?>