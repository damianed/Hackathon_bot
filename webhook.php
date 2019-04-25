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
			foreach ($stores as $store) {
				$storeId = $store['id'];
				$parts = $partsTech->requestQuote($searchParams, $storeId)['parts'];
				$storeData = [];
				$storeData['name'] = $store['name'];
				$storeData['supplierName'] = $store['supplier']['name'];
				$storeData['parts'] = [];
				foreach ($parts as $part) {
					$partName = translate($part['partName'], 'en-es');
					$storeData['parts'][] = ['partName' => $partName, 'price' => $part['price']['list'], 'quantity' => $part['availability'][0]['quantity']];
				}
				$partsByStore[] = $storeData;
				break;
			}

			$response = "Ahorita tenemos disponibles siguientes piezas disponibles en estas tiendas: \n";
			foreach ($partsByStore as $storeData) {
				$response .= "En la tienda de " . $storeData['supplierName'] ." que esta en ". $storeData['name'].": \n";
				foreach ($storeData['parts'] as $part) {
					$response .= 'Hay '.$part['quantity'].' '.$part['partName']. ' con precio de '. $part['price']."\n";
				}
			}


			$fulfillment = array(
				"fulfillmentText" => $response
			);
			die(json_encode($fulfillment));
			break;
		case 'engine':
			# code ...
			break;
		case 'submodel':
			# code ...
			break;
		case 'SearchPartName':
			$year = $params['year'];
			$makeName = $params['make'];
			$modelName = $params['model'];
			$makeId = 0;
			$allMakes = $partsTech->getMakers($year);
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

			$models = $partsTech->getModels($year, $makeId);
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
			$subModels = $partsTech->getSubModels($year, $make, $modelId);
			$response = "¿De cual versión es: ";
			foreach($subModels as $index=>$submodel){
				if($index < sizeOf())
				$response .= "$subModel";
				if($index < sizeof($subModels) - 2){
					$response .= ", ";
				}else if($index < sizeof($subModels)){
					$respose .= "o ";
				}
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
