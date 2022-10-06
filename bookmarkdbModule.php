<?php
class bookmarkDatabaseModule {
    //B -> Create Bookmark
    function b_create_bookmark($pdo, $_projectId, $_Bookmarkname, $_Summary, $_Description) {
		//Check if project hasn't reached the maximum number of bookmarks
		$sql = 'SELECT COUNT(*) FROM r_actions_bookmarks 
		INNER JOIN b_bookmarks ON r_actions_bookmarks.Id_Bookmark = b_bookmarks.Id
		WHERE b_bookmarks.Id_Project = :Id_Project';
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
		$statement->execute();
		$results = $statement->fetch(PDO::FETCH_ASSOC);
		$count = $results['COUNT(*)'];
		if ($count > 99) {
			http_response_code(400);
			$json_response = array('messageTitle' => 'Bookmark Creation Failure.', 'message' => 'Bookmark creation failure: user has already reached the maximum number of bookmarks per project.', 'content' => array());
			echo json_encode($json_response);
		}
		else {
			//Create the bookmark
			$sql2 = 'INSERT INTO b_bookmarks (Id_Project, Title, Summary, Description) VALUES (:Id_Project, :Title, :Summary, :Description)';
			$statement2 = $pdo->prepare($sql2);
			$statement2->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
			$statement2->bindParam(':Title', $_Bookmarkname, PDO::PARAM_STR);
			$statement2->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
			$statement2->bindParam(':Description', $_Description, PDO::PARAM_STR);
			$results2 = $statement2->execute();
			if (!$results2) {
				http_response_code(400);
				$json_response = array('messageTitle' => 'Bookmark Creation Failure.', 'message' => 'Bookmark creation failure: the system is currently unavailable, please try again later.', 'content' => array());
				echo json_encode($json_response);
			}
			else {
				http_response_code(201);
				$json_response = array('messageTitle' => 'Bookmark Creation Success.', 'message' => 'Bookmark creation successful.', 'content' => array());
				echo json_encode($json_response);
			}
		}
    }
    //B -> Update Bookmark
    function b_update_bookmark($pdo, $_projectId, $_bookmarkId, $_Bookmarkname, $_Summary, $_Description) {
        //Check if bookmark is within the project
        if (!$this->isBookmarkWithinProject($pdo, $_projectId, $_bookmarkId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Bookmark Update Failure.', 'message' => 'Bookmark update failure: target bookmark does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the bookmark's information
            $sql = 'UPDATE b_bookmarks SET Title = :Title, Summary = :Summary, Description = :Description WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Title', $_Bookmarkname, PDO::PARAM_STR);
            $statement->bindParam(':Summary', $_Summary, PDO::PARAM_STR);
            $statement->bindParam(':Description', $_Description, PDO::PARAM_STR);
            $statement->bindParam(':Id', $_bookmarkId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Bookmark Update Failure.', 'message' => 'Bookmark update failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Bookmark Update Success.', 'message' => 'Bookmark update successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //B -> Delete Bookmark
    function b_delete_bookmark($pdo, $_projectId, $_bookmarkId, $_Projpass) {
        //Check if bookmark is within the project
        if (!$this->isBookmarkWithinProject($pdo, $_projectId, $_bookmarkId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Bookmark Deletion Failure.', 'message' => 'Bookmark deletion failure: target bookmark does not exist in target project.', 'content' => array());
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
                $json_response = array('messageTitle' => 'Bookmark Deletion Failure.', 'message' => 'Bookmark deletion failure: the password is incorrect.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Delete the action-bookmark's information
                $sql = 'DELETE FROM r_actions_bookmarks WHERE Id_Bookmark = :Id_Bookmark';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':Id_Bookmark', $_bookmarkId, PDO::PARAM_INT);
                $results = $statement->execute();
                if (!$results) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Bookmark Deletion Failure.', 'message' => 'Bookmark deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    //Delete the bookmark's information
                    $sql2 = 'DELETE FROM b_bookmarks WHERE Id = :Id';
                    $statement2 = $pdo->prepare($sql2);
                    $statement2->bindParam(':Id', $_bookmarkId, PDO::PARAM_INT);
                    $results2 = $statement2->execute();
                    if (!$results2) {
                        http_response_code(400);
                        $json_response = array('messageTitle' => 'Bookmark Deletion Failure.', 'message' => 'Bookmark deletion failure: the system is currently unavailable, please try again later.', 'content' => array());
                        echo json_encode($json_response);
                    }
                    else {
                        http_response_code(200);
                        $json_response = array('messageTitle' => 'Bookmark Deletion Success.', 'message' => 'Bookmark deletion successful.', 'content' => array());
                        echo json_encode($json_response);
                    }
                }
            }
        }
    }
    //B -> View Bookmarks
    function b_view_bookmarks($pdo, $_projectId) {
        //Select the bookmarks's information
        $sql = 'SELECT Id, Title, Summary FROM b_bookmarks WHERE b_bookmarks.Id_Project = :Id_Project';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['bookmarkId' => $result['Id'], 'Bookmarkname' => $result['Title'], 'Summary' => $result['Summary']]);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Bookmark Viewing Success.', 'message' => 'Bookmark viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
	//B -> View Bookmark
	function b_view_bookmark($pdo, $_projectId, $_bookmarkId) {
		//Select the bookmarks's information
		$sql = 'SELECT Id, Title, Summary, Description FROM b_bookmarks WHERE b_bookmarks.Id_Project = :Id_Project AND b_bookmarks.Id = :Id';
		$statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id', $_bookmarkId, PDO::PARAM_INT);
		$statement->execute();
		$toReturn = array();
		while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $toReturn = array('bookmarkId' => $result['Id'], 'Bookmarkname' => $result['Title'], 'Summary' => $result['Summary'], 'Description' => $result['Description']);
		}
		http_response_code(200);
		$json_response = array('messageTitle' => 'Bookmark Viewing Success.', 'message' => 'Bookmark viewing successful.', 'content' => $toReturn);
		echo json_encode($json_response);
	}
    //B -> View Bookmarks In Action
    function b_view_bookmarks_inaction($pdo, $_projectId, $_actionId) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Bookmark Viewing Failure.', 'message' => 'Bookmark viewing failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Select the bookmarks's information
            $sql = 'SELECT b_bookmarks.Id as Id, b_bookmarks.Title as Title, b_bookmarks.Summary as Summary FROM r_actions_bookmarks 
            INNER JOIN b_bookmarks ON r_actions_bookmarks.Id_Bookmark = b_bookmarks.Id
            WHERE b_bookmarks.Id_Project = :Id_Project AND r_actions_bookmarks.Id_Action = :Id_Action';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
            $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $toReturn = array();
            while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                array_push($toReturn, ['bookmarkId' => $result['Id'], 'Bookmarkname' => $result['Title'], 'Summary' => $result['Summary']]);
            }
            http_response_code(200);
            $json_response = array('messageTitle' => 'Bookmark Viewing Success.', 'message' => 'Bookmark viewing successful.', 'content' => $toReturn);
            echo json_encode($json_response);
        }
    }
    //B -> Relate Bookmark To Action
    function b_relate_bookmark_toaction($pdo, $_projectId, $_bookmarkId, $_actionId) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Bookmark Relating (To Action) Failure.', 'message' => 'Bookmark relating (to action) failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Check if relation already exists
            $sql = 'SELECT COUNT(*) FROM r_actions_bookmarks 
            WHERE r_actions_bookmarks.Id_Bookmark = :Id_Bookmark AND r_actions_bookmarks.Id_Action = :Id_Action';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Bookmark', $_bookmarkId, PDO::PARAM_INT);
            $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            $count = $results['COUNT(*)'];
            if ($count != 0) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Bookmark Relating (To Action) Failure.', 'message' => 'Bookmark relating (to action) failure: target bookmark is already related to target action.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Create the bookmark-action relation
                $sql2 = 'INSERT INTO r_actions_bookmarks (Id_Bookmark, Id_Action) VALUES (:Id_Bookmark, :Id_Action)';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_Bookmark', $_bookmarkId, PDO::PARAM_INT);
                $statement2->bindParam(':Id_Action', $_actionId, PDO::PARAM_STR);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Bookmark Relating (To Action) Failure.', 'message' => 'Bookmark relating (to action) failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'Bookmark Relating (To Action) Success.', 'message' => 'Bookmark relating (to action) successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //B -> Unrelate Bookmark To Action
    function b_unrelate_bookmark_toaction($pdo, $_projectId, $_bookmarkId, $_actionId) {
        //Check if action is within the project
        if (!$this->isActionWithinProject($pdo, $_projectId, $_actionId)) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Bookmark Relating (To Action) Failure.', 'message' => 'Bookmark relating (to action) failure: target action does not exist in target project.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Check if relation exists
            $sql = 'SELECT COUNT(*) FROM r_actions_bookmarks 
            WHERE r_actions_bookmarks.Id_Bookmark = :Id_Bookmark AND r_actions_bookmarks.Id_Action = :Id_Action';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Id_Bookmark', $_bookmarkId, PDO::PARAM_INT);
            $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            $count = $results['COUNT(*)'];
            if ($count != 1) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Bookmark Unrelating (To Action) Failure.', 'message' => 'Bookmark unrelating (to action) failure: target bookmark isn\'t related to target action.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                //Delete the bookmark-action relation
                $sql2 = 'DELETE FROM r_actions_bookmarks WHERE Id_Bookmark = :Id_Bookmark AND Id_Action = :Id_Action';
                $statement2 = $pdo->prepare($sql2);
                $statement2->bindParam(':Id_Bookmark', $_bookmarkId, PDO::PARAM_INT);
                $statement2->bindParam(':Id_Action', $_actionId, PDO::PARAM_STR);
                $results2 = $statement2->execute();
                if (!$results2) {
                    http_response_code(400);
                    $json_response = array('messageTitle' => 'Bookmark Unrelating (To Action) Failure.', 'message' => 'Bookmark unrelating (to action) failure: the system is currently unavailable, please try again later.', 'content' => array());
                    echo json_encode($json_response);
                }
                else {
                    http_response_code(201);
                    $json_response = array('messageTitle' => 'Bookmark Unrelating (To Action) Success.', 'message' => 'Bookmark unrelating (to action) successful.', 'content' => array());
                    echo json_encode($json_response);
                }
            }
        }
    }
    //B -> Get Bookmark Id By Name
    function b_getbookmarkid_byname($pdo, $_projectId, $_Bookmarkname) {
		$Names = explode(',', $_Bookmarkname);
		$toReturn = '';
		for ($i = 0; $i < count($Names); $i++) {
			$Name = $Names[$i];
			//Select the bookmark's information
			$sql = 'SELECT b_bookmarks.Id AS Id
			FROM b_bookmarks
			WHERE b_bookmarks.Id_Project = :Id_Project AND b_bookmarks.Title = :Title';
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
        $json_response = array('messageTitle' => 'Bookmark Get Id By Name Success.', 'message' => 'Bookmark get id by name successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
    //B -> View Bookmarked Actions
    function b_view_bookmarked_actions($pdo, $_projectId, $_bookmarkId, $_actionId) {
        //Select the bookmarks's information
        $sql = 'SELECT s_actions.Id AS Id, s_actions.Title AS Title, Realtime, Screentime 
        FROM r_actions_bookmarks
        INNER JOIN b_bookmarks ON r_actions_bookmarks.Id_Bookmark = b_bookmarks.Id 
        INNER JOIN s_actions ON r_actions_bookmarks.Id_Action = s_actions.Id 
        WHERE b_bookmarks.Id_Project = :Id_Project AND r_actions_bookmarks.Id_Bookmark = :Id_Bookmark AND r_actions_bookmarks.Id_Action = :Id_Action';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Bookmark', $_bookmarkId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Action', $_actionId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['actionId' => $result['Id'], 'Actionname' => $result['Title'], 'Realtime' => $result['Realtime'], 'Screentime' => $result['Screentime']]);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Bookmarked Actions Viewing Success.', 'message' => 'Bookmarked actions viewing successful.', 'content' => $toReturn);
        echo json_encode($json_response);
    }
	//B -> Analyse Bookmarked Actions
    function b_analyse_bookmarked_actions($pdo, $_projectId, $_bookmarkId) {
        //Select the bookmarks's information
        $sql = 'SELECT s_actions.Id AS Id, s_actions.Title AS Title, s_actions.Summary AS Summary, 
		s_actions.Realtime AS ActionRealtime, s_actions.Screentime AS ActionScreentime, 
		s_acts.Realtime AS ActRealtime, s_acts.Screentime AS ActScreentime, 
		s_arcs.Realtime AS ArcRealtime, s_arcs.Screentime AS ArcScreentime 
		FROM r_actions_bookmarks 
        INNER JOIN b_bookmarks ON r_actions_bookmarks.Id_Bookmark = b_bookmarks.Id 
        INNER JOIN s_actions ON r_actions_bookmarks.Id_Action = s_actions.Id 
        INNER JOIN s_acts ON s_actions.Id_Act = s_acts.Id 
        INNER JOIN s_arcs ON s_acts.Id_Arc = s_arcs.Id 
        WHERE b_bookmarks.Id_Project = :Id_Project AND r_actions_bookmarks.Id_Bookmark = :Id_Bookmark
		ORDER BY s_arcs.Realtime ASC, s_acts.Realtime ASC, s_actions.Realtime ASC
		LIMIT 99';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Bookmark', $_bookmarkId, PDO::PARAM_INT);
        $statement->execute();
        $toReturn = array();
        while (($result = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            array_push($toReturn, ['actionId' => $result['Id'], 'Actionname' => $result['Title'], 'Summary' => $result['Summary'],
			'ActionRealtime' => $result['ActionRealtime'], 'ActionScreentime' => $result['ActionScreentime'],
			'ActRealtime' => $result['ActRealtime'], 'ActScreentime' => $result['ActScreentime'],
			'ArcRealtime' => $result['ArcRealtime'], 'ArcScreentime' => $result['ArcScreentime']]);
        }
        http_response_code(200);
        $json_response = array('messageTitle' => 'Bookmarked Actions Viewing Success.', 'message' => 'Bookmarked actions viewing successful.', 'content' => $toReturn);
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
    //B -> Is Bookmark Within Action
    private function isBookmarkWithinProject($pdo, $_projectId, $_bookmarkId) {
        //Check if act is within the project
        $sql = 'SELECT COUNT(*) FROM b_bookmarks WHERE b_bookmarks.Id_Project = :Id_Project AND b_bookmarks.Id = :Id_Bookmark';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_Bookmark', $_bookmarkId, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) return false;
        else return true;
    }
}
?>