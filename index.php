<?php 
//var_dump($_POST);die(); 
?>
<?php
/* Include Files *********************/
session_start(); 
include("functions.php"); 
checkLLogin();
include("../header.php");
/*************************************/
?>
<?php displayLogin();  ?>







<br />
<?php require("footer.html"); ?>

<!-- 45 sec refresh -->
<script type ="text/javascript" src="jquery.jdpicker.js"></script>
<script>
 $(document).ready(function() {
 	 $("#currentdate").load("functions.php");
   var refreshId = setInterval(function() {
      $("#currentdate").load('functions.php?cd=go');
   }, 45000);
   $.ajaxSetup({ cache: false });
});
 $(document).ready(function() {
 	 
   var refreshId = setInterval(function() {
      $("#currentdateout").load('functions.php?cdout=go');
   }, 5000);
   $.ajaxSetup({ cache: false });
});

$(document).ready(function(){
	$('#fin_date').jdPicker({date_format:'YYYY-mm-dd'});
	$('#en_date').jdPicker({date_format:'YYYY-mm-dd'});
});

$(document).ready(function(){
 $("#autoencheck").click(function () {
	$("#ae1").toggleClass("hidden");
	$("#ae2").toggleClass("hidden");
	$("#ae3").toggleClass("hidden");
 });
});
$(document).ready(function(){
 $("#autoreccheck").click(function () {
	$("#ar1").toggleClass("hidden");
	$("#ar2").toggleClass("hidden");
 });
});

</script>
<script type="text/javascript">

function editsched(title, id, gid, day, str_date, findate, start_hour, start_minute, fin_hour, fin_minute, groupschedule,dur_min,dur_sec){
	if(document.getElementById("sched"+id)){
		dur_sec_30="";
		dur_sec_0="";
		if(dur_sec=="30"){
			dur_sec_30="selected";
		}else{
			dur_sec_0="selected";
		}
		
		titleinp='<td><input size="5" type="text" id="schedtitle" name="schedtitle" value="'+title+'" /></td>';
		idinp='<td>'+id+'</td>';
		gidinp='<td><input size="1" type="text" id="schedgid" name="schedgid" value="'+gid+'" /></td>';
		dayinp='<td><select id="schedday" name="schedday"><? showdaysoptedit(); ?></select></td>';
		strdinp='<td>'+str_date+'</td>';
		findinp='<td><input size="6" type="text" id="schedfind" name="schedfind" value="'+findate+'" /></td>';
		strhinp='<td style="min-width: 95px;"><input size="1" type="text" id="schedstrh" name="schedstrh" value="'+start_hour+'" />:<input size="1" type="text" id="schedstrm" name="schedstrm" value="'+start_minute+'" /></td>';
		finhinp='<td style="min-width: 95px;"><input size="1" type="text" id="schedfinh" name="schedfinh" value="'+fin_hour+'" />:<input size="1" type="text" id="schedfinm" name="schedfinm" value="'+fin_minute+'" /></td>';
		durhinp='<td style="min-width: 95px;"><input size="1" type="text" id="sched_dur_min" name="sched_dur_min" value="'+dur_min+'" />:<select id="sched_dur_sec" name="sched_dur_sec"> <option value="00" '+dur_sec_0+'>00</option><option value="30" '+dur_sec_30+'>30</option></select></td>';
		if(groupschedule==1){
			groupschinp='<td><input type="checkbox" id="schgrouped"  name="schgrouped" value="1" checked="yes" /></td>';
		}else{
			groupschinp='<td><input type="checkbox" id="schgrouped"  name="schgrouped" value="1" /></td>';
		}
		doneinp='<td><input type="submit" onclick="sendforedit();" name="editschschfirst" value="Done"><span style="display:none;" id="tempid"></span><input type="hidden" id="schedid" name="schedid" value="'+id+'"></td>';
		document.getElementById("sched"+id).innerHTML = titleinp+idinp+gidinp+dayinp+strdinp+findinp+strhinp+finhinp+durhinp+groupschinp+doneinp;
		document.getElementById("schedday").selectedIndex = day-1;
	}
}
function sendforedit(){

	var schedtitle =document.getElementById('schedtitle').value;
	var schedgid =document.getElementById('schedgid').value; 
	var schedday =document.getElementById('schedday').value -1;
	var schedfind =document.getElementById('schedfind').value;
	var schedstrh =document.getElementById('schedstrh').value;
	var schedstrm =document.getElementById('schedstrm').value;
	var schedfinh =document.getElementById('schedfinh').value;
	var schedfinm =document.getElementById('schedfinm').value;
	var scheddurm =document.getElementById('sched_dur_min').value;
	var scheddurs =document.getElementById('sched_dur_sec').value;
	var schedid =document.getElementById('schedid').value;
	var schgrouped;
	if(document.getElementById('schgrouped').checked){
		schgrouped=1;
	}else{
		schgrouped=0;
	}
	
	inp1='<form action="index.php" name="readyforsendform" method="post"> <input type="hidden" name="schedtitle" value="'+schedtitle+'">';
	inp2='<input type="hidden" name="schedgid" value="'+schedgid+'">';
	inp3='<input type="hidden" name="schedday" value="'+schedday+'">';
	inp4='<input type="hidden" name="schedfind" value="'+schedfind+'">';
	inp5='<input type="hidden" name="schedstrh" value="'+schedstrh+'">';
	inp6='<input type="hidden" name="schedstrm" value="'+schedstrm+'">';
	inp7='<input type="hidden" name="schedfinh" value="'+schedfinh+'">';
	inp8='<input type="hidden" name="schedfinm" value="'+schedfinm+'">';
	inp9='<input type="hidden" name="schedid" value="'+schedid+'">';
	inp10='<input type="hidden" name="schgrouped" value="'+schgrouped+'">';
	inp11='<input type="hidden" name="scheddurm" value="'+scheddurm+'">';
	inp12='<input type="hidden" name="scheddurs" value="'+scheddurs+'">';
	inp13='<input type="submit" name="editschsch" value="Done"></form>';
	
	document.getElementById("tempid").innerHTML = inp1+inp2+inp3+inp4+inp5+inp6+inp7+inp8+inp9+inp10+inp11+inp12+inp13;
	document.forms["readyforsendform"].submit();
		
}


