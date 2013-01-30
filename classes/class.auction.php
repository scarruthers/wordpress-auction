<?php

class auction {
	
	// Auction details
	private $id = '';
	private $date = '';
	private $time = '';
	private $street = '';
	private $city = '';
	private $state = '';
	private $zip = '';
	private $file = '';
	private $description = '';
	private $auctionType = '';
	private $shortDescription = '';
	private $auctionFields = array( "date" => "text",
									"time" => "text",
									"street" => "text",
									"city" => "text",
									"state" => "text",
									"zip" => "text",
									"file" => "text",
									"description" => "text",
									"auctionType" => "text",
									"shortDescription" => "text" );
	public  $hasMessage = False;
	private $message = '';
	
	public function __construct( $auctionID = False ) {
		
		if( $auctionID ) {
			$this->getAuctionDataFromDatabase( $auctionID );
		}

	}

	public function showAddForm() {
		$action = "addAuction";
		$photosHTML = "<ul id='sortable'></ul>";
		$attachmentsHTML = "";
		
		require_once ABSPATH . AH_PATH . "templates/auctionForm.php";
		
		echo $form;
		
	}
	
	public function showEditForm() {
		
		global $wpdb;
		
		$action = "editAuction";
		// Pull photos from database for the auction and format them with HTML
		$sql = "SELECT photoID, photoURL, thumbURL, caption FROM " . AH_PICS . " WHERE auctionID = {$this->id} ORDER BY photoID ASC";
		$photos = $wpdb->get_results( $sql );
		$photosHTML = "<ul id='sortable'>";
			foreach ($photos as $photo) {
				if ($photo != '') {
					$photosHTML .= "
					<li class='ui-state-default' id='{$photo->photoID}'><a href='{$photo->photoURL}' class='lytebox' data-description='<a href=\"?page=ah-main&editAuction={$this->id}&removePicture={$photo->photoID}\">Remove Picture</a>' data-lyte-options='group:auction'><img src='{$photo->thumbURL}' class='edit_form_img' /></a>
						<br />
						<input type='hidden' name='photoURLs[{$photo->photoID}]' value='{$photo->photoURL}' />
						<input type='text' name='captions[{$photo->photoID}]' value='" . stripslashes($photo->caption) . "' />
						<br />
						<a class='removePic' onclick='removePicture({$photo->photoID})'>Remove Picture</a>
					</li>
					";
				}
			}
			$photosHTML .= "</ul>";
		
		// Now handle any attachments the auction has
		$sql = "SELECT attachmentID, auctionID, attachmentURL FROM " . AH_ATTACHMENTS . " WHERE auctionID = {$this->id} ORDER BY attachmentID ASC";
		$attachments = $wpdb->get_results( $sql );
		$attachmentsHTML = "";
			foreach( $attachments as $attachment ) {
				
				$fileName = getAttachmentFileName( $attachment->attachmentURL );

				$attachmentsHTML .= "
				<div id='attachment-{$attachment->attachmentID}'>
				<input type='hidden' name='attachments[{$attachment->attachmentID}]' value='{$attachment->attachmentURL}' /> <a href='{$attachment->attachmentURL}' target='_blank'>View {$fileName}</a> | <a style='cursor:pointer' onclick='jQuery(\"#attachment-{$attachment->attachmentID}\").remove()'>Remove Attachment</a>
				</div>
				";
			}
			
			require_once ABSPATH . AH_PATH . "templates/auctionForm.php";
			
			echo $form;

	}
	
	public function updateAuctionFromPost( $mode ) {
		
		// Auction Data
		$auctionData = $this->handleFormData();
		
		// Auction Pictures
		$auctionPictures = array( $_POST['photoURLs'], $_POST['captions'] );
		
		// Auction Attachments
		$auctionAttachments = $_POST['attachments'];
		
		$this->updateAuctionData( $auctionData, $auctionPictures, $auctionAttachments, $mode );
		
	}	

	private function handleFormData() {
		
		$auctionData = array();
		
		foreach( $this->auctionFields as $fieldName => $fieldType ) {

			switch( $fieldType ) {
				case "file":
					// Upload the file, then pass the permanent file URL
					$uploadedFile = $this->uploadFile( $fieldName );
					$auctionData[$fieldName] = $uploadedFile->url;
				break;
				
				case "text":
				default:
					$auctionData[$fieldName] = $_POST[$fieldName];
				break;
			}
			
			
		}
		
		return $auctionData;
		
	}
	

	public function displayLastMessage() {
		return $this->message;
	}
	
