<?php

function customTinyMce() {

	if (function_exists('wp_tiny_mce')) {
	
		add_filter('teeny_mce_before_init', create_function('$a', '
	    $a["theme"] = "advanced";
	    $a["skin"] = "wp_theme";
		$a["height"] = "200";
	    $a["width"] = "800";
	    $a["onpageload"] = "";
	    $a["mode"] = "exact";
	    $a["elements"] = "description";
	    $a["editor_selector"] = "mceEditor";
		$a["theme_advanced_buttons1"] = "bold,italic,underline,strikethrough,fontsizeselect";
	    
	    $a["forced_root_block"] = false;
	    $a["force_br_newlines"] = true;
	    $a["force_p_newlines"] = false;
	    $a["convert_newlines_to_brs"] = true;
	
	    return $a;'));
	
		wp_tiny_mce(true);
	
	}

}

?>