<?php

class data_connect{
	public $domain;    
   
	function connect(){
		$domain = $_SERVER['HTTP_HOST'];
		if ($domain == 'localhost'){
			$kapcsolat = mysql_connect("localhost", LOCALHOST_DB_USER, LOCALHOST_DB_PASSWORD);
			$adatbazis = mysql_select_db(LOCALHOST_DB_NAME);}
		else {
			$kapcsolat = mysql_connect("localhost", DOMAIN_DB_USER, DOMAIN_DB_PASSWORD);
			$adatbazis = mysql_select_db(DOMAIN_DB_NAME);
		}

		if (!$kapcsolat) { die('Hiba a MySQL szerverhez kapcsolódás közben: ' . mysql_error());}
		if (!$adatbazis) { $this->create_database();}

		$ekezet = mysql_set_charset("utf8",$kapcsolat);
	}
        
}


class user{
	public $sorszam;
	public $nev;
	public $jog;
	public $email;
	public $csoport;
	public $belephiba;
	public $html_code;

	function login(){
		$jel = mysql_real_escape_string($_REQUEST['jelszo']);
		$azon = mysql_real_escape_string($_REQUEST['azonosito']);
		if (!$_REQUEST['azonosito']){$azon = $_SESSION["sessfelhasznaloazonosito"];}
		$jel = md5($jel);

		If ($_REQUEST['logout'] == 1) {
			unset($_SESSION["sessfelhasznalo"]);
			unset($_SESSION["sessfelhasznalosorszam"]);
			unset($_SESSION["sessfelhasznaloazonosito"]);
			unset($_SESSION["sessfelhasznalojog"]);
		}

		If ($_REQUEST['azonosito'] != "") {
			$result = mysql_query("SELECT sorszam, azonosito, jog, email FROM ".$_SESSION[adatbazis_etag]."_regisztralt WHERE azonosito = '$azon' AND jelszo = '$jel'");	
			$s = mysql_fetch_row($result);
			$mostlep == 1;
		} else {
		   if ($_SESSION[sessfelhasznalosorszam]){
			$result = mysql_query("SELECT sorszam, azonosito, jog, email FROM ".$_SESSION[adatbazis_etag]."_regisztralt WHERE sorszam = '$_SESSION[sessfelhasznalosorszam]'");	
			$s = mysql_fetch_row($result);
		   }
		}
			if ($s[2] != ""){
				$this->sorszam = $s[0];
				$this->nev = $s[1];
				$this->jog = $s[2];
				$this->email = $s[3];
				$_SESSION["sessfelhasznalo"] = $s[1];
				$_SESSION["sessfelhasznalosorszam"] = $s[0];
				$_SESSION["sessfelhasznaloazonosito"] = $s[1];
				$_SESSION["sessfelhasznalojog"] = $s[2];
				$_SESSION["sessfelhasznaloemail"] = $s[3];
				if ($mostlep){
				  $loging_db = new log_db;
				  $loging_db->write($_SESSION["sessfelhasznalosorszam"], 'Bejelentkezés');
				}
			} else {
               If ($_REQUEST['azonosito'] != "") {
				$_SESSION[messagetodiv] = '<p>Figyelem!</p><ul><li>Rossz felhasználónév, vagy jelszó!</li></ul>';
               }
			}

	}
}


class admin{

	function login_admin(){

		if ($_SESSION["sessfelhasznalojog"] == "1") {
		
			//belép
			$array = array('adminmenu' => $adminmenu);

			$admin_menuuj = new html_blokk;
			$admin_menuuj->load_template_file("template/admin_menu.tpl",$array);
			$admin_menu = $admin_menuuj->html_code;

			//modul kiválasztása
			if ($_REQUEST[tartalom]){
				include('admin/'.$_REQUEST[tartalom].'.php');
			} else {
				include('admin/admin_cimlap.php');
			}
			
			$admin_htmluj = new html_blokk;
			$array = array('admin_torzs' => $admin_torzs,
								'admin_menu' => $admin_menu);
								
			$admin_htmluj->load_template_file("template/admin.tpl",$array);
			$this->html_code = $admin_htmluj->html_code;	
			
			}
		else {
			//nem lép be
			if ($_REQUEST[submit]){ $belephiba = "<tr><td colspan='2' class='cedula_ar'>Rossz felhasználónév, vagy jelszó!</td><tr>";	}
			$array = array('belephiba' => $belephiba);
			
			$admin_htmluj = new html_blokk;
			$admin_htmluj->load_template_file("template/login.tpl",$array);
			$admin_html = $admin_htmluj->html_code;
			
			$array = array('admin_torzs' => $admin_html);
			$admin_htmluj->load_template_file("template/admin.tpl",$array);
			$this->html_code = $admin_htmluj->html_code;	
			
		}

	}
}




class html_blokk{
	public $html_code;
	
	function load_template_file($fajlnev,$tomb) {
 
		if(file_exists($fajlnev) > 0) {
			$temp = fopen($fajlnev,"r");
			$tartalom = fread($temp, filesize($fajlnev));
			fclose($temp);
	 
			$tartalom = preg_replace("/{(.*?)}/si","{\$tomb[\\1]}",$tartalom);
	 
			eval("\$tartalom = \"" . addslashes($tartalom) . "\";");
			$tartalom = str_replace("\'", "'", $tartalom);
			$this->html_code = $tartalom . "\n";
		}
 
	}
}

class cikkszoveg {
	public $html_code;
	public $cikksorszam;
	public $cim;
    
