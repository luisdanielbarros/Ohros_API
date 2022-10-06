<?php
//Configurations
////Allow CORS
////CHANGE BEFORE DEPLOYMENT
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');
////Load dependencies
require "dbConnection.php";
//Actual meat
function convertChars($string) {
	$string = str_replace("&#34;", '"', $string);
	$string = str_replace("&#39;", "'", $string);
	return $string;
}
if (isset($_POST["action"])) {
    //Action
    $action = filter_var($_POST["action"], FILTER_SANITIZE_STRING);
    $conn = new databaseConnection();
    switch ($action) {
        //Action -> U -> Register
        case 'register':
            $username = convertChars(filter_var($_POST["username"], FILTER_SANITIZE_STRING));
            $password = convertChars(filter_var($_POST["password"], FILTER_SANITIZE_STRING));
            $email = convertChars(filter_var($_POST["email"], FILTER_SANITIZE_STRING));
            $conn->Connect();
            $conn->u_register($username, $password, $email);
            $conn->Disconnect();
            break;
        //Action -> U -> Confirm Account
        case 'confirm_account':
            $user_id = filter_var($_POST["user_id"], FILTER_SANITIZE_NUMBER_INT);
            $code = filter_var($_POST["code"], FILTER_SANITIZE_STRING);
            $conn->Connect();
            $conn->u_confirm_account($user_id, $code);
            $conn->Disconnect();
            break;
        //Action -> U -> Login
        case 'login':
            $username = convertChars(filter_var($_POST["username"], FILTER_SANITIZE_STRING));
            $password = convertChars(filter_var($_POST["password"], FILTER_SANITIZE_STRING));
            $conn->Connect();
            $conn->u_login($username, $password);
            $conn->Disconnect();
            break;
        //Action -> U -> Change Username
        case 'change_username':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$user_id = filter_var($_POST["user_id"], FILTER_SANITIZE_NUMBER_INT);
            $password = convertChars(filter_var($_POST["password"], FILTER_SANITIZE_STRING));
            $new_username = convertChars(filter_var($_POST["new_username"], FILTER_SANITIZE_STRING));
            $conn->Connect();
            $conn->u_change_username($access_token, $user_id, $password, $new_username);
            $conn->Disconnect();
            break;
        //Action -> U -> Change Password
        case 'change_password':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$user_id = filter_var($_POST["user_id"], FILTER_SANITIZE_NUMBER_INT);
            $password = convertChars(filter_var($_POST["password"], FILTER_SANITIZE_STRING));
            $new_password = convertChars(filter_var($_POST["new_password"], FILTER_SANITIZE_STRING));
            $conn->Connect();
            $conn->u_change_password($access_token, $user_id, $password, $new_password);
            $conn->Disconnect();
            break;
        //Action -> U -> Change Email
        case 'change_email':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$user_id = filter_var($_POST["user_id"], FILTER_SANITIZE_NUMBER_INT);
            $password = convertChars(filter_var($_POST["password"], FILTER_SANITIZE_STRING));
            $new_email = convertChars(filter_var($_POST["new_email"], FILTER_SANITIZE_STRING));
            $conn->Connect();
            $conn->u_change_email($access_token, $user_id, $password, $new_email);
            $conn->Disconnect();
            break;
        //Action -> U -> Logout
        case 'logout':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
            $conn->Connect();
            $conn->u_logout($access_token);
            $conn->Disconnect();
            break;
        //----------------------------------------------------------------
        //Action -> U -> Create Project
        case 'createproject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$projname = convertChars(filter_var($_POST["projname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$projpass = convertChars(filter_var($_POST["projpass"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->u_create_project($access_token, $projname, $summary, $description, $projpass);
			$conn->Disconnect();
            break;
		//Action -> U -> Open Project
		case 'openproject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$project_id = filter_var($_POST["project_id"], FILTER_SANITIZE_NUMBER_INT);
			$projpass = convertChars(filter_var($_POST["projpass"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->u_open_project($access_token, $project_id, $projpass);
			$conn->Disconnect();
			break;
		//Action -> U -> Close Project
		case 'closeproject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->u_close_project($access_token);
			$conn->Disconnect();
			break;
        //Action -> U -> Update Project
        case 'updateproject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$projpass = convertChars(filter_var($_POST["projpass"], FILTER_SANITIZE_STRING));
			$projname = convertChars(filter_var($_POST["projname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$new_projpass = convertChars(filter_var($_POST["new_projpass"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->u_update_project($access_token, $projpass, $projname, $summary, $description, $new_projpass);
			$conn->Disconnect();
            break;
        //Action -> U -> Delete Project
        case 'deleteproject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->u_delete_project($access_token, $projpass);
			$conn->Disconnect();
            break;
        //Action -> U -> View Projects
        case 'viewprojects':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->u_view_projects($access_token);
			$conn->Disconnect();
            break;
		//Action -> U -> View Project
		case 'viewproject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$project_id = filter_var($_POST["project_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->u_view_project($access_token, $project_id);
			$conn->Disconnect();
			break;
        //----------------------------------------------------------------
        //Action -> WB (Characters) --------------------------------
        //Action -> WB -> Create Character
        case 'createcharacter':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$concept = convertChars(utf8_encode(filter_var($_POST["concept"], FILTER_SANITIZE_STRING)));
			$reasonofconcept = convertChars(utf8_encode(filter_var($_POST["reasonofconcept"], FILTER_SANITIZE_STRING)));
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$cause = convertChars(filter_var($_POST["cause"], FILTER_SANITIZE_STRING));
			$purpose = convertChars(filter_var($_POST["purpose"], FILTER_SANITIZE_STRING));
			$myth = convertChars(filter_var($_POST["myth"], FILTER_SANITIZE_STRING));
			$jungmodel = filter_var($_POST["jungmodel"], FILTER_SANITIZE_STRING);
			$oceanmodel = filter_var($_POST["oceanmodel"], FILTER_SANITIZE_STRING);
			$ego = convertChars(filter_var($_POST["ego"], FILTER_SANITIZE_STRING));
			$complexes = convertChars(filter_var($_POST["complexes"], FILTER_SANITIZE_STRING));
			$persona = convertChars(filter_var($_POST["persona"], FILTER_SANITIZE_STRING));
			$anima = convertChars(filter_var($_POST["anima"], FILTER_SANITIZE_STRING));
			$shadow = convertChars(filter_var($_POST["shadow"], FILTER_SANITIZE_STRING));
			$self = convertChars(filter_var($_POST["self"], FILTER_SANITIZE_STRING));
			$psychicquirks = convertChars(filter_var($_POST["psychicquirks"], FILTER_SANITIZE_STRING));
			$physicquirks = convertChars(filter_var($_POST["physicquirks"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_create_character($access_token, $concept, $reasonofconcept, $wbname, $summary, $description, $cause, $purpose, $myth, $jungmodel, $oceanmodel, $ego, $complexes, $persona, $anima, $shadow, $self, $psychicquirks, $physicquirks);
			$conn->Disconnect();
            break;
        //Action -> WB -> Update Character
        case 'updatecharacter':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$concept = convertChars(utf8_encode(filter_var($_POST["concept"], FILTER_SANITIZE_STRING)));
			$reasonofconcept = convertChars(utf8_encode(filter_var($_POST["reasonofconcept"], FILTER_SANITIZE_STRING)));
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$cause = convertChars(filter_var($_POST["cause"], FILTER_SANITIZE_STRING));
			$purpose = convertChars(filter_var($_POST["purpose"], FILTER_SANITIZE_STRING));
			$myth = convertChars(filter_var($_POST["myth"], FILTER_SANITIZE_STRING));
			$jungmodel = filter_var($_POST["jungmodel"], FILTER_SANITIZE_STRING);
			$oceanmodel = filter_var($_POST["oceanmodel"], FILTER_SANITIZE_STRING);
			$ego = convertChars(filter_var($_POST["ego"], FILTER_SANITIZE_STRING));
			$complexes = convertChars(filter_var($_POST["complexes"], FILTER_SANITIZE_STRING));
			$persona = convertChars(filter_var($_POST["persona"], FILTER_SANITIZE_STRING));
			$anima = convertChars(filter_var($_POST["anima"], FILTER_SANITIZE_STRING));
			$shadow = convertChars(filter_var($_POST["shadow"], FILTER_SANITIZE_STRING));
			$self = convertChars(filter_var($_POST["self"], FILTER_SANITIZE_STRING));
			$psychicquirks = convertChars(filter_var($_POST["psychicquirks"], FILTER_SANITIZE_STRING));
			$physicquirks = convertChars(filter_var($_POST["physicquirks"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_update_character($access_token, $wb_id, $concept, $reasonofconcept, $wbname, $summary, $description, $cause, $purpose, $myth, $jungmodel, $oceanmodel, $ego, $complexes,$persona, $anima, $shadow, $self, $psychicquirks, $physicquirks);
			$conn->Disconnect();
            break;
        //Action -> WB -> Delete Character
        case 'deletecharacter':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->wb_delete_character($access_token, $projpass, $wb_id);
			$conn->Disconnect();
            break;
        //Action -> WB -> View Characters
        case 'viewcharacters':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->wb_view_characters($access_token);
			$conn->Disconnect();
            break;
		//Action -> WB -> View Character
		case 'viewcharacter':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->wb_view_character($access_token, $wb_id);
			$conn->Disconnect();
			break;
        //Action -> WB (Locations) --------------------------------
        //Action -> WB -> Create Location
        case 'createlocation':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$concept = convertChars(utf8_encode(filter_var($_POST["concept"], FILTER_SANITIZE_STRING)));
			$reasonofconcept = convertChars(utf8_encode(filter_var($_POST["reasonofconcept"], FILTER_SANITIZE_STRING)));
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$cause = convertChars(filter_var($_POST["cause"], FILTER_SANITIZE_STRING));
			$purpose = convertChars(filter_var($_POST["purpose"], FILTER_SANITIZE_STRING));
			$myth = convertChars(filter_var($_POST["myth"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_create_location($access_token, $concept, $reasonofconcept, $wbname, $summary, $description, $cause, $purpose, $myth);
			$conn->Disconnect();
            break;
        //Action -> WB -> Update Location
        case 'updatelocation':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$concept = convertChars(utf8_encode(filter_var($_POST["concept"], FILTER_SANITIZE_STRING)));
			$reasonofconcept = convertChars(utf8_encode(filter_var($_POST["reasonofconcept"], FILTER_SANITIZE_STRING)));
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$cause = convertChars(filter_var($_POST["cause"], FILTER_SANITIZE_STRING));
			$purpose = convertChars(filter_var($_POST["purpose"], FILTER_SANITIZE_STRING));
			$myth = convertChars(filter_var($_POST["myth"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_update_location($access_token, $wb_id, $concept, $reasonofconcept, $wbname, $summary, $description, $cause, $purpose, $myth);
			$conn->Disconnect();
            break;
        //Action -> WB -> Delete Location
        case 'deletelocation':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->wb_delete_location($access_token, $projpass, $wb_id);
			$conn->Disconnect();
            break;
        //Action -> WB -> View Locations
        case 'viewlocations':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->wb_view_locations($access_token);
			$conn->Disconnect();
            break;
		//Action -> WB -> View Location
		case 'viewlocation':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->wb_view_location($access_token, $wb_id);
			$conn->Disconnect();
			break;
        //Action -> WB (Objects) --------------------------------
        //Action -> WB -> Create Object
        case 'createobject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$concept = convertChars(utf8_encode(filter_var($_POST["concept"], FILTER_SANITIZE_STRING)));
			$reasonofconcept = convertChars(utf8_encode(filter_var($_POST["reasonofconcept"], FILTER_SANITIZE_STRING)));
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$cause = convertChars(filter_var($_POST["cause"], FILTER_SANITIZE_STRING));
			$purpose = convertChars(filter_var($_POST["purpose"], FILTER_SANITIZE_STRING));
			$myth = convertChars(filter_var($_POST["myth"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_create_object($access_token, $concept, $reasonofconcept, $wbname, $summary, $description, $cause, $purpose, $myth);
			$conn->Disconnect();
            break;
        //Action -> WB -> Update Object
        case 'updateobject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$concept = convertChars(utf8_encode(filter_var($_POST["concept"], FILTER_SANITIZE_STRING)));
			$reasonofconcept = convertChars(utf8_encode(filter_var($_POST["reasonofconcept"], FILTER_SANITIZE_STRING)));
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$cause = convertChars(filter_var($_POST["cause"], FILTER_SANITIZE_STRING));
			$purpose = convertChars(filter_var($_POST["purpose"], FILTER_SANITIZE_STRING));
			$myth = convertChars(filter_var($_POST["myth"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_update_object($access_token, $wb_id, $concept, $reasonofconcept, $wbname, $summary, $description, $cause, $purpose, $myth);
			$conn->Disconnect();
            break;
        //Action -> WB -> Delete Object
        case 'deleteobject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->wb_delete_object($access_token, $projpass, $wb_id);
			$conn->Disconnect();
            break;
        //Action -> WB -> View Objects
        case 'viewobjects':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->wb_view_objects($access_token);
			$conn->Disconnect();
            break;
		//Action -> WB -> View Object
		case 'viewobject':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->wb_view_object($access_token, $wb_id);
			$conn->Disconnect();
			break;
        //Action -> WB (Metaphysics) --------------------------------
        //Action -> WB -> Create Metaphysic
        case 'createmetaphysic':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$concept = convertChars(utf8_encode(filter_var($_POST["concept"], FILTER_SANITIZE_STRING)));
			$reasonofconcept = convertChars(utf8_encode(filter_var($_POST["reasonofconcept"], FILTER_SANITIZE_STRING)));
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$cause = convertChars(filter_var($_POST["cause"], FILTER_SANITIZE_STRING));
			$purpose = convertChars(filter_var($_POST["purpose"], FILTER_SANITIZE_STRING));
			$myth = convertChars(filter_var($_POST["myth"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_create_metaphysic($access_token, $concept, $reasonofconcept, $wbname, $summary, $description, $cause, $purpose, $myth);
			$conn->Disconnect();
            break;
        //Action -> WB -> Update Metaphysic
        case 'updatemetaphysic':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$concept = convertChars(utf8_encode(filter_var($_POST["concept"], FILTER_SANITIZE_STRING)));
			$reasonofconcept = convertChars(utf8_encode(filter_var($_POST["reasonofconcept"], FILTER_SANITIZE_STRING)));
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$cause = convertChars(filter_var($_POST["cause"], FILTER_SANITIZE_STRING));
			$purpose = convertChars(filter_var($_POST["purpose"], FILTER_SANITIZE_STRING));
			$myth = convertChars(filter_var($_POST["myth"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_update_metaphysic($access_token, $wb_id, $concept, $reasonofconcept, $wbname, $summary, $description, $cause, $purpose, $myth);
			$conn->Disconnect();
            break;
        //Action -> WB -> Delete Metaphysic
        case 'deletemetaphysic':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->wb_delete_metaphysic($access_token, $projpass, $wb_id);
			$conn->Disconnect();
            break;
        //Action -> S -> View Metaphysics
        case 'viewmetaphysics':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->wb_view_metaphysics($access_token);
			$conn->Disconnect();
            break;
		//Action -> S -> View Metaphysic
		case 'viewmetaphysic':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->wb_view_metaphysic($access_token, $wb_id);
			$conn->Disconnect();
			break;
		//Action -> WB (General) --------------------------------
        //Action -> WB -> Get WB Id By Name
        case 'getwbidbyname':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wbname = convertChars(filter_var($_POST["wbname"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->wb_getwbid_byname($access_token, $wbname);
			$conn->Disconnect();
            break;
        //----------------------------------------------------------------
        //Action -> S -> Create Timeline
        case 'createtimeline':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$timename = convertChars(filter_var($_POST["timename"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->s_create_timeline($access_token, $timename, $summary, $description);
			$conn->Disconnect();
            break;
        //Action -> S -> Update Timeline
        case 'updatetimeline':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$timeline_id = filter_var($_POST["timeline_id"], FILTER_SANITIZE_NUMBER_INT);
			$timename = convertChars(filter_var($_POST["timename"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->s_update_timeline($access_token, $timeline_id, $timename, $summary, $description);
			$conn->Disconnect();
            break;
        //Action -> S -> Delete Timeline
        case 'deletetimeline':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$timeline_id = filter_var($_POST["timeline_id"], FILTER_SANITIZE_NUMBER_INT);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->s_delete_timeline($access_token, $timeline_id, $projpass);
			$conn->Disconnect();
            break;
        //Action -> S -> View Timelines
        case 'viewtimelines':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->s_view_timelines($access_token);
			$conn->Disconnect();
            break;
		//Action -> S -> View Timeline
		case 'viewtimeline':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$timeline_id = filter_var($_POST["timeline_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_view_timeline($access_token, $timeline_id);
			$conn->Disconnect();
			break;
		//Action -> S -> Get Timeline Id By Name
		case 'gettimelineidbyname':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$timename = convertChars(filter_var($_POST["timename"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->s_gettimelineid_byname($access_token, $timename);
			$conn->Disconnect();
			break;
        //----------------------------------------------------------------
        //Action -> S -> Create Arc
        case 'createarc':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$timeline_id = filter_var($_POST["timeline_id"], FILTER_SANITIZE_STRING);
			$arcname = convertChars(filter_var($_POST["arcname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			if (!isset($realtime)) $realtime = 1;
			if (!isset($screentime)) $screentime = 1;
			$conn->Connect();
			$conn->s_create_arc($access_token, $timeline_id, $arcname, $summary, $description, $realtime, $screentime);
			$conn->Disconnect();
            break;
        //Action -> S -> Update Arc
        case 'updatearc':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$arc_id = filter_var($_POST["arc_id"], FILTER_SANITIZE_STRING);
			$arcname = convertChars(filter_var($_POST["arcname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_update_arc($access_token, $arc_id, $arcname, $summary, $description, $realtime, $screentime);
			$conn->Disconnect();
            break;
		//Action -> S -> Update Arc Time
        case 'updatearctime':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$arc_id = filter_var($_POST["arc_id"], FILTER_SANITIZE_STRING);
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_update_arctime($access_token, $arc_id, $realtime, $screentime);
			$conn->Disconnect();
            break;
        //Action -> S -> Delete Arc
        case 'deletearc':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$arc_id = filter_var($_POST["arc_id"], FILTER_SANITIZE_STRING);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->s_delete_arc($access_token, $arc_id, $projpass);
			$conn->Disconnect();
            break;
        //Action -> S -> View Arcs
        case 'viewarcs':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$timeline_id = filter_var($_POST["timeline_id"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->s_view_arcs($access_token, $timeline_id);
			$conn->Disconnect();
            break;
		//Action -> S -> View Arc
		case 'viewarc':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$arc_id = filter_var($_POST["arc_id"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->s_view_arc($access_token, $arc_id);
			$conn->Disconnect();
			break;
		//Action -> S -> Get Arc Id By Name
		case 'getarcidbyname':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$arcname = convertChars(filter_var($_POST["arcname"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->s_getarcid_byname($access_token, $arcname);
			$conn->Disconnect();
			break;
        //----------------------------------------------------------------
        //Action -> S -> Create Act
        case 'createact':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$arc_id = filter_var($_POST["arc_id"], FILTER_SANITIZE_NUMBER_INT);
			$actname = convertChars(filter_var($_POST["actname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_create_act($access_token, $arc_id, $actname, $summary, $description, $realtime, $screentime);
			$conn->Disconnect();
            break;
        //Action -> S -> Update Act
        case 'updateact':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$act_id = filter_var($_POST["act_id"], FILTER_SANITIZE_NUMBER_INT);
			$actname = convertChars(filter_var($_POST["actname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_update_act($access_token, $act_id, $actname, $summary, $description, $realtime, $screentime);
			$conn->Disconnect();
            break;
		//Action -> S -> Update Act Time
        case 'updateacttime':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$act_id = filter_var($_POST["act_id"], FILTER_SANITIZE_NUMBER_INT);
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_update_acttime($access_token, $act_id, $realtime, $screentime);
			$conn->Disconnect();
            break;
        //Action -> S -> Delete Act
        case 'deleteact':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$act_id = filter_var($_POST["act_id"], FILTER_SANITIZE_NUMBER_INT);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->s_delete_act($access_token, $act_id, $projpass);
			$conn->Disconnect();
            break;
        //Action -> S -> View Acts
        case 'viewacts':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$arc_id = filter_var($_POST["arc_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_view_acts($access_token, $arc_id);
			$conn->Disconnect();
            break;
		//Action -> S -> View Act
		case 'viewact':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$act_id = filter_var($_POST["act_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_view_act($access_token, $act_id);
			$conn->Disconnect();
			break;
	//Action -> S -> Get Act Id By Name
		case 'getactidbyname':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$actname = convertChars(filter_var($_POST["actname"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->s_getactid_byname($access_token, $actname);
			$conn->Disconnect();
			break;
        //----------------------------------------------------------------
        //Action -> S -> Create Action
        case 'createaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$act_id = filter_var($_POST["act_id"], FILTER_SANITIZE_NUMBER_INT);
			$actionname = convertChars(filter_var($_POST["actionname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			$wbs_ids = filter_var($_POST["wbs_ids"], FILTER_SANITIZE_STRING);
			$bookmarks_ids = filter_var($_POST["bookmarks_ids"], FILTER_SANITIZE_STRING);
			$arguments_ids = filter_var($_POST["arguments_ids"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->s_create_action($access_token, $act_id, $actionname, $summary, $description, $realtime, $screentime, $wbs_ids, $bookmarks_ids, $arguments_ids);
			$conn->Disconnect();
            break;
        //Action -> S -> Update Action
        case 'updateaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$actionname = convertChars(filter_var($_POST["actionname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_update_action($access_token, $action_id, $actionname, $summary, $description, $realtime, $screentime);
			$conn->Disconnect();
            break;
		//Action -> S -> Update Action Time
        case 'updateactiontime':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$realtime = filter_var($_POST["realtime"], FILTER_SANITIZE_NUMBER_INT);
			$screentime = filter_var($_POST["screentime"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_update_actiontime($access_token, $action_id, $realtime, $screentime);
			$conn->Disconnect();
            break;
		//Action -> S -> Delete Action
		case 'deleteaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->s_delete_action($access_token, $action_id, $projpass);
			$conn->Disconnect();
			break;
        //Action -> S -> View Actions
        case 'viewactions':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$act_id = filter_var($_POST["act_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_view_actions($access_token, $act_id);
			$conn->Disconnect();
            break;
		//Action -> S -> View Action
		case 'viewaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->s_view_action($access_token, $action_id);
			$conn->Disconnect();
			break;
		//Action -> S -> Get Action Id By Name
		case 'getactionidbyname':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$actionname = convertChars(filter_var($_POST["actionname"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->s_getactionid_byname($access_token, $actionname);
			$conn->Disconnect();
			break;
		//----------------------------------------------------------------
        //Bookmark -> B -> Create Bookmark
        case 'createbookmark':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmarkname = convertChars(filter_var($_POST["bookmarkname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->b_create_bookmark($access_token, $bookmarkname, $summary, $description);
			$conn->Disconnect();
            break;
        //Bookmark -> B -> Update Bookmark
        case 'updatebookmark':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmark_id = filter_var($_POST["bookmark_id"], FILTER_SANITIZE_NUMBER_INT);
			$bookmarkname = convertChars(filter_var($_POST["bookmarkname"], FILTER_SANITIZE_STRING));
			$summary = convertChars(filter_var($_POST["summary"], FILTER_SANITIZE_STRING));
			$description = convertChars(filter_var($_POST["description"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->b_update_bookmark($access_token, $bookmark_id, $bookmarkname, $summary, $description);
			$conn->Disconnect();
            break;
		//Bookmark -> B -> Delete Bookmark
		case 'deletebookmark':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmark_id = filter_var($_POST["bookmark_id"], FILTER_SANITIZE_NUMBER_INT);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->b_delete_bookmark($access_token, $bookmark_id, $projpass);
			$conn->Disconnect();
			break;
        //Bookmark -> B -> View Bookmarks
        case 'viewbookmarks':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->b_view_bookmarks($access_token);
			$conn->Disconnect();
            break;
        //Bookmark -> B -> View Bookmark
		case 'viewbookmark':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmark_id = filter_var($_POST["bookmark_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->b_view_bookmark($access_token, $bookmark_id);
			$conn->Disconnect();
			break;
		//Bookmark -> B -> View Bookmarks In Action
		case 'viewbookmarksinaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->b_view_bookmarks_inaction($access_token, $action_id);
			$conn->Disconnect();
			break;
		//Bookmark -> B -> Relate Bookmark To Action
		case 'relatebookmarktoaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmark_id = filter_var($_POST["bookmark_id"], FILTER_SANITIZE_NUMBER_INT);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->b_relate_bookmark_toaction($access_token, $bookmark_id, $action_id);
			$conn->Disconnect();
			break;
		//Bookmark -> B -> Unrelate Bookmark To Action
		case 'unrelatebookmarktoaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmark_id = filter_var($_POST["bookmark_id"], FILTER_SANITIZE_NUMBER_INT);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->b_unrelate_bookmark_toaction($access_token, $bookmark_id, $action_id);
			$conn->Disconnect();
			break;
		//Bookmark -> B -> View Bookmarked Actions
		case 'getbookmarkidbyname':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmarkname = convertChars(filter_var($_POST["bookmarkname"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->b_getbookmarkid_byname($access_token, $bookmarkname);
			$conn->Disconnect();
			break;
		//Bookmark -> B -> View Bookmarked Actions
		case 'viewbookmarkedactions':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmark_id = filter_var($_POST["bookmark_id"], FILTER_SANITIZE_NUMBER_INT);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->b_view_bookmarked_actions($access_token, $bookmark_id, $action_id);
			$conn->Disconnect();
			break;
		//Bookmark -> B -> Analyse Bookmarked Actions
		case 'analysebookmarkedactions':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$bookmark_id = filter_var($_POST["bookmark_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->b_analyse_bookmarked_actions($access_token, $bookmark_id);
			$conn->Disconnect();
			break;
		//----------------------------------------------------------------
		//Relation -> R -> View WBs
		case 'viewwbs':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->r_view_wbs($access_token);
			$conn->Disconnect();
			break;
		//Relation -> R -> View WBs in Actions
		case 'viewwbsinaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->r_view_wbs_inaction($access_token, $action_id);
			$conn->Disconnect();
			break;
		//Relation -> R -> Relate WB To Action
		case 'relatewbtoaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->r_relate_wb_toaction($access_token, $wb_id, $action_id);
			$conn->Disconnect();
			break;
		//Relation -> R -> Unrelate WB To Action
		case 'unrelatewbtoaction':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wb_id = filter_var($_POST["wb_id"], FILTER_SANITIZE_NUMBER_INT);
			$action_id = filter_var($_POST["action_id"], FILTER_SANITIZE_NUMBER_INT);
			$conn->Connect();
			$conn->r_unrelate_wb_toaction($access_token, $wb_id, $action_id);
			$conn->Disconnect();
			break;
		//Relation -> R -> Analyse WBs
		case 'analysewbs':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$wbs_ids = filter_var($_POST["wbs_ids"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->r_analyse_wbs($access_token, $wbs_ids);
			$conn->Disconnect();
			break;
        //----------------------------------------------------------------
        //Action -> A -> Create Argument
        case 'createargument':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$evoker_id = filter_var($_POST["evoker_id"], FILTER_SANITIZE_NUMBER_INT);
			$target_id = filter_var($_POST["target_id"], FILTER_SANITIZE_NUMBER_INT);
			$argumentname = convertChars(filter_var($_POST["argumentname"], FILTER_SANITIZE_STRING));
			$argument = convertChars(filter_var($_POST["argument"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->a_create_argument($access_token, $evoker_id, $target_id, $argumentname, $argument);
			$conn->Disconnect();
            break;
        //Action -> A -> Update Argument
        case 'updateargument':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$evoker_id = filter_var($_POST["evoker_id"], FILTER_SANITIZE_NUMBER_INT);
			$target_id = filter_var($_POST["target_id"], FILTER_SANITIZE_NUMBER_INT);
			$argumentname = convertChars(filter_var($_POST["argumentname"], FILTER_SANITIZE_STRING));
			$argument = convertChars(filter_var($_POST["argument"], FILTER_SANITIZE_STRING));
			$conn->Connect();
			$conn->a_update_argument($access_token, $evoker_id, $target_id, $argumentname, $argument);
			$conn->Disconnect();
            break;
        //Action -> A -> Delete Argument
        case 'deleteargument':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$projpass = filter_var($_POST["projpass"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->a_delete_argument($access_token, $projpass);
			$conn->Disconnect();
            break;
        //Action -> A -> View Arguments
        case 'viewarguments':
			$access_token = filter_var($_POST["access_token"], FILTER_SANITIZE_STRING);
			$conn->Connect();
			$conn->a_view_arguments($access_token);
			$conn->Disconnect();
            break;
        //----------------------------------------------------------------
        case 'status':
            $json_response = array('message' => 'Ready to receive requests.', 'content' => array());
            echo json_encode($json_response);
            break;
        default:
            $json_response = array('message' => 'Invalid action parameter.', 'content' => array());
            echo json_encode($json_response);
    }
}
else {
    $json_response = array('message' => 'Ready to receive requests.', 'content' => array());
    echo json_encode($json_response);
}
?>