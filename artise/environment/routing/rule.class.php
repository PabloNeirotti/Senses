<?php
namespace Env\Routing
{
	final class Rule
	{
		public	$url = array(),	// array ( string URL )
				$request,		// 'std' | 'ajax'
				$render,		// string Path | false
				$procedures,	// array ( object Procedure )
				$plugin,		// string Plugin id.
				$matched_url;	// string URL - Matched URL.
		
		/*
			Rule constructs with the XMLElement obtained from the routing file.
			
			Errors while constructing will be considered as Warnings.
		*/
		public function __construct($xmlElement)
		{
			/* Verifications for valid rule */
			
			if (!is_object($xmlElement))
				throw new \HunterException('W0001');
			
			// Must have an URL.
			if (!property_exists($xmlElement, 'url'))
				throw new \HunterException('W0001', 'DESC_W0001_a');
			
			// Must be linked to a Render or Procedure.
			if (!property_exists($xmlElement, 'render') && !property_exists($xmlElement, 'procedure') && !property_exists($xmlElement, 'plugin'))
				throw new \HunterException('W0001', 'DESC_W0001_b');
			
			
			/* Store data to the Object */
			
			// URLs.
			foreach($xmlElement->url as $url) {
				$tmp_url = (string)$url->attributes()->pattern;
				$this->url[] = $tmp_url . (substr($tmp_url, -1) == DS ? '' : DS);
			}
			
			
			if (property_exists($xmlElement, 'request')) {
				$allowed_request = array('std', 'ajax');
				$this->request = (in_array($xmlElement->request->attributes()->type, $allowed_request) ?
									(string)$xmlElement->request->attributes()->type :
									Routing_Request_Default);
			} else {
				$this->request = Routing_Request_Default;
			}
			
			if (property_exists($xmlElement, 'plugin')) {
				$allowed_request = array('std', 'ajax');
				$this->plugin = (string)$xmlElement->plugin->attributes()->id;
			} else {
				$this->plugin = null;
			}
			
			// Render.
			$this->render = (property_exists($xmlElement, 'render') ? (string)$xmlElement->render->attributes()->src : false);
			
			// Procedures.
			foreach($xmlElement->procedure as $procedure) {
				$procedure_id = ($procedure->attributes()->id ? (string)$procedure->attributes()->id : '');
				$this->procedures[$procedure_id] = new Procedure($procedure);
			}
		}
		
		
		/*
			Filter the URL in the rule that matches the URI best.
		*/
		public function matchURL($uri = '/')
		{
			if (!isset($this->matched_url)) {
				
				$result = array_filter($this->url, function(&$pattern) use ($uri) {
					$tokens = array();
					
					if(\Env\Uri\Validator::validate($uri, new \Env\Uri\PatternParser($pattern), $tokens)) {
						$pattern = new \Env\Uri\URL($pattern, $tokens);
						return true;
					} else {
						return false;
					}
					
					return false;
				});
				
				$selected_url = false;
				
				// Pick the longest one, which will be the most accurate match.
				foreach($result as $url) {
					if ($selected_url === false) {
						$selected_url = $url;
					} else {
						if (substr_count($url, '/') > substr_count($selected_url, '/'))
							$selected_url = $url;
					}
				}
				
				$this->matched_url = $selected_url;
			}
		}
	}
}
?>
