<?php
include('adatkapcsolat.php');

$aktualis_het = date("W");

$result = mysql_query("SELECT nev, ar, deviza, datum FROM napimenu WHERE het='$aktualis_het' ORDER BY datum");
while ($p_adat = mysql_fetch_row($result)){			
	$nev = $p_adat[0];
	$ar = $p_adat[1];
	$deviza = $p_adat[2];
	$datum = $p_adat[3];
	
	$duedt = explode("-", $datum);
	$date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
	$nap  = date('l', $date);
	
	if ($nap != $nap_a){
		if ($nap == 'Monday'){$nap_hu = 'H√âTF≈ê';}
		if ($nap == 'Tuesday'){$nap_hu = 'KEDD';}
		if ($nap == 'Wednesday'){$nap_hu = 'SZERDA';}
		if ($nap == 'Thursday'){$nap_hu = 'CS√úT√ñRT√ñK';}
		if ($nap == 'Friday'){$nap_hu = 'P√âNTEK';}
		if ($nap == 'Saturday'){$nap_hu = 'SZOMBAT';}
		if ($nap == 'Sunday'){$nap_hu = 'VAS√ÅRNAP';}
		
		if ($nap_hu != 'H√âTF≈ê'){
			$margo = ' style="margin-top: 20px;"';
		}	
		
		
		$hetimenu .= '<h2'.$margo.'>'.$nap_hu.'</h2>';
	}
	
	$nap_a = $nap;
	$margo = ' style="margin-top: 0px;"';
	$hetimenu .= '<p><span>'.$nev.'</span><span class="ar">'.$ar.' '.$deviza.'</span></p>';
}

$result2 = mysql_query("SELECT * FROM etelek WHERE menube='1' order by id");

while ($t_adat = mysql_fetch_row($result2)){			
	$nev = $t_adat[3];
	$ar = $t_adat[4];
	$deviza = $t_adat[5];
						
	$hetimenujobb .= '<p><span>'.$nev.'</span><span class="ar">'.$ar.' '.$deviza.'</span></p>';
}
echo '
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu" lang="hu">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>TURUL Cafe & √âtterem</title>
	<meta name="Keywords" content="turul,cafe,Ètterem,k·vÈzÛ,tatab·nya,restaurant,fı tÈr, fıtÈr, coffe, k·vÈ, koktÈl, ital, Ètel, ebÈd, men¸, menu" />
	<meta name="Description" content="Turul Cafe & …tterem Tatab·ny·n. Mindenkit v·runk szeretettel!" />
	<link rel="StyleSheet" type="text/css" href="stilus.css" />
</head>
<body>
	<div id="etlap">
		<h1>Heti men√º</h1>
		
		<div id="bal">
			'.$hetimenu.'
		</div>
		
		<div id="jobb">
			<h2>SAVANY√öS√ÅGOK</h2>
			'.$hetimenujobb.'
		</div>
		
		<div id="lablec">
			L√°bl√©c
		</div>
		
		<br style="clear: both;" />
	</div>
</body>
</html>
';
?>