<?php
if(isset($_GET['action']) && $_GET['action'] == 'add'){
	if($_GET['name'] == 'smalle'){
		echo 'false';
	}else{
		echo 'true';
	}
	return;
}

?>
