<?php 

	$request = file_get_contents("php://input");
	$requestJson = json_decode($request, true);

	$intentDisplayName = $requestJson['queryResult']['intent']['displayName'];
	switch ($intentDisplayName) {
		case 'partBrand':
			

			break;
		case 'search_part_number':
			$fulfillment = array(
   			    "fulfillmentText" => "si funciono";
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

