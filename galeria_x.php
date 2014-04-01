<?php
include('adatkapcsolat.php');

$megjelentcsoport = 0;
$_SESSION["lang"] = 'hu';

If ($_REQUEST['csoport'] != "") { 
		if ($_REQUEST['csoport'] != '0'){
		$csopvaltozo = "WHERE csoporttagja = $_REQUEST[csoport]";
		$vancsoport = '1';
		} 
}
else { 
	$csopvaltozo = "WHERE csoporttagja = 0";
}
	#f?csoportok beolvas?sa
	$t = mysql_query("SELECT sorszam, felirat_hu FROM ".$_SESSION[adatbazis_etag]."_galeriacsop ".$csopvaltozo." ORDER BY sorrendszam");  
	while ($next_element = mysql_fetch_array($t)){
		$eleres = $admin_konyvtar."galeria/";
		$next_sorszam = $next_element['sorszam'];
		$next_felirat_hu = $next_element['felirat_hu'];
	
		If ($_SESSION["lang"] == "hu"){ $next_felirat = $next_felirat_hu;}

		$result = mysql_query("SELECT sorszam, fajlnev_nagy, kepszam, felirat_hu, csoport FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE csoport = '$next_sorszam' AND kepszam = '1'");
		$a = mysql_fetch_row($result);  
		$csoportszam = $a[0];
		//$visszacsoport = $csoportszam;
		$next_fajlnev_nagy = $a[1];

		if ($next_fajlnev_nagy != ''){
					$megjelentcsoport++;
					$kepsor = $kepsor . '
						<div class="box">
						<p>'.$next_felirat.'</p>
						<a href="?p=galeria&amp;csoport='.$next_sorszam.'&amp;lang='.$_SESSION["lang"].'&amp;lap=1">
							<img src="' . $eleres . ''.$next_fajlnev_nagy . '" />
						</a>
						</div>
					';
		}
	}
	
	#$galeriatartalom = '<div style="clear:both;">'. $kepsor . '</div>';
	$galeriatartalom =  $kepsor ;
	
	#a csoport k?peinek megjelen?t?se
	$result = mysql_query("SELECT sorszam, fajlnev_nagy, kepszam, felirat_hu, csoport FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE csoport = '$_REQUEST[csoport]' ORDER BY kepszam");
	$kepdb = mysql_num_rows($result);
	$olddb = 0;
	
	$result = mysql_query("SELECT sorszam, fajlnev_nagy, kepszam, felirat_hu, csoport FROM ".$_SESSION[adatbazis_etag]."_galeriakepek WHERE csoport = '$_REQUEST[csoport]'");
	//ha van lapoz?s a k?pekn?l
	$kepdbv = mysql_num_rows($result);
	
	while ($next_element = mysql_fetch_array($result)){
		$kepszamlalo++;
		$eleres = $admin_konyvtar."galeria/";
		$next_sorszam = $next_element['sorszam'];
		$next_oldalszam = $next_element['oldalszam'];
		$next_fajlnev_nagy = $next_element['fajlnev_nagy'];
		$next_felirat_hu = $next_element['felirat_hu'];

		$next_csoport = $next_element['csoport'];
		If ($_SESSION["lang"] == "hu"){ $next_felirat = $next_felirat_hu;}
		$t = mysql_query("SELECT sorszam, felirat_hu, csoporttagja FROM ".$_SESSION[adatbazis_etag]."_galeriacsop WHERE sorszam= '$next_csoport'");  
		$a = mysql_fetch_row($t);  
		$csoportszam = $a[0];
		$visszacsoport = $a[4];
		If ($_SESSION["lang"] == "hu"){ $csoportszoveg = $a[1];}
		if ($_REQUEST[lap] == ""){$lapxx = 1;}
		else {$lapxx = $_REQUEST[lap];}
		#ha az oldal utols? k?p?t n?zz?k ?s m?g van ut?na oldal
		If (($kepszamlalo == $kepdbv) AND ($ig < $kepdb)){
			#$utolsokep = "utolsókép";
			$tovabblink = '<a href="index.php?p=galeria&amp;csoport='.$_REQUEST[csoport].'&amp;lang='.$_SESSION["lang"].'&amp;lap='.($lapxx+1).'" class="controlright">';}
		else {
			$tovabblink = '<a href="#" onclick="return hs.next(this)" class="controlright">';}
		#ha az oldal elsõ képét n?zz?k ?s m?g van el?tte oldal
		If (($kepszamlalo == 1) AND ($tol > 0)){
			#$elsokep = "elsõkép";
			$elozolink = '<a href="index.php?p=galeria&amp;csoport='.$_REQUEST[csoport].'&amp;lang='.$_SESSION["lang"].'&amp;lap='.($lapxx-1).'" class="controlleft">';}
		else {
			#$elsokep = "nem elsõkép";
			$elozolink = '<a href="#" onclick="return hs.previous(this)" class="controlleft">';}
		if ($megjelentcsoport == 0){
		$kepsor = $kepsor . '
						<div class="box">
						<a href="' . $eleres . $next_fajlnev_nagy . '" class="highslide" onclick="return hs.expand (this, {dimmingOpacity: 0.90})">
							<img src="' . $eleres . ''.$next_fajlnev_nagy . '" alt="" />
						</a>
						</div>
						
						<div class="highslide-caption">
							<div id="kepfelirat">'.$next_felirat.'<br />('.($tol+$kepszamlalo).'/'.$kepdb.')</div>
							'.$elozolink.'
								<img src="graphics/galeria_b_nyil.jpg" border="0" alt="következõ" title="következõ" />
							</a>
							'.$tovabblink.'
								<img src="graphics/galeria_j_nyil.jpg" border="0" alt="elõzõ" title="elõzõ" />
							</a>
						</div>
						
	';
	}
	}
	$vissza_felirat = $lang_vissza;
	if ($vancsoport == "1"){
	$galeriafejlec =
	'<div class="box"></div><div id="galeria-alfejlec" class="box"> '.$csoportszoveg.'
	</div><div class="box"></div><br style="clear: both;" />
	';
	
	/*'<table style="margin: 20px 0px 30px 0px; width: 98%;">
		<tr>
			<td class="kiemelt" align="left">'.$csoportszoveg.'</td><td align="right">'.$lapszamsor.'</td>
			<td class="normal" align="right"></td>
		</tr>
	</table>';
	*/
	$visszagomb = '<div class="box" style="float: right;"><p><a href="galeria.php?p=galeria&amp;csoport='.$visszacsoport.'&amp;lang='.$_SESSION["lang"].'" class="visszagomb">Vissza</a></p></div>';
	}
	
	$galeriatartalom = $galeriafejlec . $kepsor .$visszagomb;
	/*$fixxszoveg = '<h1>'.$lang_galeria.'</h1>'.$galeriatartalom;
	$alcim = ' - '.$lang_galeria;*/

echo $galeriatartalom;	
?>