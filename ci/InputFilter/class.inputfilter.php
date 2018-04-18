<?php

/* DUMMY REPLACEMENT FOR MISSING CLASS */

class InputFilter {
		
	function __construct($tagsArray = array(), $attrArray = array(), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1) {		

	}
	
	/** 
	  * Method to be called by another php script. Processes for XSS and specified bad code.
	  * @access public
	  * @param Mixed $source - input string/array-of-string to be 'cleaned'
	  * @return String $source - 'cleaned' version of input parameter
	  */
	function process($source) {
		return strip_tags($source);

	}
}

?>