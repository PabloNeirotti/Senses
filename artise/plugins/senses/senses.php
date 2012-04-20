<?php
/**
 *
 *		Senses Plugin
 *
 *
 *
 *
 * SENSES STRUCTURE:
 *
 * Artist:
 *  Contains media created by this artist.
 *
 * Library:
 *  Contains all media created by any artist in the library.
 */

namespace plugins\senses
{
	include dirname(__FILE__) . '/lib/artist.php';
	include dirname(__FILE__) . '/lib/group.php';
	include dirname(__FILE__) . '/lib/media_type.php';
	include dirname(__FILE__) . '/lib/library.php';
	include dirname(__FILE__) . '/lib/library/media.php';
	include dirname(__FILE__) . '/lib/library/video.php';
	
	class Main extends \Ext\Plugin
	{
		private
			$library = null,
			$artists = array();
		
		public
			$media_types = array();
			
		
		public function __initialize()
		{
			// Fetch media types from configuration.
			$media_types = $this->config()->get('media_types');
			
			// Store the media types as objects.
			foreach($media_types['media'] as $media_type_settings)
			{
				// Store the media type object.
				$media_type = new MediaType($media_type_settings, $this->lang());
				$this->media_types[$media_type->name] = $media_type;
			}
		}
		
		public function getMovies()
		{
			return $this->devkit()->plugins()->mysql()->reader('SELECT * FROM videos ORDER BY id ASC');
		}
		
		public function &library()
		{
			if(!isset($this->library))
			{
				$this->library = new Library($this->media_types);
			}
			
			return $this->library;
		}
		
		public function &artist($codename)
		{
			if(!isset($this->artists[$codename]))
			{
				$this->artists[$codename] = new Artist($codename);
			}
			
			return $this->artists[$codename];
		}
		
		
		/**
		 * Get Artists
		 *
		 * Fetches artists. Can be filtered by media types they have published.
		 *
		 * @param media_type	(optional string) Media type to filter artists by.
		 */
		public function getArtists($media_type = false, $return_reader = false)
		{
			if($media_type)
			{
				// Get artists that have this media type published.
				$artists = $this->devkit()->plugins()->mysql()->reader("SELECT artists.name, artists.codename FROM artists, media_{$media_type} WHERE artists.id = media_{$media_type}.artist_id GROUP BY artists.name ORDER BY artists.last_activity DESC");
			}
			else
			{
				// Get all artists.
				$artists = $this->devkit()->plugins()->mysql()->reader("SELECT name, codename FROM artists ORDER BY name");
			}
			
			if($return_reader)
			{
				return $artists;
			}
			else
			{
				return $artists->tableToArray();
			}
		}
		
		
		/**
		 * Get Thumbnail
		 *
		 * Either returns the actual thumbnail, or a fallback if it doesn't exists.
		 */
		public function getThumbnail($path)
		{
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $path))
			{
				return $path;
			}
			else
			{
				// Return either a regular or 2x thumbnail, depending on which kind of thumb we were provided with.
				return strpos($path, '-2x.') > 0 ?
						'/graphics/thumbs/no-thumb.png' :
						'/graphics/thumbs/no-thumb-2x.png';
			}
		}
	}
}