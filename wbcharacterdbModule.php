<?php
class wbCharacterDatabaseModule {
    //WB
    //WB -> Create Character
    function wb_create_character($pdo, $_projectId, $_Concept, $_ReasonOfConcept, $_WBname, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth, $_JungModel, $_OCEANModel, $_Ego, $_Complexes, $_Persona, $_Anima, $_Shadow, $_Self, $_PsychicQuirks, $_PhysicQuirks) {
        //Check if user hasn't reached the maximum number of characters
        $sql = 'SELECT COUNT(*) FROM wb_bases WHERE wb_bases.Id_Project = :Id_Project AND wb_bases.Id_Type = 1';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count > 99) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Character Creator Failure.', 'message' => 'Character creation failure: user has already reached the maximum number of characters per project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Create the base
            $wbType = (int)1;
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
                $json_response = array('messageTitle' => 'Character Creator Failure.', 'message' => 'Character creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                $wbId = (int)$pdo->lastInsertId();
                //Create the base
                $sql2 = 'INSERT INTO wb_characters (Id_Base, JungModel, OCEANModel, Ego, Complexes, Persona, Anima, Shadow, Self, PsychicQuirks, PhysicQuirks) 
                                             VALUES (:Id_Base, :JungModel, :OCEANModel, :Ego, :Complexes, :Persona, :Anima, :Shadow, :Self, :PsychicQuirks, :PhysicQuirks)';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_Base', $wbId, PDO::PARAM_INT);
                $statement2->bindParam(':JungModel', $_JungModel, PDO::PARAM_STR);
                $statement2->bindParam(':OCEANModel', $_OCEANModel, PDO::PARAM_STR);
                $statement2->bindParam(':Ego', $_Ego, PDO::PARAM_STR);
                $statement2->bindParam(':Complexes', $_Complexes, PDO::PARAM_STR);
                $statement2->bindParam(':Persona', $_Persona, PDO::PARAM_STR);
                $statement2->bindParam(':Anima', $_Anima, PDO::PARAM_STR);
                $statement2->bindParam(':Shadow', $_Shadow, PDO::PARAM_STR);
                $statement2->bindParam(':Self', $_Self, PDO::PARAM_STR);
                $statement2->bindParam(':PsychicQuirks', $_PsychicQuirks, PDO::PARAM_STR);
                $statement2->bindParam(':PhysicQuirks', $_PhysicQuirks, PDO::PARAM_STR);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Character Creator Failure.', 'message' => 'Character creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'Character Creator Success.', 'message' => 'Character creation successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }        
    }
    //WB -> Update Character
    function wb_update_character($pdo, $_wbId, $_Concept, $_ReasonOfConcept, $_WBname, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth, $_JungModel, $_OCEANModel, $_Ego, $_Complexes, $_Persona, $_Anima, $_Shadow, $_Self, $_PsychicQuirks, $_PhysicQuirks) {
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
             $json_response = array('messageTitle' => 'Character Update Failure.', 'message' => 'Character update failure: the system is currently unavailable, please try again later.', 'content' => array());
             echo json_encode($json_response);
         }
         else {
            //Update the character's information
            $sql = 'UPDATE wb_characters SET JungModel = :JungModel, OCEANModel = :OCEANModel, Ego = :Ego, Complexes = :Complexes, Persona = :Persona, Anima = :Anima, Shadow = :Shadow, Self = :Self, PsychicQuirks = :PsychicQuirks, PhysicQuirks = :PhysicQuirks WHERE Id_Base = :Id_Base';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':JungModel', $_JungModel, PDO::PARAM_STR);
            $statement->bindParam(':OCEANModel', $_OCEANModel, PDO::PARAM_STR);
            $statement->bindParam(':Ego', $_Ego, PDO::PARAM_STR);
            $statement->bindParam(':Complexes', $_Complexes, PDO::PARAM_STR);
            $statement->bindParam(':Persona', $_Persona, PDO::PARAM_STR);
            $statement->bindParam(':Anima', $_Anima, PDO::PARAM_STR);
            $statement->bindParam(':Shadow', $_Shadow, PDO::PARAM_STR);
            $statement->bindParam(':Self', $_Self, PDO::PARAM_STR);
            $statement->bindParam(':PsychicQuirks', $_PsychicQuirks, PDO::PARAM_STR);
            $statement->bindParam(':PhysicQuirks', $_PhysicQuirks, PDO::PARAM_STR);
            $statement->bindParam(':Id_Base', $_wbId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Character Update Failure.', 'message' => 'Character update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Character Update Success.', 'message' => 'Character update successful.', 'content' => array());
                echo json_encode($json_response);
            }
         }
    }
    //WB -> Delete Character
    function wb_delete_character($pdo, $_Projpass, $_projectId, $_wbId) {
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
            $json_response = array('messageTitle' => 'Character Deletion Failure.', 'message' => 'Character deletion failure: the password is incorrect.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Delete the character's information
            $sql2 = 'DELETE FROM wb_characters WHERE Id_Base = :Id_Base';
            $statement2 = $pdo->prepare($sql2);
            $statement2->bindParam(':Id_Base', $_wbId, PDO::PARAM_INT);
            $results2 = $statement2->execute();
            if (!$results2) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Character Deletion Failure.', 'message' => 'Character deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Delete the wb base's information
                $sql3 = 'DELETE FROM wb_bases WHERE Id = :Id';
                $statement3 = $pdo->prepare($sql3);
                $statement3->bindParam(':Id', $_wbId, PDO::PARAM_INT);
                $results3 = $statement3->execute();
                if (!$results3) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Character Deletion Failure.', 'message' => 'Character deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);                
                }
                else {
                    http_response_code(200);
                    $json_response = array('messageTitle' => 'Character Deletion Success.', 'message' => 'Character deletion successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //WB -> View Characters
    function wb_view_characters($pdo, $_projectId) {
        //Select the character's information
        $sql = 'SELECT wb_bases.Id AS Id, wb_bases.Title AS Title, wb_bases.Summary AS Summary
        FROM u_projects
        INNER JOIN wb_bases ON u_projects.Id = wb_bases.Id_Project
        INNER JOIN wb_characters ON wb_bases.Id = wb_characters.Id_Base
        WHERE u_projects.Id = :Id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_projectId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['id' => $result['Id'], 'title' => $result['Title'], 'summary' => $result['Summary']]);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Characters Viewing Success.', 'message' => 'Characters viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
    //WB -> View Character
    function wb_view_character($pdo, $_wbId) {
        //Select the character's information
        $sql = 'SELECT wb_bases.Id AS Id, wb_bases.Concept AS Concept, wb_bases.ReasonOfConcept AS ReasonOfConcept, wb_bases.Title AS Title, wb_bases.Summary AS Summary, wb_bases.Description AS Description, Cause, Purpose, Myth, 
                JungModel, OCEANModel, Ego, Complexes, Persona, Anima, Shadow, Self, PsychicQuirks, PhysicQuirks
        FROM wb_bases
        INNER JOIN wb_characters ON wb_bases.Id = wb_characters.Id_Base
        WHERE wb_bases.Id = :Id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_wbId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $toReturn = array('id' => $result['Id'], 'concept' => $result['Concept'], 'reasonofconcept' => $result['ReasonOfConcept'], 'title' => $result['Title'], 'summary' => $result['Summary'], 'description' => $result['Description'], 'cause' => $result['Cause'], 'purpose' => $result['Purpose'], 'myth' => $result['Myth'],
            'jungmodel' => $result['JungModel'], 'oceanmodel' => $result['OCEANModel'], 'ego' => $result['Ego'], 'complexes' => $result['Complexes'], 'persona' => $result['Persona'], 'anima' => $result['Anima'], 'shadow' => $result['Shadow'], 'self' => $result['Self'], 'psychicquirks' => $result['PsychicQuirks'], 'physicquirks' => $result['PhysicQuirks']);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Character Viewing Success.', 'message' => 'Character viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
}
?>