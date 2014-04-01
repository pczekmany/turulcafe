<?php
if ($_REQUEST[submit]){		//ha elküldték az űrlapot
	
	if ($_REQUEST[kiemel] == 'on'){
		$kiemel = 1;
	} else {
		$kiemel = 0;
	}
	
	$duedt = explode("-", $_REQUEST[datum]);
	$date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
	$het  = date('W', $date);

	if ($_REQUEST[id]){		//ha meglévő étel volt az űrlapon
		if ($_REQUEST[torles] == 'on'){
			$sql = "DELETE FROM napimenu WHERE id = '$_REQUEST[id]'";
		} else {
			$sql = "UPDATE napimenu SET nev = '$_REQUEST[nev]', datum = '$_REQUEST[datum]', ar='$_REQUEST[ar]', kiemel='$kiemel', het='$het' WHERE id = '$_REQUEST[id]'";
		}
	}
	else {					//ha új ételt rögzítenek
		$sql = "INSERT INTO napimenu (nev, datum, ar, kiemel, het)
			VALUES
			('$_REQUEST[nev]', '$_REQUEST[datum]', '$_REQUEST[ar]', '$kiemel', '$het')";
	}
	mysql_query($sql);
	header("Location: ?tartalom=napimenu");
}

$gomb_szoveg = 'Új étel rögzítése';

if ($_REQUEST[etel_id]){	//étel beolvasása listából
	$result = mysql_query("SELECT id, nev, datum, ar, kiemel FROM napimenu WHERE id = '$_REQUEST[etel_id]'");
	$etel = mysql_fetch_row($result);
	$etel_id = $etel[0];
	$etel_nev = $etel[1];
	$etel_datum = $etel[2];
	$etel_ar = $etel[3];
	$etel_kiemel = $etel[4];
	$gomb_szoveg = 'Módosítás mentése';
	
	if ($etel_kiemel == '1'){
		$etel_kiemel = 'checked="checked"';
	} else {
		$etel_kiemel = '';
	}
}

//ételtáblázat létrehozása
$result = mysql_query("SELECT id, nev, datum, ar, kiemel FROM napimenu ORDER BY datum DESC");
$darab = 0;
while ($p_adat = mysql_fetch_row($result)){			
	$darab++;
	if ($p_adat[4] == '1'){
		$kiemelt_sor = 'style="color: red;"';
	}
	$etel_lista .= '<tr '.$kiemelt_sor.'><td>'.$darab.'.</td><td>'.$p_adat[2].'</td><td>'.$p_adat[1].'</td><td>'.$p_adat[3].'</td><td><a href="?tartalom=napimenu&etel_id='.$p_adat[0].'"><img src="graphics/icon_edit.gif" alt="szerkesztés" /></a></td></tr>';
	unset($kiemelt_sor);
}

$admin_torzs = '
<h1>Napimenü</h1>
<form action="" method="post" name="etel_form" class="etel_urlap">
	<input type="hidden" name="id" value="'.$etel_id.'" />
	<label>Étel:</label><input type="text" name="nev" value="'.$etel_nev.'" />
	<label>Ár:</label><input type="text" name="ar" value="'.$etel_ar.'" />
	<label>Dátum:</label><input type="text" name="datum" value="'.$etel_datum.'" />
	<label>Kiemelt:</label><input type="checkbox" name="kiemel" '.$etel_kiemel.' />
	<label>Elem törlése:</label><input type="checkbox" name="torles" />
	<input type="submit" name="submit" value="'.$gomb_szoveg.'" />
</form>

<table class="etel_lista">
	<tr><th></th><th>dátum</th><th>étel</th><th>ár</th><th></th></tr>
	'.$etel_lista.'
</table>
';
?>