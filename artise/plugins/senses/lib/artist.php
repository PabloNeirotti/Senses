<?php
/**
 *
 *		Library : Video
 *
 */

namespace plugins\senses
{
	class Artist
	{
		public
			$codename;
		private
			$properties = false,
			$media = array();
		
		
		
		// Dependencies.
		private
			$devkit,
			$mysql,
			$senses;
		
		
		/***** User methods *****/
		
		
		/**
		 * Get this Artist's media.
		 */
		public function &media($media_type)
		{
			// Make sure we have properties loaded by now.
			$this->checkSetProperties();
			
			// Check if this kind of media exists.
			if(!isset($this->senses->media_types[$media_type]))
				throw new \HunterException('SENSES_E0002', $media_type);
			
			// If this object does not have this media table loaded yet, load it first.
			if(!isset($this->media[$media_type])) {
				// Get media.
				$this->media[$media_type] = $this->mysql->reader("SELECT 	media_{$media_type}.title,
																			media_{$media_type}.file_ext,
																			media_{$media_type}.external_url,
																			media_{$media_type}.codename,
																			licenses.caption AS license
																	FROM media_{$media_type}, licenses
																	WHERE 	media_{$media_type}.artist_id = {$this->id}
																			AND media_{$media_type}.license_id = licenses.id");						
				// Send to Array.
				$this->media[$media_type] = $this->media[$media_type]->tableToArray();
			}
			
			// Return the media array.
			return $this->media[$media_type];
		}
		
		
		/**
		 * Groups
		 *
		 * Fetches this artist's groups.
		 * That is, Music albums or Shows seasons.
		 *
		 * @param media_type		The type of media to filter it with. i.e: 'music', 'shows'
		 */
		public function groups($media_type)
		{
			// Make sure we have properties loaded by now.
			$this->checkSetProperties();
			
			// Define the table we are working with.
			$table = "media_{$media_type}_group";
			
			// Get groups from this artist, of the requested media type.
			$groups = $this->devkit->plugins()->mysql()->reader("SELECT $table.title, $table.codename FROM $table WHERE $table.artist_id = {$this->id} ORDER BY $table.title ASC");
			
			// Return the list of groups as an Array.
			return $groups->tableToArray();
		}
		
		/**
		 * Group
		 *
		 * Fetches a group from this artist.
		 */
		public function group($codename, $media_type)
		{
			// Make sure we have properties loaded by now.
			$this->checkSetProperties();
			
			// Return the group.
			return new Group($codename, $media_type, $this->id);
		}
		
		
		
		
		/***** Internal methods *****/
		
		/**
		 * The constructor stores the Artist codename.
		 */
		public function __construct($codename)
		{
			// Store this Artist codename.
			$this->codename = $codename;
			
			// Store dependencies.
			$this->devkit = \Artise::devkit();
			$this->mysql = $this->devkit->plugins()->mysql();
			$this->senses = $this->devkit->plugins()->senses();
		}
		
		
		/**
		 * Check Set Properties
		 *
		 * Sets the properties if they are not yet setted.
		 */
		private function checkSetProperties()
		{
			if(!$this->properties)
				$this->properties = $this->mysql->reader("SELECT id, name FROM artists WHERE codename = '{$this->codename}'")->rowToArray();
		}
		
		public function __get($name)
		{
			// Make sure we have properties loaded by now.
			$this->checkSetProperties();
			
			return isset($this->properties[$name]) ? $this->properties[$name] : null;
		}
	}
}