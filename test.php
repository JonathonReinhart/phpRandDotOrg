<html>
<body>

<pre>
<?php
	error_reporting(-1);

	include 'RandDotOrg.class.php';
	$tr = new RandDotOrg;

	$tr->get_integers(3, 5, 10);
?>


get_integers($num=3, $min=0, $max=10, $base=10);
Returns:
<?php
	$ar = $tr->get_integers($num=3, $min=0, $max=10, $base=10);
	print_r($ar);
?>



get_sequence($min=1, $max=10);
Returns:
<?php
	$ar = $tr->get_sequence($min=1, $max=10);
	print_r($ar);
?>



get_strings($num=1, $len=10, $digits=TRUE, $upperalpha=TRUE, $loweralpha=TRUE, $unique=TRUE);
Returns:
<?php
	$ar = $tr->get_strings($num=1, $len=10, $digits=TRUE, $upperalpha=TRUE, $loweralpha=TRUE, $unique=TRUE);
	print_r($ar);
?>


quota($ip=NULL);
Returns:
<?php
	$ar = $tr->quota($ip=NULL);
	print_r($ar);
?>

</pre>