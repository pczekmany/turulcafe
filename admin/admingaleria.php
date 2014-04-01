<?php

#Képek (kép törlés)
If ($_REQUEST['kepment'] == "2") {	
	#képsorrend, képfeliratok átírása
	for ($i = 1; $i < 10000; $i++){
		$kiemsor = 'sor' . $i;
		$csoportsor = 'csoport' . $i;
		$felirat_hu = 'felirat_hu' . $i;
		If ($_REQUEST[$kiemsor] != ""){	
			$sql2 = "UPDATE ".$_SESSION[adatbazis_etag]."_galeriakepek SET kepszam = $_REQUEST[$kiemsor], felirat_hu = '$_REQUEST[$felirat_hu]', csoport = '$_REQUEST[$csoportsor]' WHERE sorszam=$i";
			mysql_query($sql2);
		}
	}
	
	#kép törlése (adatbázis és fájl)
	If ($_REQUEST[torol] != ""){
		
			$t = mysql_query("SELECT sorszam, fajlnev_nagy FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE sorszam= '$_REQUEST[torol]'");  
			$a = mysql_fetch_row($t);  
			$torlendosorszam = $a[0];
			$torlendofajl = $a[1];
			mysql_query("DELETE FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE sorszam=$_REQUEST[torol]");
			#unlink("galeria/" . $torlendofajl);
			#unlink("galeria/th_" . $torlendofajl);
		
	}
}


