<?
if($_SESSION['type']==1){
?>
<br />
<br />
<br />
<div class="cpelement">

<center><h2>Add New Schedule.</h2><br /></center>
<form action="index.php" method="post">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Title: </td><td><input type="text" name="title" maxlength="50" /></td></tr>
<tr><td>Day: </td><td><select name="day">
    <? showdaysopt(); ?>
    </select></td></tr>
<tr><td>File (optional): </td><td><input type="text" name="filename" maxlength="150" /></td></tr>
<tr><td>Groud ID: </td><td><input type="text" name="gid" maxlength="2" size="2"></td></tr>
<tr><td>Starting hour: </td><td><input type="text" name="start_hour" maxlength="2" size="2" value="24" />:<input type="text" name="start_minute" maxlength="2" size="2" value="00" /></td></tr>
<tr><td>Finishing hour: </td><td><input type="text" name="fin_hour" maxlength="2" size="2" value="24" />:<input type="text" name="fin_minute" maxlength="2" size="2" value="00" /></td></tr>
<tr><td>Each date duration: </td><td><input type="text" name="minutes" maxlength="2" size="2" />Min <select name='seconds'>
    <option value='00'>00</option>
    <option value='30'>30</option>
    </select>Sec</td></tr>
	
<tr><td>Group Schedule (optional): </td><td><input type="checkbox" id="groupsch"  name="groupsch" value="1" /></td></tr>	
<tr><td>Auto enable (optional): </td><td><input type="checkbox" id="autoencheck"  name="autoencheck" /></td></tr>
<tr id="ae1" class="hidden"><td>Enable date (YYYY-MM-DD): </td><td>
<input id="en_date" type="text" name="en_date" maxlength="10" />
</td></tr>
<tr id="ae2" class="hidden"><td>Enable hour (24-hour format): </td><td><input type="text" name="en_hour" maxlength="2" size="2" /></td></tr>
<tr id="ae3"><td>Enable: </td><td><select name='onoff'>
    <option value='0' selected="selected">Off</option>
    <option value='1'>On</option>
    </select></td></tr>
	
<tr><td>Auto recurring(optional): <br />(If it's used, set only the first deadline)</td><td><input type="checkbox" id="autoreccheck" name="autoreccheck" /></td></tr>
<tr id="ar1" class="hidden"><td>Starting day: <br />(Will become enabled at 00:01 of this day)</td><td>
<select id="ar_start" name='ar_start'>
	<option value='0' >N/A</option>
    <option value='1' >Δευτέρα</option>
    <option value='2'>Τρίτη</option>
	<option value='3' >Τετάρτη</option>
    <option value='4'>Πέμπτη</option>
	<option value='5' >Παρασκευή</option>
    <option value='6'>Σάββατο</option>
	<option value='7' >Κυριακή</option>
    </select>
</td></tr>
<tr id="ar2" class="hidden"><td>Disable day: <br />(Will become disabled at 00:01 of this day)</td><td>
<select id="ar_fin" name='ar_fin'>
	<option value='0' >N/A</option>
    <option value='1' >Δευτέρα</option>
    <option value='2'>Τρίτη</option>
	<option value='3' >Τετάρτη</option>
    <option value='4'>Πέμπτη</option>
	<option value='5' >Παρασκευή</option>
    <option value='6'>Σάββατο</option>
	<option value='7' >Κυριακή</option>
    </select>
</td></tr>
<tr ><td>Deadline (YYYY-MM-DD): <br />(Date the schedule will be used. The schedule will be deleted or cleared 1 day after this date.)</td><td>
<input id="fin_date" type="text" name="fin_date" maxlength="10" value="<? echo date('Y-m-d'); ?>" />
</td></tr>

<tr><td colspan="2" align="right"><input type="submit" name="addsch" value="Add Schedule" /></td></tr>
</table>
</form>
</div>
<br /><br /><br />

<div class="cpelement" id="acpschedules">
	<? showcheduleadmin(); ?>
</div>
<br /><br /><br />

<div class="cpelement" id="acpautoen">
	<? showautoenadmin(); ?>
</div>
<br /><br /><br />

<div class="cpelement" id="acpautorec">
	<? showautorecadmin(); ?>
</div>
<br /><br /><br />

<div class="cpelement" id="acpschfiles">
	<? showschfiles(); ?>
</div>
<br /><br /><br />

<div class="cpelement" id="acpdays">
	<? showdays(); ?>
<br /><br />
</div>

<div class="cpelement" id="acpmakepublicann">
	<center><h2>Add Public Announcement.</h2><br />
	<form action="index.php" method="post" id="addpublicann">
		<table align="left" border="0" cellspacing="0" cellpadding="3">
			<tr><td>Announcement : </td></tr>
			<tr><td><textarea name="publicanninput" form="addpublicann" rows="7" cols="35" class="textareavertical"></textarea></td></tr>
			<tr><td colspan="2" align="right"><input type="submit" name="addpublicann" value="Add Announcement" /></td></tr>
		</table>
	</form>
	</center>
	<br /><br />
</div><br />

<div class="cpelement" id="acpeditpublicann">
	<? editpublicannadmincp(); ?>
<br /><br />
</div>


<? } ?>
</center>
