<?php

function displayAuctions( $type ) {

	global $wpdb;
	global $pluginName;

	if( $_GET['order'] ) {
		$order = $_GET['order'];
	}
	else {
		$order = "ASC";
	}
	$switchOrder = ( $order == "ASC" ? "DESC" : "ASC" );

	$content = "<div class='col bold'>Images</div><div class='col bold'>Date / Time (<a href='?page=ah-main&viewAuctions={$type}&order={$switchOrder}'>reverse</a>)</div><div class='col bold'>Location</div><div class='col bold'>Description</div><div class='col bold'>Sale Bill</div><div class='col bold'>&nbsp;</div><br /><hr style='clear: left;' />";

	switch( $type ) {
		
		case "previous":
			$sql = "SELECT * FROM " . AH_DATA . " WHERE date < now() ORDER BY date " . $order;
		break;

		case "upcoming":
			$sql = "SELECT * FROM " . AH_DATA . " WHERE date >= now() ORDER BY date " . $order;
		break;

		case "all":
		default:
			$sql = "SELECT * FROM " . AH_DATA . " ORDER BY date " . $order;
		break;	
			
	}
	$auctions = $wpdb->get_results($sql);

	foreach ($auctions as $auction) {
		$inline_photos = "";
		$sql = "SELECT photoURL, thumbURL FROM " . AH_PICS . " WHERE auctionID = {$auction->id} ORDER BY photoID ASC LIMIT 1";
		$photo = $wpdb->get_row( $sql );
		$description = substr(strip_tags($auction->description), 0, 50) . (strlen(strip_tags($auction->description)) > 50 ? "..." : "");
		$thumbURL = $photo->thumbURL;
		
		$w = @getimagesize($thumbURL);

		if ($w[0] < 150)
			$img_width = $w[0];
		else
			$img_width = 150;

		$file = (strlen($auction->file) > 0 ? "<a href='{$auction->file}'>Current File</a>" : "No File");
		$img = (strlen($thumbURL) > 4 ? "<img src='{$thumbURL}' width='{$img_width}px' />" : "No images.");
		$content .= "<div class='col' style='clear:left;'>{$img}</div><div class='col'>" . date("n/j/Y", strtotime($auction->date)) . " {$auction->time}</div>" . "<div class='col'>{$auction->street} {$auction->city}, {$auction->state} {$auction->zip}</div>" . "<div class='col'>" . stripslashes($description) . "</div>" . "<div class='col'>{$file}</div>";

		if (is_admin()) {
			$content .= "<div class='col' style='clear:right;'><a href='?page=ah-main&editAuction={$auction->id}'>Edit</a> | <a href='?page=ah-main&deleteAuction={$auction->id}' onclick='return confirm(\"Are you sure you wish to delete this auction?\");'>Delete</a></div>";
		}

		$content .= "<hr style='clear: left;' />";
	}

	return $content;

}

?>