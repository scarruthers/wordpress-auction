<?php

customTinyMce();

$form = "<p><strong>Please note</strong> that removing a file or picture from the auction will reload the page and you will lose any changes you make, so either save your changes first, or delete before making changes.</p>";
$form .= "
<form name='auctionForm' method='post' enctype='multipart/form-data' action='?page=ah-main&viewAuctions=all'>
	<input type='hidden' name='id' value='{$this->id}' />
	<input type='hidden' name='action' value='{$action}' />
	<h3>Date & Time</h3>
	<p>
		Date:
		<input type='text' name='date' id='datepicker' value='{$this->date}' >
		Time:
		<input type='text' name='time' id='timepicker' value='{$this->time}' >
	</p>
	<h3>Location</h3>
	<p>
		Street:
		<input type='text' name='street' value='{$this->street}' />
		City:
		<input type='text' name='city' value='{$this->city}' />
		State:
		<input type='text' name='state' value='{$this->state}' size='2' />
		ZIP:
		<input type='text' name='zip' value='{$this->zip}' size='5' />
	</p>
	<h3>Details</h3>
	<p>
		Auction Type:
		<br />
		<select name='auctionType'>
			<option value='Land/Real Estate Auction' " . ($this->auctionType == "Land/Real Estate Auction" ? "selected" : "") . " > Land/Real Estate Auction <option value='Estate Auction' " . ($this->auctionType == "Estate Auction" ? "selected" : "") . "  >Estate Auction <option value='Moving Auction' " . ($this->auctionType == "Moving Auction" ? "selected" : "") . "  >Moving Auction <option value='Farm Equipment Auction' " . ($this->auctionType == "Farm Equipment Auction" ? "selected" : "") . "  >Farm Equipment Auction <option value='Benefit Auction' " . ($this->auctionType == "Benefit Auction" ? "selected" : "") . "  >Benefit Auction <option value='Antique Auction' " . ($this->auctionType == "Antique Auction" ? "selected" : "") . "  >Antique Auction <option value='Miscellaneous Auction' " . ($this->auctionType == "Miscellaneous Auction" ? "selected" : "") . "  >Miscellaneous Auction
		</select>
	</p>
	<p>
		Short Description:
		<input type='text' name='shortDescription' value='" . stripslashes($this->shortDescription) . "' size='125' />
	</p>
	<p>
		Description:
		<br />
		<textarea rows='3' cols='100' name='description' id='description'>" . stripslashes($this->description) . "</textarea>
</p>	<h3>Files & Photos</h3>
	<p>
		Sale Bill (one .pdf file):
		<br />
		<span id='file_link'>" . (strlen($this->file) > 0 ? "<a href='{$this->file}'>Current File</a> | <a href='?page=ah-main&removeFile=true&editAuction={$this->id}'>Delete File</a>" : "No Current File") . "</a><br /><input type='file' name='file_uploatify' id='file_upload' />
	</p>
	<p>
		Other attachments:
		<br />
		<span id='attachment_div'>" . $attachmentsHTML . "</span>
		<input type='file' name='attachments' id='attachments' />
	</p>
	<p>
		Photos (as many as you wish, and no .bmps):<br />
		" . ($action == "editAuction" ? "When <strong>editing</strong> an auction, if you remove a picture, you need to 'save changes.'<br />" : "" ) . 
 $photosHTML . "
	</p>
	<br />
	<p style='clear:both;'>
		<input type='file' name='photos_field' id='photo_upload' />
	</p>
	<div id='photo_div'></div>
	<input type='hidden' name='file' id='file_url' value='{$this->file}' />
	<input type='submit' name='do_auction' value='Save Changes' style='clear:both;' />
</form>
";

?>