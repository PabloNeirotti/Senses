<?php
namespace Env\Config
{
	final class Loader
	{
		
		/**
		 * Load and parse a valid config file
		 *
		 * @author Pixelsize Artise team
		 * @param string $filename Given File
		 * @param [null|array] &$errors Errors found in xml format
		 * @return [error-const|Parser Obj]
		 */
		static public function load($filename, &$errors = null)
		{
			if(!file_exists($filename))
				throw new \HunterException(	new \Env\Source\DummyIdiom('E0050', array('element' => 'configuration')),
											new \Env\Source\DummyIdiom('file_path', array('path' => $filename)),
											'TIP_E0050');
			
			if(!is_readable($filename))
				throw new \HunterException(	new \Env\Source\DummyIdiom('E0051', array('element' => 'configuration')),
											new \Env\Source\DummyIdiom('file_path', array('path' => $filename)),
											'TIP_E0051');
			
			$xml = secure_simplexml_load_file($filename, $errors);
			
			if($xml) {
				return new Handler($xml);
			}
			else {
				throw new \HunterException(	'E0003',
											array('file_path', array('path' => $filename)),
											'TIP_E0013');
			}
		}
	}
}
?>