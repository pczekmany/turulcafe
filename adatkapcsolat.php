<?php
	if ($_SERVER['HTTP_HOST'] == 'localhost'){
			$kapcsolat = mysql_connect("localhost", "root", "");
			$adatbazis = mysql_select_db("turulcafe");
		} else {
			$kapcsolat = mysql_connect("localhost", "turulcaf_user", "user2013");
			$adatbazis = mysql_select_db("turulcaf_data");
		}

			if (!$kapcsolat) {
				die('Hiba a MySQL szerverhez kapcsol�s k�zben: ' . mysql_error());
			}	
		
			if (!$adatbazis) {
				die('Hiba a MySQL adatb�zishoz kapcsol�d�s k�zben: ' . mysql_error());
			}
		$ekezet = mysql_set_charset("utf8",$kapcsolat);
		
		$_SESSION[adatbazis_etag] = 'turul';
?>