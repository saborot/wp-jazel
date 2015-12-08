<?php

    namespace JWP_Piwik;

	class VisitorsInterest extends \WP_Piwik\Widget {
	
		public $className = __CLASS__;

		protected function configure($prefix = '', $params = array()) {
            
			$timeSettings = $this->getTimeSettings();			
			$this->parameter = array(
				'idSite' => self::$wpPiwik->getPiwikSiteId($this->blogId),
				'period' => isset($params['period'])?$params['period']:$timeSettings['period'],
				'date'  => 'today',
                'lastMinutes' => 1440,
			);
			$this->title = $prefix.__('Visitors In Real-Time', 'wp-piwik');
			$this->method = array('Live.getLastVisitsDetails','Live.getCounters');
			$this->context = 'normal';
		}
		
		public function show() {
            
			$response = array();
			$success = true;
			foreach ($this->method as $method) {
				$response[$method] = self::$wpPiwik->request($this->apiID[$method]);
				if (!empty($response[$method]['result']) && $response[$method]['result'] ='error')
					$success = false;
			}
			if (!$success)
				echo '<strong>'.__('Piwik error', 'wp-piwik').':</strong> '.htmlentities($response[$method]['message'], ENT_QUOTES, 'utf-8');
			else {
				$data = array();
                
                if ($response['Live.getCounters']) {
                    
                    foreach ( $response['Live.getCounters'] as $key => $value ) {
                        $data[] = array(
                            'Last ' . ($this->parameter['lastMinutes'] / 60) . ' hours',
                            $value['visits'][$key]?$value['visits'][$key]:'-',
                            $value['actions'][$key]?$value['actions'][$key]:'-'
                        );
                    }
                }
                
				$this->table(
					array(__('Date', 'wp-piwik'), __('Visits', 'wp-piwik'), __('Actions', 'wp-piwik')),
					array_reverse($data),
					array(),
					'clickable'
				);

                $visits_details = $response['Live.getLastVisitsDetails'];
                $ctr = 0;
                
                if ($visits_details) {
                
                    foreach ( $visits_details as $key => $value ) {

                        $ctr++;
                        $visit_duration = $value['visitDurationPretty'];
                        $visit_type = $value['visitorType'];
                        $img_visit_type = $visit_type == 'returning' ? plugins_url('../../analytics/piwik/') . $value['visitorTypeIcon'] : '';
                        $visitor_type = $visit_type == 'new' ? '' : '<img src="'.$img_visit_type.'" title="'.$visit_type.'" />';
                        $datestamp = $value['serverDatePrettyFirstAction'] . ' - ' . $value['serverTimePrettyFirstAction'];
                        $ip_address = $value['visitIp'];
                        $referrer = $value['referrerTypeName'];
                        $img_country = plugins_url('../../analytics/piwik/') . $value['countryFlag'];
                        $img_browser = plugins_url('../../analytics/piwik/') . $value['browserIcon'];
                        $img_os = plugins_url('../../analytics/piwik/') . $value['operatingSystemIcon'];
                        $img_profile = plugins_url('../../analytics/piwik/plugins/Live/images/visitorProfileLaunch.png');
                        $provider = $value['location'] . ', ' . (isset($value['providerName']) ? $value['providerName'] : 'Provider');
                        $browser = $value['browser'] . ', Plugins: ' . $value['plugins'];
                        $os = $value['operatingSystem'] . ', ' . $value['resolution'];

                        require_once('VisitorProfile.php');
                        $profile = new VisitorProfile ( self::$wpPiwik, self::$settings, self::$wpPiwik->statsPageId, $value ); ?>
                        
                        <ul class="live-visit">
                            <li>
                                <span class="visit-time"><?= $datestamp ?><em>( <?= $visit_duration ?> )</em></span><?=$visitor_type?>
                                <label class="btn" for="modal-<?= $ctr ?>"><img src="<?= $img_profile ?>" title="Visitor Profile" /></label>
                                <input class="modal-state" id="modal-<?= $ctr ?>" type="checkbox" />
                                <div class="modal">
                                    <label class="modal__bg" for="modal-<?= $ctr ?>"></label>
                                    <div class="modal__inner">
                                        <label class="modal__close" for="modal-<?= $ctr ?>"></label>
                                        <?php $profile->show_popup(); ?>
                                    </div>
                                </div>
                                <div>
                                    <img src="<?= $img_country ?>" title="<?= $provider ?>" />
                                    <img src="<?= $img_browser ?>" title="<?= $browser ?>" />
                                    <img src="<?= $img_os ?>" title="<?= $os ?>" />
                                    <span class="visit-ip">IP: <?= $ip_address ?></span>
                                </div>
                                <div><?= $referrer ?></div>
                            </li>
                            <li class="actions">
                                <span>Actions: </span>
                                <?php
                                $ctr = 0;
                                foreach ( $value['actionDetails'] as $action ) {
                                    
                                    $time_on_page = isset($action['timeSpentPretty']) ? "\nTime on page: " . $action['timeSpentPretty'] : '';
                                    $folder_icon = plugins_url('../../analytics/piwik/plugins/Live/images/file') . $ctr . '.png';
                                    $page_title = $action['pageTitle'];
                                    $action_time = $action['serverTimePretty'];
                                    $img_title = $page_title . "\n" . $action_time . ' ' . $time_on_page;
                                    $ctr++;
                                    $ctr = $ctr <= 9 ? $ctr : 1;                                
                                    ?>
                                    <a href="<?= $action['url'] ?>" target="_blank" >
                                        <img src="<?= $folder_icon ?>" title="<?= $img_title ?>" />
                                    </a>
                                <?php } ?>
                            </li>
                        </ul>              
                    <?php }
                } else {
                    
                    echo 'No data available at the moment.';
                }
            }
		}
	}
?>