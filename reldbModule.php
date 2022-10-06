<?php
class relationDatabaseModule {
    //R
    //R -> View WBs
    function r_view_wbs($pdo, $_projectId) {
        //Select the wb's information
        $sql = 'SELECT wb_bases.Id AS Id, wb_bases.Id_Type AS Type, wb_bases.Title AS Title, wb_bases.Summary AS Summary 
        FROM wb_bases
        WHERE wb_bases.Id_Project = :Id_Project
		ORDER BY wb_bases.Title ASC';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['Id' => $result['Id'], 'Type' => $result['Type'], 'Title' => $result['Title'], 'Summary' => $result['Summary']]);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'WBs Viewing Success.', 'message' => 'WBs viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
    //R -> View WBs In Action
    function r_view_wbs_inaction($pdo, $_projectId, $_actionId) {
        //Select the wb's in action information
        $sql = 'SELECT wb_bases.Id AS Id, wb_bases.Id_Type AS Type, wb_bases.Title AS Title, wb_bases.Summary AS Summary
        FROM r_actions_wbs
        INNER JOIN wb_bases ON r_actions_wbs.Id_WB = wb_bases.Id 
        WHERE wb_bases.Id_Project = :Id_Project AND r_actions_wbs.Id_Action = :Id_Action
		ORDER BY wb_bases.Title ASC';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['Id' => $result['Id'], 'Type' => $result['Type'], 'Title' => $result['Title'], 'Summary' => $result['Summary']]);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'WBs in Action Viewing Success.', 'message' => 'WBs in action viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
    //R -> Relate WB To Action
    function r_relate_wb_toaction($pdo, $_projectId, $_wbId, $_actionId) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'WB Relating (To Action) Failure.', 'message' => 'WB relating (to action) failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Check if relation already exists
            $sql = 'SELECT COUNT(*) FROM r_actions_wbs
            WHERE r_actions_wbs.Id_WB = :Id_WB AND r_actions_wbs.Id_Action = :Id_Action';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_WB', $_wbId, PDO::PARAM_INT);
            $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            $count = $results['COUNT(*)'];
            if ($count != 0) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'WB Relating (To Action) Failure.', 'message' => 'WB relating (to action) failure: target wb is already related to target action.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Create the wb-action relation
                $sql2 = 'INSERT INTO r_actions_wbs (Id_WB, Id_Action) VALUES (:Id_WB, :Id_Action)';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_WB', $_wbId, PDO::PARAM_INT);
                $statement2->bindParam(':Id_Action', $_actionId, PDO::PARAM_STR);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'WB Relating (To Action) Failure.', 'message' => 'WB relating (to action) failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'WB Relating (To Action) Success.', 'message' => 'WB relating (to action) successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //R -> Unrelate WB To Action
    function r_unrelate_wb_toaction($pdo, $_projectId, $_wbId, $_actionId) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'WB Relating (To Action) Failure.', 'message' => 'WB relating (to action) failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Check if relation exists
            $sql = 'SELECT COUNT(*) FROM r_actions_wbs 
            WHERE r_actions_wbs.Id_WB = :Id_WB AND r_actions_wbs.Id_Action = :Id_Action';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_WB', $_wbId, PDO::PARAM_INT);
            $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            $count = $results['COUNT(*)'];
            if ($count != 1) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'WB Unrelating (To Action) Failure.', 'message' => 'WB unrelating (to action) failure: target wb isn\'t related to target action.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Delete the wb-action relation
                $sql2 = 'DELETE FROM r_actions_wbs WHERE Id_WB = :Id_WB AND Id_Action = :Id_Action';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_WB', $_wbId, PDO::PARAM_INT);
                $statement2->bindParam(':Id_Action', $_actionId, PDO::PARAM_STR);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'WB Unrelating (To Action) Failure.', 'message' => 'WB unrelating (to action) failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'WB Unrelating (To Action) Success.', 'message' => 'WB unrelating (to action) successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
	//R -> Analyse WBs
    function r_analyse_wbs($pdo, $_projectId, $_wbsIds) {
		$Ids = explode(',', $_wbsIds);
        //Select the actions that contain at least one of the WBs
		$injectedString = '';
		for ($i = 0; $i < count($Ids); $i++) {
			$Id = $Ids[$i];
			if ($injectedString != '') $injectedString .= ' OR ';
			$injectedString .= ' r_actions_wbs.Id_WB = ' . $Id;
			
		}
        $sql = 'SELECT DISTINCT s_actions.Id FROM r_actions_wbs
		INNER JOIN wb_bases ON r_actions_wbs.Id_WB = wb_bases.Id
        INNER JOIN s_actions ON r_actions_wbs.Id_Action = s_actions.Id
		WHERE wb_bases.Id_Project = :Id_Project AND ('.$injectedString.')';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
			$actionId = $result['Id'];
			//Select the actions that contain all of the WBs
			$containsAllWBs = true;
			$WBsContained = 0;
			for ($i = 0; $i < count($Ids); $i++) {
				$Id = $Ids[$i];
				$sql2 = 'SELECT s_actions.Id AS Id FROM s_actions
				INNER JOIN r_actions_wbs ON s_actions.Id = r_actions_wbs.Id_Action
				WHERE r_actions_wbs.Id_Action = :Id_Action AND r_actions_wbs.Id_WB = :Id_WB';
				$statement2 = $pdo->prepare($sql2);
				$statement2->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
				$statement2->bindParam(':Id_WB', $Id, PDO::PARAM_INT);
				$statement2->execute();
				$results2 = $statement2->fetch(PDO::FETCH_ASSOC);
				if ($results2) $WBsContained = $WBsContained + 1;
			}
			if ($WBsContained != count($Ids)) $containsAllWBs = false;
			//Select the actions' information
			if ($containsAllWBs) {
				$sql2 = 'SELECT DISTINCT s_actions.Id AS Id, s_actions.Title AS Title, s_actions.Summary AS Summary,
				s_actions.Realtime AS ActionRealtime, s_actions.Screentime AS ActionScreentime, 
				s_acts.Realtime AS ActRealtime, s_acts.Screentime AS ActScreentime, 
				s_arcs.Realtime AS ArcRealtime, s_arcs.Screentime AS ArcScreentime 
				FROM r_actions_wbs
				INNER JOIN wb_bases ON r_actions_wbs.Id_WB = wb_bases.Id 
				INNER JOIN s_actions ON r_actions_wbs.Id_Action = s_actions.Id 
				INNER JOIN s_acts ON s_actions.Id_Act = s_acts.Id 
				INNER JOIN s_arcs ON s_acts.Id_Arc = s_arcs.Id 
				WHERE wb_bases.Id_Project = :Id_Project AND r_actions_wbs.Id_Action = :Id_Action
				ORDER BY s_arcs.Realtime ASC, s_acts.Realtime ASC, s_actions.Realtime ASC
				LIMIT 99';
				$statement2 = $pdo->prepare($sql2);
				$statement2->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
				$statement2->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
				$statement2->execute();
				while (($result2 = $statement2->fetch(PDO::FETCH_ASSOC)) !== false) {					
					array_push($toReturn, ['actionId' => $result2['Id'], 'Actionname' => $result2['Title'], 'Summary' => $result2['Summary'],
					'ActionRealtime' => $result2['ActionRealtime'], 'ActionScreentime' => $result2['ActionScreentime'],
					'ActRealtime' => $result2['ActRealtime'], 'ActScreentime' => $result2['ActScreentime'],
					'ArcRealtime' => $result2['ArcRealtime'], 'ArcScreentime' => $result2['ArcScreentime']]);
				}
			}
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'WB Analysis Success.', 'message' => 'WB analysis successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
    //B -> Is Action Within Project
    private function isActionWithinProject($pdo, $_projectId, $_actionId) {
        //Check if act is within the project
        $sql = 'SELECT COUNT(*) FROM s_actions 
        INNER JOIN s_acts ON s_actions.Id_Act = s_acts.Id
        INNER JOIN s_arcs ON s_acts.Id_Arc = s_arcs.Id
        INNER JOIN s_timelines ON s_arcs.Id_Timeline = s_timelines.Id
        WHERE s_timelines.Id_Project = :Id_Project AND s_actions.Id = :Id_Action';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) return false;
        else return true;
    }
}
?>