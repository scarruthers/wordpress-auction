<?php

/*
 * The purpose of this file is to send out all calls to other functions, based on $_POST and $_GET
 */

// Check for particular $_POST and $_GET values


if (isset($_GET['page']) && !isset($_GET['page_id'])) {
	// Make sure we are on the backend
	// Output links
	echo links();

	// Process $_GET / $_POST, and call the necessary function

	echo "<div class='user_msg'>";

	if (isset($_POST)) {
		
		if ( isset($_POST['action']) && $_POST['action'] == 'addAuction') {
			$addAuction = new auction();
			$addAuction->updateAuctionFromPost( "insert" );
			if( $addAuction->hasMessage ) {
				echo $addAuction->displayLastMessage();
			}
		}
		if ( isset($_POST['action']) && $_POST['action'] == 'editAuction') {
			$editAuction = new auction( $_POST['id'] );
			$editAuction->updateAuctionFromPost( "update" );
			if( $editAuction->hasMessage ) {
				echo $editAuction->displayLastMessage();
			}
		}

	}

	if (isset($_GET)) {
		
		if (isset($_GET['removePicture'])) {
			$editAuction = new auction( $_GET['editAuction'] );
			$editAuction->deletePicture( $_GET['removePicture'] );
		}
		if (isset($_GET['removeFile'])) {
			$editAuction = new auction( $_GET['editAuction'] );
			$editAuction->deleteFile();
		}
		if (isset($_GET['deleteAuction'])) {
			$editAuction = new auction( $_GET['deleteAuction'] );
			$editAuction->deleteAuctionAndPictures();
		}
		
	echo "</div><!--End user_msg-->";
	
		if (isset($_GET['viewAuctions'])) {
			echo displayAuctions( $_GET['viewAuctions'] );
		}
		else if (isset($_GET['addAuction'])) {
			// Displaying add form
			$addAuction = new auction();
			echo $addAuction->showAddForm();
		}
		else if (isset($_GET['editAuction'])) {
			// Displaying edit form
			$editAuction = new auction( $_GET['editAuction'] );
			echo $editAuction->showEditForm();
		}
		else {
			echo displayAuctions("all");
		}

	}

}
?>