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
							$response += $submodel['submodelName'].', '
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
			# code ...
			break;
			
		default:
			# code...
			break;
	}
?>

