<?php
	$apiKey = "trnsl.1.1.20190424T160245Z.9c5adc3a0b583d0e.cdbc585449976b1d6f3bb2770b84d745f3b67620";


	// $text: the text you want to translate
	// $lang: The translation direction. Example: es-en
	// Language codes: https://tech.yandex.com/translate/doc/dg/concepts/api-overview-docpage/#api-overview__languages
	function translate($text, $lang)
	{
		global $apiKey;
		$ch = curl_init();

		$url = "https://translate.yandex.net/api/v1.5/tr.json/translate";
		$params = array(
			'key' => $apiKey,
			'text' => $text,
			'lang' => $lang);

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

		$resp = curl_exec($ch);
		// Close request to clear up some resources
		curl_close($ch);

		return json_decode($resp, true);
	}


?>
