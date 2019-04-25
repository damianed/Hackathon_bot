<?php

/**
 *
 */
class PartsTech
{
	private static $baseUrl = "https://api.beta.partstech.com";
	// private $accessToken;
	function __construct()
	{
		$body = [
			'accessType' => 'user',
			'credentials' =>
			[
				'user' =>
				[
					'id' => 'hackteam_4',
					'key' => 'b17934a273b74f83ad81c9eeed997d84',
				],
				'partner' =>
				[
					'id' => 'beta_bosch',
					'key' => '4700fc1c26dd4e54ab26a0bc1c9dd40d',
				],
			],
		];

		$result = $this->makeRequest(self::$baseUrl.'/oauth/access', json_encode($body), ['isPostRequest' => true]);
		$result = json_decode($result, true);
		$this->accessToken = $result['accessToken'];

	}

	public function getYears($make, $model)
	{
		$body = [
			'make' => $make,
			'model' => $model,
		];
		$result = $this->makeRequest(self::$baseUrl.'/taxonomy/vehicles/years', json_encode($body), ['needsAuth'=> true]);
		return json_decode($result, true);
	}

	public function getMakes($year, $model, $submodel)
	{
		$body = [
			'year' => $year,
			'model' => $model,
			'submodel' => $submodel,
		];
		$result = $this->makeRequest(self::$baseUrl.'/taxonomy/vehicles/makes', json_encode($body), ['needsAuth'=> true]);

		return json_decode($result, true);
	}

	public function getModels($year, $make, $submodel)
	{
		$body = [
			'year' => $year,
			'make' => $make,
			'submodel' => $submodel,
		];
		$result = $this->makeRequest(self::$baseUrl.'/taxonomy/vehicles/models', json_encode($body), ['needsAuth'=> true]);

		return json_decode($result, true);
	}

	public function getSubModels($year, $make, $model, $engine)
	{
		$body = [
			'year' => $year,
			'make' => $make,
			'model' => $model,
			'engine' => $engine,
		];
		$result = $this->makeRequest(self::$baseUrl.'/taxonomy/vehicles/submodels', json_encode($body), ['needsAuth'=> true]);

		return json_decode($result, true);
	}

	public function getEngines($year, $make, $model, $submodel)
	{
		$body = [
			'year' => $year,
			'make' => $make,
			'model' => $model,
			'submodel' => $submodel,
		];
		$result = $this->makeRequest(self::$baseUrl.'/taxonomy/vehicles/engines', json_encode($body), ['needsAuth'=> true]);

		return json_decode($result, true);
	}

	public function requestQuote($searchParams, $storeId, $filters='')
	{
		$body = [
			'searchParams' => $searchParams,
			'storeId' => $storeId,
			'filters' => $filters,
		];
		$result = $this->makeRequest(self::$baseUrl.'/catalog/quote', json_encode($body), ['needsAuth'=> true, "isPostRequest" => true]);

		return json_decode($result, true);
	}

	public function getPart($partId)
	{
		$result = $this->makeRequest(self::$baseUrl.'/catalog/parts/'.$partId, '', ['needsAuth'=> true]);
		return json_decode($result, true);
	}

	public function getStore($storeId='')
	{
		if($storeId) {
			$storeId = '/'.$storeId;
		}
		$result = $this->makeRequest(self::$baseUrl.'/stores'.$storeId, '', ['needsAuth'=> true]);
		return json_decode($result, true);
	}

	private function makeRequest($url, $body, $requestParams=[])
	{
		$ch = curl_init();
		$needsAuth = false;
		if(isset($requestParams['needsAuth'])) {
			$needsAuth = $requestParams['needsAuth'];
		}

		$isPostRequest = false;
		if(isset($requestParams['isPostRequest'])) {
			$isPostRequest = $requestParams['isPostRequest'];
		}

		if($needsAuth) {
			$headers = [
				'Authorization: Bearer '.$this->accessToken
			];
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		if($isPostRequest) {
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $body);
		} else {
			curl_setopt($ch,CURLOPT_POST, false);

			if($body){
				$url .= '?'.http_build_query(json_decode($body));
			}
		}
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

		$resp = curl_exec($ch);
		// Close request to clear up some resources
		curl_close($ch);
		return $resp;
	}
}
$start_time = microtime(true);
require('translate.php');
$partsTech = new PartsTech();
$stores = $partsTech->getStore();

$partsByStore = [];

$searchParams = [	"partNumber" => ["18042"]];
$foundPart = false;
foreach ($stores as $store) {
	$storeId = $store['id'];
	$parts = $partsTech->requestQuote($searchParams, $storeId)['parts'];

	$storeData = [];
	$storeData['name'] = $store['name'];
	$storeData['supplierName'] = $store['supplier']['name'];
	$storeData['parts'] = [];
	if($parts) {
		foreach ($parts as $part) {
			// $partName = translate($part['partName'], 'en-es');
			$partName = $part['partName'];
			$storeData['parts'][] = ['partName' => $partName, 'price' => $part['price']['list'], 'quantity' => $part['availability'][0]['quantity']];
		}
		$partsByStore[] = $storeData;
		$foundPart = true;
	}
	if($foundPart) {
		break;
	}
}

$response = "Ahorita tenemos disponibles siguientes piezas disponibles en estas tiendas: \n";
foreach ($partsByStore as $storeData) {
	$response .= "En la tienda de " . $storeData['supplierName'] ." que esta en ". $storeData['name'].": \n";
	foreach ($storeData['parts'] as $part) {
		if($part['quantity'] > 0) {
			$response .= 'Hay '.$part['quantity'].' '.$part['partName']. ' con precio de '. $part['price']."\n";
		}
	}
}
$end_time = microtime(true);

$execution_time = ($end_time - $start_time);

echo " Execution time of script = ".$execution_time." sec";

 ?>
