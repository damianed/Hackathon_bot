<?php 

	$request = file_get_contents("php://input");
	$requestJson = json_decode($request, true);

	$intentDisplayName = $requestJson['queryResult']['intent']['displayName'];
	$params = $requestJson['queryResult']['parameters'];
	$partsTech = new PartsTech();
	switch ($intentDisplayName) {
		case 'partBrand':
			

			break;
		case 'search_part_number':
			if(isset($params['part_number'])) {
				$response = "Estas buscando el parte ".$params['part_number']."?";
			} else {
				$response = "No entendi tu pregunta";
			}

			$fulfillment = array(
   			    "fulfillmentText" => $response
   			);
   			echo(json_encode($fulfillment));
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

