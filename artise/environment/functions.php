<?php
function dim_count(&$stack)
{
	static $fn = __FUNCTION__;

	if(is_array(reset($stack))) {
		return $fn(reset($stack)) + 1;
	}
	else {
		return 1;
	}
}

function array_filter_keys($array, $callback, $filtered_output = "")
{
   $ret = array();
   foreach($array as $key=>$value) {
       if($callback($key,$value)) {
           if(is_array($value)) {
               $ret[$key] = array_filter_keys($value, $callback, $filtered_output);
           }
           elseif(is_object($value)) {
               $ret[$key] = array_filter_keys(get_object_vars($value), $callback, $filtered_output);
           }
           else {    
               $ret[$key]=$value;
           }
       }
       else {
           $ret[$key]=$filtered_output;
       }
   }
   return $ret;
}


/**
 * Modo seguro simplexml_load_string
 *
 * @author Pixelsize Artise team
 *
 * @param string $data XML Content
 * @param array &$errors lista de errores de parseo en el xml
 * @return object SimpleXML o FALSE si hay errores
 *
 */
function secure_simplexml_load_string($data, &$errors = NULL, $libXMLOptions = LIBXML_NOCDATA)
{
	libxml_use_internal_errors(true);

	$xml = simplexml_load_string($data, 'SimpleXMLElement', $libXMLOptions);

	if($xml) {
		return $xml;
	}
	else {
		$errors = array();

		foreach(libxml_get_errors() as $error) {
			$errors[] = (array)$error;
		}

		libxml_clear_errors();

		return false;
	}
}


/**
 * Modo seguro simplexml_load_file
 *
 * @author Pixelsize Artise team
 *
 * @param string $filename XML File
 * @param array &$errors lista de errores de parseo en el xml
 * @return object SimpleXML o FALSE si hay errores
 *
 */
function secure_simplexml_load_file($filename, &$errors = NULL, $libXMLOptions = LIBXML_NOCDATA)
{
	libxml_use_internal_errors(true);

	$xml = simplexml_load_file($filename, 'SimpleXMLElement', $libXMLOptions);

	if($xml) {
		return $xml;
	}
	else {
		$errors = array();

		foreach(libxml_get_errors() as $error) {
			$errors[] = (array)$error;
		}

		libxml_clear_errors();
		
		return false;
	}
}


/**
 * Transforma un objeto simplexml a array
 *
 * @author Pixelsize Artise team
 *
 * @param object $object
 * @param bool $constants Tomar valores de constantes
 * @return object SimpleXML o FALSE si hay errores
 *
 */
function simplexml_to_array($object)
{
	if(!is_object($object) && !is_array($object)) {
		return $object;
	}

	if(is_object($object)) {
		$object = get_object_vars($object);
	}

	/* A veces cuando se lee un objeto XML y la etiqueta estaba vacia, devuelve un array vacio
lo convierto a string */
	if(count($object) === 0) {
		return '';
	}
	
	$mapped = array_map(__FUNCTION__, $object);
	
	if(isset($mapped['@attributes'])) {
		return array($mapped);
	}
	return $mapped;
}


/**
 * Convierte un objeto a array
 *
 * @author Pixelsize Artise team
 *
 * @param object $object
 * @return array
 *
 */
function object_to_array($object)
{
	if(!is_object($object) && !is_array($object)) {
		return $object;
	}

	if(is_object($object)) {
		$object = get_object_vars($object);
	}

	return array_map(__FUNCTION__, $object);
}


/**
 * Reemplaza a escribir header location
 *
 * @author Pixelsize Artise team
 *
 * @param string $url Url a redireccionar
 * @return void
 *
 */
function redirect($url)
{
	header('Location: ' . $url);
	exit;
}


/**
 * Obfusca las posibles etiquetas de token encontradas en el source ingresado
 *
 * @param string $str
 * @return string
 */
function slashTokens($str)
{
	return str_replace(array('@', '<!--block:', '%'), array('@///', '<!///--block:', '%///'), $str);
}


/**
 * Revierte lo producido con slashTokens
 *
 * @param string $str
 * @return string
 */
function restoreTokens($str)
{
	return str_replace(array('@///', '<!///--block:', '%///'), array('@', '<!--block:', '%'), $str);
}


/**
 * Slashes tokens that have been slashed to be skipped.
 *
 * @param string $str
 * @return string
 */
function slashSlashedTokens($str)
{
	return str_replace(array('\@', '\%'), array('\@///', '\%///'), $str);
}


/**
 * Restores slashSlashedTokens.
 *
 * @param string $str
 * @return string
 */
function restoreSlashedTokens($str)
{
	return str_replace(array('\@///', '\%///'), array('@', '%'), $str);
}

/**
 * Sorts an Array by length of it's string keys. (DESCENDING)
 *
 * @param array &$array
 */
function sortByKeyLength(&$array)
{
	uksort($array, 'sortByKeyLength_aid');
}

/**
 * Asissting function for sortByKeyLength.
 */
function sortByKeyLength_aid($a, $b){
     return strlen($b) - strlen($a);
}

?>
