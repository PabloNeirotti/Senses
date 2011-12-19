<?php
namespace actions\pages
{
	class library extends \Ext\Action
	{
		public function execute()
		{
			// Split the command by it's segments.
			if($this->site()->router()->uri()->object)
				$command = explode(':', $this->site()->router()->uri()->object);
			else
				$command = array();
			
			// Fetch the Senses plugin.
			$senses = $this->devkit()->plugins()->senses();
			
			// Define default value for the Page title.
			$page_title = null;
			
			// Define default value for the Page's stylesheet and thumbnail.
			$stylesheet = null;
			$thumbnail = null;
			
			// Define an empty request array, where we will store the request data we collect.
			$request = array();
			
			try
			{
				
				
				// Check if a media type was provided
				if(isset($command[0]))
				{
					// CURRENT PATH: /library/<media>/
					
					/* Media type was provided. Send this media's item listing. */
					
					// Store the requested media,
					$request['media'] = $command[0];
					
					// Fetch the media type object.
					$media_type = $senses->media_types[$request['media']];
					
					// Split handling wether navigation of the media will be artist orientated or not.
					if($media_type->library_artist_priority)
					{
						// Check wether an artist was requested or not.
						if(isset($command[1]))
						{
							// CURRENT PATH: /library/<media>:<artist>/
							
							// Store the artist.
							$request['artist'] = $command[1];
							
							// Fetch the requested artist.
							$artist = $senses->artist($request['artist']);
							
							// Check wether this media supports groups or not.
							if($media_type->groups)
							{
								// Check wether a group was requested or not.
								if(isset($command[2]))
								{
									// CURRENT PATH: /library/<media>:<artist>:<group>/
									
									/* Group requested. Send the items in this group, from this artist, of this media type. */
									
									// Store the group request.
									$request['group'] = $command[2];
									
									// Fetch the group object.
									$group = $artist->group($request['group'], $request['media']);
									
									// Add page stylesheet if one exists.
									// [HARDCODED] I'm just sending more of these stylesheets =P
									if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/library/{$artist->codename}/style.css"))
									{
										$stylesheet = "/library/{$artist->codename}/style.css";
									}
									
									// Set this page's thumbnail.
									$thumbnail = $senses->getThumbnail('/library/'
													. $artist->codename
													. '/' . $request['media']
													. '/' . $group->codename
													. '/thumb.jpg');
									
									// Define player type. (audio or video).
									$player_type = $media_type->video ? 'video' : 'audio';
									
									// Define the page type.
									$page_type = 'items_' . $media_type->view_type;
									
									// Define the page title.
									$page_title = $group->title;
									
									// Fetch the requested media from this artist.
									$items_list = $group->media();
									
									// Pre-process to add data.
									foreach($items_list as &$row)
									{
										$row['link_type'] = 'media';
										$row['player_type'] = $player_type;
										$row['media_type'] = $media_type->name;
										$row['auto_playlist'] = $media_type->auto_playlist;
										
										// Media source format: /library/<artist_name>/<media_type>/<group>/<codename>.<file_ext>
										$media_source = "/library/"
														. $artist->codename
														. "/{$request['media']}"
														. "/{$group->codename}/"
														. $row['codename'];
														
										$row['media_src'] = $row['external_url'] ? $row['external_url'] : $media_source . '.' . $row['file_ext'];
										$row['thumbnail'] = $senses->getThumbnail($media_source . '.jpg');
										$row['artist_name'] = $artist->name;
										$row['artist_codename'] = $artist->codename;
										
										// Remove unnecesary rows.
										unset($row['file_ext'], $row['external_url']);
									}
								}
								else
								{
									// CURRENT PATH: /library/<media>:<artist>/
									
									/* No group requested. SEND the GROUPS from this artist, of this media type. */
									
									// Add page stylesheet if one exists.
									// Stylesheets are only sent on the first "child" page of the Stylesheet owner (this case, an Artist).
									if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/library/{$artist->codename}/style.css"))
									{
										$stylesheet = "/library/{$artist->codename}/style.css";
									}
									
									// Define the page type.
									$page_type = 'items_' . $media_type->groups_view_type;
									
									// Define the page title.
									$page_title = $artist->name;
									
									// Fetch the groups from this artist.
									$groups_list = $artist->groups($request['media']);
									
									// Create the items list.
									$items_list = array();
									
									// Add the required data to the items list.
									foreach($groups_list as $row)
									{
										$items_list[] = array(	'link_type' => 'folder',
																'caption' => $row['title'],
																'link' => "/library/{$request['media']}:{$artist->codename}:{$row['codename']}",
																'thumbnail' => $senses->getThumbnail('/library/'
																				. $artist->codename
																				. '/' . $request['media']
																				. '/' . $row['codename']
																				. '/thumb.jpg')
																);
									}
								}
							}
							else
							{
								// CURRENT PATH: /library/<media>:<artist>/
								
								/* Does not support groups. SEND MEDIA ITEMS from this Artist, of this media type. */
								
								// Add page stylesheet if one exists.
								// Stylesheets are only sent on the first "child" page of the Stylesheet owner (this case, an Artist).
								if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/library/{$artist->codename}/style.css"))
								{
									$stylesheet = "/library/{$artist->codename}/style.css";
								}
								
								// Define player type. (audio or video).
								$player_type = $media_type->video ? 'video' : 'audio';
								
								// Define the page type.
								$page_type = 'items_' . $media_type->view_type;
								
								// Define the page title.
								$page_title = $artist->name;
								
								// Fetch the requested media from this artist.
								$items_list = $artist->media($request['media']);
								
								// Pre-process to add data.
								foreach($items_list as &$row)
								{
									$row['link_type'] = 'media';
									$row['player_type'] = $player_type;
									$row['media_type'] = $media_type->name;
									
									// Media source format: /library/<artist_name>/<media_type>/<codename>.<file_ext>
									$media_source = "/library/"
													. $artist->codename
													. "/{$request['media']}/"
													. $row['codename'];
													
									$row['media_src'] = $row['external_url'] ? $row['external_url'] : $media_source . '.' . $row['file_ext'];
									$row['thumbnail'] = $senses->getThumbnail($media_source . '.jpg');
									$row['artist_name'] = $artist->name;
									$row['artist_codename'] = $artist->codename;
									
									// Remove unnecesary rows.
									unset($row['file_ext'], $row['external_url']);
								}
							}
						}
						else
						{
							// CURRENT PATH: /library/<media>/
							
							/* Send this media type's ARTISTS listing */
							
							// Define the page type.
							$page_type = 'items_' . $media_type->artists_view_type;
							
							// Fetch the artists from this media type.
							$artists_list = $senses->getArtists($request['media']);
							
							// Create the items list.
							$items_list = array();
							
							// Add the required data to the items list.
							foreach($artists_list as $row)
							{
								$items_list[] = array(	'link_type' => 'folder',
														'caption' => $row['name'],
														'link' => "/library/{$request['media']}:{$row['codename']}",
														'thumbnail' => $senses->getThumbnail("/library/{$row['codename']}/thumb.jpg")
														);
							}
						}
					}
					else
					{
						// CURRENT PATH: /library/<media>/
						
						/* Send this media's items listing */
						
						// Store the grouping, if any.
						//if(isset($command[1]))
							//$request['grouping'] = $command[1];
						// [WARNING] Grouping not yet implemented. Used for Music Albums and Series Seasons.
						
						// Define the page type.
						$page_type = 'items_' . $media_type->view_type;
						
						// Fetch the requested media.
						$items_list = $senses->library()->media($request['media']);
						
						// Define player type. (audio or video).
						$player_type = $media_type->video ? 'video' : 'audio';
						
						// Pre-process to add data.
						foreach($items_list as &$row)
						{
							$row['link_type'] = 'media';
							$row['player_type'] = $player_type;
							$row['media_type'] = $media_type->name;
							
							// Media source format: /library/<artist_name>/<media_type>/(<group>/)<codename>.<file_ext>
							
							if($media_type->grouping)
							{
								// Media under a grouping.
								$media_source = "/library/"
												. $row['artist_codename']
												. "/{$request['media']}/"
												. $row['codename'];
							}
							else
							{
								// Media not in a grouping.
								$media_source = "/library/"
												. $row['artist_codename']
												. "/{$request['media']}/"
												. $row['codename'];
							}
							
							if(!$row['file_ext'])
							{
								// Use the external URL.
								$row['media_src'] = $row['external_url'];
							}
							else
							{
								// Define the media source.
								// Grouping changes the way the path is generated.
								if($media_type->grouping)
								{
									// Use the file extension to build the path.
									$row['media_src'] = $media_source . '.' . $row['file_ext'];
								}
								else
								{
									// Use the file extension to build the path.
									$row['media_src'] = $media_source . '/movie.' . $row['file_ext'];
								}
								
								
							}
							
							$row['thumbnail'] = $senses->getThumbnail($media_source . '/thumb.jpg');
							
							// Remove unnecesary rows.
							unset($row['file_ext'], $row['external_url']);
						}
					}
				}
				else
				{
					// CURRENT PATH: /library/
					
					/* No media type was provided. Send media listing. */
					
					// Define the page type.
					$page_type = 'media_types';
					
					// Fetch the available media types.
					$media_types = $senses->library()->mediaTypesListing();
					
					$items_list = array();
					
					// Build the list.
					foreach($media_types as $media_type)
					{
						// Only information relevant to the client is passed.
						$items_list[] = array(	'link_type' => 'folder',
												'caption' => $this->site()->lang()->phrase("media_{$media_type->name}"),
												'link' => "/library/{$media_type->name}"
												);
					}
				}
				
				// Return JSON with the Page info and Items listing.
				return array(	array(	'type' => $page_type,			// Page type.
										'title' => $page_title,			// Title for this page.
										'stylesheet' => $stylesheet,	// Stylesheet for this page.
										'thumbnail' => $thumbnail),		// Thumbnail for this page.
								$items_list);
			
			}
			catch(\HunterException $e)
			{
				// Report the error to Hunter.
				$this->devkit()->hunter()->error($e);
			}
		}
	}
}

?>