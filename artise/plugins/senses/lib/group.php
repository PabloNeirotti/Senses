<?php
/**
 *
 *		Library : Video
 *
 */

namespace plugins\senses
{
	class Group
	{
		public
			$codename,
			$media_type,
			$artist_id;
			
		private
			$properties = false,
			$media;
		
		
		
		// Dependencies.
		private
			$devkit,
			$mysql,
			$senses;
		
		
		/***** User methods *****/
		
		
		
		
		/**
		 * Media
		 *
		 * Fetches the media items in the provided group.
		 * That is, the Music albums's songs or Shows seasons's episodes.
		 */
		public function &media()
		{
			// Make sure we have properties loaded by now.
			$this->checkSetProperties();
			
			// If this object does not have this media table loaded yet, load it first.
			if(!isset($this->media)) {
				// Define the tables we are working with.
				$items_table = "media_{$this->media_type}";
				$items_group_table = "media_{$this->media_type}_group";
				
				// Get groups from this artist, of the requested media type.
				$this->media = $this->devkit->plugins()->mysql()->reader("
																		SELECT 	$items_table.*,
																				licenses.caption AS license
																		FROM	$items_table, licenses
																		WHERE 	$items_table.group_id = {$this->id}
																				AND media_{$this->media_type}.license_id = licenses.id");
				
				
				// Send to Array.
				$this->media = $this->media->tableToArray();
			}
			
			// Return the list of groups.
			return $this->media;
		}
		
		
		
		
		
		/***** Internal methods *****/
		
		/**
		 * The constructor stores the Group codename.
		 */
		public function __construct($codename, $media_type, $artist_id)
		{
			// Store this Group codename, media type and artist id.
			$this->codename = $codename;
			$this->media_type = $media_type;
			$this->artist_id = $artist_id;
				
			// Store dependencies.
			$this->devkit = \Artise::devkit();
			$this->mysql = $this->devkit->plugins()->mysql();
			$this->senses = $this->devkit->plugins()->senses();
			
			// Check if this kind of media exists.
			if(!isset($this->senses->media_types[$media_type]))
				throw new \HunterException('SENSES_E0002', $media_type);
		}
		
		
		/**
		 * Check Set Properties
		 *
		 * Sets the properties if they are not yet setted.
		 */
		private function checkSetProperties()
		{
			if(!$this->properties)
				$this->properties = $this->mysql->reader("SELECT id, title, year FROM media_{$this->media_type}_group WHERE codename = '{$this->codename}' AND artist_id = {$this->artist_id}")->rowToArray();
		}
		
		public function __get($name)
		{
			// Make sure we have properties loaded by now.
			$this->checkSetProperties();
			
			return isset($this->properties[$name]) ? $this->properties[$name] : null;
		}
	}
}