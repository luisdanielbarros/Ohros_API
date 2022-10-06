<?php
class projectDatabaseModule {
    //U -> Create Project
    function u_create_project($pdo, $_userId, $_Projname, $_Summary, $_Description, $_Projpass) {
        //Check if user hasn't reached the maximum number of projects
        $sql = 'SELECT COUNT(*) FROM u_projects
        INNER JOIN u_projwhitelists ON u_projects.Id = u_projwhitelists.Id_Project
        WHERE u_projwhitelists.Id_User = :Id_User';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_User', $_userId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count > 2) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Project Creation Failure.', 'message' => 'Project creation failure: user has already reached the maximum number of projects per account.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Create the project
			$hashedProjpass = substr(md5($_Projpass), 0, 127);
            $sql = 'INSERT INTO u_projects (Projname, Summary, Description, Projpass) VALUES (:Projname, :Summary, :Description, :Projpass)';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Projname', $_Projname, PDO::PARAM_STR);
            $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
            $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
            $statement->bindParam(':Projpass', $hashedProjpass, PDO::PARAM_STR);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Project Creation Failure.', 'message' => 'Project creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Create the project's permissions
                $projectId = (int)$pdo->lastInsertId();
                $userProjectRole = (int)1;
                $sql = 'INSERT INTO u_projwhitelists (Id_User, Id_Project, Id_ProjectRole) VALUES (:Id_User, :Id_Project, :Id_ProjectRole)';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':Id_User', $_userId, PDO::PARAM_INT);
                $statement->bindParam(':Id_Project', $projectId, PDO::PARAM_INT);
                $statement->bindParam(':Id_ProjectRole', $userProjectRole, PDO::PARAM_INT);
                $results = $statement->execute();
                if (!$results) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Project Creation Failure.', 'message' => 'Project creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'Project Creation Success.', 'message' => 'Project creation successful.', 'content' => ['projectId' => $projectId]);
                    echo json_encode($json_response);
                }
            }
        }        
    }
    //U -> Update Project
    function u_update_project($pdo, $_projectId, $_Projpass, $_Projname, $_Summary, $_Description, $_NewProjpass) {
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
            $json_response = array('messageTitle' => 'Project Update Failure.', 'message' => 'Project update failure: the password is incorrect.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the project's information
			$hashedNewProjpass = substr(md5($_NewProjpass), 0, 127);
            $sql = 'UPDATE u_projects SET Projname = :Projname, Summary = :Summary, Description = :Description, Projpass = :Projpass WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Projname', $_Projname, PDO::PARAM_STR);
            $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
            $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
            $statement->bindParam(':Projpass', $hashedNewProjpass, PDO::PARAM_STR);
            $statement->bindParam(':Id', $_projectId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Project Update Failure.', 'message' => 'Project update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Project Update Success.', 'message' => 'Project update successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //U -> Delete Project
    function u_delete_project($pdo, $_projectId, $_Projpass) {
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
            $json_response = array('messageTitle' => 'Project Deletion Failure.', 'message' => 'Project deletion failure: the password is incorrect.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Delete the project's role matrix
            $sql = 'DELETE FROM u_projwhitelists WHERE Id_Project = :Id_Project';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Project Deletion Failure.', 'message' => 'Project deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Select the timeline's information
                $sql = 'SELECT s_timelines.Id as Id FROM s_timelines WHERE s_timelines.Id_Project = :Id_Project';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
                $statement->execute();
                while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                    $timelineId = $result['Id'];
                    //Select the arc's information
                    $sql2 = 'SELECT s_arcs.Id as Id FROM s_arcs WHERE s_arcs.Id_Timeline = :Id_Timeline';
                    $statement2 = $pdo->prepare($sql2);
                    $statement2->bindParam(':Id_Timeline', $timelineId, PDO::PARAM_INT);
                    $statement2->execute();
                    while (($result2 = $statement2->fetch(PDO::FETCH_ASSOC)) !== false) {
                        $arcId = $result2['Id'];
                        //Select the act's information
                        $sql3 = 'SELECT s_acts.Id as Id FROM s_acts WHERE s_acts.Id_Arc = :Id_Arc';
                        $statement3 = $pdo->prepare($sql3);
                        $statement3->bindParam(':Id_Arc', $arcId, PDO::PARAM_INT);
                        $statement3->execute();
                        while (($result3 = $statement3->fetch(PDO::FETCH_ASSOC)) !== false) {
                            $actId = $result3['Id'];
                            //Delete the action's information
                            $sql4 = 'DELETE FROM s_actions WHERE Id_Act = :Id_Act';
                            $statement4 = $pdo->prepare($sql4);
                            $statement4->bindParam(':Id_Act', $actId, PDO::PARAM_INT);
                            $results4 = $statement4->execute();
                            if (!$results4) {
                                http_response_code(400);
                                $json_response = array('messageTitle' => 'Project Deletion Failure.', 'message' => 'Project deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                                echo json_encode($json_response);                                
                                exit;
                            }
                        }
                        //Delete the act's information
                        $sql3 = 'DELETE FROM s_acts WHERE Id_Arc = :Id_Arc';
                        $statement3 = $pdo->prepare($sql3);
                        $statement3->bindParam(':Id_Arc', $arcId, PDO::PARAM_INT);
                        $results3 = $statement3->execute();
                        if (!$results3) {
                            http_response_code(400);
                            $json_response = array('messageTitle' => 'Project Deletion Failure.', 'message' => 'Project deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                            echo json_encode($json_response);
                            exit;
                        }
                    }
                    //Delete the arc's information
                    $sql2 = 'DELETE FROM s_arcs WHERE Id_Timeline = :Id_Timeline';
                    $statement2 = $pdo->prepare($sql2);
                    $statement2->bindParam(':Id_Timeline', $timelineId, PDO::PARAM_INT);
                    $results2 = $statement2->execute();
                    if (!$results2) {
                        http_response_code(400);
                        $json_response = array('messageTitle' => 'Project Deletion Failure.', 'message' => 'Project deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                        echo json_encode($json_response);
                        exit;
                    }
                }
                //Delete the timeline's information
                $sql = 'DELETE FROM s_timelines WHERE Id_Project = :Id_Project';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
                $results = $statement->execute();
                if (!$results) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Project Deletion Failure.', 'message' => 'Project deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
					//Update the project token's information
					$sql2 = 'UPDATE u_tokens SET Id_Project = NULL WHERE Id_Project = :Id_Project';
					$statement2 = $pdo->prepare($sql2);
					$statement2->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
					$results2 = $statement2->execute();
					if (!$results2) {
						http_response_code(400);
						$json_response = array('messageTitle' => 'Project Deletion Failure.', 'message' => 'Project deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
						echo json_encode($json_response);
					}
					else {
						//Delete the project's information
						$sql3 = 'DELETE FROM u_projects WHERE Id = :Id';
						$statement3 = $pdo->prepare($sql3);
						$statement3->bindParam(':Id', $_projectId, PDO::PARAM_INT);
						$results3 = $statement3->execute();
						if (!$results3) {
							http_response_code(400);
							$json_response = array('messageTitle' => 'Project Deletion Failure.', 'message' => 'Project deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
							echo json_encode($json_response);
						}
						else {
							http_response_code(200);
							$json_response = array('messageTitle' => 'Project Deletion Success.', 'message' => 'Project deletion successful.', 'content' => array());
							echo json_encode($json_response);
						}
					}
                }
            }
        }
    }
    //U -> View Projects
    function u_view_projects($pdo, $_userId) {
        //Select the project's information
        $sql = 'SELECT u_projects.Id as Id, Projname, Summary FROM u_projects
        INNER JOIN u_projwhitelists ON u_projwhitelists.Id_Project = u_projects.Id
        WHERE u_projwhitelists.Id_User = :Id_User';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_User', $_userId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['projectId' => $result['Id'], 'Projname' => $result['Projname'], 'Summary' => $result['Summary']]);
        }
        http_response_code(200);
		$json_response = array('messageTitle' => 'Project Viewing Success.', 'message' => 'Project viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
    //U -> View Project
    function u_view_project($pdo, $_userId, $_projectId) {
        //Select the project's information
        $sql = 'SELECT u_projects.Id as Id, Projname, Summary, Description FROM u_projects
        INNER JOIN u_projwhitelists ON u_projwhitelists.Id_Project = u_projects.Id
        WHERE u_projects.Id = :Id AND u_projwhitelists.Id_User = :Id_User';
        $statement = $pdo->prepare($sql);
		$statement->bindParam(':Id', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_User', $_userId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $toReturn = array('projectId' => $result['Id'], 'Projname' => $result['Projname'], 'Summary' => $result['Summary'], 'Description' => $result['Description']);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Project Viewing Success.', 'message' => 'Project viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
}
?>