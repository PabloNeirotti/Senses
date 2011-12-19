<?php
namespace actions\library
{
	class addMusicAlbum extends \Ext\Action
	{
		public function execute()
		{
			/*** Settings ***/
			
			// Album.
			$album_title = 'Indie Rocket Science';
			$album_codename = 'Indie-Rocket-Science';
			$album_year = 2010;
			$artist_id = 11;
			$album_id = 6;
			
			// Songs.
			$songs = array(	'Deangelo Vickers',
							'What Is Hip-Hop? (featuring KRS-One, Rittz and mc chris)',
							'Black and Yellow T-Shirts (featuring MC Frontalot)',
							'Lars Attacks!',
							'Twenty-Three (2011 remix) (featuring Weerd Science)',
							'Annabel Lee R.I.P.',
							'Distant Planet (the Roswell Incident',
							'Me and the Mouse (featuring Random)',
							'Male Feminist (featuring Mazeman)',
							'By the Time I Get Shot Up in Arizona (featuring Sole)',
							'Industry 1-8-7 (featuring Weerd Science)',
							'Paul and Phil Are Friends (featuring Nick Lavallee)',
							'Lord of the Fries',
							'The Alien Song (featuring Kosha Dillz, Geo of the Blue Scholars and Homeboy Sandman)',
							'Living in the Future 2.5 (featuring Akira the Don, Big Narstie, Eddie Argos and Scroobius Pip)',
							'Soledad (featuring MC Bob Nielsen)',
							'#busbros (featuring Grieves)',
							'Art of Darkness (featuring Sage Francis)'
							);
			$media_format = 'mp3';
			$genre = 15;
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
				
				$mysql->execute("INSERT INTO media_music VALUES (null, '$song_title', '$artist_id', '$song_codename', '$media_format', null, null, '$genre', '$album_id', '$track_order', '$license_id', 0)");
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