<?php

namespace Env\xLang;

class xLang extends \Ext\Source\Instances
{
	
	private
		$path,					// Path to the Lang directory.
		$active_lang = false,	// Current language.
		$manifest = false;		// Available languages.
	
	static public
		$instance;
	
	
	/*** User methods ***/
	
	
	
	/**
	 * Get available languages.
	 *
	 * @return boolean if $lang provided | Listing otherwise.
	 */
	public function manifest($lang = false)
	{
		
		// Ensure the list of available languages is loaded.
		$this->cs_manifest();
		
		// Return the availability of the requested language, if any.
		if ($lang !== false)
			return in_array($lang, $this->manifest);
		
		// Return the list of available Languages.
		return new \Readers\Listing($this->manifest);
	}
	
	
	
	/**
	 * Returns the active language.
	 *
	 * @return string
	 */
	public function active()
	{
		return $this->active_lang;
	}
	
	
	/* Get a particular language */
	
	public function &__call($function, $args)
	{
		return $this->lang($function);
	}
	
	
	
	/*** Functions acting as a bridge between xLang and the active Lang ***/
	
	public function phrase($phrase, $vars = false)
	{
		return $this->lang()->phrase($phrase, $vars);
	}
	
	public function word($word, $quantity = 0, $genre = 'n')
	{
		return $this->lang()->word($word, $quantity, $genre);
	}
	
	public function phraseExists($phrase = '')
	{
		return $this->lang()->phraseExists($phrase);
		
	}
	
	
	
	/*** Internal methods ***/
	
	/**
	 * Get a language
	 *
	 * @param string lang id. Fallbacks to active lang id.
	 * @return Lang object or null.
	 */
	private function &lang($lang = false)
	{
		try {
			// Was a Lang provided?
			if ($lang !== false) {
				// If the language does not exist, fallback to the default one.
				if ($this->manifest($lang) === false)
					$lang = $this->active_lang;
			} else {
				$lang = $this->active_lang;
			}
			
			return $this->getInstance($lang, array($this->path));
		} catch (\HunterException $e) {
			/* Failed to instance */
			
			// Report error.
			\Artise::hunter()->error($e);
			
			// Return empty object.
			$empty_object = new \Env\Source\EmptyObject("Lang->$lang()");
			return $empty_object;
		}
	}
	
	
	public function __construct($plugin = null)
	{
		if($plugin === null)
			self::$instance = &$this;
		
		try {
			// Stores the path depending wether it is a plugin Lang or not.
			if($plugin)
				$this->path = Artise_Path_Plugins . DS . $plugin . DS . 'langs' . DS;
			else
				$this->path = Artise_Path_Langs . DS;
			
			// Define the instances class.
			$this->instances_class_path = '\Env\xLang\Idiom';
		} catch (\HunterException $e) {
			throw $e;
		}
	}
	
	/**
	 * Checkset of Available languages.
	 */
	private function cs_manifest()
	{
		if ($this->manifest === false) {
			/* Load the languages list */
			
			// Set the files to avoid.
			$avoids = array('.', '..', '.svn', '.DS_STORE');
			
			// Opens the directory.
			$d = dir($this->path);
			
			// Set available to Array.
			$this->manifest = array();
			
			// Read throug the directory.
			while(false !== ($lang = $d->read())) {
				// Ignore those to avoid.
				if(in_array($lang, $avoids))
					continue;
				
				// Check if the file exists.
				if(is_file($this->path . $lang) && strlen($lang) > 4) {
					// If it's an XML file, add it to the list.
					if (strtolower(substr($lang, -4)) == '.xml')
						$this->manifest[] = substr($lang, 0, -4);
				}
			}
	
			// Close the directory.
			$d->close();
		}
	}
	
	/**
	 * Setea el lenguaje a utilizar en el sitio
	 * @param string $lang
	 * @return void
	 */
	public function set($lang) {
		// Current language can only be set once.
		if ($this->active_lang !== false)
			return false;
		
		// Ensure the list of available languages is loaded.
		$this->cs_manifest();
		
		if($this->manifest($lang))
			$this->active_lang = $lang;
		else
			return false;
		
		return true;
	}
	
	
	
	/**
	 * Gets an instance by reference of xLang.
	 */
	public static function &get()
	{
		return self::$instance;
	}
	
}



?>