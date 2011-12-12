<?php
namespace Env\Rendering\Page
{
	final class Head extends \Ext\Source\SiteScope
	{
		private
			$tags = array();		// List of tags appended to the Head.
		
		public
			$title;					// Page title.
		
		/*** User methods ***/
		
		
		/**
		 * Add a CSS file.
		 *
		 * @param path - Path to the CSS file.
		 * @param media - Media on which to use the CSS file.
		 * @return Referential instance of the Tag.
		 */
		public function &addCSS($path, $media = null)
		{
			// Set all the CSS attributes.
			$attributes = array('href' => $path,
								'rel' => 'stylesheet',
								'type' => 'text/css'
								);
			
			// Add optional attribute Media.
			if ($media != null)
				$attributes['media'] = $media;
			
			// Create the tag and return it.
			return $this->addTag('link', $attributes);
		}
		
		
		/**
		 * Add a JavaScript file.
		 *
		 * @param path - Path to the JS file.
		 * @return Referential instance of the Tag.
		 */
		public function &addJS($path)
		{
			// Set all the JS attributes.
			$attributes = array('src' => $path,
								'type' => 'text/javascript'
								);
			
			// Create the tag and return it.
			return $this->addTag('script', $attributes, false);
		}
		
		
		/**
		 * Add a Meta tag.
		 *
		 * @param name
		 * @param content
		 * @return Referential instance of the Tag.
		 */
		public function &addMeta($name, $content = '')
		{
			// Set all the Meta attributes.
			$attributes = array('name' => $name,
								'content' => $content
								);
			
			// Create the tag and return it.
			return $this->addTag('meta', $attributes);
		}
		
		
		/**
		 * Add a Feed tag.
		 *
		 * @param type
		 * @param url
		 * @return Referential instance of the Tag.
		 */
		public function &addFeed($type, $url)
		{
			$feeds_titles = array(	Head_Feed_RSS => 'RSS .92',
									Head_Feed_RSS2 => 'RSS 2.0',
									Head_Feed_Atom => 'Atom 0.3');
			
			// Set all the Link attributes.
			$attributes = array('href' => $url,
								'rel' => 'alternate',
								);
			
			// Add optional attribute Title.
			if (isset($feeds_titles[$type]))
				$attributes['title'] = $feeds_titles[$type];
			
			// Set the Type attribute.
			$attributes['type'] = $type;
			
			// Create the tag and return it.
			return $this->addTag('link', $attributes);
		}
		
		
		/**
		 * Add a Link tag.
		 *
		 * @param type
		 * @param url
		 * @return Referential instance of the Tag.
		 */
		public function &addLink(array $attributes)
		{
			// Create the tag and return it.
			return $this->addTag('link', $attributes);
		}
		
		
		/**
		 * Add a tag to the Head
		 *
		 * @return Referential instance of the Tag.
		 */
		public function &addTag($name, $attributes = null, $self_closing = true)
		{
			$tag = new \Ext\Rendering\Page\Tag($name, $attributes, $self_closing);
			
			$this->tags[] = &$tag;
			
			return $tag;
		}
		
		
		/*** Internal methods ***/
		
		public function __toString()
		{
			// Prepare the Title tag.
			$title = $this->addTag('title', array(), false);
			
			$title->innerHTML = $this->title;
			
			// Join Title and other tags together.
			$tags = array_merge(array($title), $this->tags);
			
			// Return the HTML code for Head.
			return implode($tags, PHP_EOL);
		}
		
		
	}
}
?>
