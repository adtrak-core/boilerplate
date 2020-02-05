<?php
	class MBP_Upgrader{
		private $updateto;
		
		public function __construct($version){			
			if(!version_compare($version, '0.0.1', '>=')){
				return false;
			}
			$this->updateto = str_replace('.', '_', $version);
			return true;
		}
		
		public function run(){
			if(!method_exists($this, 'upgrade_'.$this->updateto)){ return false; }
			return call_user_func(array($this, 'upgrade_'.$this->updateto));
		}
		
		public function upgrade_2_1_7(){
			/*
				Pre 2.1.7 deleting a parent WP post would cause the GMB schedule to be orphaned
				and continueing to post. This upgrader will delete any orphaned schedules	
			*/
			
			$cronjobs = _get_cron_array();
			
			foreach($cronjobs as $cronjob){
				if(!array_key_exists('mbp_schedule_post', $cronjob)){ continue; }
				$job = reset($cronjob['mbp_schedule_post']);
				
				$parent_post_id = $job['args'][0];
				
				//Parent still exists, go to next job
				if(get_post($parent_post_id)){ continue; }
				
				wp_clear_scheduled_hook('mbp_schedule_post', $job['args']);
			}
			return true;
		}
		
	}