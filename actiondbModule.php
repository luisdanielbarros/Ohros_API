<?php
class actionDatabaseModule {
    //S -> Create Action
    function s_create_action($pdo, $_projectId, $_actId, $_Actionname, $_Summary, $_Description, $_Realtime, $_Screentime, $_WBsIds, $_bookmarksIds, $_argumentsIds) {
        //Check if act is within the project
        if (!$this->isActWithinProject($pdo, $_projectId, $_actId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Action Creation Failure.', 'message' => 'Action creation failure: target act does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Check if user hasn't reached the maximum number of actions
            $sql = 'SELECT COUNT(*) FROM s_actions WHERE s_actions.Id_Act = :Id_Act';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Act', $_actId, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            $count = $results['COUNT(*)'];
            if ($count > 99) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Action Creation Failure.', 'message' => 'Action creation failure: user has already reached the maximum number of actions per acts.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Create the action
                $sql2 = 'INSERT INTO s_actions (Id_Act, Title, Summary, Description, Realtime, Screentime) VALUES (:Id_Act, :Title, :Summary, :Description, :Realtime, :Screentime)';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_Act', $_actId, PDO::PARAM_INT);
                $statement2->bindParam(':Title', $_Actionname, PDO::PARAM_STR);
                $statement2->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
                $statement2->bindParam(':Description', $_Description, PDO::PARAM_STR);
                $statement2->bindParam(':Title', $_Actionname, PDO::PARAM_STR);
                $statement2->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
                $statement2->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Action Creation Failure.', 'message' => 'Action creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'Action Creation Success.', 'message' => 'Action creation successful.', 'content' => array());
                    echo json_encode($json_response);
                }
				//Select the action's Id
				$actionId = $pdo->lastInsertId();
				//Create the action-wbs relations
				var_dump('Create the action-wbs relations');
				if ($_WBsIds != '') {
					$Ids = explode(',', $_WBsIds);
					for ($i = 0; $i < count($Ids); $i++) {
						$Id = $Ids[$i];
						//Create the action-wb relation
						$sql3 = 'INSERT INTO r_actions_wbs (Id_Action, Id_WB) VALUES (:Id_Action, :Id_WB)';
						$statement3 = $pdo->prepare($sql3);
						$statement3->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
						$statement3->bindParam(':Id_WB', $Id, PDO::PARAM_INT);
						$results3 = $statement3->execute();
						if (!$results3) {
							http_response_code(400);
							$json_response = array('messageTitle' => 'Action Creation Failure.', 'message' => 'Action creation failure: the system is currently unavailable, please try again later.', 'content' => array());
							echo json_encode($json_response);
						}
					}
				}
				//Create the action-bookmarks relations
				if ($_bookmarksIds != '') {
					$Ids = explode(',', $_bookmarksIds);
					for ($i = 0; $i < count($Ids); $i++) {
						$Id = $Ids[$i];
						//Create the action-bookmark relation
						$sql3 = 'INSERT INTO r_actions_bookmarks (Id_Action, Id_Bookmark) VALUES (:Id_Action, :Id_Bookmark)';
						$statement3 = $pdo->prepare($sql3);
						$statement3->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
						$statement3->bindParam(':Id_Bookmark', $Id, PDO::PARAM_INT);
						$results3 = $statement3->execute();
						if (!$results3) {
							http_response_code(400);
							$json_response = array('messageTitle' => 'Action Creation Failure.', 'message' => 'Action creation failure: the system is currently unavailable, please try again later.', 'content' => array());
							echo json_encode($json_response);
						}
					}
				}
				//Create the action-arguments relations
				if ($_argumentsIds != '') {
				}
            }
        }    
    }
    //S -> Update Action
    function s_update_action($pdo, $_projectId, $_actionId, $_Actname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Action Update Failure.', 'message' => 'Action update failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the action's information
            $sql = 'UPDATE s_actions SET Title = :Title, Summary = :Summary, Description = :Description, Realtime = :Realtime, Screentime = :Screentime WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Title', $_Actname, PDO::PARAM_STR);
            $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
            $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
            $statement->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
            $statement->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
            $statement->bindParam(':Id', $_actionId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Action Update Failure.', 'message' => 'Action update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Action Update Success.', 'message' => 'Action update successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
	//S -> Update Action Time
    function s_update_actiontime($pdo, $_projectId, $_actionId, $_Realtime, $_Screentime) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Action Update Failure.', 'message' => 'Action update failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the action's information
            $sql = 'UPDATE s_actions SET Realtime = :Realtime, Screentime = :Screentime WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
            $statement->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
            $statement->bindParam(':Id', $_actionId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Action Time Update Failure.', 'message' => 'Action time update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Action Time Update Success.', 'message' => 'Action time update successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //S -> Delete Action
    function s_delete_action($pdo, $_projectId, $_actionId, $_Projpass) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Action Deletion Failure.', 'message' => 'Action deletion failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
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
                $json_response = array('messageTitle' => 'Action Deletion Failure.', 'message' => 'Action deletion failure: the password is incorrect.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Delete the wbs's information
				$sql2 = 'DELETE FROM r_actions_wbs WHERE Id_Action = :Id_Action';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Action Deletion Failure.', 'message' => 'Action deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    //Delete the arguments's information
                    $sql3 = 'DELETE FROM r_actions_arguments WHERE Id_Action = :Id_Action';
                    $statement3 = $pdo->prepare($sql3);
                    $statement3->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
                    $results3 = $statement3->execute();
                    if (!$results3) {
                        http_response_code(400);
                        $json_response = array('messageTitle' => 'Action Deletion Failure.', 'message' => 'Action deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                        echo json_encode($json_response);
                    }
                    else {
                        //Delete the bookmarks's information
                        $sql4 = 'DELETE FROM r_actions_bookmarks WHERE Id_Action = :Id_Action';
                        $statement4 = $pdo->prepare($sql4);
                        $statement4->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
                        $results4 = $statement4->execute();
                        if (!$results4) {
                            http_response_code(400);
                            $json_response = array('messageTitle' => 'Action Deletion Failure.', 'message' => 'Action deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                            echo json_encode($json_response);
                        }
                    }
                }
            }
            //Delete the action's information
            $sql = 'DELETE FROM s_actions WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id', $_actionId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Action Deletion Failure.', 'message' => 'Action deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Action Deletion Success.', 'message' => 'Action deletion successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //S -> View Actions
    function s_view_actions($pdo, $_projectId, $_actId) {
        //Check if act is within the project
        if (!$this->isActWithinProject($pdo, $_projectId, $_actId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Action Viewing Failure.', 'message' => 'Action viewing failure: target act does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Select the action's information
            $sql = 'SELECT Id, Title, Summary, Realtime, Screentime FROM s_actions WHERE s_actions.Id_Act = :Id_Act';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Act', $_actId, PDO::PARAM_INT);
            $statement->execute();
            $toReturn = array();
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                array_push($toReturn, ['actionId' => $result['Id'], 'Actionname' => $result['Title'], 'Summary' => $result['Summary'], 'Realtime' => $result['Realtime'], 'Screentime' => $result['Screentime']]);
            }
            http_response_code(200);
            $json_response = array('messageTitle' => 'Action Viewing Success.', 'message' => 'Action viewing successful.', 'content' => $toReturn);
            echo json_encode($json_response);
        }
    }
    //S -> View Action
    function s_view_action($pdo, $_projectId, $_actionId) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Action Viewing Failure.', 'message' => 'Action viewing failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
			//Select the related bookmarks's information
            $sql = 'SELECT Title FROM r_actions_bookmarks 
			INNER JOIN b_bookmarks ON r_actions_bookmarks.Id_Bookmark = b_bookmarks.Id 
			WHERE r_actions_bookmarks.Id_Action = :Id_Action
			ORDER BY Title ASC';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $Bookmarks = '';
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
				if ($Bookmarks != '') $Bookmarks .= ',,';
                $Bookmarks .= $result['Title'];
            }
			//Select the related wb's information
            $sql = 'SELECT Title FROM r_actions_wbs 
			INNER JOIN wb_bases ON r_actions_wbs.Id_WB = wb_bases.Id 
			WHERE r_actions_wbs.Id_Action = :Id_Action
			ORDER BY Title ASC';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $WBs = '';
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
				if ($WBs != '') $WBs .= ',,';
                $WBs .= $result['Title'];
            }
			//Select the related arguments's information
            $sql = 'SELECT Title FROM r_actions_arguments 
			INNER JOIN a_arguments ON r_actions_arguments.Id_Argument = a_arguments.Id 
			WHERE r_actions_arguments.Id_Action = :Id_Action
			ORDER BY Title ASC';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $Arguments = '';
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
				if ($Arguments != '') $Arguments .= ',,';
                $Arguments .= $result['Title'];
            }
            //Select the action's information
            $sql = 'SELECT Id, Title, Summary, Description, Realtime, Screentime FROM s_actions WHERE s_actions.Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $toReturn = array();
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                $toReturn = array('actionId' => $result['Id'], 'Actionname' => $result['Title'], 'Summary' => $result['Summary'], 'Description' => $result['Description'], 'Realtime' => $result['Realtime'], 'Screentime' => $result['Screentime'], 'Bookmarks' => $Bookmarks, 'WBs' => $WBs, 'Arguments' => $Arguments);
            }
            http_response_code(200);
            $json_response = array('messageTitle' => 'Action Viewing Success.', 'message' => 'Action viewing successful.', 'content' => $toReturn);
            echo json_encode($json_response);
        }
    }
    //S -> Get Action Id By Name
    function s_getactionid_byname($pdo, $_projectId, $_Actionname) {
            //Select the action's information
            $sql = 'SELECT s_actions.Id AS Id FROM s_actions 
            INNER JOIN s_acts ON s_actions.Id_Act = s_acts.Id 
            INNER JOIN s_arcs ON s_acts.Id_Arc = s_arcs.Id 
            INNER JOIN s_timelines ON s_arcs.Id_Timeline = s_timelines.Id 
            WHERE s_timelines.Id_Project = :Id_Project AND s_actions.Title = :Title LIMIT 1';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
            $statement->bindParam(':Title', $_Actionname, PDO::PARAM_STR);
            $statement->execute();
            $toReturn = array();
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                $toReturn = array('actionId' => $result['Id']);
            }
            http_response_code(200);
            $json_response = array('messageTitle' => 'Action Get Id By Name Success.', 'message' => 'Action get id by name successful.', 'content' => $toReturn);
            echo json_encode($json_response);
    }
    //S -> Is Act Within Project
    private function isActWithinProject($pdo, $_projectId, $_actId) {
        //Check if act is within the project
        $sql = 'SELECT COUNT(*) FROM s_acts 
        INNER JOIN s_arcs ON s_acts.Id_Arc = s_arcs.Id
        INNER JOIN s_timelines ON s_arcs.Id_Timeline = s_timelines.Id
        WHERE s_timelines.Id_Project = :Id_Project AND s_acts.Id = :Id_Act';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Act', $_actId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) return false;
        else return true;
    }
    //S -> Is Action Within Project
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