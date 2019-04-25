<?php
	$apiKey = "AIzaSyCTWpoN9Ruy4GuwIpRShSvrH188lZzIUm0";


	// $text: the text you want to translate
	// $lang: The translation direction. Example: es-en
	// Language codes: https://tech.yandex.com/translate/doc/dg/concepts/api-overview-docpage/#api-overview__languages
	require_once 'vendor/autoload.php';
	use Stichoza\GoogleTranslate\GoogleTranslate;
	function translate($text, $lang)
	{
		$tr = new GoogleTranslate($lang);
		$tr->setSource(); // Detect language automatically
		echo $tr->translate($text);
		// global $apiKey;
		// $ch = curl_init();

		// $url = "https://translation.googleapis.com/language/translate/v2?target=$lang&key=$apiKey&q=$text";
		// // $params = array(
		// // 	'key' => $apiKey,
		// // 	'text' => $text,
		// // 	'target' => $lang);

		// // //set the url, number of POST vars, POST data
		// // curl_setopt($ch,CURLOPT_URL, $url);
		// // curl_setopt($ch,CURLOPT_POST, true);
		// // curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($params));
		// // curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

		// $resp = file_get_contents($url);
		// // Close request to clear up some resources
		// //curl_close($ch);

		// return json_decode($resp, true);
	}

?>