function editautoen(title, id, en_date, en_hour){
	if(document.getElementById("scheden"+id)){
		titleinp='<td>'+title+'</td>';
		idinp='<td>'+id+'</td>';
		dateinp='<td><input size="6" type="text" id="scheddate" name="scheddate" value="'+en_date+'" /></td>';
		hourinp='<td><input size="3" type="text" id="schedhour" name="schedhour" value="'+en_hour+'" /></td>';		
		doneinp='<td><input type="submit" onclick="sendforeditaen();" name="editschschfirst" value="Done"><span style="display:none;" id="tempid"></span><input type="hidden" id="schedid" name="schedid" value="'+id+'"></td>';
		document.getElementById("scheden"+id).innerHTML = titleinp+idinp+dateinp+hourinp+doneinp;
	}
}
function sendforeditaen(){

	var schedid =document.getElementById('schedid').value; 
	var scheddate =document.getElementById('scheddate').value;
	var schedhour =document.getElementById('schedhour').value; 
	
	inp1='<form action="index.php" name="readyforsendformen" method="post">';
	inp2='<input type="hidden" name="scheddate" value="'+scheddate+'">';
	inp3='<input type="hidden" name="schedhour" value="'+schedhour+'">';
	inp4='<input type="hidden" name="schedid" value="'+schedid+'">';
	inp5='<input type="submit" name="editschschen" value="Done"></form>';
	
	document.getElementById("tempid").innerHTML = inp1+inp2+inp3+inp4+inp5;
	document.forms["readyforsendformen"].submit();
		
}


function editautorec(title, id, ar_start, ar_fin){
	if(document.getElementById("schedrec"+id)){
		titleinp='<td>'+title+'</td>';
		idinp='<td>'+id+'</td>';
		dateinp='<td><select id="schedstartday" name="schedstartday" ><option value="1" >Δευτέρα</option><option value="2">Τρίτη</option><option value="3" >Τετάρτη</option><option value="4">Πέμπτη</option><option value="5" >Παρασκευή</option><option value="6">Σάββατο</option><option value="7" >Κυριακή</option></select></td>';
		hourinp='<td><select id="schedfinday" name="schedfinday" ><option value="1" >Δευτέρα</option><option value="2">Τρίτη</option><option value="3" >Τετάρτη</option><option value="4">Πέμπτη</option><option value="5" >Παρασκευή</option><option value="6">Σάββατο</option><option value="7" >Κυριακή</option></select></td>';		
		doneinp='<td><input type="submit" onclick="sendforeditarec();" name="editschschfirst" value="Done"><span style="display:none;" id="tempid"></span><input type="hidden" id="schedid" name="schedid" value="'+id+'"></td>';
		document.getElementById("schedrec"+id).innerHTML = titleinp+idinp+dateinp+hourinp+doneinp;
		document.getElementById('schedstartday').value = ar_start;
		document.getElementById('schedfinday').value = ar_fin;
	}
}
function sendforeditarec(){

	var schedid =document.getElementById('schedid').value; 
	var schedstartday =document.getElementById('schedstartday').value;
	var schedfinday =document.getElementById('schedfinday').value; 
	
	inp1='<form action="index.php" name="readyforsendformrec" method="post">';
	inp2='<input type="hidden" name="schedstartday" value="'+schedstartday+'">';
	inp3='<input type="hidden" name="schedfinday" value="'+schedfinday+'">';
	inp4='<input type="hidden" name="schedid" value="'+schedid+'">';
	inp5='<input type="submit" name="editschschrec" value="Done"></form>';
	
	document.getElementById("tempid").innerHTML = inp1+inp2+inp3+inp4+inp5;
	document.forms["readyforsendformrec"].submit();
		
}


function colorize(i){
document.getElementById("daymenu"+i).style.backgroundColor = '#050A2F'; 
}

function showusercp(total){
for(i=1; i<= total; i++){
	if(document.getElementById("xron"+i)){
		document.getElementById("xron"+i).style.display = 'none';
	}
}

document.getElementById("usercpmain").style.display = 'block';

}

function menu(j, max){
	document.getElementById("usercpmain").style.display = 'none';

	for(i=1; i<= max; i++){
		if(document.getElementById("xron"+i)){
		if(i==j){
			document.getElementById("xron"+i).style.display = 'block';
		}else{
			document.getElementById("xron"+i).style.display = 'none';
		}
		}
	}

}
</script>
<div id='userinfopopupdiv'></div>
</body>
<div style="visibility:hidden">
 <a href="http://apycom.com/">Apycom jQuery Menus</a>
 </div>
<script type="text/javascript" src="/js/awstats_misc_tracker.js"></script>
<noscript><img src="/js/awstats_misc_tracker.js?nojs=y" height=0 width=0 border=0 style="displa
y: none"></noscript>

</html>

