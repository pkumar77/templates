// recreate table - based on stan_id and study_id
function showfrom_db(){
	var mydivname='right_column_body';
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
	  if (xmlhttp.readyState==4 && xmlhttp.status==200){
	  	//alert(mydivname);
			document.getElementById(mydivname).innerHTML=xmlhttp.responseText;
	   }
	 }
	xmlhttp.open("GET","select_action.php?class=main&axion=showfrom_db",true);
	xmlhttp.send();
}


