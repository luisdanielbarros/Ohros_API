<?php
class wbGeneralDatabaseModule {
    //WB
    //WB -> Get WB Id By Name
    function wb_getwbid_byname($pdo, $_projectId, $_WBname) {
		$Names = explode(',', $_WBname);
		$toReturn = '';
		for ($i = 0; $i < count($Names); $i++) {
			$Name = $Names[$i];
			//Select the wb's information
			$sql = 'SELECT wb_bases.Id AS Id
			FROM wb_bases
			WHERE wb_bases.Id_Project = :Id_Project AND wb_bases.Title = :Title';
			$statement = $pdo->prepare($sql);
			$statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
			$statement->bindParam(':Title', $Name, PDO::PARAM_STR);
			$statement->execute();
			while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
				if ($toReturn != '') $toReturn .= ',';
				$toReturn .= $result['Id'];
			}
		}
        http_response_code(200);
        $json_response = array('messageTitle' => 'WB Get Id By Name Success.', 'message' => 'WB get id by name successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
}
?>