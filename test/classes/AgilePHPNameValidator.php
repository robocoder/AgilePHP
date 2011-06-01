<?php
class AgilePHPNameValidator implements Validator {
	
	public static function validate($name) {
		return $name == 'AgilePHP';
	}
}
?>