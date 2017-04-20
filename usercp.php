<div class="cpelement">
	  <center><h2>User CP</h2><br />
<form action="<? echo $_SERVER[PHP_SELF]; ?>" method="post" autocomplete="off">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Username <br />(latin chars, max 25): </td><td>
	<input type="text" name="username" maxlength="24" value="<? echo $_SESSION['username']; ?>">
</td></tr>
<tr><td>E-mail <br />(latin chars): </td><td>
	<input type="text" name="email" value="<? echo $_SESSION['email']; ?>">
</td></tr>
<tr><td>First Name <br />(greek chars): </td><td>
	<input type="text" name="firstname" maxlength="25" value="<? echo $_SESSION['fname']; ?>">
</td></tr>
<tr><td>Last Name <br />(greek chars): </td><td>
	<input type="text" name="lastname" maxlength="25" value="<? echo $_SESSION['lname']; ?>">
</td></tr>
<tr><td>A.E.M.: </td><td>
	<label for="aem"><? echo $_SESSION['aem']; ?></label>
</td></tr>
<tr><td>Phone number: </td><td>
	<input type="text" name="telephone" maxlength="10" value="<? if($_SESSION['telephone'] >0){echo $_SESSION['telephone'];} ?>">
</td></tr>
<tr><td>Password <br /> (latin/greek/numbers): </td><td>
	<input type="password" name="pass" maxlength="25" value="<? echo $_SESSION['password']; ?>" autocomplete="off">
</td></tr>

<tr><td colspan="2" align="right">
<input  type="hidden" name="user_id" value="<? echo $_SESSION['id']; ?>">
<input  type="submit" name="change" value="Change">
</td></tr>
</table>
</form>
</div>

<div class="cpelement">
	  <center><h2>Change Academic ID</h2><br />
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Academic ID: </td><td>
	<input type="text" name="academic_id" maxlength="12" value="<? echo $_SESSION['academic_id']; ?>">
</td></tr>
<tr><td colspan="2" align="right">
<input  type="hidden" name="user_id" value="<? echo $_SESSION['id']; ?>">
<input  type="submit" name="change_academic_id" value="Change Academic ID">
</td></tr>
</table>
</form>
</div>

<?php
/**
 * Resend the activation mail. 
 * The form appears only if the account is not active.
 */
if($_SESSION['active']=='0'){
echo '
<div class="cpelement">
<br />
<form action="';
echo $_SERVER['PHP_SELF'];
echo '" method="post">
<table align="left" border="0" cellspacing="0" cellpadding="3" style="margin-top:40px;">
<tr><td>Resend the activation mail</td>
<td>
<input  type="hidden" name="user_aem" value="';
echo $_SESSION['aem'];
echo '">
<input  type="submit" name="reactivmail" value="Send">
</td></tr>
</table>
</form>
</div>';
}
echo '<br /><br />';
?>


