<?php
		include('adatkapcsolat.php');
		
		$mainap = date("Y-m-d");
		$nap = date("l");
		if ($nap == 'Monday'){$nap = 'hétfő';}
		if ($nap == 'Tuesday'){$nap = 'kedd';}
		if ($nap == 'Wednesday'){$nap = 'szerda';}
		if ($nap == 'Thursday'){$nap = 'csütörtök';}
		if ($nap == 'Friday'){$nap = 'péntek';}
		if ($nap == 'Saturday'){$nap = 'szombat';}
		if ($nap == 'Sunday'){$nap = 'vasárnap';}
		$ho = date("F");
		if ($ho == 'January'){$ho = 'Január';}
		if ($ho == 'February'){$ho = 'Február';}
		if ($ho == 'March'){$ho = 'Március';}
		if ($ho == 'April'){$ho = 'Április';}
		if ($ho == 'May'){$ho = 'Május';}
		if ($ho == 'June'){$ho = 'Június';}
		if ($ho == 'July'){$ho = 'Július';}
		if ($ho == 'August'){$ho = 'Augusztus';}
		if ($ho == 'September'){$ho = 'Szeptember';}
		if ($ho == 'Oktober'){$ho = 'Október';}
		if ($ho == 'November'){$ho = 'November';}
		if ($ho == 'December'){$ho = 'December';}
		$mainap2 = date("j");
		$mainap2 = '<tr><td colspan="2" style="font-size:12px;"><b><div align="center">'.$ho.' '.$mainap2.'. - '.$nap.'</div></td></tr><tr><td></td></tr>';
		$result = mysql_query("SELECT * FROM napimenu WHERE datum='$mainap'");
		while ($p_adat = mysql_fetch_row($result)){			
			$id = $p_adat[0];
			$datum = $p_adat[1];
			$nev = $p_adat[2];
			$ar = $p_adat[3];
			$deviza = $p_adat[4];
			$kiemel = $p_adat[5];
			if ($kiemel== '1' ){
				$szin = ' color: red; ';}
			else {
				$szin = '';}
				
			$kajak .= "\n".'<tr><td style="font-size:12px;'.$szin.'">'.$nev.'</td><td style="font-size:12px;'.$szin.'" width="60">'.$ar.' '.$deviza.'</td></tr>';
		}
		echo $mainap2;
		echo $kajak;
?>