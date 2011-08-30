<html>
<body>

<pre>
<?php
	error_reporting(-1);

	include 'RandDotOrg.class.php';
	$tr = new RandDotOrg;
?>

RandDotOrg::VER 
Returns:
<?php
	print_r(RandDotOrg::VER);
?>



get_integers($num=3, $min=0, $max=10, $base=10);
Returns:
<?php
	$ar = $tr->get_integers(3, 0, 10, 10);
	print_r($ar);
?>



get_sequence($min=1, $max=10);
Returns:
<?php
	$ar = $tr->get_sequence(1, 10);
	print_r($ar);
?>



get_strings($num=3, $len=6, $digits=TRUE, $upperalpha=TRUE, $loweralpha=TRUE, $unique=TRUE);
Returns:
<?php
	$ar = $tr->get_strings(3, 6, TRUE, TRUE, TRUE, TRUE);
	print_r($ar);
?>


quota($ip=NULL);
Returns:
<?php
	$ar = $tr->quota($ip=NULL);
	print_r($ar);
?>

</pre>
