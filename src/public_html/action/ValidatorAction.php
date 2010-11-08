<?php 

/**
 * Action for when a page needs to be validated.
 * @author wtaylor
 *
 */
abstract class action_ValidatorAction extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Perform the validation.
	 */
	public function validate() {
		$v = $this->getValidationConfig();
		return ValidationUtil::validate($v);
	}	
	
	/**
	 * Return the validation configuration.
	 */
	protected function getValidationConfig() {
		return array();
	}
}

?>