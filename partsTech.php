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
					'key' => 'USER_KEY',
				],
				'partner' =>
				[
					'id' => 'beta_bosch',
					'key' => 'PARTER_KEY',
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
 ?>
