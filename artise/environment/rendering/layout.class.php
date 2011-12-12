<?php
namespace Ext\Rendering
{
	class Layout extends \Ext\Source\SiteScope
	{
		private
			$files = array(), /* array(filename => array(html, array outputs)) */
			$vars = array(),
			$zones = array();
		
		public $filename = '';		// Made Public for Hunter: hunter.class.php
		protected $html = '';
		
		
		
		
		/*** User methods ***/
		
		
		final public function &zone($name)
		{
			if(isset($this->zones[$name])) {
					return $this->zones[$name];
			}
			else {
				$this->zones[$name] = new Layout\Zone($this->site, $this->devkit());
				return $this->zones[$name];
			}
		}

		final public function set($filename)
		{
			$this->filename = $filename;
		}
		
		final public function __set($name, $value)
		{
			$this->vars[$name] = $value;
		}
		
		
		
		
		
		
		/*** Internal methods ***/
		
	
		final public function __toString()
		{
			try {
				// Render the Page if it's a stand alone.
				if(method_exists($this, 'render'))
					$this->render();
				
				$this->load(); //Load html
				$this->html = slashSlashedTokens($this->html);
				$this->replacePainters();
				$this->replacePhrases();
				$this->replaceVars();
				$this->replaceZones();
				$this->html = restoreSlashedTokens($this->html);
				$this->html = restoreTokens($this->html);
			}
			catch(\HunterException $e) {
				$this->devkit()->hunter()->warning($e->getTitle(), $e->getDescription(), $e->getTip());
			}
		
			return $this->html;
		}

		final private function load()
		{
			if($this->filename) {
				if(!isset($this->files[$this->filename])) {
					$path = \Artise::router()->getPath(Artise_Path_Layouts) . DS;
					$filename = $path . $this->filename . '.html';
					
					if(!file_exists($filename))
						throw new \HunterException(	new \Env\Source\DummyIdiom('E0050', array('element' => 'Layout')),
													new \Env\Source\DummyIdiom('file_path_abstract', array(	'path' => $path,
																											'filename' => $this->filename)),
													'TIP_E0050');
					
					if(!is_readable($filename))
						throw new \HunterException(	new \Env\Source\DummyIdiom('E0051', array('element' => 'Layout')),
													new \Env\Source\DummyIdiom('file_path_abstract', array(	'path' => $path,
																											'filename' => $this->filename)),
													'TIP_E0051');

					$this->files[$this->filename]['html'] = file_get_contents($filename);
					$this->files[$this->filename]['painters'] = $this->matchPainters();
					$this->files[$this->filename]['phrases'] = $this->matchPhrases();
				}
				$this->html = $this->files[$this->filename]['html']; //Global to local (copy)
			} else {
				throw new \HunterException(	new \Env\Source\DummyIdiom('W0050', array('element' => 'Layout')),
											null,
											'TIP_E0051');
			}
		}
	
		final private function matchPainters()
		{
			preg_match_all('#<!--painter:(.*)-->#U', $this->files[$this->filename]['html'], $matches, PREG_PATTERN_ORDER);
			return $matches[1];
		}

		final private function replacePainters()
		{
			foreach($this->files[$this->filename]['painters'] as $painter) {
				$this->html = str_replace('<!--painter:' . $painter . '-->', slashTokens((string)new \Env\Rendering\Painter($painter, $this->site(), $this->devkit())), $this->html);
			}
		}

		final private function matchPhrases()
		{
			preg_match_all('#\@([a-zA-Z0-9_]+)#s', $this->files[$this->filename]['html'], $matches);
			arsort($matches[1]); //Reverse
			return $matches[1];
		}

		final private function replacePhrases()
		{
			foreach($this->files[$this->filename]['phrases'] as $phrase) {
				if($this->site()->lang()->phraseExists($phrase)) {
					$value = $this->site()->lang()->phrase($phrase);
					$this->html = str_replace('@' . $phrase, slashTokens($value), $this->html);
				}
			}
		}

		final private function replaceVars()
		{
			//arsort($this->vars); //Reverse
			
			// Sort by Key Length, descending. Prevent replacing a part of a token as a full token.
			sortByKeyLength($this->vars);
			
			foreach($this->vars as $name => $value) {
				$this->html = str_replace('%' . $name, slashTokens($value), $this->html);
			}
		}
		
		final private function replaceZones()
		{
			foreach($this->zones as $name => $piece) {
				$this->html = str_replace('<!--zone:' . $name . '-->', (string)$piece, $this->html);
			}
		}
	
		final public function __clone()
		{
			trigger_error('Piece cannot be clonned', E_USER_ERROR);
		}
	}
}
?>
