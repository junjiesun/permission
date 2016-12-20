<?php
if(isset($_GET['action']) && $_GET['action'] == 'add') {
	if($_GET['name'] == 'smalle') {
		echo 'true';
	} else if($_GET['name'] == 'aezocn') {
		echo 'true';
	} else {
		echo 'false';
	}
	return;
}

?>