#Csoportok)
If ($_REQUEST['csoportment'] == "1") {	
	#csoportfeliratok átírása
	for ($i = 1; $i < 10000; $i++){
		$kiemsor = 'sor' . $i;
		$felirat_hu = 'csop_felirat_hu' . $i;
		If ($_REQUEST[$kiemsor] != ""){	
			$sql2 = "UPDATE ".$_SESSION[adatbazis_etag]."_galeriacsop SET felirat_hu = '$_REQUEST[$felirat_hu]', sorrendszam = '$_REQUEST[$kiemsor]' WHERE sorszam=$i";
			mysql_query($sql2);
		}
	}
	# törölni azt a sorszámú csoportot az adatbázisból, aminek "on" az értéke 
	for ($i = 1; $i < 20000; $i++){
		$ii = 'csop'.$i;
		If ($_REQUEST[$ii] == "on"){
			$tomb[]= $i;
		}
	}
	If (isset($tomb) == TRUE){
		foreach ($tomb as $ertek) {
			$t = mysql_query("SELECT sorszam FROM ".$_SESSION[adatbazis_etag]."_galeriacsop WHERE sorszam= '$ertek'");  
			$a = mysql_fetch_row($t);  
			$torlendosorszam = $a[0];
			mysql_query("DELETE FROM ".$_SESSION[adatbazis_etag]."_galeriacsop WHERE sorszam=$torlendosorszam");
		}
	}
	If ($_REQUEST['csoportkarb'] != "") {
		#új csoport rögzítés
		$result2 = mysql_query("SELECT MAX(sorszam) FROM ".$_SESSION[adatbazis_etag]."_galeriacsop");
		$row = mysql_fetch_array($result2); 
		$num_rows=$row[0];
		$num_rows = 100 + $num_rows + 1;
		$result2 = mysql_query("SELECT MAX(sorrendszam) FROM ".$_SESSION[adatbazis_etag]."_galeriacsop");
		$rowcsoport = mysql_fetch_array($result2); 
		$num_rowcsoports=$rowcsoport[0];
		$num_rowcsoports++;
		if ($_REQUEST[csoport] == ''){ $xcsoport = '0';} else {$xcsoport = $_REQUEST[csoport];}
		$sql2 = "INSERT INTO ".$_SESSION[adatbazis_etag]."_galeriacsop (sorszam, felirat_hu, sorrendszam, csoporttagja) values ('$num_rows', '$_REQUEST[csoportkarb]', '$num_rowcsoports', '$xcsoport')";
		mysql_query($sql2);
	}
}


	
#kép feltöltés, rögzítés adatbázisban	
If ($_REQUEST['kepment'] == "1") {
if ($_REQUEST['f5'] == $_SESSION["f5"]){
	$result = mysql_query("SELECT MAX(sorszam) FROM ".$_SESSION[adatbazis_etag]."_galeriakepek");
	$row = mysql_fetch_array($result); 
	$num_rows=$row[0];
	$num_rows++;
	$result2 = mysql_query("SELECT MAX(kepszam) FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE csoport = '$_REQUEST[csoport]'");
	$rowkep = mysql_fetch_array($result2); 
	$num_rowkeps=$rowkep[0];
	$num_rowkeps++;
	$fajlnev_n = $num_rows . ".jpg";
	$fajlnev_k = "th_" . $num_rows . ".jpg";
	#file feltöltése
	$konyvtar = 'galeria/';
	#mkdir($konyvtar, 0777);
	foreach($_FILES as $allomanynev => $all_tomb) {
		move_uploaded_file($all_tomb['tmp_name'], "$konyvtar/$fajlnev_n");
		If ($all_tomb['tmp_name'] == "") {
			$nincsfajl=1;
		}
		$filenev = "galeria/".$all_tomb['name'];
		$filename = $all_tomb['name'];
		settype ($filenev, 'string');
	}
	
	
	
	# Maximális szélesség és magasság beállítása
	$width = 150;
	$height = 150;
	# Kép méreteinek beolvasása, oldalarány kiszámítása, új oldalméret kiszámítása
	list($width_orig, $height_orig) = getimagesize($konyvtar . '/'. $fajlnev_n);
	$ratio_orig = $width_orig/$height_orig;
	if ($width/$height > $ratio_orig) {
	   $width = $height*$ratio_orig;}
	else {
	   $height = $width/$ratio_orig;
	}
	# Resample
	$image_p = imagecreatetruecolor($width, $height);
	$image = imagecreatefromjpeg($konyvtar . '/'. $fajlnev_n);
	If ($degrees != "") {
		// Create a square image the size of the largest side of our src image
		$kulonb = ($width_orig - $height_orig) /2;
		$tmp = imageCreateTrueColor($width_orig, $width_orig);
		// Exchange sides
		$image_p = imageCreateTrueColor($height, $width);
		// Now copy our src image to tmp where we will rotate and then copy that to $out
		imageCopy($tmp, $image, 0, $kulonb, 0, 0, $width_orig, $height_orig);
		$tmp2 = imageRotate($tmp, $degrees, 0);
		// Now copy tmp2 to $out;
		#majd kicsinyíteni a kívánt méretre
		imagecopyresampled($image_p, $tmp2, 0, 0, $kulonb, 0, $width, $height+38, $width_orig, $width_orig);}
	else {
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	}
	// Output
	imagejpeg($image_p, $konyvtar . '/'. $fajlnev_k );
	imagedestroy($image_p);

		
	# Maximális szélesség és magasság beállítása
			$width = 800;
			$height = 800;
			# Kép méreteinek beolvasása, oldalarány kiszámítása, új oldalméret kiszámítása
			list($width_orig, $height_orig) = getimagesize($konyvtar . '/'. $fajlnev_n);
			$ratio_orig = $width_orig/$height_orig;
			if ($width/$height > $ratio_orig) {
			   $width = $height*$ratio_orig;}
			else {
			   $height = $width/$ratio_orig;
			}
			# Resample
			$image_p = imagecreatetruecolor($width, $height);
			$image = imagecreatefromjpeg($konyvtar . '/'. $fajlnev_n);
			If ($degrees != "") {
				// Create a square image the size of the largest side of our src image
				$kulonb = ($width_orig - $height_orig) /2;
				$tmp = imageCreateTrueColor($width_orig, $width_orig);
				// Exchange sides
				$image_p = imageCreateTrueColor($height, $width);
				// Now copy our src image to tmp where we will rotate and then copy that to $out
				imageCopy($tmp, $image, 0, $kulonb, 0, 0, $width_orig, $height_orig);
				$tmp2 = imageRotate($tmp, $degrees, 0);
				// Now copy tmp2 to $out;
				#majd kicsinyíteni a kívánt méretre
				imagecopyresampled($image_p, $tmp2, 0, 0, $kulonb, 0, $width, $height+200, $width_orig, $width_orig);}
			else {
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
			}
			# Output
			imagejpeg($image_p, $konyvtar . '/'. $fajlnev_n );
			imagedestroy($image_p);

	
	#a képek adatainak rögzítése az adatbázisba
	If ($nincsfajl != 1) {
		$sql2 = "INSERT INTO ".$_SESSION[adatbazis_etag]."_galeriakepek (sorszam, fajlnev_nagy, felirat_hu, csoport, kepszam) values ('$num_rows', '$fajlnev_n', '$_REQUEST[felirat_hu]', '$_REQUEST[csoport]', '$num_rowkeps')";
		mysql_query($sql2);
	}
}	
}

