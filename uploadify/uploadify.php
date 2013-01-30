<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

/*
 * Modified by Sean Carruthers
 *
 * 
*/
error_reporting(E_ALL);
ini_set('display_errors','Off');
ini_set('error_log','my_file.log');

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$fileName = mt_rand() . "-" . str_replace( " ", "", $_FILES['Filedata']['name'] );
	$dateFolder = date("Y-m-d");
	$uploadingImage = FALSE;
	
	// Check if we are uploading images or pdfs
	if( strpos( $_REQUEST['folder'], "imgs") !== false ) {
		// Uploading images
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . "/wp-content/plugins/alhughes-revised/uploads/imgs/{$dateFolder}/";
		$uploadingImage = TRUE;
	}/*
	else if( strpos( $_REQUEST['folder'], "pdfs" ) !==  false ) {
		// Uploading the main pdf, or Sale Bill
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . "/wp-content/plugins/alhughes-revised/uploads/pdfs/{$dateFolder}/";
	}*/
	else {
		// We are just uploading attachments
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . "/wp-content/plugins/alhughes-revised/uploads/attachments/{$dateFolder}/";

	}

	// Create the main directory, if necessary
	if( !is_dir( $targetPath ) )
		mkdir( $targetPath, 0777, true);
	
	// Declare and upload the main file
	$mainFile = $targetPath . $fileName;		
	move_uploaded_file( $tempFile, $mainFile);
	error_log( $mainFile, 0 );
	// Output the file URL
	echo "http://" . str_replace( $_SERVER['DOCUMENT_ROOT'], '', $_SERVER['HTTP_HOST'] . $mainFile );

	// If the file is an image, create a thumbnail
	if( $uploadingImage == TRUE ) {
		
		
		// Create the thumbnail directory, if necessary
		$thumbFolder = $_SERVER['DOCUMENT_ROOT'] . "/wp-content/plugins/alhughes-revised/uploads/thumbnails/{$dateFolder}/";
		
		if( !is_dir( $thumbFolder ) )
			mkdir( $thumbFolder, 0755, true );
		
		// Get the image extension
		$ext = substr( $fileName, strlen($fileName) - 3, 3 );
		$ext = ( strtolower($ext) == "jpg" ? "jpeg" : $ext );		
		
		// Process thumbnail
		$thumb_url = $thumbFolder . $fileName;
		
		// Get size (height & width) of original file
		$img_size = getimagesize( $mainFile );
		$oWidth = $img_size[0];
		$oHeight = $img_size[1];
		
		// Calculate size of new file
		// If the width is smaller than 150, keep the old width
		$nWidth = ( $oWidth < 150 ? $oWidth : 150 );
		$nHeight = ( $nWidth / $oWidth) * $oHeight;
		
		// Generate thumbnail
		// Include necessary file
		require_once( "../classes/class.thumbnail.php" );
		
		// Do some error logging
	 	$file = "featuredLog.txt";
	 	$fileHandler = fopen( $file, 'a');
	 	$line = $tempFile . PHP_EOL . $mainFile . PHP_EOL . $thumb_url . PHP_EOL . date("D, d M Y H:i:s", strtotime("now-5hours")) . PHP_EOL . date("D, d M Y H:i:s") .  PHP_EOL . $img_size[0] . $img_size[1] . PHP_EOL;
	 	fwrite( $fileHandler, $line );
	 	fclose( $fileHandler );
		
		// Create the thumbnail
		$thumb_gen = new ThumbnailGenerator( $mainFile, $thumb_url, $nWidth, $nHeight, $ext );
		$thumb_gen->generate();
		
		// Output the thumbnail URL
		echo ":::";
		echo "http://" . str_replace( $_SERVER['DOCUMENT_ROOT'], '', $_SERVER['HTTP_HOST'] . $thumb_url );
	}

}
?>