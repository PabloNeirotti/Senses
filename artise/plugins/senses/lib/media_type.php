<?php
/**
 *
 *		Media type
 *
 *
 * This object is read-only.
 *
 * It is used to obtain the settings of a media type.
 */

namespace plugins\senses
{
	class MediaType
	{
		// Properties.
		private
			$properties = Array();
		
		/***** User methods *****/
		
		
		
		
		
		/***** Internal methods *****/
		
		/**
		 * The constructor processes and stores the settings for this media type.
		 */
		public function __construct($settings, $lang)
		{
			// Process settings.
			foreach($settings as $key => $value)
			{
				// Lower case the value to standarize the value for checking.
				$value_lower = strtolower($value);
				
				// Check if it's a "true" boolean.
				if($value_lower == 'true' || $value_lower == '1')
					$value = true;
				
				// Check if it's a "false" boolean.
				if($value_lower == 'false' || $value_lower == '0')
					$value = false;
				
				// Store this property.
				$this->properties[$key] = $value;
			}
		}
		
		
		
		public function __get($name)
		{
			return isset($this->properties[$name]) ? $this->properties[$name] : null;
		}
		
	}
}