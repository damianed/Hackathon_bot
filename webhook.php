<?php
	require('partsTech.php');
	require('translate.php');
	$request = file_get_contents("php://input");
	$requestJson = json_decode($request, true);

	$intentDisplayName = $requestJson['queryResult']['intent']['displayName'];
	$params = $requestJson['queryResult']['parameters'];
	$partsTech = new PartsTech();
	switch ($intentDisplayName) {
		case 'partBrand':


			break;
		case 'search_part_number':
			$stores = $partsTech->getStore();

			$partsByStore = [];

			$searchParams = [	"partNumber" => [$params['part_number']]];
			$response = "Ahorita tenemos disponibles siguientes piezas disponibles en estas tiendas: \n";
			foreach ($stores as $store) {
				$storeId = $store['id'];
				$parts = $partsTech->requestQuote($searchParams, $storeId)['parts'];
				$storeData = [];
				$storeData['name'] = $store['name'];
				$storeData['supplierName'] = $store['supplier']['name'];
				$storeData['parts'] = [];
				$response .= "En la tienda de " . $storeData['supplierName'] ." que esta en ". $storeData['name']."tienen : \n";
				foreach ($parts as $part) {
					// $partName = translate($part['partName'], 'en-es');
					$partName = $part['partName'];
					// $storeData['parts'][] = ['partName' => $partName, 'price' => $part['price']['list'], 'quantity' => $part['availability'][0]['quantity']];
					if($part['quantity'] > 0) {
						$response .=  $part['availability'][0]['quantity'].' '.$partName. ' con precio de '.  $part['price']['list']."\n";
					}
				}
				// $partsByStore[] = $storeData;
				break;
			}

			// foreach ($partsByStore as $storeData) {
			// 	foreach ($storeData['parts'] as $part) {
			// 	}
			// }


			$fulfillment = array(
				"fulfillmentText" => $response
			);
			echo json_encode($fulfillment);
			break;
		case 'engine':
			# code ...
			break;
		case 'submodel':
			$response = "Hello";
			if(empty($params['submodel'])) {
				$response = "No me mandaste ningun modelo, ¿Cual es el model de tu carro?";
			}
			else {
				$solicitedYear = $params['outputContexts'][1]['parameters']['year'];
				$solicitedMake  = $params['outputContexts'][0]['parameters']['submodel'];
				$availableMakes = $partsTech.getMakes($year, "", "");
				foreach ($availableMakes as $key => $make) {
					$makeName = $make['makeName'];
					if ($makeName == $solicitedMake) {
						$id = $make[$key]['makeId'];
					}
				}
				if (empty($id)) {
					$solicitedModel = $params['outputContexts'][1]['parameters']['model'];
					$submodels = $partsTech.getSubModels($year, $solicitedMake, $solicitedModel, "");
					$response = 'No encontre la version de tu carro con ese nombre, ¿Seguro que lo escribiste bien? Las versiones de tu carro son: ';
					foreach ($submodels as $key => $submodel) {
							$response .= $submodel['submodelName'].', ';
					}
				}
				else {
					$response = 'Cual es el Motor de tu carro?';
				}
			}

			$fulfillment = array(
				"fulfillmentText" => $response
			);
			echo(json_encode($fulfillment));
			break;
		case 'SearchPartName':
			$year = $params['year'];
			$makeName = $params['make'];
			$modelName = $params['model'];
			$makeId = 0;
			$allMakes = $partsTech->getMakes($year, '', '');
			foreach($allMakes as $make){
				if($make["makeName"] == $makeName){
					$makeId = $make["makeId"];
					break;
				}
			}
			if($makeId == 0){
				$fulfillment = array(
					"fulfillmentText" => "No encontre la marca ".$makeName.", ¿Estas seguro que lo escribiste bien?"
				);
				echo(json_encode($fulfillment));
				break;
			}

			$models = $partsTech->getModels($year, $makeId, '');
			$modelId = 0;
			foreach($models as $model){
				if($model["modelName"] == $modelName){
					$modelId = $model["modelId"];
					break;
				}
			}
			if($modelId == 0){
				$fulfillment = array(
					"fulfillmentText" => "No encontre la marca ".$modelName.", ¿Estas seguro que lo escribiste bien?"
				);
				echo(json_encode($fulfillment));
				break;
			}
			$subModels = $partsTech->getSubModels($year, $makeId, $modelId, '');
			$response = "¿De cual versión es: ";
			die(json_encode($subModels, JSON_PRETTY_PRINT));
			foreach($subModels as $index=>$subModel){
				$response .= $subModels[$index]["submodelName"];
				//if($index < sizeof($subModels) - 1){
					$response .= ", ";
				//}
			}
			$response .= "?";
			$fulfillment = array(
				"fulfillmentText" => $response,
			);
			echo(json_encode($fulfillment));
			break;

		default:
			# code...
			break;
	}
?>