	public function getAuctionDataFromDatabase( $auctionID ) {
		
		global $wpdb;
		
		$sql = "SELECT * FROM " . AH_DATA . " WHERE id = " . $auctionID . " LIMIT 1";
		$auctionData = $wpdb->get_row( $sql, ARRAY_A );
		
		/*
		$sql = "SELECT * FROM " . AH_PICS . " WHERE auctionID = " . $auctionID;
		$auctionPictures = $wpdb->get_results( $sql, ARRAY_A );
		*/
		foreach( $auctionData as $key => $value ) {
			$this->$key = $value;
		}
		
	}
	
	private function updateAuctionData( $auctionData, $auctionPictures, $auctionAttachments, $mode ) {

		global $wpdb;
		
		switch( $mode ) {
			case "insert":
					// Insert auction info
					$wpdb->insert( AH_DATA, $auctionData );
				break;
				
			case "update":
					// Update auction info
					$where = array( "id" => $this->id );
					$wpdb->update( AH_DATA, $auctionData, $where );
					
					// Delete old pictures
					$sql = "DELETE FROM " . AH_PICS . " WHERE auctionID = " . $this->id;
					$wpdb->query( $sql );
					
					// Delete old attachments
					$sql = "DELETE FROM " . AH_ATTACHMENTS . " WHERE auctionID = " . $this->id;
					$wpdb->query( $sql );
				break;
		}
		
		// Iterate through photos and update database
		
		$photoURLs = $auctionPictures[0];
		$captions = $auctionPictures[1];

		foreach( $photoURLs as $key => $photoURL ) {

			$pictureData['photoURL'] = $photoURL;
			$pictureData['thumbURL'] = str_replace( "/imgs/", "/thumbnails/", $photoURL );
			$pictureData['caption'] = $captions[$key];
			$pictureData['auctionID'] = $this->id;

			$wpdb->insert( AH_PICS, $pictureData );

		}
		
		// Iterate through attachments and update database
		
		foreach( $auctionAttachments as $key => $attachmentURL ) {
			
			$attachmentData['attachmentURL'] = $attachmentURL;
			$attachmentData['auctionID'] = $this->id;
			
			$wpdb->insert( AH_ATTACHMENTS, $attachmentData );
		}

	}
	
	public function deleteAuctionAndPictures() {
		
		$this->deleteAuction();
		$this->deletePictures();
		$this->deleteAttachments();
		
	}
	
	public function deleteAuction() {
		
		global $wpdb;
		
		// Delete the main auction entry
		$sql = "DELETE FROM " . AH_DATA . " WHERE id = " . $this->id . " LIMIT 1";
		$wpdb->query( $sql );
		
	}
	
	private function deletePictures() {
		
		global $wpdb;
		
		// Delete all asscoiated pictures
		$sql = "DELETE FROM " . AH_PICS . " WHERE auctionID = " . $this->id;
		$wpdb->query( $sql );
		
	}
	
	private function deleteAttachments() {
		
		global $wpdb;
		
		// Delete all associated attachments
		$sql = "DELETE FROM " . AH_ATTACHMENTS. " WHERE auctionID = " . $this->id;
		$wpdb->query( $sql );
	}
	
	public function deletePicture( $photoID ) {
		
		global $wpdb;
		
		// Delete selected picture
		$sql = "DELETE FROM " . AH_PICS . " WHERE auctionID = " . $this->id . " AND photoID = " . $photoID . " LIMIT 1 ";
		$wpdb->query( $sql );
		
	}
	
	public function deleteFile() {
		
		global $wpdb;
		
		$sql = "UPDATE " . AH_DATA . " SET file = '' WHERE id = " . $this->id . " LIMIT 1";
		$wpdb->query( $sql );
		
	}
	
	
	private function uploadFile( $fieldName ) {
		
		$uploadedFile = wp_upload_bits( $_FILES[$fieldName]['name'], null, file_get_contents( $_FILES[$fieldName]['tmp_name'] ) );
		
		/*
		$imgSize = getimagesize( $_FILES[$fieldName]['tmp_name'] );
		if( $imgSize != FALSE ) {
			// Dealing with an image, so create a thumbnail
			
			// Set up new image size
			$oldWidth = $imgSize[0];
			$oldHeight = $imgSize[1];

			$newWidth = ( $oldWidth < 150 ? $oldWidth : 150 );
			$newHeight = ( $newWidth / $oldWidth) * $oldHeight;
			
			$uploadDirectory = wp_upload_dir();
			$destinationPath = $uploadDirectory['path'] . "\\thumbs\\" . $_FILES[$fieldName]['name'];
			
			$thumbnail = new ThumbnailGenerator( $_FILES[$fieldName]['tmp_name'], $destinationPath, $newWidth, $newHeight );
			$thumbnail->generate();
			
			$thumbURL = str_replace( $_FILES[$fieldName]['name'], "/thumbs/"  . $_FILES[$fieldName]['name'], $uploadedFile->url );
			
			$uploadedFile->thumbURL = $thumbURL;
		}
		*/
		 
		return $uploadedFile;
		
	}
}




?>