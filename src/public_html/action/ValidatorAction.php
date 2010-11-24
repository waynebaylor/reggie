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
	 * Perform the validation. If fieldNames is non-empty, then only the fields
	 * specified will be validated.
	 */
	public function validate($fieldNames = array()) {
		$allFields = $this->getValidationConfig();
		
		$fields = array();
		if(!empty($fieldNames)) {
			foreach($allFields as $fieldConfig) {
				if(in_array($fieldConfig['name'], $fieldNames)) {
					$fields[] = $fieldConfig;
				}
			}
		}
		else {
			$fields = $allFields;
		}
		
		return ValidationUtil::validate($fields);
	}	
	
	/**
	 * Return the validation configuration.
	 */
	protected function getValidationConfig() {
		return array();
	}
}

?>