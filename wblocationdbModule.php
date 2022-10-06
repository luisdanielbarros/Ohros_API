<?php
class wbLocationDatabaseModule {
    //WB
    //WB -> Create Location
    function wb_create_location($pdo, $_projectId, $_Concept, $_ReasonOfConcept, $_WBname, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth) {
        //Check if user hasn't reached the maximum number of locations
        $sql = 'SELECT COUNT(*) FROM wb_bases WHERE wb_bases.Id_Project = :Id_Project AND wb_bases.Id_Type = 2';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count > 99) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Location Creation Failure.', 'message' => 'Location creation failure: user has already reached the maximum number of locations per project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Create the base
            $wbType = (int)2;
            $sql = 'INSERT INTO wb_bases (Id_Project, Id_Type, Concept, ReasonOfConcept, Title, Summary, Description, Cause, Purpose, Myth) 
                                   VALUES (:Id_Project, :Id_Type, :Concept, :ReasonOfConcept, :Title, :Summary, :Description, :Cause, :Purpose, :Myth)';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
            $statement->bindParam(':Id_Type', $wbType, PDO::PARAM_INT);
            $statement->bindParam(':Concept', $_Concept, PDO::PARAM_STR);
            $statement->bindParam(':ReasonOfConcept', $_ReasonOfConcept, PDO::PARAM_STR);
            $statement->bindParam(':Title', $_WBname, PDO::PARAM_STR);
            $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
            $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
            $statement->bindParam(':Cause', $_Cause, PDO::PARAM_STR);
            $statement->bindParam(':Purpose', $_Purpose, PDO::PARAM_STR);
            $statement->bindParam(':Myth', $_Myth, PDO::PARAM_STR);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Location Creation Failure.', 'message' => 'Location creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                $wbId = (int)$pdo->lastInsertId();
                //Create the base
                $sql2 = 'INSERT INTO wb_locations (Id_Base) VALUES (:Id_Base)';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_Base', $wbId, PDO::PARAM_INT);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Location Creation Failure.', 'message' => 'Location creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'Location Creation Success.', 'message' => 'Location creation successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //WB -> Update Location
    function wb_update_location($pdo, $_wbId, $_Concept, $_ReasonOfConcept, $_WBname, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth) {
        //Update the wb base's information
        $sql = 'UPDATE wb_bases SET Concept = :Concept, ReasonOfConcept = :ReasonOfConcept, Title = :Title, Summary = :Summary, Description = :Description, Cause = :Cause, Purpose = :Purpose, Myth = :Myth WHERE Id = :Id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Concept', $_Concept, PDO::PARAM_STR);
        $statement->bindParam(':ReasonOfConcept', $_ReasonOfConcept, PDO::PARAM_STR);
        $statement->bindParam(':Title', $_WBname, PDO::PARAM_STR);
        $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
        $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
        $statement->bindParam(':Cause', $_Cause, PDO::PARAM_STR);
        $statement->bindParam(':Purpose', $_Purpose, PDO::PARAM_STR);
        $statement->bindParam(':Myth', $_Myth, PDO::PARAM_STR);
        $statement->bindParam(':Id', $_wbId, PDO::PARAM_INT);
        $results = $statement->execute();
        if (!$results) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Location Update Failure.', 'message' => 'Location update failure: the system is currently unavailable, please try again later.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            http_response_code(200);
            $json_response = array('messageTitle' => 'Location Update Success.', 'message' => 'Location update successful.', 'content' => array());
            echo json_encode($json_response);
        }
    }
    //WB -> Delete Location
    function wb_delete_location($pdo, $_Projpass, $_projectId, $_wbId) {
        //Check if the password is correct
		$hashedProjpass = substr(md5($_Projpass), 0, 127);
        $sql = 'SELECT COUNT(*) FROM u_projects WHERE Id = :Id AND Projpass = :Projpass';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Projpass', $hashedProjpass, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Location Deletion Failure.', 'message' => 'Location deletion failure: the password is incorrect.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Delete the location's information
            $sql2 = 'DELETE FROM wb_locations WHERE Id_Base = :Id_Base';
            $statement2 = $pdo->prepare($sql2);
            $statement2->bindParam(':Id_Base', $_wbId, PDO::PARAM_INT);
            $results2 = $statement2->execute();
            if (!$results2) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Location Deletion Failure.', 'message' => 'Location deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);            }
            else {
                //Delete the wb base's information
                $sql3 = 'DELETE FROM wb_bases WHERE Id = :Id';
                $statement3 = $pdo->prepare($sql3);
                $statement3->bindParam(':Id', $_wbId, PDO::PARAM_INT);
                $results3 = $statement3->execute();
                if (!$results3) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Location Deletion Failure.', 'message' => 'Location deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(200);
                    $json_response = array('messageTitle' => 'Location Deletion Success.', 'message' => 'Location deletion successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //WB -> View Locations
    function wb_view_locations($pdo, $_projectId) {
        //Select the location's information
        $sql = 'SELECT wb_bases.Id AS Id, wb_bases.Title AS Title, wb_bases.Summary AS Summary
        FROM u_projects
        INNER JOIN wb_bases ON u_projects.Id = wb_bases.Id_Project
        INNER JOIN wb_locations ON wb_bases.Id = wb_locations.Id_Base
        WHERE u_projects.Id = :Id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_projectId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['id' => $result['Id'], 'title' => $result['Title'], 'summary' => $result['Summary']]);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Locations Viewing Success.', 'message' => 'Locations viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);    
    }
    //WB -> View Location
    function wb_view_location($pdo, $_wbId) {
        //Select the location's information
        $sql = 'SELECT wb_bases.Id AS Id, wb_bases.Concept AS Concept, wb_bases.ReasonOfConcept AS ReasonOfConcept, wb_bases.Title AS Title, wb_bases.Summary AS Summary, wb_bases.Description AS Description, Cause, Purpose, Myth
        FROM wb_bases
        INNER JOIN wb_locations ON wb_bases.Id = wb_locations.Id_Base
        WHERE wb_bases.Id = :Id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_wbId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
			$toReturn = array('id' => $result['Id'], 'concept' => $result['Concept'], 'reasonofconcept' => $result['ReasonOfConcept'], 'title' => $result['Title'], 'summary' => $result['Summary'], 'description' => $result['Description'], 'cause' => $result['Cause'], 'purpose' => $result['Purpose'], 'myth' => $result['Myth']);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Location Viewing Success.', 'message' => 'Location viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);    
    }
}
?>