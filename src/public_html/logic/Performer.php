<?php

abstract class logic_Performer
{
	function __construct() {}
	
	protected function strictFindById($manager, $id) {
		$obj = $manager->find($id);
		
		if(empty($obj)) {
			throw new Exception('Object does not exist: '.$id);
		}
		
		return $obj;
	}
	
	protected function purifyHtml($html) {
		require_once '../htmlp/HTMLPurifier.standalone.php';
		
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.DefinitionID', 'reggie');
		$config->set('HTML.DefinitionRev', 3);
		$config->set('HTML.Doctype', 'HTML 4.01 Strict');
		$config->set('HTML.TidyLevel', 'heavy');
		$config->set('Attr.EnableID', true); // allow the 'id' attribute.
		$def = $config->maybeGetRawHTMLDefinition(); // allow <a> tag to have 'target' attribute.
		if(!empty($def)) {
			$def->addAttribute('a', 'target', 'Enum#_blank');	
		}
		
		$htmlp = new HTMLPurifier($config);
		
		return $htmlp->purify($html);
	}
}