<?php
namespace painters
{
	class hunter extends \Ext\Painter
	{
		public function filter() {
			
			// Get the latest log.
			$log = $this->devkit->plugins()->hunter()->getLatestLog();
			
			if(!$log)
				return false;
			
			// Bring the Slate.
			$sl = $this->createSlate('filter');
			
			// Define the filter options.
			$filters = array('all', 'flag', 'warning', 'error', 'php');
			
			foreach($filters as $key => $filter) {
				if($key > 0)
					$block = $sl->block('item');
				else
					$block = $sl->block('item_active');
				
				
				$block->id = $filter;
				$block->caption = $this->site->lang()->word($filter, 2);
			}
			
			
			return $sl;
		}
		
		public function logList() {
			// Get the latest log.
			$log = $this->devkit->plugins()->hunter()->getLatestLog();
			//die(print_r($log, true));
			if(!$log)
				return false;
			
			// Bring the Slate.
			$sl = $this->createSlate('logs');
			
			while($log->read()) {
				$b_entry = $sl->block('entry');
					
				$b_entry->log_type = $log->type;
				
				
				/* Block: Thumb */
				
				if($log->component) {
					$b_entry_thumb = $b_entry->block('thumb');
					
					$b_entry_thumb->component = $log->component;
					$b_entry_thumb->name = $log->name ? $log->name : '';
					
					if($log->method) {
						$b_entry_thumb_method = $b_entry_thumb->block('method');
						$b_entry_thumb_method->method = $log->method;
					}
					
					if($log->call) {
						$b_entry_thumb_call = $b_entry_thumb->block('call');
						$b_entry_thumb_call->call = $this->site->lang()->phrase('call_name', array('name' => $log->call));
						$b_entry_thumb_call->line = $log->line ? $this->site->lang()->phrase('line_number', array('number' => $log->line)) : '';
					}
				}
				
				
				/* Block: Data */
				
				$b_entry_data = $b_entry->block('data');
				
				$b_entry_data->title = $this->getMultiPhrase($log->title);
				if ($log->desc)
					$b_entry_data->desc = $this->getMultiPhrase($log->desc);
				else
					$b_entry_data->desc = '';
					
				if($log->tip) {
					$b_entry_data_tip = $b_entry_data->block('tip');
					$b_entry_data_tip->content = $this->getMultiPhrase($log->tip);
				}
			}
			
			return $sl;
		}
		
		private function getMultiPhrase($phrase)
		{
			if($phrase instanceof \Env\Source\DummyIdiom)
				return $this->site->lang()->phrase($this->cleanStrings($phrase->phrase), $this->cleanStrings($phrase->vars));
			else
				return $this->site->lang()->phrase($phrase);
		}
		
		private function cleanStrings($array)
		{
			if (is_array($array)) {
				array_walk($array, 'self::cleanString');
				return $array;
			} else {
				$this->cleanString($array);
				return $array;
			}
		}
		
		private function cleanString(&$string)
		{
			$string = str_replace($_SERVER['DOCUMENT_ROOT'], '', $string);
		}
	}
}
?>
