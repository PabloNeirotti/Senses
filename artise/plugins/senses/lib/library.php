<?php
/**
 *
 *		Library : Video
 *
 */

namespace plugins\senses
{
	class Library
	{
		// Media tables.
		private
			$media,
			$media_types = array(),
			$media_object = array();
		
		// Dependencies.
		private
			$devkit,
			$mysql;
		
		
		/***** User methods *****/
		
		/**
		 * Get a Library's media.
		 */
		public function &media($media_type)
		{
			// Check if this kind of media exists.
			if(!isset($this->media_types[$media_type]))
				throw new \HunterException('SENSES_E0002', $media_type);
			
			// If this object does not have this media table loaded yet, load it first.
			if(!isset($this->media[$media_type])) {
				// Get media.
				$this->media[$media_type] = $this->mysql->reader("SELECT 	media_{$media_type}.*,
																			licenses.caption AS license,
																			artists.name AS artist_name,
																			artists.codename AS artist_codename
																	FROM media_{$media_type}, artists, licenses
																	WHERE 	media_{$media_type}.artist_id = artists.id AND
																			media_{$media_type}.license_id = licenses.id");
																	
				// Send to Array.
				$this->media[$media_type] = $this->media[$media_type]->tableToArray();
			}
			
			// Return the media array.
			return $this->media[$media_type];
		}
		
		/**
		 * Get a Library's media specific object.
		 */
		public function &mediaObject($id, $media_type)
		{
			// Check if this kind of media exists.
			if(!isset($this->media_types[$media_type]))
				throw new \HunterException('SENSES_E0002', $media_type);
			
			// If this object does not have this media table loaded yet, load it first.
			if(!isset($this->media_object[$media_type])) {
				// Get media.
				$this->media_object[$media_type] = new Library\Media($id, $media_type);
			}
			
			// Return the media array.
			return $this->media_object[$media_type];
		}
		
		/**
		 * Get the Library's media types listing.
		 *
		 * @return array	Example: (	video => Array(name => 'video', audio => true, video => true, ...),
		 *								music => Array(name => 'music', audio => true, video => false, ...),
		 *								... )
		 */
		public function &mediaTypesListing() {
			return $this->media_types;
		}
		
		
		
		
		
		/***** Internal methods *****/
		
		/**
		 * The constructor stores the list of media types.
		 */
		public function __construct(&$media_types)
		{
			// Store the media types.
			$this->media_types =& $media_types;
			
			// Store dependencies.
			$this->devkit = \Artise::devkit();
			$this->mysql = $this->devkit->plugins()->mysql();
		}
		
	}
}