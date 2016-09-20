
<?php
if($mailMessage != '')
{
	echo $mailMessage;
?>
<br>
<?php
}
?>

Please find attachment of Invoice.<br>

<?php
if($payment_link != '')
{
?>
Click on below link to pay<br>
<a href="<?php echo $payment_link;?>"><?php echo $payment_link;?></a>
<?php
}
?>