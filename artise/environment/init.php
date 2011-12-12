<?php

/*** Artise initialization ***/

include 'artise.php';

Artise::initialize();





/*** Includes ***/

include 'definitions.php';
include 'functions.php';
include 'devkit.php';

include 'source/dummyidiom.class.php';
include 'source/emptyobject.class.php';
include 'source/instances.class.php';

include 'readers/model.interface.php';
include 'readers/table.class.php';
include 'readers/list.class.php';

include 'config/handler.class.php';
include 'config/loader.class.php';
include 'events/callback.class.php';
include 'events/handler.class.php';
include 'exception/exception.class.php';
include 'html/tag.class.php';

include 'hunter/constants.php';
include 'hunter/exception.class.php';
include 'hunter/hunter.class.php';
include 'hunter/logger.class.php';
include 'hunter/PHPErrorHandler.class.php';

include 'lang/idiom.class.php';
include 'lang/lang.class.php';

include 'plugins/loader.class.php';
include 'plugins/manager.class.php';
include 'plugins/pluginset.class.php';
include 'plugins/plugin.class.php';

include 'source/sitescope.class.php';

include 'rendering/block.class.php';
include 'rendering/document.class.php';
include 'rendering/head.class.php';
include 'rendering/body.class.php';
include 'rendering/layout.class.php';
include 'rendering/page.class.php';
include 'rendering/ext.painter.class.php';
include 'rendering/env.painter.class.php';
include 'rendering/render.painter.class.php';
include 'rendering/palette.class.php';
include 'rendering/slate.class.php';
include 'rendering/zone.class.php';

include 'routing/action.class.php';
include 'routing/file.class.php';
include 'routing/procedure.class.php';
include 'routing/request.class.php';
include 'routing/router.class.php';
include 'routing/rule.class.php';
include 'routing/storage.class.php';

include 'site/site.php';
include 'uri/slicer.class.php';
include 'uri/url.class.php';
include 'uri/parser.class.php';
include 'uri/pattern.class.php';
include 'uri/validator.class.php';





/*** Shortcuts ***/

use Env\Config\Loader as ConfigLoader;
use Env\Plugins\Loader as PluginsLoader;
use Env\Routing\Router as Router;
use Env\Routing\File as RoutingFile;





/*** Execution ***/

// Fetch Hunter instance.
$hunter = Artise::hunter();

// Define Devkit.
$devkit = new \Env\Devkit($hunter);

// Register Devkit.
Artise::devkit($devkit);





/*** Plugins ***/

// Create the Plugins singleton.
$plugins = new PluginsLoader($devkit);

// Integrity check.
if(!is_dir(Artise_Path_Plugins))
	trigger_error('Plugins folder <strong>' . Artise_Path_Plugins . '</strong> does not exist', E_USER_ERROR);
	
if(!is_readable(Artise_Path_Cfg_File))
	trigger_error('Config file <strong>' . Artise_Path_Cfg_File . '</strong> does not exist', E_USER_ERROR);

if(!is_writable(Artise_Path_Hunter_Logs))
	trigger_error('Wrong permissions at folder <strong>' . Artise_Path_Hunter_Logs . '</strong>', E_USER_ERROR);
//-----------------------------------------

	
// Load Plugins.
$d = dir(Artise_Path_Plugins);
while (false !== ($plugin = $d->read())) {
	if (substr($plugin, 0, 1) != '.') {
		$loadState = $plugins->load($plugin, $d->path);
		
		if($loadState === $plugins::MAIN_FILE_INACCESIBLE)
			$hunter->error(	array('E0009', array('plugin' => $plugin)),
							'',
							'TIP_E0009');
	}
}
$d->close();






/*** Site configuration ***/

// Load Site Config.
try {
	$siteConfig = ConfigLoader::load(Artise_Path_Cfg_File, $errors);
	
	// Enable Hunter logging according to Configuration.
	$hunter->enabled($siteConfig->get('saveHunterLogs') == '1');

} catch (\HunterException $e) {
	// A Critical Error occurred. Artise will still attempt to continue.
	Artise::fallback(true);
	
	// Create fallback site configuration.
	$siteConfig = new \Env\Config\Handler(false);
	
	// Store the error
	$hunter->enabled(true);
	$hunter->error($e);
	
	// Log Fallback mode.
	$hunter->warning('W0007', 'DESC_W0007_A', 'TIP_W0007');
}

// Register the site config.
\Artise::config($siteConfig);





/*** Routing ***/

try {
	// Create the Router instance.
	$router = new Router($siteConfig, $devkit, Artise_Path_Rtg_File);
	
	// Store the Router at Artise.
	\Artise::router($router);
	
	// Creates the Site Lang object container: empty until Router instances it.
	$lang;
	
	// Routes the Request URI.
	$return = $router->route($_SERVER['REQUEST_URI'], $lang);
	
	// If we are returned a Plugin Router, set it as the current Router.
	if($return instanceof \Env\Routing\Router)
		$router = $return;
	
	// Create the Site object.
	$site = new \Env\Site($siteConfig, $lang, $router);
}
catch(Hunter\Exception $e) {
	die('HUNTER ERROR init');
	die(print_r($e, true));
}

// Append the Lang to Artise static object.
Artise::lang($lang);

// [CHECKPOINT] Lang is now available.



/*** Procedure ***/

$procedure = $router->procedure();

// Execute actions.
if (is_object($procedure)) {
	// Run Actions.
	$actions_return = $procedure->runActions($site, $devkit);
	
	// Redirect on result.
	$redirect = $procedure->resultRedirect();
	if($redirect) {
		Artise::finalize(false);
		header("Location: $redirect");
	}
}





/*** Render ***/

if($router->render) {
	
	// Generate the HTML.
	$html = $router->render($site, $devkit);
	
	// Set the content type.
	$content_type = $siteConfig->get('site:contentType');

} else if (is_object($procedure)) {
	
	// Generate the JSON.
	$html = json_encode($actions_return);
	
	// Set the content type.
	$content_type = 'application/json';
}





/*** Finalization ***/

// Content Type Send
if(isset($content_type))
	header('Content-Type: ' . $content_type . '; charset=' . $siteConfig->get('site:charset'));

// Print the HTML, save Hunter logs, display the Console.
Artise::finalize($html);

?>
