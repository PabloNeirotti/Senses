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
			$media_type,
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
		public function __construct($id, $media_type)
		{
			// Store this media's id and type.
			$this->id = $id;
			$this->media_type = $media_type;
			
			// Store dependencies.
			$this->devkit = \Artise::devkit();
			$this->mysql = $this->devkit->plugins()->mysql();
		}
		
		
		/**
		 * Increase play count
		 *
		 * [WARNING] Needs to be polished. A lot.
		 */
		public function increasePlayCount()
		{
			// Get dependencies.
			$this->checkSetProperties();
			
			// Increase play count.
			$this->play_count = $this->play_count + 1;
			
			//die(print_r("UPDATE media_{$this->media_type} SET play_count = {$this->play_count} WHERE id = {$this->id}", true));
			
			// Update the play count at the database.
			$this->mysql->execute("UPDATE media_{$this->media_type} SET play_count = {$this->play_count} WHERE id = {$this->id}");
		}
		
		
		/**
		 * Sets the properties when required, if they are not set yet.
		 */
		public function checkSetProperties()
		{
			if(count($this->properties) == 0)
			{
				// Fetch the properties from the database.
				$properties = $this->mysql->reader("SELECT * FROM media_{$this->media_type} WHERE id = {$this->id}");
				
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