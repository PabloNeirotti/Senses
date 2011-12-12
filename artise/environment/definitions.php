<?php
/**
 * ASCII
 */
define('TAB', "\t");
define('CR', "\n");
define('LF', "\r");
define('DS', '/');
define('WDS', '\\');


define('AVOIDED_DIRECTORIES', '..svn.DS_STORE');
define('HUNTER_LOG_READ_DEFAULT', false);


define('URI', $_SERVER['REQUEST_URI']);
define('Root', $_SERVER['DOCUMENT_ROOT']);

/**
 * File System
 */
define('Artise_Path_Palettes', Root . '/artise/design/palettes');
define('Artise_Path_Painters', Root . '/artise/code/painters');
define('Artise_Path_Actions', Root . '/artise/code/actions');
define('Artise_Path_Renders', Root . '/artise/code/renders');
define('Artise_Path_Layouts', Root . '/artise/design/layouts');
define('Artise_Path_Plugins', Root . '/artise/plugins');
define('Artise_Path_Langs', Root . '/artise/langs');
define('Artise_Path_Hunter_Logs', Root . '/artise/logs');
define('Artise_Path_Cfg_File', Root . '/artise/config.xml');
define('Artise_Path_Rtg_File', Root . '/artise/code/routing.xml');


/**
 * DocType
 * %s se reemplaza por el idioma actual
 */
define('DOCTYPE_HTML_401_STRICT', '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//%s" "http://www.w3.org/TR/html4/strict.dtd">');
define('DOCTYPE_HTML_401_TRANSITIONAL', '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//%s" "http://www.w3.org/TR/html4/loose.dtd">');
define('DOCTYPE_HTML_401_FRAMESET', '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//%s" "http://www.w3.org/TR/html4/frameset.dtd">');
define('DOCTYPE_XHTML_10_STRICT', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//%s" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
define('DOCTYPE_XHTML_10_TRANSITIONAL', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//%s" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
define('DOCTYPE_XHTML_10_FRAMESET', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//%s" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">');
define('DOCTYPE_XHTML_11', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 //%s" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">');
define('DOCTYPE_HTML_5', '<!DOCTYPE HTML>');


/*** Head ***/
define('Head_Feed_RSS', 'text/xml');
define('Head_Feed_RSS2', 'application/rss+xml');
define('Head_Feed_Atom', 'application/atom+xml');



/**
 * Errors
 */


define('Error_Failed_Loading', -1);


/**
 * Routing
 */

define('Routing_Request_Default', 'std');
define('Standard_Request', 'std');
define('AJAX_Request', 'ajax');

/**
 * Miscelanious
 */
define('Artise_Version', '1.0.0alpha');
?>
