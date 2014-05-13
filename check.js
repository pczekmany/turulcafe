function emailCheck(emailmezo){
	var elsopont = emailmezo.value.indexOf('.',0);
	var lastpont = emailmezo.value.lastIndexOf('.');
	var kukac = emailmezo.value.indexOf('@',0);
	var last_kukac = emailmezo.value.lastIndexOf('@');
	var email_size = emailmezo.value.length;

	if (emailmezo.value=='' || kukac==(-1) || kukac==0 || elsopont==0 

|| elsopont==(-1) || (lastpont==(email_size-1)) || (kukac==(email_size-1)) || 

((kukac+1)==elsopont) || (kukac!=last_kukac))
    {
     alert('Hibás email cím formátum!')
     emailmezo.focus()
     emailmezo.select()
	 emailmezo.value = '';
     return false;
    }
    if (emailmezo.name == "cimzett") {
		alert("Az adatlap e-mailben elküldve!");}
	
	return true;
}

function uresCheck(uresmezo){
  if (uresmezo.name == "email") { var uzenet = "Üres az e-mail mező!";}
  if (uresmezo.name == "kuldo") { var uzenet = "Üres az e-mail mező!";}
  if (uresmezo.value != "") return true;
  else{
    alert(uzenet);
    return false;
  }
}

function menujelolo(id){
	document.getElementById(id).style.backgroundColor = '#4875a7';
	
	
	if (id == 'referenciak_haz'){ document.getElementById('referenciak_lakas').style.backgroundColor = '#4875a7';}
	if (id == 'referenciak_iroda'){ document.getElementById('referenciak_lakas').style.backgroundColor = '#4875a7';}
	if (id == 'referenciak_egyeb'){ document.getElementById('referenciak_lakas').style.backgroundColor = '#4875a7';}
	if (id == 'referenciak_futes'){ document.getElementById('referenciak_lakas').style.backgroundColor = '#4875a7';}
	
	if (id == 'energia_ho'){ document.getElementById('energia_alt').style.backgroundColor = '#4875a7';}
	if (id == 'energia_geo'){ document.getElementById('energia_alt').style.backgroundColor = '#4875a7';}
	if (id == 'energia_nap'){ document.getElementById('energia_alt').style.backgroundColor = '#4875a7';}
	if (id == 'energia_szel'){ document.getElementById('energia_alt').style.backgroundColor = '#4875a7';}
	
	if (id == 'tanusit_mirol'){ document.getElementById('tanusit_miert').style.backgroundColor = '#4875a7';}
	if (id == 'tanusit_tanusitas'){ document.getElementById('tanusit_miert').style.backgroundColor = '#4875a7';}
	if (id == 'tanusit_jogi'){ document.getElementById('tanusit_miert').style.backgroundColor = '#4875a7';}
	if (id == 'tanusit_arak'){ 
		document.getElementById('tanusit_miert').style.backgroundColor = '#4875a7';
		//document.getElementById('tanusit_miert').style.borderTop = '12px solid #4875a7';
		//document.getElementById('tanusit_miert').style.paddingBottom = '10px';
		}
}


function divdisp_on(id){

	if (id == 'logindiv'){
		document.getElementById('logindiv').style.display = 'block';
	}
	
	if (id == 'admin_termekadatlap' || id == 'admin_termekfotok' || id == 'admin_termekforum' || id == 'admin_termekdokumentumok'){
		document.getElementById('admin_termekadatlap').style.display = 'none';
		document.getElementById('admin_termekfotok').style.display = 'none';
		document.getElementById('admin_termekforum').style.display = 'none';
		document.getElementById('admin_termekdokumentumok').style.display = 'none';
		document.getElementById('kepfeltolt').style.display = 'none';
		document.getElementById('dokfeltolt').style.display = 'none';
	}
	
	if (id == 'penztar_kosarx' || id == 'penztar_vasarlox' || id == 'penztar_fizetesx'){
		document.getElementById('penztar_kosar').style.color = '#ee3224';
		document.getElementById('penztar_kosar').style.backgroundColor = '#ffffff';
		document.getElementById('penztar_vasarlo').style.color = '#ee3224';
		document.getElementById('penztar_vasarlo').style.backgroundColor = '#ffffff';
		document.getElementById('penztar_fizetes').style.color = '#ee3224';
		document.getElementById('penztar_fizetes').style.backgroundColor = '#ffffff';
		document.getElementById('penztar_kosarx').style.display = 'none';
		document.getElementById('penztar_vasarlox').style.display = 'none';
		document.getElementById('penztar_fizetesx').style.display = 'none';
	}
	
	if (id == 'admin_cikkadatlap' || id == 'admin_cikkszoveg' || id == 'admin_cikktermek'){
		document.getElementById('admin_cikkadatlap').style.display = 'none';
		document.getElementById('admin_cikkszoveg').style.display = 'none';
		document.getElementById('admin_cikktermek').style.display = 'none';
	}
	
	if (id == 'admin_termekfotok'){document.getElementById('kepfeltolt').style.display = 'block';}
	if (id == 'admin_termekdokumentumok'){document.getElementById('dokfeltolt').style.display = 'block';}
	
	if (id == 'penztar_kosarx'){
		document.getElementById('penztar_tovabb').style.display = 'block';
		document.getElementById('penztar_vissza').style.display = 'none';
	}
	
	if (id == 'penztar_vasarlox'){
		document.getElementById('penztar_tovabb').style.display = 'block';
		document.getElementById('penztar_vissza').style.display = 'block';
	}
	
	if (id == 'penztar_fizetesx'){
		document.getElementById('penztar_tovabb').style.display = 'none';
		document.getElementById('penztar_vissza').style.display = 'block';
		vasarlo_check();
		}
	
	document.getElementById(id).style.display = 'block';
	
	var newStr = id.substring(0, id.length-1);
	document.getElementById(newStr).style.color = '#ffffff';
	document.getElementById(newStr).style.backgroundColor = '#ee3224';
	
}

function divdisp_off(id){

	if (id == 'logindiv'){
		document.getElementById('logindiv').style.display = 'none';
	}
	
	if (id==1){id = 'admin_termekadatlap';}
	if (id==2){id = 'admin_termekfotok';}
	document.getElementById(id).style.display = 'none';
}

function megerosites_x(torolszam, formnev, termek) {
	if (formnev == "galeriakep") {
		var answer = confirm ("Ön a KÉP TÖRLÉSÉT választotta.\n Biztosan szeretné?");
		if (answer) { window.location="admin.php?tartalom=admingaleria&kepment=2&csoport="+termek+"&torol="+torolszam;}
	}
}