    public $hibalista;
    public $hivatkozas;
    public $menucsoport;
    public $cikk_errors = array();
	
	public $tartalom;
	public $nyelv;
	public $menu;
	public $archiv;
	public $hir;
	public $bevezeto;
	public $kiemelt;
	public $menunev;
	public $menufent;
	public $sorszam;
	
	public $kelt;
	public $megjelenes;
	public $esemeny;
	public $esemeny_ig;
	public $php_file;
	public $sorrend;
	
	function mysql_read($cikksorszam, $nyelv){
		
		if (($nyelv == '') OR ($nyelv == 'hu')){
			$nyelvszures = "AND nyelv = 'hu'";}
		else {
			$nyelvszures = "AND nyelv = '".$nyelv."'";
		}
        if (is_numeric($cikksorszam)){
            $r = mysql_query("SELECT tartalom, cim, archiv, php_file, bevezeto, hivatkozas, menucsoport, nyelv, menu_fent, hir,
			   kiemelt, menunev, sorszam, kelt, megjelenes, esemeny, esemeny_ig, php_file, sorrend
			   FROM ".$_SESSION[adatbazis_etag]."_szoveg
			   WHERE sorszam =" . $cikksorszam . " ".$nyelvszures."");
        } else {
		   $r = mysql_query("SELECT tartalom, cim, archiv, php_file, bevezeto, hivatkozas, menucsoport, nyelv, menu_fent, hir,
			  kiemelt, menunev, sorszam, kelt, megjelenes, esemeny, esemeny_ig, php_file, sorrend
			  FROM ".$_SESSION[adatbazis_etag]."_szoveg
			  WHERE hivatkozas ='" . $cikksorszam . "' ".$nyelvszures."");
        }
		$a = mysql_fetch_row($r);
		$cikkszoveg = $a[0];
		$cikkarchiv = $a[2];
		$cikkphp = $a[3];
        $cikkbevezeto = $a[4];
        $cikkhivatkozas = $a[5];
        $cikkmenucsoport = $a[6];
		
		$this->tartalom = $a[0];
		$this->archiv = $a[2];
		$this->cim = $a[1];
		$this->bevezeto = $a[4];
		$this->nyelv = $a[7];
		$this->menufent = $a[8];
		$this->hir = $a[9];
		$this->kiemelt = $a[10];
		$this->menunev = $a[11];
		$this->sorszam = $a[12];
		$this->kelt = $a[13];
		$this->megjelenes = $a[14];
		$this->esemeny = $a[15];
		$this->esemeny_ig = $a[16];
		$this->php_file = $a[17];
		$this->sorrend = $a[18];
		$this->cikksorszam = $cikksorszam;
        $this->bevezeto = $cikkbevezeto;
        $this->hivatkozas = $cikkhivatkozas;
        $this->menucsoport = $cikkmenucsoport;
		if ($cikkarchiv == 1){
			$this->html_code= '
			<h2 class="lapcim">Hiba történt!</h2>
			<div class="szovegblokk">
				A keresett oldal nem található!
			</div>';
		}
		
		
		}
	}


class email{
   public $feladonev;
   public $feladocim;
   public $cimzettcim;
   public $targy;
   public $uzenet_text;
   public $uzenet_html;
   
   function kuldes(){
      if ($this->uzenet_html){
         $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
         $fileatt2 = 'graphics/logo_email.jpg';//put the relative path to the file here on your server
         $fileatt_name2 = 'logo_email.jpg';//just the name of the file here
         $fileatt_type2 = filetype($fileatt2);
         $file2 = fopen($fileatt2,'rb');
         $data2 = fread($file2,filesize($fileatt2));
         fclose($file2);
         $data2 = chunk_split(base64_encode($data2));
         $headers = "From: ".$this->feladonev." <".$this->feladocim.">";
         // Add the headers for a file attachment
         $headers .= "\nMIME-Version: 1.0\n" .
         "Content-Type: multipart/related;\n" .
         " boundary=\"{$mime_boundary}\"";

//         $message = "\n\n--{$mime_boundary}\n" .
//         "Content-Type: text/html; charset=\"iso-8859-2\"\n" .
//         "Content-Transfer-Encoding: 7bit\n\n" .
//         $this->uzenet_html."\r\n";
		 
		 $message = "\n\n--{$mime_boundary}\n" .
         "Content-Type: text/html; charset=\"utf-8\"\n" .
         "Content-Transfer-Encoding: 7bit\n\n" .
         $this->uzenet_html."\r\n";

         // Add file attachment to the message
         $message .= "\n\n--{$mime_boundary}\n" .
         "Content-Type: image/jpg;" . // {$fileatt_type}
         " name=\"{$fileatt_name2}\"\n" .
         "Content-Disposition: inline;" .
         " filename=\"{$fileatt_name2}\"\n" .
         "Content-Transfer-Encoding: base64\n" .
         "Content-ID: <123456789>\n\n" .
         $data2 . "\n\n" .
         //"\n--{$mime_boundary}\n" .
         //"Content-Type: image/jpg;" . // {$fileatt_type}
         //" name=\"{$fileatt_name}\"\n" .
         //"Content-Disposition: inline;" .
         //" filename=\"{$fileatt_name}\"\n" .
         //"Content-Transfer-Encoding: base64\n" .
         //"Content-ID: <akarmi>\n\n" .
         "\n--{$mime_boundary}--\n"; 
      }
      
      if($this->uzenet_text){
         $headers = 'From: '.$this->feladonev.'<'.$this->feladocim.'>' . "\r\n" .
       'Reply-To: '.$this->feladocim.'' . "\r\n" .
       'X-Mailer: PHP/' . phpversion();

         $message = $this->uzenet_text;
      }
      
      mail($this->cimzettcim, $this->targy, $message, $headers);
   }
}

class navsav{
	//egy lista navigációs sávjának elkészítése (várt adat az sql, melyik lapon vagyunk)
	public $tol;
	public $ig;
	public $lap;
	public $termekdb;
	public $lapszamsor;
	
	function create_navsav($sql_query, $lap, $db_peroldal, $xkategoriaszures, $adminpublic){
		$result = mysql_query($sql_query);
		$this->termekdb = mysql_num_rows($result);
		
		If (($lap == "") OR ($lap == 1)) {
			$this->tol = 0;
			$this->ig = $db_peroldal;}
		else {
			$this->tol = $db_peroldal * ($lap-1);
			$this->ig = $db_peroldal;
		}
		
		if ($_REQUEST[akcio]){
		   $akcios_link = '&amp;akcio=1';
		}
		
		if ($_REQUEST[search]){
		   $search_link = '&amp;search='.$_REQUEST[search];
		}
		
		if ($_REQUEST[meret]){
		   $meret_link = '&amp;meret='.$_REQUEST[meret];
		}
		
		if ($_REQUEST[szin]){
		   $szin_link = '&amp;szin='.$_REQUEST[szin];
		}
		
		$olddb = 0;
		$oldelemdb = 0;
		#10 számos oldalszámblokk elemei
		if ($lap != ""){
			$kapott_oldal = $lap;}
		else {
			$kapott_oldal = 1;
		}
			
		$kapott_oldal_m = $kapott_oldal;
		$kapott_oldal_p = $kapott_oldal;

		for ($i = 0; 10>$i; $i++){
			If (($kapott_oldal_m %10 == 0) OR ($kapott_oldal_m == 1)) {
				if ($min_oldal == ""){
					$min_oldal = $kapott_oldal_m;
				}
			}
			If ($kapott_oldal_p %10 == 0) {
				if ($max_oldal == ""){
				$max_oldal = $kapott_oldal_p;
				}
			}
			$kapott_oldal_m--;
			$kapott_oldal_p++;
		}
		
		if (($adminpublic == 'public') OR ($adminpublic == '')) {$cel = 'index.php?x=list&amp;lap=';}
		if ($adminpublic == 'admin') {$cel = 'admin.php?tartalom=termeklist&amp;lap=';}
		
		If ($this->termekdb > $db_peroldal){
			$olddb = ($min_oldal-1);
			for ($i = ($min_oldal-1); $i <= $this->termekdb; $i++){
				If (($i %$db_peroldal == 0) OR ($i == 0)) {
					$olddb++;
					$oldelemdb++;
					$oldvalt = "oldalszam";
					If ($olddb == $lap){$oldvalt = "oldalszamv";}
					If (($lap == "") AND ($i == 0)) {$oldvalt = "oldalszamv";}
					if ($xkategoriaszures != "") {$kategoriaszuresxx = '&amp;kategoriaszures='.$xkategoriaszures;}
					if ($_REQUEST[k] != "") {$kategoriaszuresxx = '&amp;x=list&amp;k='.$_REQUEST[k];}
					if ($_REQUEST[fk] != "") {$kategoriaszuresxx = '&amp;x=list&amp;fk='.$_REQUEST[fk];}
					$this->lapszamsor .= '<a class="'.$oldvalt.'" href="'.$cel.$olddb.$kategoriaszuresxx.$akcios_link.$search_link.$szin_link.$meret_link.'">'.$olddb.'</a>'."\n";}
					if ($oldelemdb == 10) {break;}
					if ($olddb == round($this->termekdb/$db_peroldal,0)+1){break;}
				}
		}
		
		if ($this->lapszamsor != ""){
			$elozooldal = $kapott_oldal-1;
			$kovetkezooldal = $kapott_oldal+1;
			if ($elozooldal < 1) {$elozooldal = 1;}
			if ($kovetkezooldal > round($this->termekdb/$db_peroldal,0)){ $kovetkezooldal = (round($this->termekdb/$db_peroldal,0)+1);}
			if ($_REQUEST[k] != "") {$kategoriaszuresxx = '&amp;k='.$_REQUEST[k];}
			$this->lapszamsor = '
			<div class="navsav">
			   <a href="'.$cel.'1'.$kategoriaszuresxx.$akcios_link.$search_link.$szin_link.$meret_link.'" class="oldalszam" title="első">&#60;&#60;</a>
			   <a href="'.$cel.$elozooldal.$kategoriaszuresxx.$akcios_link.$search_link.$szin_link.$meret_link.'" class="oldalszam" title="előző">&#60;</a>
			   '. $this->lapszamsor .'
			   <a href="'.$cel.$kovetkezooldal.$kategoriaszuresxx.$akcios_link.$search_link.$szin_link.$meret_link.'" class="oldalszam" title="következő">&#62;</a>
			   <a href="'.$cel.(round($this->termekdb/$db_peroldal,0)+1).$kategoriaszuresxx.$akcios_link.$search_link.$szin_link.$meret_link.'" class="oldalszam" title="utolsó">&#62;&#62;</a>
			</div>';
			
		}
		
	}
}

class oldalterkep_sav{
	public $html_code;
	public $aktualis_kategoria;
	
	function mysql_read($id=NULL, $kategoria=NULL) {
		 
		$result = mysql_query("SELECT sorszam, kategoria FROM ".$_SESSION[adatbazis_etag]."_elemek WHERE sorszam = '$id'");
		$t = mysql_fetch_row($result);	
		$kategoriax = $t[1];
		
		if ($kategoria){ $kategoriax = $kategoria;}
		
		$result = mysql_query("SELECT kategorianev, szulo FROM ".$_SESSION[adatbazis_etag]."_kategoriak WHERE sorszam  = '" . $kategoriax . "'");
		$tt = mysql_fetch_row($result);
		$kategorianev = $tt[0];
		$this->aktualis_kategoria = $tt[0];
		$kategoriaszulo = $tt[1];
		if ($kategoriaszulo == '0'){$kategoriaszulo = $kategoriax;}
		
		$result_db = mysql_query("SELECT sorszam FROM ".$_SESSION[adatbazis_etag]."_elemek WHERE kategoria = '$kategoriax'");
		$kategorianev_db = mysql_num_rows($result_db);
		
		if ($kategoriaszulo){
		 $result = mysql_query("SELECT kategorianev FROM ".$_SESSION[adatbazis_etag]."_kategoriak WHERE sorszam  = '" . $kategoriaszulo . "'");
		 $ttt = mysql_fetch_row($result);
		 if ($_REQUEST[fk]){ $this->aktualis_kategoria = $ttt[0];}
		 $result = mysql_query("SELECT sorszam FROM ".$_SESSION[adatbazis_etag]."_kategoriak WHERE szulo = '" . $kategoriaszulo . "'");
		 
		 while ($next_element = mysql_fetch_array($result)){
			$result_db = mysql_query("SELECT sorszam FROM ".$_SESSION[adatbazis_etag]."_elemek WHERE kategoria = '$next_element[sorszam]'");
			$fokategorianev_db = $fokategorianev_db + mysql_num_rows($result_db);
		 }
		 $fokategorianev = '<a href="/'.MAIN_DIRECTORY.'list&amp;fk='.$kategoriaszulo.'"> '.$ttt[0].' ('.$fokategorianev_db.' db)</a>';
		}
		
		if ($kategorianev_db > 0){
		$alkategorianev = ' ><a href="/'.MAIN_DIRECTORY.'list&amp;k='.$kategoriax.'"> '.$kategorianev.' ('.$kategorianev_db.' db)</a>';
		}
		if ($kategorianev != ""){ $navigacio .=	''.$fokategorianev.$alkategorianev;}
		
		if ($_REQUEST[szin]){
		   $resultc = mysql_query("SELECT szin FROM ".$_SESSION[adatbazis_etag]."_szinek WHERE id  = '" . $_REQUEST[szin] . "'");
			$tt = mysql_fetch_row($resultc);
		   $kiegeszit = ' > '.$tt[0];
		}
		
		if ($navigacio){
		$this->html_code = '
		<div class="oldalsav">
			'.$navigacio. $kiegeszit.'
		</div>';
		}
	}
}


class termek{
   public $id;
   public $megnevezes;
   public $kategoria;
   public $kategoriaszam;
   public $szulo_kategoria;
   public $szin;
   public $szin2;
   public $meret;
   public $anyag;
   public $leiras;
   public $akcios;
   public $kulonis;
   public $aktiv;
   public $listaar;
   public $listaar_ft;
   public $akciosar;
   public $akciosar_ft;
   public $kep1;
   public $kep2;
   public $ar;
   public $ar_ft;
   
   var $kepek = array();
   var $meretek = array();
   var $szinek = array();
   var $szinek_elem = array();
   var $szinek_id = array();
   var $csatoltak = array();
		   
   function mysql_read($id) {
	  $result = mysql_query("SELECT e.sorszam, e.megnevezes, e.kategoria, e.kulonis, e.szin, e.szin2, e.meret, e.anyag, e.leiras, e.akcios,
		 e.aktiv, e.listaar, e.akciosar, e.aktiv, k.kategorianev, k.szulo, sz.szin AS szin
		 FROM ".$_SESSION[adatbazis_etag]."_elemek AS e
		 LEFT JOIN ".$_SESSION[adatbazis_etag]."_kategoriak AS k ON e.kategoria = k.sorszam
		 LEFT JOIN ".$_SESSION[adatbazis_etag]."_szinek AS sz ON e.szin = sz.id
		 WHERE e.sorszam = '$id'");
	  $r = mysql_fetch_assoc($result);
	  
	  $this->id = $r['sorszam'];
	  $this->megnevezes = $r['megnevezes'];
	  $this->kategoria = $r['kategorianev'];
	  $this->kategoriaszam = $r['kategoria'];
	  $this->szulo_kategoria = $r['szulo'];
	  $this->szin = $r['szin'];
	  $this->szin2 = $r['szin2'];
	  $this->anyag = $r['anyag'];
	  $this->meret = $r['meret'];
	  $this->leiras = $r['leiras'];
	  $this->listaar = $r['listaar'];
	  $this->akciosar = $r['akciosar'];
	  $this->akcios = $r['akcios'];
	  $this->kulonis = $r['kulonis'];
	  $this->aktiv = $r['aktiv'];
	  
	  $this->listaar_ft = number_format($this->listaar, 0, ',', '.'). ' Ft';
	  
	  if ($this->akciosar > 0){
		 $ar = $this->akciosar;
	  } else {
		 $ar = $this->listaar;
	  }

	  $this->ar = $ar;
	  $this->ar_ft = number_format($ar, 0, ',', '.'). ' Ft';
	  
	  if ($this->szin2){
		$resultc = mysql_query("SELECT szin FROM ".$_SESSION[adatbazis_etag]."_szinek WHERE id  = '" . $this->szin2 . "'");
		$tt = mysql_fetch_row($resultc);
		$this->szin2 = $tt[0];
	  }
	  
	  $rr = mysql_query("SELECT filenev, fokep FROM ".$_SESSION[adatbazis_etag]."_elemkepek WHERE elemsorszam = '$id'");
	  $ii = 1;
	  while ($next_elementx = mysql_fetch_array($rr)){
		 if ($ii == 1){$this->kep1 = $next_elementx[filenev];}
		 if ($next_elementx[fokep] == '1'){$this->kep1 = $next_elementx[filenev];}
		 if ($next_elementx[fokep] != '1'){$this->kep2 = $next_elementx[filenev];}
		 $ii++;
	  }
	  
	  
		$result33 = mysql_query("SELECT sorszam, filenev, fokep FROM ".$_SESSION[adatbazis_etag]."_elemkepek WHERE elemsorszam = '$id'");
		while ($next_element = mysql_fetch_array($result33)){
			
			$this->kepek[$next_element['sorszam']] = 'elemkepek/'.$next_element['filenev'];
		}
		
		$result33 = mysql_query("SELECT elemsorszam, csatoltelem FROM ".$_SESSION[adatbazis_etag]."_csatolt WHERE elemsorszam = '$id'");
		while ($next_element = mysql_fetch_array($result33)){
			$resultcc = mysql_query("SELECT filenev FROM ".$_SESSION[adatbazis_etag]."_elemkepek WHERE elemsorszam  = '" . $next_element['csatoltelem'] . "' AND fokep = '1'");
			$ttt = mysql_fetch_row($resultcc);
			$this->csatoltak[$next_element['csatoltelem']] = 'elemkepek/'.$ttt[0];
		}
		
		#if ($this->kategoriaszam == '2'){
		 #$sorrend_gyerek = 'ORDER BY m.meret';
		 #}
		
		
		$result33 = mysql_query("SELECT ma.meretsorszam, m.meret FROM ".$_SESSION[adatbazis_etag]."_meretadatok AS ma
		   LEFT JOIN ".$_SESSION[adatbazis_etag]."_meretek AS m ON ma.meretsorszam = m.sorszam
		   WHERE ma.elemsorszam = '$id'");
		while ($next_element = mysql_fetch_array($result33)){
			
			$this->meretek[$next_element[meretsorszam]] = $next_element['meret'];
		}
		$this->meretek = array_unique($this->meretek);
		
		
		
		if (($this->kategoriaszam == 2) OR ($this->kategoriaszam > "31")){
		 asort($this->meretek);
		}
		
		
		$result33 = mysql_query("SELECT e.sorszam, sz.szin, sz.id, e.szin2 FROM ".$_SESSION[adatbazis_etag]."_elemek AS e
		   LEFT JOIN ".$_SESSION[adatbazis_etag]."_szinek AS sz ON e.szin = sz.id
		   WHERE e.megnevezes = '$this->megnevezes' AND e.kategoria = '$this->kategoriaszam'");
		while ($next_element = mysql_fetch_array($result33)){
		   
		   $szin_szoveg = $next_element['szin'];
		   if ($next_element['szin2']){
			  $resultc = mysql_query("SELECT szin FROM ".$_SESSION[adatbazis_etag]."_szinek WHERE id  = '" . $next_element['szin2'] . "'");
			  $tt = mysql_fetch_row($resultc);
			  $szin2 = $tt[0];
			  if ($szin2){
			   $szin_szoveg .= ' ('.$szin2.')';
			  }
		   }
		   
			$this->szinek[] = $szin_szoveg;
			$this->szinek_elem[] = $next_element['sorszam'];
			$this->szinek_id[] = $next_element['id'];
		}
		#$this->szinek = array_unique($this->szinek);
   }
   
   function mysql_delete($id){
	  $result = "DELETE FROM ".$_SESSION[adatbazis_etag]."_elemek WHERE sorszam = $id LIMIT 1";
	  mysql_query($result);
   }
   
   function mysql_checking(){
	  if ($_REQUEST[termek_akcios] == "on") { $this->akcios = 1;}
	  else { $this->akcios = 0;}
	  if ($_REQUEST[termek_kulonis] == "on") { $this->kulonis = 1;}
	  else { $this->kulonis = 0;}
	  if ($_REQUEST[termek_aktiv] == "on") { $this->aktiv = 1;}
	  else { $this->aktiv = 0;}
	  if ($_REQUEST[termek_listaar] != "") { $this->listaar = $_REQUEST[termek_listaar];}
	  else { $this->listaar = 0;}
	  if ($_REQUEST[termek_akciosar] != "") { $this->akciosar = $_REQUEST[termek_akciosar];}
	  else { $this->akciosar = 0;}
	  $this->szin = $_REQUEST[termek_szin1];
	  $this->szin2 = $_REQUEST[termek_szin2];
   }
   
   function mysql_insert(){
	  	  if ($_REQUEST[termek_megnevezes] != ""){
		$result = mysql_query("SELECT MAX(sorszam) FROM ".$_SESSION[adatbazis_etag]."_elemek");
		$row = mysql_fetch_array($result); 
		$num_rows=$row[0];
		$num_rows++;
		$ujelemsorszam = $num_rows;
		$this->id = $num_rows;
		$result_termek = "INSERT INTO ".$_SESSION[adatbazis_etag]."_elemek 
		   (sorszam, cikkszam, kategoria, megnevezes, anyag, leiras, megjegyzes, listaar, akciosar, aktiv, akcios)
		VALUES ('$num_rows', '$_REQUEST[termek_cikkszam]', '$_REQUEST[kategoria_szulo]', '$_REQUEST[termek_megnevezes]', '$_REQUEST[termek_anyag]', '$_REQUEST[termek_leiras]', '$_REQUEST[termek_megjegyzes]', $this->listaar, $this->akciosar, $this->aktiv, $this->akcios)";
		mysql_query($result_termek);
		$ujtermekszam = $num_rows;
		
		$sql = "DELETE FROM ".$_SESSION[adatbazis_etag]."_meretadatok WHERE elemsorszam = $this->id";
		 mysql_query($sql);
		for ($i = 0; $i < 10000; $i++){
			$meret_szam = 'meret_' . $i;
			if ($_REQUEST[$meret_szam] == 'on'){
					$sql2 = "INSERT INTO ".$_SESSION[adatbazis_etag]."_meretadatok 
					(elemsorszam, meretsorszam) VALUES
					('$this->id', '$i')";
					mysql_query($sql2);
			}
		}
	
	$kategoriacombonev = 'kategoria_szulo_0';
			if ($i == 0){
				if ($_REQUEST[$kategoriacombonev] != ''){
					$result = mysql_query("SELECT MAX(sorszam) FROM ".$_SESSION[adatbazis_etag]."_kategoriak_adat");
					$rowx = mysql_fetch_array($result); 
					$numrowsx=$rowx[0];
					$numrowsx++;
					$sql2 = "INSERT INTO ".$_SESSION[adatbazis_etag]."_kategoriak_adat 
					(sorszam, elemsorszam, kategoriasorszam) VALUES
					('$numrowsx', '$ujtermekszam', '".$_REQUEST[$kategoriacombonev]."')";
					mysql_query($sql2);
				}
			}
	
	}
   }
   
   function mysql_update($id){
	  		$result_termek = "UPDATE ".$_SESSION[adatbazis_etag]."_elemek SET 
					cikkszam='$_REQUEST[termek_cikkszam]',
					megnevezes='$_REQUEST[termek_megnevezes]', 
					leiras='$_REQUEST[termek_leiras]',
					kategoria='$_REQUEST[kategoria_szulo]',
					megjegyzes='$_REQUEST[termek_megjegyzes]',
					anyag='$_REQUEST[termek_anyag]',
					szin='$this->szin',
					szin2='$this->szin2',
					listaar='$_REQUEST[termek_listaar]',
					akciosar='$_REQUEST[termek_akciosar]',
					aktiv='$this->aktiv',
					akcios='$this->akcios',
					kulonis='$this->kulonis'
					WHERE sorszam='$_REQUEST[termek_azonosito]'";
		mysql_query($result_termek);
		
		
		$sql = "DELETE FROM ".$_SESSION[adatbazis_etag]."_meretadatok WHERE elemsorszam = $_REQUEST[termek_azonosito]";
		 mysql_query($sql);
		for ($i = 0; $i < 10000; $i++){
			$meret_szam = 'meret_' . $i;
			if ($_REQUEST[$meret_szam] == 'on'){
					$sql2 = "INSERT INTO ".$_SESSION[adatbazis_etag]."_meretadatok 
					(elemsorszam, meretsorszam) VALUES
					('$_REQUEST[termek_azonosito]', '$i')";
					mysql_query($sql2);
			}
		}
		
		#kateg�ri�k m�dos�t�sa
		#$result = mysql_query("SELECT MAX(sorszam) FROM ".$_SESSION[adatbazis_etag]."_kategoriak_adat");
		#$rowx = mysql_fetch_array($result); 
		#$numrowsx=$rowx[0];
		#$numrowsx++;
		
		if ($_REQUEST[termek_azonosito] == ''){
			$ujkategoriax = $ujelemsorszam;}
		else {
			$ujkategoriax = $_REQUEST[termek_azonosito];}
		
		for ($i = 0; $i < 10000; $i++){
			$kategoriacombonev = 'kategoria_szulo_' . $i;
			if ($i == 0){
				if ($_REQUEST[$kategoriacombonev] != ''){
					$result = mysql_query("SELECT MAX(sorszam) FROM ".$_SESSION[adatbazis_etag]."_kategoriak_adat");
					$rowx = mysql_fetch_array($result); 
					$numrowsx=$rowx[0];
					$numrowsx++;
					$sql2 = "INSERT INTO ".$_SESSION[adatbazis_etag]."_kategoriak_adat 
					(sorszam, elemsorszam, kategoriasorszam) VALUES
					('$numrowsx', '$ujkategoriax', '".$_REQUEST[$kategoriacombonev]."')";
					mysql_query($sql2);
				}
			}
			else {
				if ($_REQUEST[$kategoriacombonev] != ''){
					$sql2 = "UPDATE ".$_SESSION[adatbazis_etag]."_kategoriak_adat SET kategoriasorszam = '$_REQUEST[$kategoriacombonev]' WHERE sorszam=$i";
					mysql_query($sql2);
				}
			}
		}
		
		#jellemz�k m�dos�t�sa
		$result = mysql_query("SELECT MAX(sorszam) FROM ".$_SESSION[adatbazis_etag]."_elemjellemzok");
		$row = mysql_fetch_array($result); 
		$numrows=$row[0];
		$numrows++;
		
		for ($i = 1; $i < 10000; $i++){
			$j_jellemzo = 'termek_jellemzo_' . $i;
			$j_ertek = 'termek_jellemzo_ert_' . $i;
			$sql2 = "UPDATE ".$_SESSION[adatbazis_etag]."_elemjellemzok SET jellemzosorszam = '$_REQUEST[$j_jellemzo]', jellemzoertek = '$_REQUEST[$j_ertek]' WHERE sorszam=$i AND elemsorszam = $_REQUEST[termek_azonosito]";
			mysql_query($sql2);	
		}
		for ($i = 1; $i < 10000; $i++){
			$termek_dok = 'termek_dok' . $i;
			$sql2 = "UPDATE ".$_SESSION[adatbazis_etag]."_file SET title = '$_REQUEST[$termek_dok]' WHERE sorszam=$i AND elemsorszam = $_REQUEST[termek]";
			mysql_query($sql2);	
		}
		If ($_REQUEST[termek_jellemzo_ert_xxx] != "") {
			If ($_REQUEST[termek] != "") { $ujelemsorszam = $_REQUEST[termek];}
			$sql2 = "INSERT INTO ".$_SESSION[adatbazis_etag]."_elemjellemzok (sorszam, elemsorszam, jellemzosorszam, jellemzoertek) VALUES ('$numrows', '$ujelemsorszam', '$_REQUEST[termek_jellemzo_xxx]', '$_REQUEST[termek_jellemzo_ert_xxx]')";
			mysql_query($sql2);	
		}
   }
}

class kategoriak_combo{
	public $html_code;
	
	function combo($termek_kategoria, $szulokapcs, $comboszam) {
		$result = mysql_query("SELECT sorszam, kategorianev, szulo FROM ".$_SESSION[adatbazis_etag]."_kategoriak ORDER BY sorrend");
		while ($next_element = mysql_fetch_array($result)){
			$szulo_jel = '';
			if ($next_element[szulo] > 0){
				$result2 = mysql_query("SELECT kategorianev FROM ".$_SESSION[adatbazis_etag]."_kategoriak WHERE sorszam = $next_element[szulo]");
				$ss = mysql_fetch_row($result2);
				$szulo_nev = $ss[0];
				$szulo_jel = $szulo_nev .' - ';
			}
			if ($termek_kategoria != $next_element['sorszam']){
				$kategorialista .= '<option value="'.$next_element['sorszam'].'">'.$szulo_jel.$next_element['kategorianev'].'</option>';
			}
		}
		
		$result = "SELECT sorszam, kategorianev, szulo FROM ".$_SESSION[adatbazis_etag]."_kategoriak WHERE sorszam = $termek_kategoria";
		$check = mysql_query($result);  
		if ($check) {
			$s = mysql_fetch_row($check); 
			$jelolt_nev = $s[1];
			$szulo_sorszam = $s[2];
		}
		
		$result = "SELECT kategorianev FROM ".$_SESSION[adatbazis_etag]."_kategoriak WHERE sorszam = $szulo_sorszam";
		$check = mysql_query($result);  
		if ($check) { 
			$ss = mysql_fetch_row($check);
			$szulo_nev = $ss[0];
		}
		if ($szulokapcs == 'szulo1'){
			$kijelolt_kategoria = '<option selected="selected" value="'.$szulo_sorszam.'">'.$szulo_nev.'</option>';}
		else {
			$kijelolt_kategoria = '<option selected="selected" value="'.$termek_kategoria.'">'.$jelolt_nev.'</option>';}
		
		if ($comboszam != ''){
			$combofej = '<select name="kategoria_szulo_'.$comboszam.'">';}
		else {
			$combofej = '<select name="kategoria_szulo">';}
		
		if ($szulokapcs == 'szuro'){$combofej = '<select name="kategoriaszures" style="float: left; margin-right: 30px; clear:both;" onchange="szures(this.value, \'kat_admin\')">';}
		
		$this->html_code = 
			$kijelolt_kategoria.'
			
			'.$kategorialista.'';
	}
}

class vasarlo{
   public $sorszam;
   public $szallitasi_nev;
   public $szallitasi_irsz;
   public $szallitasi_telepules;
   public $szallitasi_cim;
   public $telefon;
   public $email;
   public $szamla_nev;
   public $szamla_irsz;
   public $szamla_telepules;
   public $szamla_cim;
   public $szamla_adoszam;
   
   function mysql_read($sorszam, $szures = 'regisztraltszam'){
	  if ($szures == 'sorszam'){
		 $result = mysql_query("SELECT szallitasi_nev, szallitasi_irsz, szallitasi_telepules, szallitasi_cim, telefon, email, szamla_nev, szamla_irsz, szamla_telepules, szamla_cim, adoszam, sorszam FROM ".$_SESSION[adatbazis_etag]."_vevok WHERE sorszam = '$sorszam'");
	  } else {
		 $result = mysql_query("SELECT szallitasi_nev, szallitasi_irsz, szallitasi_telepules, szallitasi_cim, telefon, email, szamla_nev, szamla_irsz, szamla_telepules, szamla_cim, adoszam, sorszam FROM ".$_SESSION[adatbazis_etag]."_vevok WHERE regisztraltszam = '$sorszam' AND archiv = '0'");
	  }
	  if (is_resource($result)) {
		$row = mysql_fetch_array($result); 
		$this->szallitasi_nev=$row[0];
		$this->szallitasi_irsz=$row[1];
		$this->szallitasi_telepules=$row[2];
		$this->szallitasi_cim=$row[3];
		$this->telefon=$row[4];
		$this->email=$row[5];
		$this->szamla_nev=$row[6];
		$this->szamla_irsz=$row[7];
		$this->szamla_telepules=$row[8];
		$this->szamla_cim=$row[9];
        $this->szamla_adoszam=$row[10];
		$this->sorszam=$row[11];
	}
   }
   
   function checking(){
	  $this->szallitasi_nev = $_REQUEST[vasarlo_szall_nev];
	  $this->szallitasi_irsz = $_REQUEST[vasarlo_szall_irszx];
	  $this->szallitasi_telepules = $_REQUEST[vasarlo_szall_telepules];
	  $this->szallitasi_cim = $_REQUEST[vasarlo_szall_cim];
	  $this->telefon = $_REQUEST[vasarlo_szall_tel];
	  $this->szamla_adoszam = $_REQUEST[vasarlo_adoszam];
   }
}

class kosar{
   public $sorszam;
   public $idopont;
   public $megrendeloszama;
   public $vevoszama;
   public $intezve;
   public $fizetes;
   public $szallitasiktg;
   public $megjegyzes;
   public $kosarertek;
   var $tetelek = array();
   public $vasarlo_sorszam;
   public $szallitasi_nev;
   public $szallitasi_irsz;
   public $szallitasi_telepules;
   public $szallitasi_cim;
   public $telefon;
   public $email;
   public $szamla_nev;
   public $szamla_irsz;
   public $szamla_telepules;
   public $szamla_cim;
   public $szamla_adoszam;
   
   function mysql_read($sorszam){
	  $result = mysql_query("SELECT sorszam, idopont, vevoszama, megrendeloszama, intezve, fizetes, szallitasiktg, megjegyzes FROM ".$_SESSION[adatbazis_etag]."_rendelesek WHERE sorszam = '$sorszam' ORDER BY idopont desc");
	  $row = mysql_fetch_assoc($result);
	  $this->sorszam = $row[sorszam];
	  $this->idopont = $row[idopont];
	  $this->megrendeloszama = $row[megrendeloszama];
	  $this->vevoszama = $row[vevoszama];
	  $this->intezve = $row[intezve];
	  $this->fizetes = $row[fizetes];
	  $this->szallitasiktg = $row[szallitasiktg];
	  $this->megjegyzes = $row[megjegyzes];
	  
	  $result2 = mysql_query("SELECT sorszam, rendelessorszam, termeksorszam, termekdarabszam, termekar, meret FROM ".$_SESSION[adatbazis_etag]."_rendelestetelek WHERE rendelessorszam = '$this->sorszam'");
	  while ($x = mysql_fetch_array($result2)){
		$this->tetelek[$x['sorszam']]['termeksorszam'] = $x['termeksorszam'];
		$this->tetelek[$x['sorszam']]['termekdarabszam'] = $x['termekdarabszam'];
		$this->tetelek[$x['sorszam']]['termekar'] = $x['termekar'];
		$this->tetelek[$x['sorszam']]['meret'] = $x['meret'];
		$this->tetelek[$x['sorszam']]['egysegar'] = $x['termekar'] / $x['termekdarabszam'];
		$this->kosarertek = $this->kosarertek + $x['termekar'];
		
		$termek = new termek();
		$termek->mysql_read($x['termeksorszam']);
		
		if ($termek->szin2){$szin_kieg = $termek->szin2 . ', ';}
		
		$this->tetelek[$x['sorszam']]['megnevezes'] = $termek->megnevezes.', '.$termek->szin.', '.$szin_kieg.$termek->meret;
		$this->tetelek[$x['sorszam']]['id'] = $termek->id;
		
		$vasarlo = new vasarlo();
	if ($this->megrendeloszama == 0){
		$vasarlo->mysql_read($this->vevoszama, 'sorszam');}		
	else {
	    $vasarlo->mysql_read($next_element[sorszam], 'regisztraltszam');}
	  }
	  $this->vasarlo_sorszam = $vasarlo->sorszam;
	  $this->szallitasi_nev = $vasarlo->szallitasi_nev;
	  $this->szallitasi_irsz = $vasarlo->szallitasi_irsz;
	  $this->szallitasi_telepules = $vasarlo->szallitasi_telepules;
	  $this->szallitasi_cim = $vasarlo->szallitasi_cim;
	  $this->telefon = $vasarlo->telefon;
	  $this->email = $vasarlo->email;
	  $this->szamla_nev = $vasarlo->szamla_nev;
	  $this->szamla_irsz = $vasarlo->szamla_irsz;
	  $this->szamla_telepules = $vasarlo->szamla_telepules;
	  $this->szamla_cim = $vasarlo->szamla_cim;
	  $this->szamla_adoszam = $vasarlo->szamla_adoszam;

   }
}

class log_db {
	public function write($user, $message) {
        $idopont = date("Y-m-d H:i:s");
        $sql2 = "INSERT INTO ".$_SESSION[adatbazis_etag]."_log (idopont, user, uri, message, host, user_agent, ip)
            VALUES ('$idopont', '$user', '$_SERVER[REQUEST_URI]', '$message', '$_SERVER[REMOTE_HOST]', '$_SERVER[HTTP_USER_AGENT]', '$_SERVER[REMOTE_ADDR]')";
            mysql_query($sql2);
	}
}
?>
