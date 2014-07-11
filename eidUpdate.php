<?php
	$username="user";
	$password="pass";
	$database="dbname";

	$db_connection = mysql_connect("localhost",$username,$password);
	@mysql_select_db($database) or die( "Unable to select database");
	
	$i = 0;
	
	$csv = fopen('eID.csv', 'r');
	$conflict = fopen('conflicts.txt', 'w');
	while (($data = fgetcsv($csv)) !== FALSE) {
	//$data is an array of the csv elements
		$i = 0;
		$eID = $data[$i];
		$name = explode(", ", $data[$i+2]); 
		$last = $name[0];
		$firstMID = explode(' ', $name[1]);
		$first = $firstMID[0];
			
		$query = "SELECT * FROM USER_INFO WHERE FIRST_NAME LIKE '$first' and LAST_NAME LIKE '$last'";
		$result=mysql_query($query);
		
		if ($result != false) {
			if (mysql_num_rows($result) == 1) {
				$row=mysql_fetch_array($result);
				if ($row) {
					$update="UPDATE USER_INFO SET EID = '$eID' WHERE FIRST_NAME LIKE '$first' and LAST_NAME LIKE '$last'";
					$result=mysql_query($update);
					echo "$update  \n\n";
				}
			} else if (mysql_num_rows($result) > 1) {
				echo "More than one result for '$first' '$last'.";
				fwrite($conflict, "More than one result for $first $last.\n");
				$id = $data[$i+1];				
				$query = "SELECT * FROM USER_INFO WHERE FIRST_NAME LIKE '$first' and LAST_NAME LIKE '$last' and ID LIKE '$id'";
				$result=mysql_query($query);
				
				if ($result != false) {
					$row=mysql_fetch_array($result);
					if ($row) {
						$update="UPDATE USER_INFO SET EID = '$eID' WHERE FIRST_NAME LIKE '$first' and LAST_NAME LIKE '$last' and ID LIKE '$id'";
						$result=mysql_query($update);
						echo "$update  \n\n";
						fwrite($conflict, "->Result: updated $first $last with ID $id to EID $eID.\n");
					}
				} else {
					$query = "SELECT EID FROM USER_INFO WHERE FIRST_NAME LIKE '$first' and LAST_NAME LIKE '$last'";
					$result=mysql_query($query);
					if (result == NULL || result == 0) {
							$update="UPDATE USER_INFO SET EID = '$eID' WHERE FIRST_NAME LIKE '$first' and LAST_NAME LIKE '$last'";
							$result=mysql_query($update);
							echo "$update  \n\n";
							fwrite($conflict, "->Result: updated $first $last to EID $eID. ID not found.\n");
					} else {
						$update="UPDATE USER_INFO SET EID = '$eID' WHERE FIRST_NAME LIKE '$first' and LAST_NAME LIKE '$last'";
						$result=mysql_query($update);
						echo "$update  \n\n";
						fwrite($conflict, "->Result: updated $first $last to EID $eID. Overwrote previous EID. May require manual verification.\n");
					}
				}				
			}
		}
	}
	fclose($csv);
	fclose($conflict);
?>
