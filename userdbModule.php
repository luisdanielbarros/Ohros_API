<?php
class userDatabaseModule {
    //U
    //U -> Register
    function u_register($pdo, $_Username, $_Userpass, $_Email) {
        //Check if username already exists
        $sql = 'SELECT COUNT(*) FROM u_users WHERE Username = :Username';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Username', $_Username, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count == 1) {
            http_response_code(104);
			$json_response = array('messageTitle' => 'Register Failure.', 'message' => 'Register Failure: username already exists.', 'content' => array());
			echo json_encode($json_response);
        }
        else {
            //Check if email already exists
            $sql = 'SELECT COUNT(*) FROM u_users WHERE Email = :Email';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Email', $_Email, PDO::PARAM_STR);
            $statement->execute();
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            $count = $results['COUNT(*)'];
            if ($count == 1) {
                http_response_code(105);
				$json_response = array('messageTitle' => 'Register Failure.', 'message' => 'Register Failure: email already exists.', 'content' => array());
				echo json_encode($json_response);
            }
            else {
                //Register the user
				$hashedUserpass = substr(md5($_Userpass), 0, 127);
                $sql = 'INSERT INTO u_users (Username, Userpass, Email) VALUES (:Username, :Userpass, :Email)';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':Username', $_Username, PDO::PARAM_STR);
                $statement->bindParam(':Userpass', $hashedUserpass, PDO::PARAM_STR);
                $statement->bindParam(':Email', $_Email, PDO::PARAM_STR);
                $results = $statement->execute();
                if (!$results) {
                    http_response_code(400);
					$json_response = array('messageTitle' => 'Register Failure.', 'message' => 'Register failure: the system is currently unavailable, please try again later.', 'content' => array());
					echo json_encode($json_response);
                }
                else {
                    //Create user confirmation code
                    $userId = (int)$pdo->lastInsertId();
                    $userCode = bin2hex(random_bytes(128));
                    $userCodeExpired = (int)0;
                    $sql = 'INSERT INTO u_usercodes (Id_User, Code, Expired) VALUES (:Id_User, :Code, :Expired)';
                    $statement = $pdo->prepare($sql);
                    $statement->bindParam(':Id_User', $userId, PDO::PARAM_INT);
                    $statement->bindParam(':Code', $userCode, PDO::PARAM_STR);
                    $statement->bindParam(':Expired', $userCodeExpired, PDO::PARAM_INT);
                    $results = $statement->execute();
					//Send the confirmation code via email
					$to = $_Email;
					$subject = 'Ohros - Account Confirmation Code';
					$message = '<!DOCTYPE html>
								<html>
								<head>
								</head>
								<body>
								<div>
								<h1>Welcome to Ohros!</h1>
								<h2>A story-writing platform.</h2>
								<p>Please confirm your account by following the link below.</p>
								<p><a href="localhost:3000/users/confirm-account/'.$userId.'/'.$userCode.'">Confirm account</a></p>
								<p>Alternatively, you can copy & paste the hyperlink below into your search bar.</p>
								<p>localhost:3000/users/confirm-account/'.$userId.'/'.$userCode.'</p>
								<p>Thank you,</p>
								<p>The Ohros Team</p>
								</div>
								</body>
								</html>';
					$headers = 'From: ohrosteam@gmail.com'."\r\n".
						'Reply-To: ohrosteam@gmail.com'."\r\n".
						'X-Mailer: PHP/' . phpversion()."\r\n".
						"MIME-Version: 1.0"."\r\n".
						"Content-type:text/html;charset=UTF-8"."\r\n";
					$emailResult = mail($to, $subject, $message, $headers);
					if(!$emailResult) {
						http_response_code(106);
						$json_response = array('messageTitle' => 'Register Failure.', 'message' => 'Register failure: account confirmation email could not be sent.', 'content' => array());
						echo json_encode($json_response);
					} else {
						http_response_code(201);
						$json_response = array('messageTitle' => 'Register Success.', 'message' => 'Register successful.', 'content' => array());
						echo json_encode($json_response);
					}
                }
            }
        }        
    }
    //U -> Confirm Account
    function u_confirm_account($pdo, $_userId, $_userCode) {
        //Check if user id and code match
        $sql = 'SELECT Expired, COUNT(*) FROM u_usercodes WHERE Id_User = :Id_User AND Code = :Code';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id_User', $_userId, PDO::PARAM_INT);
        $statement->bindParam(':Code', $_userCode, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
		$Count = $results['COUNT(*)'];
        if ($Count == 0) {
            http_response_code(104);
			$json_response = array('messageTitle' => 'Confirmation Failure.', 'message' => 'Confirmation failure: user id and code do not match.', 'content' => array());
			echo json_encode($json_response);
        }
        else {
            $Expired = $results['Expired'];
            if ($Expired) {
                http_response_code(105);
				$json_response = array('messageTitle' => 'Confirmation Failure.', 'message' => 'Confirmation failure: code has already expired.', 'content' => array());
				echo json_encode($json_response);
            }
            else {
                //Update the user's account
                $Confirmed = (int)1;
                $sql = 'UPDATE u_users SET Confirmed = :Confirmed WHERE Id = :Id';
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':Confirmed', $Confirmed, PDO::PARAM_INT);
                $statement->bindParam(':Id', $_userId, PDO::PARAM_INT);
                $results = $statement->execute();
                if (!$results) {
                    http_response_code(400);
					$json_response = array('messageTitle' => 'Confirmation Failure.', 'message' => 'Confirmation failure: please contact the support team.', 'content' => array());
					echo json_encode($json_response);
                }
                else {
                    //Update the user's code
                    $Expired = (int)1;
                    $sql = 'UPDATE u_usercodes SET Expired = :Expired WHERE Id_User = :Id_User';
                    $statement = $pdo->prepare($sql);
                    $statement->bindParam(':Expired', $Expired, PDO::PARAM_INT);
                    $statement->bindParam(':Id_User', $_userId, PDO::PARAM_INT);
                    $results = $statement->execute();
                    if (!$results) {
                        http_response_code(400);
						$json_response = array('messageTitle' => 'Confirmation Failure.', 'message' => 'Confirmation failure: please contact the support team.', 'content' => array());
						echo json_encode($json_response);
                    }
                    else {
                        http_response_code(200);
						$json_response = array('messageTitle' => 'Confirmation Success.', 'message' => 'Confirmation successful.', 'content' => array());
                        echo json_encode($json_response);
                    }
                }
            }
        }
    }
    //U -> Change username
    function u_change_username($pdo, $_userId, $_Userpass, $_NewUsername) {
        //Check if the password is correct
		$hashedUserpass = substr(md5($_Userpass), 0, 127);
        $sql = 'SELECT COUNT(*) FROM u_users WHERE Id = :Id AND Userpass = :Userpass';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_userId, PDO::PARAM_INT);
        $statement->bindParam(':Userpass', $hashedUserpass, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Username Change Failure.', 'message' => 'Username change failure: the password is incorrect.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the user's username
            $sql = 'UPDATE u_users SET Username = :Username WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Username', $_NewUsername, PDO::PARAM_STR);
            $statement->bindParam(':Id', $_userId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Username Change Failure.', 'message' => 'Username change failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Username Change Success.', 'message' => 'Username change successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //U -> Change password
    function u_change_password($pdo, $_userId, $_Userpass, $_NewUserpass) {
        //Check if the password is correct
		$hashedUserpass = substr(md5($_Userpass), 0, 127);
        $sql = 'SELECT COUNT(*) FROM u_users WHERE Id = :Id AND Userpass = :Userpass';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_userId, PDO::PARAM_INT);
        $statement->bindParam(':Userpass', $hashedUserpass, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Password Change Failure.', 'message' => 'Password change failure: the password is incorrect.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the user's password
			$hashedNewUserpass = substr(md5($_NewUserpass), 0, 127);
            $sql = 'UPDATE u_users SET Userpass = :Userpass WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Userpass', $hashedNewUserpass, PDO::PARAM_STR);
            $statement->bindParam(':Id', $_userId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Password Change Failure.', 'message' => 'Password change failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Password Change Success.', 'message' => 'Password change successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
    //U -> Change email
    function u_change_email($pdo, $_userId, $_Userpass, $_NewEmail) {
        //Check if the password is correct
		$hashedUserpass = substr(md5($_Userpass), 0, 127);
        $sql = 'SELECT COUNT(*) FROM u_users WHERE Id = :Id AND Userpass = :Userpass';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':Id', $_userId, PDO::PARAM_INT);
        $statement->bindParam(':Userpass', $hashedUserpass, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $count = $results['COUNT(*)'];
        if ($count != 1) {
            http_response_code(400);
            $json_response = array('messageTitle' => 'Email Change Failure.', 'message' => 'Email change failure: the password is incorrect.', 'content' => array());
            echo json_encode($json_response);
        }
        else {
            //Update the user's email
            $sql = 'UPDATE u_users SET Email = :Email WHERE Id = :Id';
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':Email', $_NewEmail, PDO::PARAM_STR);
            $statement->bindParam(':Id', $_userId, PDO::PARAM_INT);
            $results = $statement->execute();
            if (!$results) {
                http_response_code(400);
                $json_response = array('messageTitle' => 'Email Change Failure.', 'message' => 'Email change failure: the system is currently unavailable, please try again later.', 'content' => array());
                echo json_encode($json_response);
            }
            else {
                http_response_code(200);
                $json_response = array('messageTitle' => 'Email Change Success.', 'message' => 'Email change successful.', 'content' => array());
                echo json_encode($json_response);
            }
        }
    }
}
?>