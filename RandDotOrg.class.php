<?php
/*
    phpRandDotOrg - a PHP client for random.org.
    Jonathon Reinhart - 2008

    1.1.0:  8-30-2011 Feature. Thanks to Prasanth Gangaraju for submitting this.
        Added: Uses get_file_contents() for non-curl installations.
        
    1.0.3:  Bugfixes. Thanks to Mindaugas J. for pointing these out:
        Bugfix: get_strings()  'unique' parameter was not being included.
                                Incorrect variable in Exception for 'len' checking.
                                
    1.0.2:  Bugfixes. Many thanks to Justin Phillips for pointing these out:
        Bugfix: __construct()  $this->user_agent unintentionally used instead of local parameter $user_agent.
        Bugfix: quota()        if $ip was null, $params was never declared caused an error when passed to make_request().
    
    
*/

class RandDotOrg
{
    // Constants
    const VER   = '1.1.0';
    const BASE_URL = 'http://www.random.org/';
    
    // Declarations
    private     $use_curl;      // True if curl is found, false to use get_file_contents
    private     $curl_ch;       // curl channel
    private     $user_agent;    // HTTP User-Agent string. Only used by curl.
    

    
    public function __construct($user_agent='')
    {
        // Check for Curl support
        $this->uses_curl = function_exists('curl_init');
        
        $this->user_agent = 'phpRandDotOrg ' . self::VER . ' : ' . $user_agent;

        if ($this->uses_curl)
        {
            $this->curl_ch = curl_init();   // Open the cURL channel
        }
    }
    
    
    
    public function __destruct()
    {
        if ($this->uses_curl)
        {
            curl_close($this->curl_ch);         // Close the cURL channel
        }
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
            
            
        $params = array(    'num'   => $num,
                            'min'   => $min,
                            'max'   => $max,
                            'base'  => $base,
                        );
        $int = $this->make_request('integer', $params);
        
        return $int;
    }



    
    public function get_sequence($min=1, $max=10)
    {
        // Sanity Checking
        if ($max<=$min)
            throw new Exception('max must be greater than min.');
            
        $params = array(    'min'   => $min,
                            'max'   => $max,
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

        $params = array(    'num'        => $num,
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


    // Integer Generator    integers
    // Sequence Generator   sequences
    // String Generator     strings
    // Quota Checker        quota
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
        
        
        if ($this->uses_curl)
        {
            curl_setopt($this->curl_ch, CURLOPT_URL, $url);
            curl_setopt($this->curl_ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->curl_ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($this->curl_ch, CURLOPT_USERAGENT, $this->user_agent);
            //curl_setopt($this->curl_ch, CURLOPT_TIMEOUT, $timeout);
            $raw_data = trim(curl_exec($this->curl_ch));
        }
        else
        {
            $raw_data = trim(file_get_contents($url));
        }
        
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
            $error = substr($raw_data, 7);      // Remove the 'Error: ' from the beginning.
            throw new Exception('RandDotOrg Error: '.$error);
        }

        $raw_data = rtrim($raw_data);               // Remove newline from end
        $parsed_data = explode("\n", $raw_data);    // Separate the data by newline.
        
        return $parsed_data;
    }
    

    // Form an HTTP query string from a simple array
    // Ex:      $a = array('name'=>'joe' , 'weight'=>162 , 'height'=>5.7 )
    //  ==>     'name=joe&weight=162&height=5.7'
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

