<?php

/**
 * @author Jonathon Reinhart
 * @copyright 2008
 * @date 08-10-2010
 */

/*
	1.0.2:	Bugfixes. Many thanks to Justin Phillips for pointing these out:
		Bugfix: __construct()  $this->user_agent unintentionally used instead of local parameter $user_agent.
		Bugfix: quota()        if $ip was null, $params was never declared caused an error when passed to make_request().
    
    1.0.3:  Bugfixes. Thanks to Mindaugas J. for pointing these out:
        Bugfix: get_strings()  'unique' parameter was not being included.
                                Incorrect variable in Exception for 'len' checking.
*/

class RandDotOrg
{
	// Constants
	const VER	= '1.0.3';
	const BASE_URL = 'http://www.random.org/';
	
	// Declarations
	private		$curl_ch;		// cURL channel
	
	
	
	
	
	public function __construct($user_agent='')
	{
		// Open the cURL channel
		$this->curl_ch = curl_init();
		
		$user_agent = 'phpRandDotOrg ' . self::VER . ' : ' . $user_agent;
		curl_setopt($this->curl_ch, CURLOPT_USERAGENT, $user_agent);
	}
	
	
	
	public function __destruct()
	{
		// Close the cURL channel
		curl_close($this->curl_ch);
	}
	
	
	public function get_integers($num=1, $min=0, $max=10, $base=10)
	{
		// Sanity Checking
		if ($num<1)
			throw new Exception('num must be at least 1.');
		if ($max<=$min)
			throw new Exception('max must be greater than min.');
		if ( !($base==2 || $base==8 | $base==10 | $base==16) )
			throw new Exception('Base must be 2, 8, 10, or 16.');
			
			
		$params = array(	'num'	=> $num,
							'min'	=> $min,
							'max'	=> $max,
							'base'	=> $base,
						);
		$int = $this->make_request('integer', $params);
		
		return $int;
	}



	
	public function get_sequence($min=1, $max=10)
	{
		// Sanity Checking
		if ($max<=$min)
			throw new Exception('max must be greater than min.');
			
		$params = array(	'min'	=> $min,
							'max'	=> $max,
						);
		$seq = $this->make_request('sequence', $params);
		
		return $seq;
	}
	
	
	
	public function get_strings($num=1, $len=10, $digits=TRUE, $upperalpha=TRUE,
								$loweralpha=TRUE, $unique=TRUE)
	{
		// Sanity Checking
		if ($num<1)
			throw new Exception('num must be at least 1.');
		if ($len<1 || $len>20)
			throw new Exception('len must be from 1 and 20.');
		if ( !($digits || $upperalpha || $loweralpha) )
			throw new Exception('At least one character group must be true.');

		$params = array(	'num'        => $num,
							'len'        => $len,
							'digits'     => ($digits) ? 'on' : 'off',
							'upperalpha' => ($upperalpha) ? 'on' : 'off',
							'loweralpha' => ($loweralpha) ? 'on' : 'off',
							'unique'     => ($unique) ? 'on' : 'off' 
						);
		$str = $this->make_request('string', $params);
		
		return $str;
	}
	
	
	
	public function quota($ip=NULL)
	{
		$params = array();

		if ($ip)
			$params['ip'] = $ip;
		
		$quota = $this->make_request('quota', $params);
		
		return $quota;
	}
	
	// Returns a string with the global parameters
	private static function global_params()
	{
		return "col=1&format=plain&rnd=new";
	}


	// Integer Generator	integers
	// Sequence Generator	sequences
	// String Generator		strings
	// Quota Checker		quota
	private function make_request($type, $params)
	{
		//echo "TYPE: $type\n";
		//echo "PARAMS:";
		//print_r($params);
		
		$url = self::BASE_URL;
		switch ($type)
		{
			case 'integer':
				$url .= 'integers/';
				break;
			case 'sequence':
				$url .= 'sequences/';
				break;
			case 'string':
				$url .= 'strings/';
				break;
			case 'quota':
				$url .= 'quota/';
				break;
		}
		$url .= "?";
		if(!empty($params))
			$url .= self::query_string($params);
		$url .= "&" . self::global_params();
		
		//echo "URL: $url\n";
		
		curl_setopt($this->curl_ch, CURLOPT_URL, $url);
		curl_setopt($this->curl_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl_ch, CURLOPT_FOLLOWLOCATION, TRUE);
		//curl_setopt($this->curl_ch, CURLOPT_TIMEOUT, $timeout);
		$raw_data = curl_exec($this->curl_ch);
		
		//echo "\n\nRAW DATA: $raw_data\n\n";
		
		return $this->parse_result($raw_data);
	}
	
	
	// Parses the raw data received by the cURL request
	// and handles errors as necessary
	private function parse_result($raw_data)
	{
		// Check to see if 'Error:' exists in the returned data,
		// indicating an error.\
		if ( strpos($raw_data, 'Error:') !== FALSE )
		{
			$error = substr($raw_data, 7);		// Remove the 'Error: ' from the beginning.
			throw new Exception('RandDotOrg Error: '.$error);
		}
		
		// If the last character is a newline, remove it
		if ( substr($raw_data,-1) == "\n" )
			$raw_data = substr($raw_data,0,-1);
		
		// Parse the data by newline.
		$parsed_data = explode("\n", $raw_data);
		
		return $parsed_data;
	}
	

	// Form an HTTP query string from a simple array
	// Ex:		$a = array('name'=>'joe' , 'weight'=>162 , 'height'=>5.7 )
	// 	==>		'name=joe&weight=162&height=5.7'
	private static function query_string($array)
	{
		$string = '';
		foreach($array as $k=>$v)
		{
			if (!is_array($v))
				$string .= $k . '=' . $v . '&';
		}
		
		// Remove last &
		$string = substr($string, 0, -1);
		return $string;
	}
}

?>