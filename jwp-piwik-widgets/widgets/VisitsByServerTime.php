<?php

    namespace JWP_Piwik;

	class VisitsServerTime extends \WP_Piwik\Widget {
	
		public $className = __CLASS__;

		protected function configure($prefix = '', $params = array()) {
            
			$timeSettings = $this->getTimeSettings();			
			$this->parameter = array(
				'idSite' => self::$wpPiwik->getPiwikSiteId($this->blogId),
				'period' => isset($params['period'])?$params['period']:$timeSettings['period'],
                'date' => 'today',
				'hideFutureHoursWhenToday'  => true,
			);
			$this->title = $prefix.__('Visits by Server Time', 'wp-piwik');
			$this->method = array('VisitTime.getVisitInformationPerServerTime');
			$this->context = 'normal';
			
			wp_enqueue_script('wp-piwik-jqplot',self::$wpPiwik->getPluginURL().'js/jqplot/wp-piwik.jqplot.js',array('jquery'));
            wp_enqueue_script('jwp-piwik-jqplot-bar', plugins_url().'/jwp-piwik-widgets/js/jqplot.barRenderer.min.js',array('jquery'));
            wp_enqueue_script('jwp-piwik-jqplot-axis', plugins_url().'/jwp-piwik-widgets/js/jqplot.categoryAxisRenderer.min.js',array('jquery'));
            wp_enqueue_script('jwp-piwik-jqplot-tick', plugins_url().'/jwp-piwik-widgets/js/jqplot.axisTickRenderer.js',array('jquery'));
            wp_enqueue_script('jwp-piwik-jqplot-legend', plugins_url().'/jwp-piwik-widgets/js/jqplot.enhancedLegendRenderer.min.js',array('jquery'));
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
                
                $chart_visit = '';
                $chart_actions = '';
                $chart_unique = '';
                $chart_users = '';
                $chart_conversions = '';
                
                if ( $response['VisitTime.getVisitInformationPerServerTime'] ) {
                
                    foreach ( $response['VisitTime.getVisitInformationPerServerTime'] as $visit ) {
                        
                        $chart_visit .= (isset($visit['nb_visits']) ? str_replace('h','',$visit['nb_visits']) : '0') .',';
                        $chart_actions .= (isset($visit['nb_actions']) ? str_replace('h','',$visit['nb_actions']) : '0') .',';
                        $chart_unique .= (isset($visit['nb_uniq_visitors']) ? str_replace('h','',$visit['nb_uniq_visitors']) : '0') .',';
                        $chart_users .= (isset($visit['nb_users']) ? str_replace('h','',$visit['nb_users']) : '0') .',';
                        $chart_conversions .= (isset($visit['nb_visits_converted']) ? str_replace('h','',$visit['nb_visits_converted']) : '0') .',';
                    }
                } else {
                    echo 'No data available at the moment.';
                }
                
                ?>
                
                <div id="chartdiv" style="height:400px;width:100%; "></div>
                
                <script>
                    
                    jQuery(document).ready(function($){
                            $.jqplot.config.enablePlugins = true;
                            var visitors = [<?=$chart_visit?>];
                            var actions = [<?=$chart_actions?>];
                            var unique = [<?=$chart_unique?>];
                            var users = [<?=$chart_users?>];
                            var conversions = [<?=$chart_conversions?>];
                            
                            var legend = ['Visits','Actions','Unique Visitors','Users','Conversions'];
                             
                            plot1 = $.jqplot('chartdiv', [visitors,actions,unique,users,conversions], {
                                // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
                                animate: !$.jqplot.use_excanvas,
                                seriesDefaults:{
                                    renderer:$.jqplot.BarRenderer,
                                    rendererOptions: { shadowAlpha: 0 },
                                    pointLabels: { show: true }
                                },
                                legend: {
                                    renderer: $.jqplot.EnhancedLegendRenderer,
                                    show: true, 
                                    location: 'n', 
                                    placement: 'outside',
                                    labels: legend,
                                    rendererOptions: {
                                        numberRows: 1
                                    },
                                    seriesToggle: true
                                },                                
                                axes: {
                                    xaxis: {
                                        renderer: $.jqplot.CategoryAxisRenderer,
                                        tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                                        //ticks: ticks,
                                        tickOptions: {
                                            formatter: function(format, value) { return value + 'h'; },
                                            showGridline: false                                        }
                                    }
                                },
                                series: {
                                    markerOptions: {
                                        show: false,
                                        bands: {
                                            show: false                                            
                                        }
                                    }
                                },
                                highlighter: { show: true }
                            });
                         
                            $('#chartdiv').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
                                    
                                console.log('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
                            });
                        });                    
                
                </script>
                
                <?php
			}
		}
	}
?>