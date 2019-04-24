<?php 

	$request = file_get_contents("php://input");
	$requestJson = json_decode($request, true);

	$intentDisplayName = $requestJson['queryResult']['intent']['displayName'];
	$params = $requestJson['queryResult']['parameters'];
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
			# code ...
			break;
			
		default:
			# code...
			break;
	}
?>

