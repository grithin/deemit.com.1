<?
$url = 'http://deemit.com/user/emailVerify?id='.$page->userId.'&code='.$page->code;
?>
<h2>User Signup</h2>

<div>
	Dear <?=$page->name?>,<br/>
	You have signed up at Deemit.com.  Please verify this email address by going here:<br/>
	<a href="<?=$url?>"><?=$url?></a>
</div>