#galériaképek blokk adatainak beolvasása
$result = mysql_query("SELECT sorszam, felirat_hu FROM ".$_SESSION[adatbazis_etag]."_galeriacsop ORDER BY felirat_hu");
$csoportok = array();
while ($next_element = mysql_fetch_array($result)){
	If ($next_element['sorszam'] != ""){ 
		$csoportlista = $csoportlista . '<option value="' . $next_element[sorszam] . '">' . $next_element[felirat_hu] . '</option>' . "\n";}
}

#kiválasztott csoport képeinek beolvasása
If ($_REQUEST[csoport] != "") {
	$t = mysql_query("SELECT sorszam, felirat_hu, csoporttagja FROM ".$_SESSION[adatbazis_etag]."_galeriacsop WHERE sorszam= '$_REQUEST[csoport]'");  
	$a = mysql_fetch_row($t);  
	$csoportszam = $a[0];
	$csoportszoveg = $a[1];
	$csoporttagja = $a[2];
}

$result2 = mysql_query("SELECT COUNT(*) FROM ".$_SESSION[adatbazis_etag]."_galeriakepek");
$dbbb = mysql_fetch_array($result2);
If ($dbbb != "0"){
	$result = mysql_query("SELECT * FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE csoport= '$_REQUEST[csoport]' ORDER BY kepszam");  
	while ($next_element = mysql_fetch_array($result)){
		$kepszamlalo++;
		$eleres = $admin_konyvtar."galeria/";
		$next_sorszam = $next_element['sorszam'];
		$next_kepszam = $next_element['kepszam'];
		$next_felirat_hu = $next_element['felirat_hu'];
		$next_fajlnev_nagy = $next_element['fajlnev_nagy'];
		$next_csoport = $next_element['csoport'];
		$t = mysql_query("SELECT sorszam, felirat_hu FROM ".$_SESSION[adatbazis_etag]."_galeriacsop WHERE sorszam= '$next_csoport'");  
		$a = mysql_fetch_row($t);  
		$csoportszam = $a[0];
		$csoportszoveg = $a[1];

		$kepsor = $kepsor . '
			<table border="0" style="border-bottom: 2px solid #686868; margin-bottom: 5px;">
				<tr>
					<td align="center">
						<a href="' . $eleres . $next_fajlnev_nagy . '" class="highslide" onclick="return hs.expand (this)">
							<img src="' . $eleres . $next_fajlnev_nagy . '" style="background-color: #ffffff;" height="100" border="1">
						</a>
					</td>
					<td>
						<table>
						<tr><td colspan="2" align="center"> </td></tr>
						<tr><td>HU</td><td><input name="felirat_hu' . $next_sorszam . '" size="40" type="text" value="' . $next_felirat_hu . '"></td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Kép sorszáma: <input type="text" name="sor' . $next_sorszam . '" size="3" maxlength="3" value="' . $next_kepszam . '" onkeyup="numerikusCheck(admingaleria.sor' . $next_sorszam . ')"></td>
					<td align="center">
						<a href="#"><img src="graphics/icon_del.png" title="a kép törlése" border="0" width="19" style="margin: 5px 0px 0px 0px;" onclick="megerosites_x('.$next_sorszam.', \'galeriakep\', '.$csoportszam.')"></a>
						&nbsp;&nbsp;&nbsp; Áthelyez: 
						<select name="csoport' . $next_sorszam . '" class="bev_combo">
						<option selected value="'.$csoportszam.'">'.$csoportszoveg.'</option>
						'. $csoportlista .'
						</select>
					</td>
				</tr>	
			</table>';
	}
}

