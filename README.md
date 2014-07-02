# phpRandDotOrg

PHP client for Random.org

### Usage / Examples


**`get_integers($num=1, $min=0, $max=10, $base=10)`**<br/>
Ex: `$rand_org->get_integers(3, 5, 10);`

    Array
    (
        [0] => 7
        [1] => 7
        [2] => 8
    )

<br/>
**`get_sequence($min=1, $max=10)`**<br/>
Ex: `$rand_org->get_sequence(1, 7);`

    Array
    (
        [0] => 5
        [1] => 7
        [2] => 1
        [3] => 3
        [4] => 4
        [5] => 6
        [6] => 2
    )

<br/>
**`get_strings($num=1, $len=10, $digits=TRUE, $upperalpha=TRUE, $loweralpha=TRUE, $unique=TRUE)`**<br/>
Ex: `$rand_org->get_strings(3, 20, FALSE, TRUE, TRUE, FALSE);`

    Array
    (
        [0] => kMLSqKeLLiBcXrcPKBso
        [1] => pIBpxMIAuagOFeDcgALy
        [2] => jhHKhTTkidDcXVCCwExR
    )

<br/>
**`quota($ip=NULL)`**<br/>
Ex: `$rand_org->quota();`

    Array
    (
        [0] => 994378
    )

<br/>
Also note: it is best to provide your email address / URL in the parameter when initializing this object:

	include 'RandDotOrg.class.php';
	$rand_org = new RandDotOrg('MyApp - exmaple.com');
