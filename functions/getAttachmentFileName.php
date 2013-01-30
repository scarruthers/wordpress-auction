<?php

function getAttachmentFileName( $attachmentURL ) {
	// Assume the filename is of the format {timestamp}-{filename}.{ext}
	
	// Create an array of strings, delimited by "/"
	$fileName = explode( "/", $attachmentURL);
	
	// Get the last element of this array, containing the full file name
	$fileName = $fileName[ count($fileName) - 1 ];
	
	// We only want what is after the "-", so find that string position
	$strPosOfHyphen = strpos( $fileName, "-" ) + 1;
	
	// Now narrow down our filename to everything after that string position
	$fileName = substr( $fileName, $strPosOfHyphen, strlen($fileName) - $strPosOfHyphen );
	
	// Return the parsed filename
	
	return $fileName;
}


?>