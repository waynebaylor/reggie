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
		require_once 'HTMLPurifier.standalone.php';
		
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.DefinitionID', 'reggie');
		$config->set('HTML.DefinitionRev', 8);
		$config->set('HTML.Doctype', 'HTML 4.01 Strict');
		$config->set('HTML.TidyLevel', 'heavy');
		$config->set('Attr.EnableID', true); // allow the 'id' attribute.

		// allow <a> tag to have 'target' attribute. allow style tag.
		$def = $config->maybeGetRawHTMLDefinition(); 
		if(!empty($def)) {
			$def->addAttribute('a', 'target', 'Enum#_blank');
			$def->addElement('style', 'Block', 'Flow', 'Common', array('type' => 'Enum#text/css'));	
		}
		
		$htmlp = new HTMLPurifier($config);
		
		$text = $htmlp->purify($html);
		
		return $text;
	}
}