<?php
namespace actions\library
{
	class addMusicAlbum extends \Ext\Action
	{
		public function execute()
		{
			/*** Settings ***/
			
			// Album.
			$album_title = 'The Ziur Movement';
			$album_codename = 'The-Ziur-Movement';
			$album_year = 2009;
			$artist_id = 12;
			$album_id = false;
			
			// Songs.
			$songs = array(	'Movement II - Vasat',
							'Movement III - Ziur',
							'Cold Arrival',
							'Lucid Drawl'
							);
			$media_format = 'mp3';
			$genre = 32;
			$license_id = 6;
			
			
			/*** Execution ***/
			
			// Fetch the MySQL plugin.
			$mysql = $this->devkit()->plugins()->mysql();
			
			if(!$album_id)
			{
				// Create the album.
				$album_id = $mysql->execute("INSERT INTO media_music_group VALUES (null, '$album_title', '$album_codename', '$album_year', $artist_id)")->insertId();
			}
			
			// Create the song entries.
			foreach($songs as $track_order => $song) {
				$track_order ++;
				$song_title = $song;
				$song_codename = $this->createCodename($song);
				
				$mysql->execute("INSERT INTO media_music VALUES (null, '$song_title', null, '$artist_id', '$song_codename', '$media_format', null, null, '$genre', '$album_id', '$track_order', '$license_id', 0)");
			}
		}
		
		private function createCodename($string)
		{
			$string = str_replace(	array(' ', ',', '!', '.'),
									array('-', '-', '',  '-'),
									$string);
			
			return $string;
		}
	}
}

?>