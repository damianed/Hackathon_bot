<?php
	require('partsTech.php');
	require('translate.php');
	$request = file_get_contents("php://input");
	$requestJson = json_decode($request, true);

	$intentDisplayName = $requestJson['queryResult']['intent']['displayName'];
	$params = $requestJson['queryResult']['parameters'];
	$ouputContexts = $requestJson['queryResult']['outputContexts'];
	$partsTech = new PartsTech();
	switch ($intentDisplayName) {
		case 'partBrand':


			break;
		case 'search_part_number':
			$stores = [
				['id' => 149918,"name" => "Avenida Felipe Ángeles No. 333-A, Col. Progreso, Guadalajara, JA 44730, MX", "supplierName" => "NAPA Auto Parts" ],
				['id' => 149914,"name" => "CALZ. DEL EJERCITO #1396, COL. QUINTA VELARDE, Guadalajara, JA 44430, MX", "supplierName" => "AutoZone" ],
				['id' => 149919,"name" => "Av. Revolución #705, Col. General Real, Guadalajara, JA 44890, MX", "supplierName" => "WORLDPAC" ]
			];

			$searchParams = [	"partNumber" => [$params['part_number']]];
			$responseMsg = [];
			$responseMsg['pre'] = "Ahorita tenemos disponibles siguientes piezas disponibles en estas tiendas: \n";
			$foundPart = false;
			$responseMsg['store'] ='';
			$partsNameEnglish = [];
			$partExist = false;
			foreach ($stores as $store) {
				$storeId = $store['id'];
				$parts = $partsTech->requestQuote($searchParams, $storeId)['parts'];
				if(sizeof($parts) > 0) {
					$partExist = true;
					$storeData['parts'] = [];
					$responseMsg['store'] .= "En la tienda de " . $store['supplierName'] ." que esta en ". $store['name']." tienen : \n";
					foreach ($parts as $part) {
						$partName = $part['partName'];
						$quantity = $part['quantity'];
						if($quantity == 0) {
							$quantity = $part['availability'][0]['quantity'];
						}

						if($part['quantity'] > 0) {
							$partsNameEnglish[] = $partName;
							$responseMsg['store'] .=  $quantity.' ['.sizeof($partsNameEnglish).'] con precio de '.  $part['price']['cost']."\n";
							$foundPart = true;
						}
					}

				}
				if($foundPart) {
					break;
				}
				$responseMsg['store'] ='';
			}

			if($responseMsg['store'] == '') {
				if($partExist) {
					$response = "Lo siento, pero el producto con ese numero de parte no esta disponible por el momento";
				} else {
					$response = "Disculpa pero ese numero de parte no pertenece a ningun producto que tengamos registrado";
				}
			} else {
				$response = $responseMsg['pre'] . $responseMsg['store'];
			}

			$fulfillment = array(
				"fulfillmentText" => $response
			);
			echo json_encode($fulfillment);
			break;
		case 'engine':
			# code ...
 			break;
		case 'submodel':
			if(empty($params['submodel'])) {
				$response = "No me mandaste ninguna version, ¿Cual es la version de tu carro?";
			}
			else {
				$solicitedYear = $ouputContexts[1]['parameters']['year'];
				$solicitedMake  = $ouputContexts[1]['parameters']['make'];
				$availableMakes = $partsTech->getMakes($solicitedYear, "", "");
				foreach ($availableMakes as $make) {
					$makeName = $make['makeName'];
					if ($makeName == $solicitedMake) {
						$makeId = $make['makeId'];
					}
				}
				$solicitedModel  = $ouputContexts[1]['parameters']['model'];
				$availableModels = $partsTech->getModels($solicitedYear, $makeId, "");
				foreach ($availableModels as $model) {
					$modelName = $model['modelName'];
					if ($modelName == $solicitedModel) {
						$modelId = $model['modelId'];
					}
				}
				$solicitedSubmodel = $ouputContexts[1]['parameters']['submodel'];
				$submodels = $partsTech->getSubModels($solicitedYear, $makeId, $modelId, "");
				foreach ($submodels as $submodel) {
					$submodelName = $submodel["submodelName"];
					if ($solicitedSubmodel == $submodelName) {
						$submodelId  = $submodel['submodelId'];
					}
				}
				if (empty($submodelId)) {
					$response = 'No encontre una version de tu carro con ese nombre, ¿Seguro que lo escribiste bien? Las versiones de tu carro son: ';
					foreach ($submodels as $key => $submodel) {
						if ($key < count($submodels)) {
							$response .= $submodel['submodelName'].', ';
						}
						else {
							$response .= 'o '.$submodel['submodelName'];
						}
					}
				}
				else {
					$availableEngines = $partsTech->getModels($solicitedYear, $makeId, $modelId, $submodelId);
					$response = 'Que motor tiene tu carro: ';
					foreach ($availableEngines as $key => $engine) {
						if ($key < count($availableEngines)) {
							$response .= $engine['engineName'].', ';
						}
						else {
							$response .= 'o '.$engine['engineName'];
						}
					}
				}
				$response .= '?';
			}

			$fulfillment = array(
				"fulfillmentText" => $availableEngines
			);
			echo(json_encode($fulfillment));
			break;
		case 'SearchPartName':
			$outputContexts = $requestJson['queryResult']["outputContexts"];
			$year = $params['year'];
			$makeName = $params['make'];
			$modelName = $params['model'];
			$makeId = 0;
			$allMakes = $partsTech->getMakes($year, '', '');
			foreach($allMakes as $make){
				if($make["makeName"] == $makeName){
					$makeId = $make["makeId"];
					$ouputContexts[1]['parameters']['makeId'] = $makeId;
					break;
				}
			}
			if($makeId == 0){
				$fulfillment = array(
					"fulfillmentText" => "No encontre la marca ".$makeName.", ¿Estas seguro que lo escribiste bien?"
				);
				echo(json_encode($fulfillment));
				die;
			}

			$models = $partsTech->getModels($year, $makeId, '');
			$modelId = 0;
			foreach($models as $model){
				if($model["modelName"] == $modelName){
					$modelId = $model["modelId"];
					$ouputContexts[1]['parameters']['modelId'] = $modelId;
					break;
				}
			}
			if($modelId == 0){
				$fulfillment = array(
					"fulfillmentText" => "No encontre el modelo ".$modelName.", ¿Estas seguro que lo escribiste bien?"
				);
				echo(json_encode($fulfillment));
				die;
			}
			$subModels = $partsTech->getSubModels($year, $makeId, $modelId, '');
			if(sizeof($subModels) < 2){
				$outputContexts[] =	array(
										"name" => $requestJson["session"]."/contexts/engineSelection",
										"lifespanCount" => 1,
										"parameters"=> array(
											"submodelId" => $subModels[0]['submodelId'],
										)
									);
				$response = "¿Cual es el motor que necesita?";
				$fulfillment = array(
					"fulfillmentText" => $response,
					"outputContexts" => $outputContexts,
				);
				echo(json_encode($fulfillment));
				die;
			}
			$response = "¿De cual versión es: ";

			foreach($subModels as $index=>$subModel){
				$response .= $subModel["submodelName"];
				if($index == sizeof($subModels) - 2){
					$response .= " o ";
				}else{
					if($index < sizeof($subModels) - 1){
						$response .= ", ";
					}
				}
			}
			$response .= "?";
			$fulfillment = array(
				"fulfillmentText" => $response,
				"outputContexts" => $outputContexts,
			);
			echo(json_encode($fulfillment));
			break;

		default:
			# code...
			break;
	}
?>
