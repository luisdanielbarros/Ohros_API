<?php
class timelineDatabaseModule {
    //S -> Create Timeline
    function s_create_timeline($pdo, $projectId, $_Timename, $_Summary, $_Description) {
        //Check if project hasn't reached the maximum number of timelines
        $sql = 'SELECT COUNT(*) FROM s_timelines WHERE Id_Project = :Id_Project';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $projectId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count > 9) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Timeline Creation Failure.', 'message' => 'Timeline creation failure: user has already reached the maximum number of timelines per project.', 'content' => array());
			echo json_encode($json_response);
		}
        else {
            //Create the timeline
            $sql = 'INSERT INTO s_timelines (Id_Project, Title, Summary, Description) VALUES (:Id_Project, :Title, :Summary, :Description)';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Project', $projectId, PDO::PARAM_INT);
            $statement->bindParam(':Title', $_Timename, PDO::PARAM_STR);
            $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
            $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Timeline Creation Failure.', 'message' => 'Timeline creation failure: the system is currently unavailable, please try again later.', 'content' => array());
				echo json_encode($json_response);
			}
            else {
                http_response_code(201);
                $json_response = array('messageTitle' => 'Timeline Creation Success.', 'message' => 'Timeline creation successful.', 'content' => array());
				echo json_encode($json_response);
			}
        }        
    }
    //S -> Update Timeline
    function s_update_timeline($pdo, $_projectId, $_timelineId, $_Timename, $_Summary, $_Description) {
		//Check if timeline is within the project
		if (!$this->isTimelineWithinProject($pdo, $_projectId, $_timelineId)) {
			http_response_code(400);
			$json_response = array('messageTitle' => 'Timeline Update Failure.', 'message' => 'Timeline update failure: target timeline does not exist in target project.', 'content' => array());
			echo json_encode($json_response);
		}
		else {
			//Update the timeline's information
			$sql = 'UPDATE s_timelines SET Title = :Title, Summary = :Summary, Description = :Description WHERE Id = :Id';
			$statement = $pdo->prepare($sql);
			$statement->bindParam(':Title', $_Timename, PDO::PARAM_STR);
			$statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
			$statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
			$statement->bindParam(':Id', $_timelineId, PDO::PARAM_INT);
			$results = $statement->execute();
			if (!$results) {
				http_response_code(400);
				$json_response = array('messageTitle' => 'Timeline Update Failure.', 'message' => 'Timeline update failure: the system is currently unavailable, please try again later.', 'content' => array());
				echo json_encode($json_response);
			}
			else {
				http_response_code(200);
				$json_response = array('messageTitle' => 'Timeline Update Success.', 'message' => 'Timeline update successful.', 'content' => array());
				echo json_encode($json_response);
			}
		}
    }
    //S -> Delete Timeline
    function s_delete_timeline($pdo, $_projectId, $_timelineId, $_Projpass) {
		//Check if timeline is within the project
		if (!$this->isTimelineWithinProject($pdo, $_projectId, $_timelineId)) {
			http_response_code(400);
			$json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: target timeline does not exist in target project.', 'content' => array());
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
                $json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: the password is incorrect.', 'content' => array());
                echo json_encode($json_response);
            } else {
				//Select the arc's information
				$sql2 = 'SELECT s_arcs.Id as Id FROM s_arcs WHERE s_arcs.Id_Timeline = :Id_Timeline';
				$statement2 = $pdo->prepare($sql2);
				$statement2->bindParam(':Id_Timeline', $_timelineId, PDO::PARAM_INT);
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
						//Select the action's information
						$sql4 = 'SELECT s_actions.Id as Id FROM s_actions WHERE s_actions.Id_Act = :Id_Act';
						$statement4 = $pdo->prepare($sql4);
						$statement4->bindParam(':Id_Act', $actId, PDO::PARAM_INT);
						$statement4->execute();
						while (($result4 = $statement4->fetch(PDO::FETCH_ASSOC)) !== false) {
							$actionId = $result4['Id'];
							//Delete the wb's information
							$sql5 = 'DELETE FROM r_actions_wbs WHERE Id_Action = :Id_Action';
							$statement5 = $pdo->prepare($sql5);
							$statement5->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
							$results5 = $statement5->execute();
							if (!$results5) {
								http_response_code(400);
								$json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
								echo json_encode($json_response);
							}
							else {
								//Delete the arguments's information
								$sql6 = 'DELETE FROM r_actions_arguments WHERE Id_Action = :Id_Action';
								$statement6 = $pdo->prepare($sql6);
								$statement6->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
								$results6 = $statement6->execute();
								if (!$results6) {
									http_response_code(400);
									$json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
									echo json_encode($json_response);
								}
								else {
									//Delete the bookmarks's information
									$sql7 = 'DELETE FROM r_actions_bookmarks WHERE Id_Action = :Id_Action';
									$statement7 = $pdo->prepare($sql7);
									$statement7->bindParam(':Id_Action', $actionId, PDO::PARAM_INT);
									$results7 = $statement7->execute();
									if (!$results7) {
										http_response_code(400);
										$json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
										echo json_encode($json_response);
									}
								}
							}
							//Delete the action's information
							$sql5 = 'DELETE FROM s_actions WHERE Id = :Id';
							$statement5 = $pdo->prepare($sql5);
							$statement5->bindParam(':Id', $actionId, PDO::PARAM_INT);
							$results5 = $statement5->execute();
							if (!$results5) {
								http_response_code(400);
								$json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
								echo json_encode($json_response);
							}
						}
						//Delete the act's information
						$sql4 = 'DELETE FROM s_acts WHERE Id = :Id';
						$statement4 = $pdo->prepare($sql4);
						$statement4->bindParam(':Id', $actId, PDO::PARAM_INT);
						$results4 = $statement4->execute();
						if (!$results4) {
							http_response_code(400);
							$json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
							echo json_encode($json_response);
						}
					}
					//Delete the arc's information
					$sql3 = 'DELETE FROM s_arcs WHERE Id = :Id';
					$statement3 = $pdo->prepare($sql3);
					$statement3->bindParam(':Id', $arcId, PDO::PARAM_INT);
					$results3 = $statement3->execute();
					if (!$results3) {
						http_response_code(400);
						$json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
						echo json_encode($json_response);
					}
				}
				//Delete the timeline's information
				$sql2 = 'DELETE FROM s_timelines WHERE Id = :Id';
				$statement2 = $pdo->prepare($sql2);
				$statement2->bindParam(':Id', $_timelineId, PDO::PARAM_INT);
				$results2 = $statement2->execute();
				if (!$results2) {
					http_response_code(400);
					$json_response = array('messageTitle' => 'Timeline Deletion Failure.', 'message' => 'Timeline deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
					echo json_encode($json_response);
				}
				else {
					http_response_code(200);
					$json_response = array('messageTitle' => 'Timeline Deletion Success.', 'message' => 'Timeline deletion successful.', 'content' => array());
					echo json_encode($json_response);
				}
			}
		}
    }
    //S -> View Timelines
    function s_view_timelines($pdo, $projectId) {
        //Select the timeline's information
        $sql = 'SELECT Id, Title, Summary FROM s_timelines WHERE s_timelines.Id_Project = :Id_Project';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $projectId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['timelineId' => $result['Id'], 'Timename' => $result['Title'], 'Summary' => $result['Summary']]);
        }
		http_response_code(200);
        $json_response = array('messageTitle' => 'Timeline Viewing Success.', 'message' => 'Timeline viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
	//S -> View Timeline
	function s_view_timeline($pdo, $projectId, $_timelineId) {
		//Select the timeline's information
		$sql = 'SELECT Id, Title, Summary, Description FROM s_timelines WHERE s_timelines.Id_Project = :Id_Project AND s_timelines.Id = :Id';
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':Id_Project', $projectId, PDO::PARAM_INT);
		$statement->bindParam(':Id', $_timelineId, PDO::PARAM_INT);
		$statement->execute();
		$toReturn = array();
		while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
			$toReturn = array('timelineId' => $result['Id'], 'Timename' => $result['Title'], 'Summary' => $result['Summary'], 'Description' => $result['Description']);
		}
		http_response_code(200);
		$json_response = array('messageTitle' => 'Timeline Viewing Success.', 'message' => 'Timeline viewing successful.', 'content' => $toReturn);
		echo json_encode($json_response);
	}
	//S -> Get Timeline Id By Name
	function s_gettimelineid_byname($pdo, $_projectId, $_Timename) {
		//Select the timeline's information
		$sql = 'SELECT Id FROM s_timelines WHERE Id_Project = :Id_Project AND Title = :Title LIMIT 1';
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
		$statement->bindParam(':Title', $_Timename, PDO::PARAM_STR);
		$statement->execute();
		$toReturn = array();
		while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
			$toReturn = array('timelineId' => $result['Id']);
		}
		http_response_code(200);
		$json_response = array('messageTitle' => 'Timeline Get Id By Name Success.', 'message' => 'Timeline get id by name successful.', 'content' => $toReturn);
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
}
?>