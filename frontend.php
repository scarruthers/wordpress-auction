<?php

function displayCalendar() {
	
	global $wpdb;
	$monthNames = Array("January", "February", "March", "April", "May", "June",
				 "July", "August", "September", "October", "November", "December"); // textual names for the months
	$month = date("n"); // the month we are in
	$year = date("Y"); // the year we are in

	$cMonth = $month; // month of iteration
	$cYear = $year; // year of iteration
	
	$content = "";
    $content .= "<script type='text/javascript'>
    				jQuery(document).ready( function() {
    					jQuery('.dayOn').tooltip();
					});
				</script>";
				
	// let's determine when the last event in the database is
	$sql = "SELECT MAX(date) as adate FROM " . AH_DATA;
	$max_date = $wpdb->get_results( $sql );

	$possibleEndDate = strtotime($max_date[0]->adate . " + 3 months");
	$initialEndDate = strtotime("now + 12 months");	
	
	if( $initialEndDate < $possibleEndDate ) {
		$r = ($possibleEndDate - strtotime("now") ) / 2629743;
		$r = ceil($r);
	}
	else {
		$r = 12;
	}

	for( $v = 0; $v < $r; $v++ ) {
		$content .= "<div class='slide'>" . 
			    "<div class='month'>{$monthNames[$cMonth-1]} {$cYear}</div>" .
		            "<div class='weekDayWrapper'><!--Weekday Wrapper-->" .
	            	"<div class='weekDayFirst'><img src='http://alhughesauction.com/wp-content/themes/hughes/images/weekday-sun.jpg' alt='Sunday' /></div>" .
        	        "<div class='weekDay'><img src='http://alhughesauction.com/wp-content/themes/hughes/images/weekday-mon.jpg' alt='Monday' /></div>" .
                	"<div class='weekDay'><img src='http://alhughesauction.com/wp-content/themes/hughes/images/weekday-tues.jpg' alt='Tuesday' /></div>" .
	                "<div class='weekDay'><img src='http://alhughesauction.com/wp-content/themes/hughes/images/weekday-wed.jpg' alt='Wednesday' /></div>" .
        	        "<div class='weekDay'><img src='http://alhughesauction.com/wp-content/themes/hughes/images/weekday-thu.jpg' alt='Thursday' /></div>" .
                	"<div class='weekDay'><img src='http://alhughesauction.com/wp-content/themes/hughes/images/weekday-fri.jpg' alt='Friday' /></div>" .
	                "<div class='weekDayLast'><img src='http://alhughesauction.com/wp-content/themes/hughes/images/weekday-sat.jpg' alt='Saturday' /></div>" .
        		    "</div><!-- weekday wrapper -->";

		$timestamp = mktime(0,0,0,$cMonth,1,$cYear);
		$maxday = date("t",$timestamp);
		$thismonth = getdate($timestamp);
		$startday = $thismonth['wday'];
		for ($i=0; $i<($maxday+$startday); $i++) {

			if($i < $startday) {
				// filler div
				$content .= "<div class='day'></div>\n";
			}
			else {
				$cDay = ($i - $startday + 1);
				// check to see if there is an auction on this day, if there is then add a link and special background
				$mysqlDate = mktime(0,0,0,$cMonth,$cDay,$cYear);
				$mysqlDate = date( "Y-m-d", $mysqlDate );
				$sql = "SELECT * FROM " . AH_DATA . " WHERE date = '" . $mysqlDate . "'";
				$auctions = $wpdb->get_results( $sql );
				if( $wpdb->num_rows > 0 ) { // there are auctions on this day
					$content .= "<a class='a_tool'><div class='dayOn'><div class='dayNumber'>". $cDay . "</div></div></a>\n";
					$content .= "<div class='tooltip_div'>";
					foreach($auctions as $auction) {
						$content .= "<a href='?page_id=8&auction_id={$auction->id}'>".substr($auction->shortDescription,0, 20)."...</a><br />";
					}
					$content .= "</div>";
				}
				else // no auctions on this day
					$content .= "<div class='day'><div class='dayNumber'>". $cDay . "</div></div>\n";
			}
		}
		$j = 0;
		while( ( $maxday + $startday + $j ) % 7 != 0 ) {
			$content .= "<div class='day'></div>";
			$j++;
		}
		
		// Update the next month & year
		$cMonth++;
		if ($cMonth == 13 ) {
			$cMonth = 1;
			$cYear++;
		}

		$content .= "</div><!-- slide div -->";

	}

	return $content;
}

function displayFrontend( $type, $homepage = false ) {

   global $wpdb;
   global $pluginName;

   $now = "'" . date("Y-m-d", strtotime("now-5hours")) . "'";

   if ( $homepage == true) {
      $content = "<div class='header'>Upcoming Auctions</div><div class='content'>";
      $limit = " LIMIT 3";
   }
   else {
      $content = "<h1>" . ucfirst( $type ) . " Auctions</h1>";
      $limit = "";
   }


   switch( $type ) {

      case "previous":
	 $sql = "SELECT * FROM " . AH_DATA . " WHERE date < {$now} ORDER BY date DESC";
      break;
      
      case "upcoming":
	 $sql = "SELECT * FROM " . AH_DATA . " WHERE date >= {$now} ORDER BY date ASC" . $limit;
      break;
      
      case "all":
      default:
	 $sql = "SELECT * FROM " . AH_DATA . " ORDER BY date ASC";
      break;
      
   }
   
   $auctions = $wpdb->get_results( $sql );
   
	foreach( $auctions as $auction ) {
		$date = date( "l, F d, Y", strtotime( $auction->date ) );
      
		switch ($homepage) {
      
		case false:
			$sql = "SELECT photoURL FROM " . AH_PICS . " WHERE auctionID = {$auction->id} ORDER BY photoID ASC LIMIT 1";
			$photo = $wpdb->get_results( $sql );
			$photoURL = $photo[0]->photoURL;
			$content .= "<div class='auctionWrapper'><!--Start of Auction excerpt-->" .
		     "<img src='{$photoURL}' width='150px' class='thumb' />" . 
		     "<h2>{$date} - {$auction->time}</h2>" .
		     "<p>".stripslashes($auction->shortDescription)."</p>" .
		     "<p><strong>{$auction->auctionType}</strong></p>" . 
		     "<p>{$auction->street} {$auction->city}, {$auction->state} {$auction->zip}</p>" . 
		     "<a href='?page_id=8&auction_id={$auction->id}'><div id='details'></div></a>" . 
		     "</div><!--End of Auction excerpt-->";
      break;
      
      case true:
	 $content .= "<h2>{$date} - {$auction->time}</h2>" .
		     "<p>".stripslashes($auction->shortDescription)."</p>" .
		     "<p><strong>{$auction->auctionType}</strong></p>" .
		     "<p>{$auction->street} {$auction->city}, {$auction->state} {$auction->zip}</p>" .
		     "<a href='?page_id=8&auction_id={$auction->id}'><p class='moreDetails'>&raquo;&nbsp;More Details</p></a>";
      break;
      
      }
   }
   
   if( $homepage == true )
      $content .= "</div>";
      
   return $content;

}


?>