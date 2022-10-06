<?php
class arcDatabaseModule {
    //S -> Create Arc
    function s_create_arc($pdo, $_projectId, $_timelineId, $_Arcname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        //Check if timeline is within the project
        if (!$this->isTimelineWithinProject($pdo, $_projectId, $_timelineId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Arc Creation Failure.', 'message' => 'Arc creation failure: target timeline does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Check if user hasn't reached the maximum number of arcs
            $sql = 'SELECT COUNT(*) FROM s_arcs WHERE s_arcs.Id_Timeline = :Id_Timeline';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Timeline', $_timelineId, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            $count = $results['COUNT(*)'];
            if ($count > 99) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Arc Creation Failure.', 'message' => 'Arc creation failure: user has already reached the maximum number of arcs per timeline.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Create the arc
                $sql2 = 'INSERT INTO s_arcs (Id_Timeline, Title, Summary, Description, Realtime, Screentime) VALUES (:Id_Timeline, :Title, :Summary, :Description, :Realtime, :Screentime)';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_Timeline', $_timelineId, PDO::PARAM_INT);
                $statement2->bindParam(':Title', $_Arcname, PDO::PARAM_STR);
                $statement2->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
                $statement2->bindParam(':Description', $_Description, PDO::PARAM_STR);
                $statement2->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
                $statement2->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Arc Creation Failure.', 'message' => 'Arc creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'Arc Creation Success.', 'message' => 'Arc creation successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }        
    }
    //S -> Update Arc
    function s_update_arc($pdo, $_projectId, $_arcId, $_Arcname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        //Check if arc is within the project
        if (!$this->isArcWithinProject($pdo, $_projectId, $_arcId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Arc Update Failure.', 'message' => 'Arc update failure: target arc does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the arc's information
            $sql = 'UPDATE s_arcs SET s_arcs.Title = :Title, Summary = :Summary, Description = :Description, Realtime = :Realtime, Screentime = :Screentime WHERE s_arcs.Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Title', $_Arcname, PDO::PARAM_STR);
            $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
            $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
            $statement->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
            $statement->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
            $statement->bindParam(':Id', $_arcId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Arc Update Failure.', 'message' => 'Arc update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Arc Update Success.', 'message' => 'Arc update successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
	//S -> Update Arc Time
    function s_update_arctime($pdo, $_projectId, $_arcId, $_Realtime, $_Screentime) {
        //Check if arc is within the project
        if (!$this->isArcWithinProject($pdo, $_projectId, $_arcId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Arc Update Failure.', 'message' => 'Arc update failure: target arc does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the arc's information
            $sql = 'UPDATE s_arcs SET Realtime = :Realtime, Screentime = :Screentime WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
            $statement->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
            $statement->bindParam(':Id', $_arcId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Arc Time Update Failure.', 'message' => 'Arc time update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Arc Time Update Success.', 'message' => 'Arc time update successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //S -> Delete Arc
    function s_delete_arc($pdo, $_projectId, $_arcId, $_Projpass) {
        //Check if arc is within the project
        if (!$this->isArcWithinProject($pdo, $_projectId, $_arcId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Arc Deletion Failure.', 'message' => 'Arc deletion failure: target arc does not exist in target project.', 'content' => array());
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
                $json_response = array('messageTitle' => 'Arc Deletion Failure.', 'message' => 'Arc deletion failure: the password is incorrect.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Select the act's information
                $sql = 'SELECT s_acts.Id as Id FROM s_acts WHERE s_acts.Id_Arc = :Id_Arc';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':Id_Arc', $_arcId, PDO::PARAM_INT);
                $statement->execute();
                while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                    $actId = $result['Id'];
                    //Select the action's information
                    $sql2 = 'SELECT s_actions.Id as Id FROM s_actions WHERE s_actions.Id_Act = :Id_Act';
                    $statement2 = $pdo->prepare($sql2);
                    $statement2->bindParam(':Id_Act', $actId, PDO::PARAM_INT);
                    $statement2->execute();
                    while (($result2 = $statement2->fetch(PDO::FETCH_ASSOC)) !== false) {
                        $actionId = $result2['Id'];
                        //Delete the wb's information
                        $sql3 = 'DELETE FROM r_actions_wbs WHERE Id_Action = :Id_Action';
                        $statement3 = $pdo->prepare($sql3);
                        $statement3->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
                        $results3 = $statement3->execute();
                        if (!$results3) {
                            http_response_code(400);
                            $json_response = array('messageTitle' => 'Arc Deletion Failure.', 'message' => 'Arc deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                            echo json_encode($json_response);
                        }
                        else {
                            //Delete the arguments's information
                            $sql4 = 'DELETE FROM r_actions_arguments WHERE Id_Action = :Id_Action';
                            $statement4 = $pdo->prepare($sql4);
                            $statement4->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
                            $results4 = $statement4->execute();
                            if (!$results4) {
                                http_response_code(400);
                                $json_response = array('messageTitle' => 'Arc Deletion Failure.', 'message' => 'Arc deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                                echo json_encode($json_response);
                            }
                            else {
                                //Delete the bookmarks's information
                                $sql5 = 'DELETE FROM r_actions_bookmarks WHERE Id_Action = :Id_Action';
                                $statement5 = $pdo->prepare($sql5);
                                $statement5->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
                                $results5 = $statement5->execute();
                                if (!$results5) {
                                    http_response_code(400);
                                    $json_response = array('messageTitle' => 'Arc Deletion Failure.', 'message' => 'Arc deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                                    echo json_encode($json_response);
                                }
                            }
                        }
                        //Delete the action's information
                        $sql3 = 'DELETE FROM s_actions WHERE Id = :Id';
                        $statement3 = $pdo->prepare($sql3);
                        $statement3->bindParam(':Id', $actionId, PDO::PARAM_INT);
                        $results3 = $statement3->execute();
                        if (!$results3) {
                            http_response_code(400);
                            $json_response = array('messageTitle' => 'Arc Deletion Failure.', 'message' => 'Arc deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                            echo json_encode($json_response);
                        }
                    }
                    //Delete the act's information
                    $sql2 = 'DELETE FROM s_acts WHERE Id = :Id';
                    $statement2 = $pdo->prepare($sql2);
                    $statement2->bindParam(':Id', $actId, PDO::PARAM_INT);
                    $results2 = $statement2->execute();
                    if (!$results2) {
                        http_response_code(400);
                        $json_response = array('messageTitle' => 'Arc Deletion Failure.', 'message' => 'Arc deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                        echo json_encode($json_response);
                    }
                }
                //Delete the arc's information
                $sql3 = 'DELETE FROM s_arcs WHERE Id = :Id';
                $statement3 = $pdo->prepare($sql3);
                $statement3->bindParam(':Id', $_arcId, PDO::PARAM_INT);
                $results3 = $statement3->execute();
                if (!$results3) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Arc Deletion Failure.', 'message' => 'Arc deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(200);
                    $json_response = array('messageTitle' => 'Arc Deletion Success.', 'message' => 'Arc deletion successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //S -> View Arcs
    function s_view_arcs($pdo, $_projectId, $_timelineId) {
        //Check if timeline is within the project
        if (!$this->isTimelineWithinProject($pdo, $_projectId, $_timelineId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Arc Viewing Failure.', 'message' => 'Arc viewing failure: target timeline does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
        //Select the arc's information
            $sql = 'SELECT Id, Title, Summary, Realtime, Screentime FROM s_arcs WHERE s_arcs.Id_Timeline = :Id_Timeline';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Timeline', $_timelineId, PDO::PARAM_INT);
            $statement->execute();
            $toReturn = array();
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                array_push($toReturn, ['arcId' => $result['Id'], 'Arcname' => $result['Title'], 'Summary' => $result['Summary'], 'Realtime' => $result['Realtime'], 'Screentime' => $result['Screentime']]);
            }
            http_response_code(200);
            $json_response = array('messageTitle' => 'Arc Viewing Success.', 'message' => 'Arc viewing successful.', 'content' => $toReturn);
            echo json_encode($json_response);
        }
    }
    //S -> View Arc
    function s_view_arc($pdo, $_projectId, $_arcId) {
        //Check if arc is within the project
        if (!$this->isArcWithinProject($pdo, $_projectId, $_arcId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Arc Viewing Failure.', 'message' => 'Arc viewing failure: target arc does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
        //Select the arc's information
            $sql = 'SELECT Id, Title, Summary, Description, Realtime, Screentime FROM s_arcs WHERE s_arcs.Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id', $_arcId, PDO::PARAM_INT);
            $statement->execute();
            $toReturn = array();
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                $toReturn = array('arcId' => $result['Id'], 'Arcname' => $result['Title'], 'Summary' => $result['Summary'], 'Description' => $result['Description'], 'Realtime' => $result['Realtime'], 'Screentime' => $result['Screentime']);
            }
            http_response_code(200);
            $json_response = array('messageTitle' => 'Arc Viewing Success.', 'message' => 'Arc viewing successful.', 'content' => $toReturn);
            echo json_encode($json_response);
        }
    }
	//S -> Get Id By Name
    function s_getarcid_byname($pdo, $_projectId, $_Arcname) {
        //Select the arc's information
        $sql = 'SELECT s_arcs.Id AS Id FROM s_arcs 
        INNER JOIN s_timelines on s_arcs.Id_Timeline = s_timelines.Id
        WHERE s_timelines.Id_Project = :Id_Project AND s_arcs.Title = :Title LIMIT 1';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Title', $_Arcname, PDO::PARAM_STR);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $toReturn = array('arcId' => $result['Id']);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Arc Get Id By Name Success.', 'message' => 'Arc get id by name successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
    //S -> Is Timeline Within Project
    private function isTimelineWithinProject($pdo, $_projectId, $_timelineId) {
        //Check if act is within the project
        $sql = 'SELECT COUNT(*) FROM s_timelines 
        WHERE s_timelines.Id_Project = :Id_Project AND s_timelines.Id = :Id_Timeline';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Timeline', $_timelineId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) return false;
        else return true;
    }
    //S -> Is Arc Within Project
    private function isArcWithinProject($pdo, $_projectId, $_arcId) {
        //Check if act is within the project
        $sql = 'SELECT COUNT(*) FROM s_arcs 
        INNER JOIN s_timelines ON s_arcs.Id_Timeline = s_timelines.Id
        WHERE s_timelines.Id_Project = :Id_Project AND s_arcs.Id = :Id_Arc';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Arc', $_arcId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) return false;
        else return true;
    }
}
?>