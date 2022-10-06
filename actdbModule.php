<?php
class actDatabaseModule {
    //S -> Create Act
    function s_create_act($pdo, $_projectId, $_arcId, $_Actname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        //Check if arc is within the project
        if (!$this->isArcWithinProject($pdo, $_projectId, $_arcId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Act Creation Failure.', 'message' => 'Act creation failure: target arc does not exist in target project.', 'content' => array());
			echo json_encode($json_response);
        }
        else {
            //Check if user hasn't reached the maximum number of acts
            $sql = 'SELECT COUNT(*) FROM s_acts WHERE s_acts.Id_Arc = :Id_Arc';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Arc', $_arcId, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            $count = $results['COUNT(*)'];
            if ($count > 99) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Act Creation Failure.', 'message' => 'Act creation failure: user has already reached the maximum number of acts per arcs.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Create the arc
                $sql2 = 'INSERT INTO s_acts (Id_Arc, Title, Summary, Description, Realtime, Screentime) VALUES (:Id_Arc, :Title, :Summary, :Description, :Realtime, :Screentime)';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_Arc', $_arcId, PDO::PARAM_INT);
                $statement2->bindParam(':Title', $_Actname, PDO::PARAM_STR);
                $statement2->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
                $statement2->bindParam(':Description', $_Description, PDO::PARAM_STR);
                $statement2->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
                $statement2->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Act Creation Failure.', 'message' => 'Act creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'Act Creation Success.', 'message' => 'Act creation successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //S -> Update Act
    function s_update_act($pdo, $_projectId, $actId, $_Actname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        //Check if act is within the project
        if (!$this->isActWithinProject($pdo, $_projectId, $actId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Act Update Failure.', 'message' => 'Act update failure: target act does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the act's information
            $sql = 'UPDATE s_acts SET Title = :Title, Summary = :Summary, Description = :Description, Realtime = :Realtime, Screentime = :Screentime WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Title', $_Actname, PDO::PARAM_STR);
            $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
            $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
            $statement->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
            $statement->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
            $statement->bindParam(':Id', $actId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Act Update Failure.', 'message' => 'Act update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Act Update Success.', 'message' => 'Act update successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
	//S -> Update Act Time
    function s_update_acttime($pdo, $_projectId, $_actId, $_Realtime, $_Screentime) {
        //Check if act is within the project
        if (!$this->isActWithinProject($pdo, $_projectId, $_actId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Act Update Failure.', 'message' => 'Act update failure: target act does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the act's information
            $sql = 'UPDATE s_acts SET Realtime = :Realtime, Screentime = :Screentime WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Realtime', $_Realtime, PDO::PARAM_INT);
            $statement->bindParam(':Screentime', $_Screentime, PDO::PARAM_INT);
            $statement->bindParam(':Id', $_actId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Act Time Update Failure.', 'message' => 'Act time update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Act Time Update Success.', 'message' => 'Act time update successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //S -> Delete Act
    function s_delete_act($pdo, $_projectId, $_actId, $_Projpass) {
        //Check if act is within the project
        if (!$this->isActWithinProject($pdo, $_projectId, $_actId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Act Deletion Failure.', 'message' => 'Act deletion failure: target act does not exist in target project.', 'content' => array());
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
                $json_response = array('messageTitle' => 'Act Deletion Failure.', 'message' => 'Act deletion failure: the password is incorrect.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Select the action's information
                $sql = 'SELECT s_actions.Id as Id FROM s_actions WHERE s_actions.Id_Act = :Id_Act';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':Id_Act', $_actId, PDO::PARAM_INT);
                $statement->execute();
                while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                    $actionId = $result['Id'];
                    //Delete the wb's information
                    $sql2 = 'DELETE FROM r_actions_wbs WHERE Id_Action = :Id_Action';
                    $statement2 = $pdo->prepare($sql2);
                    $statement2->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
                    $results2 = $statement2->execute();
                    if (!$results2) {
                        http_response_code(400);
                        $json_response = array('messageTitle' => 'Act Deletion Failure.', 'message' => 'Act deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                        echo json_encode($json_response);
                    }
                    else {
                        //Delete the arguments's information
                        $sql3 = 'DELETE FROM r_actions_arguments WHERE Id_Action = :Id_Action';
                        $statement3 = $pdo->prepare($sql3);
                        $statement3->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
                        $results3 = $statement3->execute();
                        if (!$results3) {
                            http_response_code(400);
                            $json_response = array('messageTitle' => 'Act Deletion Failure.', 'message' => 'Act deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                            echo json_encode($json_response);
                        }
                        else {
                            //Delete the bookmarks's information
                            $sql4 = 'DELETE FROM r_actions_bookmarks WHERE Id_Action = :Id_Action';
                            $statement4 = $pdo->prepare($sql4);
                            $statement4->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
                            $results4 = $statement4->execute();
                            if (!$results4) {
                                http_response_code(400);
                                $json_response = array('messageTitle' => 'Act Deletion Failure.', 'message' => 'Act deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                                echo json_encode($json_response);
                            }
                        }
                    }
                    //Delete the action's information
                    $sql2 = 'DELETE FROM s_actions WHERE Id = :Id';
                    $statement2 = $pdo->prepare($sql2);
                    $statement2->bindParam(':Id', $actionId, PDO::PARAM_INT);
                    $results2 = $statement2->execute();
                    if (!$results2) {
                        http_response_code(400);
                        $json_response = array('messageTitle' => 'Act Deletion Failure.', 'message' => 'Act deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                        echo json_encode($json_response);
                    }
                }
                //Delete the act's information
                $sql2 = 'DELETE FROM s_acts WHERE Id = :Id';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id', $_actId, PDO::PARAM_INT);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Act Deletion Failure.', 'message' => 'Act deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(200);
                    $json_response = array('messageTitle' => 'Act Deletion Success.', 'message' => 'Act deletion successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //S -> View Acts
    function s_view_acts($pdo, $_projectId, $_arcId) {
        //Check if arc is within the project
        if (!$this->isArcWithinProject($pdo, $_projectId, $_arcId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Act Viewing Failure.', 'message' => 'Act viewing failure: target arc does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Select the act's information
            $sql = 'SELECT Id, Title, Summary, Realtime, Screentime FROM s_acts WHERE s_acts.Id_Arc = :Id_Arc';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Arc', $_arcId, PDO::PARAM_INT);
            $statement->execute();
            $toReturn = array();
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                array_push($toReturn, ['actId' => $result['Id'], 'Actname' => $result['Title'], 'Summary' => $result['Summary'], 'Realtime' => $result['Realtime'], 'Screentime' => $result['Screentime']]);
            }
            http_response_code(200);
            $json_response = array('messageTitle' => 'Act Viewing Success.', 'message' => 'Act viewing successful.', 'content' => $toReturn);
            echo json_encode($json_response);
        }
    }
    //S -> View Act
    function s_view_act($pdo, $_projectId, $_actId) {
        //Check if act is within the project
        if (!$this->isActWithinProject($pdo, $_projectId, $_actId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Act Viewing Failure.', 'message' => 'Act viewing failure: target act does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Select the act's information
            $sql = 'SELECT Id, Title, Summary, Description, Realtime, Screentime FROM s_acts WHERE s_acts.Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id', $_actId, PDO::PARAM_INT);
            $statement->execute();
            $toReturn = array();
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                $toReturn = array('actId' => $result['Id'], 'Actname' => $result['Title'], 'Summary' => $result['Summary'], 'Description' => $result['Description'], 'Realtime' => $result['Realtime'], 'Screentime' => $result['Screentime']);
            }
            http_response_code(200);
            $json_response = array('messageTitle' => 'Act Viewing Success.', 'message' => 'Act viewing successful.', 'content' => $toReturn);
            echo json_encode($json_response);
        }
    }
    //S -> Get Act Id By Name
    function s_getactid_byname($pdo, $_projectId, $_Actname) {
        $sql = 'SELECT s_acts.Id AS Id FROM s_acts 
        INNER JOIN s_arcs ON s_acts.Id_Arc = s_arcs.Id 
        INNER JOIN s_timelines ON s_arcs.Id_Timeline = s_timelines.Id 
        WHERE s_timelines.Id_Project = :Id_Project AND s_acts.Title = :Title LIMIT 1';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Title', $_Actname, PDO::PARAM_STR);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $toReturn = array('actId' => $result['Id']);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Act Get Id By Name Success.', 'message' => 'Act get id by name successful.', 'content' => $toReturn);
        echo json_encode($json_response);
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
}
?>