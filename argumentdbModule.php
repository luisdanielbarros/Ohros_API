<?php
class argumentDatabaseModule {
    //A -> Create Argument
    function a_create_argument($pdo, $_Id_Evoker, $_Id_Target, $_Argumentname, $_Argument) {
        //Check if user hasn't reached the maximum number of arguments
        $sql = 'SELECT COUNT(*) FROM a_arguments WHERE a_arguments.Id_Project = :Id_Project';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_SESSION['projectId'], PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count > 99) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Argument Creation Failure.', 'message' => 'Argument creation failure: user has already reached the maximum number of arguments per project.', 'content' => array());
			echo json_encode($json_response);
	   }
        else {
            //Create the argument
            $sql = 'INSERT INTO a_arguments (Id_Project, Id_Evoker, Id_Target, Title, Argument) VALUES (:Id_Project, :Id_Evoker, :Id_Target, :Title, :Argument)';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Project', $_SESSION['ProjectId'], PDO::PARAM_INT);
            $statement->bindParam(':Id_Evoker', $_Id_Evoker, PDO::PARAM_INT);
            $statement->bindParam(':Id_Target', $_Id_Target, PDO::PARAM_INT);
            $statement->bindParam(':Title', $_Argumentname, PDO::PARAM_STR);
            $statement->bindParam(':Argument', $_Argument, PDO::PARAM_STR);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Argument Creation Failure.', 'message' => 'Argument creation failure: the system is currently unavailable, please try again later.', 'content' => array());
				echo json_encode($json_response);
			}
            else {
                $argumentId = (int)$pdo->lastInsertId();
                $_SESSION['ArgumentId'] = $argumentId;
                http_response_code(201);
                $json_response = array('messageTitle' => 'Argument Creation Success.', 'message' => 'Argument creation successful.', 'content' => array());
				echo json_encode($json_response);
			}
        }
    }
    //A -> Update Argument
    function a_update_argument($pdo, $_Id_Evoker, $_Id_Target, $_Argumentname, $_Argument) {
        //Update the argument's information
        $sql = 'UPDATE a_arguments SET Id_Evoker = :Id_Evoker, Id_Target = :Id_Target, Title = :Title, Argument = :Argument WHERE Id = :Id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Evoker', $_Id_Evoker, PDO::PARAM_INT);
        $statement->bindParam(':Id_Target', $_Id_Target, PDO::PARAM_INT);
        $statement->bindParam(':Title', $_Argumentname, PDO::PARAM_STR);
        $statement->bindParam(':Argument', $_Argument, PDO::PARAM_STR);
        $statement->bindParam(':Id', $_SESSION['ArgumentId'], PDO::PARAM_INT);
        $results = $statement->execute();
        if (!$results) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Argument Update Failure.', 'message' => 'Argument update failure: the system is currently unavailable, please try again later.', 'content' => array());
			echo json_encode($json_response);
		}
        else {
            http_response_code(200);
            $json_response = array('messageTitle' => 'Argument Update Success.', 'message' => 'Argument update successful.', 'content' => array());
			echo json_encode($json_response);
		}
    }
    //A -> Delete Argument
    function a_delete_argument($pdo, $_Projpass) {
        //Check if the password is correct
		$hashedProjpass = password_hash($_Projpass, PASSWORD_DEFAULT);
        $sql = 'SELECT COUNT(*) FROM u_projects WHERE Id = :Id AND Projpass = :Projpass';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_SESSION['ProjectId'], PDO::PARAM_INT);
        $statement->bindParam(':Projpass', $hashedProjpass, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Argument Deletion Failure.', 'message' => 'Argument deletion failure: the password is incorrect.', 'content' => array());
			echo json_encode($json_response);
		}
        else {
            //Delete the argument's link's information
            $sql2 = 'DELETE FROM a_links WHERE (Id_Argument = :Id) OR (Id_ArgumentTwo = :Id) OR (Id_Result = :Id)';
            $statement2 = $pdo->prepare($sql2);
            $statement2->bindParam(':Id', $_SESSION['ArgumentId'], PDO::PARAM_INT);
            $results2 = $statement2->execute();
            if (!$results2) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Argument Deletion Failure.', 'message' => 'Argument deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
				echo json_encode($json_response);
			}
            else {
                //Delete the actions-arguments link's information
                $sql3 = 'DELETE FROM r_actions_arguments WHERE Id_Argument = :Id';
                $statement3 = $pdo->prepare($sql3);
                $statement3->bindParam(':Id', $_SESSION['ArgumentId'], PDO::PARAM_INT);
                $results3 = $statement3->execute();
                if (!$results3) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Argument Deletion Failure.', 'message' => 'Argument deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
					echo json_encode($json_response);
				} 
                else {
                    //Delete the arguments's information
                    $sql4 = 'DELETE FROM a_arguments WHERE Id = :Id';
                    $statement4 = $pdo->prepare($sql4);
                    $statement4->bindParam(':Id', $_SESSION['ArgumentId'], PDO::PARAM_INT);
                    $results4 = $statement4->execute();
                    if (!$results4) {
                        http_response_code(400);
                        $json_response = array('messageTitle' => 'Argument Deletion Failure.', 'message' => 'Argument deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
						echo json_encode($json_response);
					}
                    else {
                        $_SESSION['ArgumentId'] = null;
                        http_response_code(200);
                        $json_response = array('messageTitle' => 'Argument Deletion Success.', 'message' => 'Argument deletion successful.', 'content' => array());
						echo json_encode($json_response);
					}
                }
            }
        }
    }
    //A -> View Arguments
    function a_view_arguments($pdo) {
        //Select the argument's information
        $sql = 'SELECT a_arguments.Id as Id, Id_Evoker AS Evoker, Id_Target AS Target, Title, Argument
        FROM u_projects
        INNER JOIN a_arguments ON u_projects.Id = a_arguments.Id_Project
        WHERE u_projects.Id = :Id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_SESSION['ProjectId'], PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['id' => $result['Id'], 'evoker' => $result['Evoker'], 'target' => $result['Target'], 'argument name' => $result['Title'], 'argument' => $result['Argument']]);
        }
		http_response_code(200);
        $json_response = array('messageTitle' => 'Argument Viewing Success.', 'message' => 'Argument viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
}
?>