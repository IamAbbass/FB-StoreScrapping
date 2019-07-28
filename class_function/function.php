<?php

	//Time & Date
	date_default_timezone_set ("Asia/Karachi");

	//Dated
    $yesterday_             = strtotime('-1 day',strtotime(date("d-M-Y")));
    $today_                 = strtotime(date("d-M-Y"));
    $tomorrow_              = strtotime('+1 day',strtotime(date("d-M-Y")));
    $last_week_             = strtotime('-7 day',strtotime(date("d-M-Y")));


    //Rand Numbers
	$rand 		 		    = mt_rand(1, 999999);

	//Hash
	$hash 			        = md5(time() . $rand);



	function round_10($amount){
		$diff = 10-($amount%10);

		if($diff >= 10){
		  $diff = 0;
		}

		return ($amount+$diff);
	}

	// PDO Function To Handle Prepared Statements (FOR ALL QUERY)
	function sql($DBH, $query, $params, $return) {
		try {
			// Prepare statement
			$STH = $DBH->prepare($query);
			// Execute statement
			$STH->execute($params);
			// Decide whether to return the rows themselves, or just count the rows
			if ($return == "rows") {
				return $STH->fetchAll();
			}
		  	elseif ($return == "count") {
				return $STH->rowCount();
			}
		}
		catch(PDOException $e) {
			file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND); # Errors Log File
		}
	}



	//Redirect
	function redirect($url) {
		try {
			header("Location: ".$url);
			exit();
		}
		catch(PDOException $e) {
			file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND); # Errors Log File
		}
	}

	//Logout
	function logout($url) {
		try {
			session_unset();
			session_destroy();
			header("Location: $url");
			exit();
		}
		catch(PDOException $e) {
			file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND); # Errors Log File
		}
	}

	//Filter Date Difference
		function date_difference($date1,$date2){
		$date1 = date("D d M Y",strtotime($date1));
		$date2 = date("D d M Y",strtotime($date2));
		$days = (strtotime($date2) - strtotime($date1)) / (60 * 60 * 24);
		return $days;
	}

	//Convert Number into Words
	function convert_number_to_words($number)
	{
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);

		if (!is_numeric($number)) {
			return false;
		}

		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ($number < 0) {
			return $negative . convert_number_to_words(abs($number));
		}

		$string = $fraction = null;

		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}

		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . convert_number_to_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= convert_number_to_words($remainder);
				}
				break;
		}

		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}

		return $string;
	}

	//MC Encrypt

	$key_one = "E335271011A545921B5674790738E6B8"; //Don't Change This Key
	function mc_encrypt($encrypt, $key){
        $encrypt = serialize($encrypt);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        $key = pack('H*', $key);
        $mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
        $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
        $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
        return $encoded;
    }

    function mc_decrypt($decrypt, $key){
        $decrypt = explode('|', $decrypt.'|');
        $decoded = base64_decode($decrypt[0]);
        $iv = base64_decode($decrypt[1]);
        if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
        $key = pack('H*', $key);
        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
        $mac = substr($decrypted, -64);
        $decrypted = substr($decrypted, 0, -64);
        $calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
        if($calcmac!==$mac){ return false; }
        $decrypted = unserialize($decrypted);
        return $decrypted;
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hr',
            'i' => 'min',
            's' => 'sec',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    function my_simple_date($ts){
    	if((time()-$ts) < 86400){
    		$simple_dt = time_elapsed_string("@".$ts);
    	}else if ((time()-$ts)>=86400 && (time()-$ts)< ((86400)*2)){
    		$simple_dt = "Yesterday at ".date("h:ia", $ts);;
    	}else{
    		if(date("Y", $ts) == date("Y", time())){
    			$simple_dt = date("M d", $ts)." at ".date("h:ia", $ts);
    		}else{
    			$simple_dt = date("M d, Y", $ts)." at ".date("h:ia", $ts);
    		}
    	}
    	return $simple_dt;
    }


?>
