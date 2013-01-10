<?php
	$nocahce = $_REQUEST["nocache"];
	if($nocahce != ""){
		$personas = array(
		array("nombre"=>"Amy Jones", "cedula"=>"", "codigo"=>"12345", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/amy_jones.jpg", "telefono"=>""),
		array("nombre"=>"Eugene Lee", "cedula"=>"", "codigo"=>"23658", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/eugene_lee.jpg", "telefono"=>""),
		array("nombre"=>"Gary Donovan", "cedula"=>"", "codigo"=>"42359", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/gary_donovan.jpg", "telefono"=>""),
		array("nombre"=>"James King", "cedula"=>"", "codigo"=>"42365", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/james_king.jpg", "telefono"=>""),
		array("nombre"=>"John Williams", "cedula"=>"", "codigo"=>"13504", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/john_williams.jpg", "telefono"=>""),
		array("nombre"=>"Julie Taylor", "cedula"=>"", "codigo"=>"01235", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/julie_taylor.jpg", "telefono"=>""),
		array("nombre"=>"Kathleen Byrne", "cedula"=>"", "codigo"=>"04542", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/kathleen_byrne.jpg", "telefono"=>""),
		array("nombre"=>"Lisa Wong", "cedula"=>"", "codigo"=>"12537", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/lisa_wong.jpg", "telefono"=>""),
		array("nombre"=>"Paul Jones", "cedula"=>"", "codigo"=>"76515", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/paul_jones.jpg", "telefono"=>""),
		array("nombre"=>"Paula Gates", "cedula"=>"", "codigo"=>"13845", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/paula_gates.jpg", "telefono"=>""),
		array("nombre"=>"Ray Moore", "cedula"=>"", "codigo"=>"78546", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/ray_moore.jpg", "telefono"=>""),
		array("nombre"=>"Steven Wells", "cedula"=>"", "codigo"=>"78564", "imagen"=>"http://192.168.1.121/medicosApp/images/pics/steven_wells.jpg", "telefono"=>"61011142")
		);
		
		echo '{"success":true,"personas":' . json_encode($personas) . '}';
	}
?>