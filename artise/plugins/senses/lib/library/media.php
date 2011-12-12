<?php
/**
 *
 *		Library : Video
 *
 */

namespace plugins\senses\library
{
	class Media
	{
		// Properties.
		private
			$id,
			$properties = Array();
		
		// Dependencies.
		private
			$devkit,
			$mysql;
		
		/***** User methods *****/
		
		
		
		
		
		/***** Internal methods *****/
		
		/**
		 * The constructor stores the id.
		 */
		public function __construct($id)
		{
			// Store this media's id.
			$this->id = $id;
			
			// Store dependencies.
			$this->devkit = \Artise::devkit();
			$this->mysql = $this->devkit->plugins()->mysql();
		}
		
		
		/**
		 * Sets the properties when required, if they are not set yet.
		 */
		public function checkSetProperties()
		{
			if(count($this->properties) == 0)
			{
				// Fetch the properties from the database.
				$properties = $this->mysql->reader("SELECT * FROM {$this->media_type} WHERE id = {$this->id}");
				
				if($properties->rowCount() > 0)
				{
					// Store the properties as an array.
					$this->properties = $properties->rowToArray();
				}
				else
				{
					// We cannot proceed. Throw an error.
					throw new \HunterException('SENSES_E0001');
				}
			}
		}
		
		public function __get($name)
		{
			try
			{
				// Makes sure the dependencies for this method are met.
				$this->checkSetProperties();
				
				return isset($this->properties[$name]) ? $this->properties[$name] : null;
			}
			catch (\HunterException $e)
			{
				$this->devkit->hunter->error($e);
				return null;
			}
		}
		
		public function __set($name, $value)
		{
			$this->properties[$name] = $value;
		}
		
	}
}