#csoport beolvasás
If ($_REQUEST['csoport'] != "") { $csopvaltozo = "WHERE csoporttagja = $_REQUEST[csoport]";} else { $csopvaltozo = "WHERE csoporttagja = 0";}
$eleres = $admin_konyvtar."galeria/";
$result = mysql_query("SELECT sorszam, felirat_hu, sorrendszam, csoporttagja FROM ".$_SESSION[adatbazis_etag]."_galeriacsop ".$csopvaltozo." ORDER BY sorrendszam");
while ($next_element = mysql_fetch_array($result)){
	$m = mysql_query("SELECT kepszam FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE csoport = '$next_element[sorszam]'"); 
	$kepdb = mysql_num_rows($m);  
	$z = mysql_query("SELECT sorszam, fajlnev_nagy, kepszam, csoport FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE csoport = '$next_element[sorszam]' AND kepszam = 1"); 
	$r = mysql_fetch_row($z);  
	$csoportkep = $r[1];
	If ($kepdb > 0) {
		$fokep = '<a href="' . $eleres . $csoportkep . '" class="highslide" onclick="return hs.expand (this)">
					<img src="' . $eleres . $csoportkep . '" style="background-color: #ffffff;" height="100" border="1">
				</a>';
		}
	else {
		$fokep = 'Csoport törlése: <input type="checkbox" name="csop' . $next_element['sorszam'] . '">';
	}
	$vancsoport = 1;
	$csoprogz = $csoprogz . 
	'<table style="border-bottom: 2px solid #686868; width: 98%;">
		<tr>
			<td>
				<table>
					<tr>
						<td align="right"><input name="sor'.$next_element['sorszam'].'" size="2" type="text" value="'.$next_element['sorrendszam'].'"></td>
						<td align="left"><input name="csop_felirat_hu'.$next_element['sorszam'].'" size="30" type="text" value="'.$next_element['felirat_hu'].'">HU</td>
					</tr>
					
					<tr>
						<td> </td><td align="center">'.$kepdb.' db kép</td>
					</tr>
					<tr>
						<td> </td><td align="center"><a href="admin.php?tartalom=admingaleria&csoport='.$next_element['sorszam'].'">csoport képeinek karbantartása</a></td>
					</tr>
				</table>
			</td>
			<td>
				'.$fokep.'
			</td>
		</tr>
	</table>';
	}
$csoportszoveg = strtoupper($csoportszoveg);
#If ($_REQUEST['csoport'] != "") {$csopkieg = '<br>('.$csoportszoveg.' GALÉRIÁN BELÜL)';}	
If ($vancsoport == 1) {
$csoprogz = '
<form action="admin.php?tartalom=admingaleria&csoportment=1&csoport='.$_REQUEST['csoport'].'" enctype="multipart/form-data" method="post" class="admin_form">
		<div class="a_form_fej">
			GALÉRIACSOPORTOK '.$csopkieg.'
			<input type="submit" name="submit" value="Mentés" class="a_form_mentes" />
		</div>
			<table border="0" width="100%">
				<tr><td colspan="3" align="center">'.$csoprogz.'</td></tr>
				<tr>
					<td colspan="3" align="center">
					<input name="f5" id="f5" type="text" style="display: none;" value="'.$_SESSION["f5"].'">
					</td>
				</tr>
				</table>
</form>
		
';}

if ($kepsor != "") {
	$tartkepek = '
	<FORM ACTION="admin.php?tartalom=admingaleria&csoport='.$csoportszam.'&kepment=2" METHOD=POST name="galeria" class="admin_form">
		<div class="a_form_fej">
			'.$csoportszoveg.' GALÉRIA KÉPEI 
			<input type="submit" name="torol" value="oldalszámok, feliratok módosítása" class="a_form_mentes">
		</div>
			<table border="0">
				<tr><td align="center">'.$kepsor.'</td></tr>
			</table>
	</FORM>';
}
	
	If ($csoporttagja != "") {$visszalink = '<a href="admin.php?tartalom=admingaleria&csoport='.$csoporttagja.'" style="color: #cecece;">VISSZA</a>';}
	
	#include ('f5.php');
	$admin_torzs = '
	<div class="tabla_k">
		'.$csoprogz.'
		'.$tartkepek.'	
		<table border="0" width="100%">
			<FORM ACTION="admin.php?tartalom=admingaleria&csoportment=1&csoport='.$_REQUEST['csoport'].'" enctype="multipart/form-data" METHOD="POST">
			<tr>
				<td colspan="3" align="center"><br><br>
					Új csoport rögzítése ide:<input name="csoportkarb" size="30" type="text" value=""><INPUT type="submit" name="submit" value="Rögzítés"><input name="f5" id="f5" type="text" style="display: none;" value="'.$_SESSION["f5"].'">
				</td>
			</tr>
			</FORM>
		</table>
		
	</div>

	<br><br>
	<div class="tabla_k">
		<FORM ACTION="admin.php?tartalom=admingaleria&csoport='.$csoportszam.'&kepment=1" enctype="multipart/form-data" METHOD="POST">
			<table border="0" width="100%">
				<tr><td colspan="2" align="center">ÚJ GALÉRIA-CSOPORTKÉP FELTÖLTÉSE</td></tr>
				<tr><td colspan="2"><INPUT type="file" name="file" size="30" accept="image/*" maxlength=50><INPUT type="submit" name="submit" value="Feltöltés"><input name="f5" id="f5" type="text" style="display: none;" value="'.$_SESSION["f5"].'"></td></tr>
			</table>
		</FORM>	
	</div>
	<br><br>
	<a href="admin.php?tartalom=admingaleria">Vissza a galéria csoportokhoz</a>';

?>