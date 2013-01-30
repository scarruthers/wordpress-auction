<?php

function links() {
	
	global $backend_url;
	$link_url = $backend_url;

	$links = "<br />";
	$links .= "<a href='" . $link_url . "&viewAuctions=all'>All Auctions</a> | ";
	$links .= "<a href='" . $link_url . "&viewAuctions=upcoming'>Upcoming Auctions</a> | ";
	$links .= "<a href='" . $link_url . "&viewAuctions=previous'>Previous Auctions</a>";

	$links .= " | <a href='" . $link_url . "&addAuction=true'>Add Auction</a>";
	
	$links .= "<br /><br />";
	
	return $links;
	
}

?>
