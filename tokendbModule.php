<?php
class tokenDatabaseModule {
    //U
    //U -> Create User Token
    function u_create_user_token($pdo, $_Username, $_Userpass) {
        //Check if username and password match
		$hashedUserpass = substr(md5($_Userpass), 0, 127);
        $sql = 'SELECT Id, Confirmed, Email FROM u_users WHERE Username = :Username AND Userpass = :Userpass';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Username', $_Username, PDO::PARAM_STR);
        $statement->bindParam(':Userpass', $hashedUserpass, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$results) {
            http_response_code(200);
			$json_response = array('messageTitle' => 'User Token Creation Failure.', 'message' => 'User Token Creation failure: username and password do not match.', 'content' => array());
			echo json_encode($json_response);
        }
        else {
            //Check if account is confirmed
			$userConfirmed = $results['Confirmed'];
            if (!$userConfirmed) {
                http_response_code(200);
				$json_response = array('messageTitle' => 'User Token Creation Failure.', 'message' => 'User Token Creation failure: user account isn\'t confirmed yet, please check your e-mail.', 'content' => array());
				echo json_encode($json_response);
            }
            else {
                //Obtain the remaining account information
                $userEmail = $results['Email'];
                //Create the tokens
                $accessToken = bin2hex(random_bytes(128));
                $refreshToken = bin2hex(random_bytes(128));
                date_default_timezone_set('Europe/Lisbon');
                $expirationDate = date('Y-m-d H:i:s', strtotime('+2 hours'));
                $userId = $results['Id'];
                $projectId = null;
                $sql = 'INSERT INTO u_tokens (AccessToken, RefreshToken, ExpirationDate, Id_User, Id_Project) VALUES (:AccessToken, :RefreshToken, :ExpirationDate, :Id_User, :Id_Project)';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':AccessToken', $accessToken, PDO::PARAM_STR);
                $statement->bindParam(':RefreshToken', $refreshToken, PDO::PARAM_STR);
                $statement->bindParam(':ExpirationDate', $expirationDate, PDO::PARAM_STR);
                $statement->bindParam(':Id_User', $userId, PDO::PARAM_INT);
                $statement->bindParam(':Id_Project', $projectId, PDO::PARAM_INT);
                $results = $statement->execute();
                if (!$results) {
                    http_response_code(200);
					$json_response = array('messageTitle' => 'User Token Creation Failure.', 'message' => 'User Token Creation failure: the system is currently unavailable, please try again later.', 'content' => array());
					echo json_encode($json_response);
                }
                else {
                    http_response_code(202);
                    $json_response = array('messageTitle' => 'User Token Creation Success.', 'message' => 'User Token Creation successful.', 'content' => array('accessToken' => $accessToken, 'refreshToken' => $refreshToken, 'userId' => $userId, 'Username' => $_Username, 'Email' => $userEmail));
                    echo json_encode($json_response);
                }
            }
        }
    }
    //U
    //U -> Create Project Token
    function u_create_project_token($pdo, $_accessToken, $_userId, $_projectId, $_Projpass) {
        //Check if user id and projpass match
		$hashedProjpass = substr(md5($_Projpass), 0, 127);
        $sql = 'SELECT Id_Project FROM u_projects
        INNER JOIN u_projwhitelists on u_projects.Id = u_projwhitelists.Id_Project
        WHERE u_projects.Id = :Id_Project AND u_projwhitelists.Id_User = :Id_User AND u_projects.Projpass = :Projpass';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_Project', $_projectId, PDO::PARAM_INT);
        $statement->bindParam(':Id_User', $_userId, PDO::PARAM_INT);
        $statement->bindParam(':Projpass', $hashedProjpass, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$results) {
            http_response_code(200);
			$json_response = array('messageTitle' => 'Project Token Creation Failure.', 'message' => 'Project Token Creation failure: project id and project password do not match.', 'content' => array());
			echo json_encode($json_response);
        }
        else {
            //Update the token
            $projectId = $results['Id_Project'];
            $sql2 = 'UPDATE u_tokens SET Id_Project = :Id_Project WHERE AccessToken LIKE :AccessToken';
            $statement2 = $pdo->prepare($sql2);
            $statement2->bindParam(':Id_Project', $projectId, PDO::PARAM_INT);
            $statement2->bindValue(':AccessToken', $_accessToken.'%', PDO::PARAM_STR);
            $results2 = $statement2->execute();
            if (!$results2) {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Project Token Creation Failure.', 'message' => 'Project Token Creation failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(202);
                $json_response = array('messageTitle' => 'Project Token Creation Success.', 'message' => 'Project Token Creation successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //U -> Expire User Token
    function u_expire_user_token($pdo, $_accessToken) {
        //Update the token
        $sql = 'UPDATE u_tokens SET ExpirationDate = NOW() WHERE AccessToken LIKE :AccessToken';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':AccessToken', $_accessToken.'%', PDO::PARAM_STR);
        $results = $statement->execute();
        if (!$results) {
            http_response_code(200);
			$json_response = array('messageTitle' => 'User Token Expiration Failure.', 'message' => 'User Token Expiration failure: the system is currently unavailable, please try again later.', 'content' => array());
			echo json_encode($json_response);
        }
        else {
            http_response_code(202);
            $json_response = array('messageTitle' => 'User Token Expiration Success.', 'message' => 'User Token Expiration successful.', 'content' => array());
            echo json_encode($json_response);
        }
    }
	//U -> Expire Project Token
    function u_expire_project_token($pdo, $_accessToken) {
        //Update the token
        $sql = 'UPDATE u_tokens SET Id_Project = NULL WHERE AccessToken LIKE :AccessToken';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':AccessToken', $_accessToken.'%', PDO::PARAM_STR);
        $results = $statement->execute();
        if (!$results) {
            http_response_code(200);
			$json_response = array('messageTitle' => 'Project Token Expiration Failure.', 'message' => 'Project Token Expiration failure: the system is currently unavailable, please try again later.', 'content' => array());
			echo json_encode($json_response);
        }
        else {
            http_response_code(202);
            $json_response = array('messageTitle' => 'Project Token Expiration Success.', 'message' => 'Project Token Expiration successful.', 'content' => array());
            echo json_encode($json_response);
        }
    }
    //U -> Check User Token
    function u_check_user_token($pdo, $_AccessToken) {
        //Check if user id, access token and expiration date match
        date_default_timezone_set('Europe/Lisbon');
        $expirationDate = date('Y-m-d H:i:s');
        $sql = 'SELECT Id_User FROM u_tokens WHERE AccessToken LIKE :AccessToken AND ExpirationDate > :ExpirationDate';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':AccessToken', $_AccessToken.'%', PDO::PARAM_STR);
        $statement->bindParam(':ExpirationDate', $expirationDate, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        if ($results) return $results['Id_User'];
		else return false;
    }
    //U -> Print Check User Token
    function u_print_check_user_token($return) {
        if (!$return) {
            http_response_code(199);
            $json_response = array('messageTitle' => 'User Token Check Failure.', 'message' => 'User Token Check failure: access token has already expired.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            http_response_code(202);
            $json_response = array('messageTitle' => 'User Token Check Success.', 'message' => 'User Token Check successful.', 'content' => array());
            echo json_encode($json_response);
        }
    }
    //U -> Check Project Token
    function u_check_project_token($pdo, $_AccessToken) {
        //Check if user id, access token and expiration date match
        date_default_timezone_set('Europe/Lisbon');
        $expirationDate = date('Y-m-d H:i:s');
        $sql = 'SELECT Id_Project FROM u_tokens WHERE AccessToken LIKE :AccessToken AND ExpirationDate > :ExpirationDate';
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':AccessToken', $_AccessToken.'%', PDO::PARAM_STR);
        $statement->bindParam(':ExpirationDate', $expirationDate, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$results) return false;
        else return $results['Id_Project'];
    }
    //U -> Print Check Project Token
    function u_print_check_project_token($return) {
        if (!$return) {
            http_response_code(198);
            $json_response = array('messageTitle' => 'Project Token Check Failure.', 'message' => 'Project Token Check failure: access token has already expired.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            http_response_code(202);
            $json_response = array('messageTitle' => 'Project Token Check Success.', 'message' => 'Project Token Check successful.', 'content' => array());
            echo json_encode($json_response);
        }
    }
}