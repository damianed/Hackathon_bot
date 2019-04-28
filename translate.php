<?php
	// $text: the text you want to translate
	// $target: The language you what to translate to. Exmple: en
	// $source: The original language of the text. Example: es
	require_once ('vendor/autoload.php');
	use \Statickidz\GoogleTranslate;
	function translate($text, $target, $source)
	{
		$trans = new GoogleTranslate();
		$result = $trans->translate($source, $target, $text);
		return $result;
	}
?>
