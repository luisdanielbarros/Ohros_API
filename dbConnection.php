<?php
require 'tokendbModule.php';
require 'userdbModule.php';
require 'projectdbModule.php';
require 'wbcharacterdbModule.php';
require 'wblocationdbModule.php';
require 'wbobjectdbModule.php';
require 'wbmetaphysicsdbModule.php';
require 'wbgeneraldbModule.php';
require 'timelinedbModule.php';
require 'arcdbModule.php';
require 'actdbModule.php';
require 'actiondbModule.php';
require 'bookmarkdbModule.php';
require 'reldbModule.php';
require 'argumentdbModule.php';
class databaseConnection {

    public $pdo;

    public $tokenModule;
    public $userModule;
    public $projectModule;
    public $wbCharacterModule;
    public $wbLocationModule;
    public $wbObjectModule;
    public $wbMetaphysicsModule;
    public $wbGeneralModule;
    public $timelineModule;
    public $arcModule;
    public $actModule;
    public $actionModule;
    public $bookmarkModule;
    public $relationModule;
    public $argumentModule;

    function Connect() {
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=ohros;charset=UTF8', 'root', '');
            $this->tokenModule = new tokenDatabaseModule();
            $this->userModule = new userDatabaseModule();
            $this->projectModule = new projectDatabaseModule();
            $this->wbCharacterModule = new wbCharacterDatabaseModule();
            $this->wbLocationModule = new wbLocationDatabaseModule();
            $this->wbObjectModule = new wbObjectDatabaseModule();
            $this->wbMetaphysicsModule = new wbMetaphysicstDatabaseModule();
            $this->wbGeneralModule = new wbGeneralDatabaseModule();
            $this->timelineModule = new timelineDatabaseModule();
            $this->arcModule = new arcDatabaseModule();
            $this->actModule = new actDatabaseModule();
            $this->actionModule = new actionDatabaseModule();
            $this->bookmarkModule = new bookmarkDatabaseModule();
            $this->relationModule = new relationDatabaseModule();
            $this->argumentModule = new argumentDatabaseModule();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    function Disconnect() {
        $this->pdo = null;
    }
    //Utilities ----------------------------------------------------------------
    //Util
    ////Util -> Is Logged In
    private function isLoggedIn($_pdo, $_accessToken) {
        $isLoggedIn = $this->tokenModule->u_check_user_token($_pdo, $_accessToken);
        if (!$isLoggedIn) $this->tokenModule->u_print_check_user_token($isLoggedIn);
        return $isLoggedIn;
    }
    ////Util -> Is In Project
    private function isInProject($_pdo, $_accessToken) {
        $isInProject = $this->tokenModule->u_check_project_token($_pdo, $_accessToken);
        if (!$isInProject) $this->tokenModule->u_print_check_project_token($isInProject);
        return $isInProject;
    }
    //User ----------------------------------------------------------------
    //U
    //U -> Register
    function u_register($_Username, $_Userpass, $_Email) {
        $this->userModule->u_register($this->pdo, $_Username, $_Userpass, $_Email);
    }
    //U -> Confirm Account
    function u_confirm_account($_UserId, $_userCode) {
        $this->userModule->u_confirm_account($this->pdo, $_UserId, $_userCode);
    }
    //U -> Login
    function u_login($_Username, $_Userpass) {
        $this->tokenModule->u_create_user_token($this->pdo, $_Username, $_Userpass);
    }
    //U -> Logout
    function u_logout($_accessToken) {
        if ($this->isLoggedIn($this->pdo, $_accessToken)) $this->u_expire_user_token($this->pdo, $_accessToken);
    }
    //U -> Change username
    function u_change_username($_accessToken, $_userId, $_Userpass, $_NewUsername) {
        if ($this->isLoggedIn($this->pdo, $_accessToken)) $this->userModule->u_change_username($this->pdo, $_userId, $_Userpass, $_NewUsername);
    }
    //U -> Change password
    function u_change_password($_accessToken, $_userId, $_Userpass, $_NewUserpass) {
        if ($this->isLoggedIn($this->pdo, $_accessToken)) $this->userModule->u_change_password($this->pdo, $_userId, $_Userpass, $_NewUserpass);
    }
    //U -> Change email
    function u_change_email($_accessToken, $_userId, $_Userpass, $_NewEmail) {
        if ($this->isLoggedIn($this->pdo, $_accessToken)) $this->userModule->u_change_email($this->pdo, $_userId, $_Userpass, $_NewEmail);
    }
    //Project ----------------------------------------------------------------
    //U -> Create Project
    function u_create_project($_accessToken, $_Projname, $_Summary, $_Description, $_Projpass) {
        $userId = $this->isLoggedIn($this->pdo, $_accessToken);
        if ($userId) $this->projectModule->u_create_project($this->pdo, $userId, $_Projname, $_Summary, $_Description, $_Projpass);
    }
    //U -> Open project
    function u_open_project($_accessToken, $_projectId, $_Projpass) {
        $userId = $this->isLoggedIn($this->pdo, $_accessToken);
        $this->tokenModule->u_create_project_token($this->pdo, $_accessToken, $userId, $_projectId, $_Projpass);
    }
	//U -> Close project
    function u_close_project($_accessToken) {
        $userId = $this->isLoggedIn($this->pdo, $_accessToken);
        $this->tokenModule->u_expire_project_token($this->pdo, $_accessToken);
    }
    //U -> Update Project
    function u_update_project($_accessToken, $_Projpass, $_Projname, $_Summary, $_Description, $_NewProjpass) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->projectModule->u_update_project($this->pdo, $projectId, $_Projpass, $_Projname, $_Summary, $_Description, $_NewProjpass);
    }
    //U -> Delete Project
    function u_delete_project($_accessToken, $_Projpass) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->projectModule->u_delete_project($this->pdo, $projectId, $_Projpass);
    }
    //U -> View Projects
    function u_view_projects($_accessToken) {
        $userId = $this->isLoggedIn($this->pdo, $_accessToken);
        if ($userId) $this->projectModule->u_view_projects($this->pdo, $userId);
    }
    //U -> View Project
    function u_view_project($_accessToken, $_projectId) {
        $userId = $this->isLoggedIn($this->pdo, $_accessToken);
        if ($userId) $this->projectModule->u_view_project($this->pdo, $userId, $_projectId);
    }
    //World Building Concept ----------------------------------------------------------------
    //WB
    //WB -> Character --------------------------------
    //WB -> Create Character
    function wb_create_character($_accessToken, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth, $_JungModel, $_OCEANModel, $_Ego, $_Complexes, $_Persona, $_Anima, $_Shadow, $_Self, $_PsychicQuirks, $_PhysicQuirks) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbCharacterModule->wb_create_character($this->pdo, $projectId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth, $_JungModel, $_OCEANModel, $_Ego, $_Complexes, $_Persona, $_Anima, $_Shadow, $_Self, $_PsychicQuirks, $_PhysicQuirks);   
    }
    //WB -> Update Character
    function wb_update_character($_accessToken, $_wbId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth, $_JungModel, $_OCEANModel, $_Ego, $_Complexes, $_Persona, $_Anima, $_Shadow, $_Self, $_PsychicQuirks, $_PhysicQuirks) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbCharacterModule->wb_update_character($this->pdo, $_wbId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth, $_JungModel, $_OCEANModel, $_Ego, $_Complexes, $_Persona, $_Anima, $_Shadow, $_Self, $_PsychicQuirks, $_PhysicQuirks);
    }
    //WB -> Delete Character
    function wb_delete_character($_accessToken, $_Projpass, $_wbId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbCharacterModule->wb_delete_character($this->pdo, $_Projpass, $projectId, $_wbId);
    }
    //WB -> View Characters
    function wb_view_characters($_accessToken) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbCharacterModule->wb_view_characters($this->pdo, $projectId);
    }
    //WB -> View Character
    function wb_view_character($_accessToken, $_wbId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbCharacterModule->wb_view_character($this->pdo, $_wbId);
    }
    //WB -> Location --------------------------------
    //WB -> Create Location
    function wb_create_location($_accessToken, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbLocationModule->wb_create_location($this->pdo, $projectId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth);
    }
    //WB -> Update Location
    function wb_update_location($_accessToken, $_wbId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbLocationModule->wb_update_location($this->pdo, $_wbId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth);
    }
    //WB -> Delete Location
    function wb_delete_location($_accessToken, $_Projpass, $_wbId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbLocationModule->wb_delete_location($this->pdo, $_Projpass, $projectId, $_wbId);
    }
    //WB -> View Locations
    function wb_view_locations($_accessToken) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbLocationModule->wb_view_locations($this->pdo, $projectId);
    }
    //WB -> View Location
    function wb_view_location($_accessToken, $_wbId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbLocationModule->wb_view_location($this->pdo, $_wbId);
    }
    //WB -> Object --------------------------------
    //WB -> Create Object
    function wb_create_object($_accessToken, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbObjectModule->wb_create_object($this->pdo, $projectId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth);
    }
    //WB -> Update Object
    function wb_update_object($_accessToken, $_wbId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbObjectModule->wb_update_object($this->pdo, $_wbId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth);
    }
    //WB -> Delete Object
    function wb_delete_object($_accessToken, $_Projpass, $_wbId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbObjectModule->wb_delete_object($this->pdo, $_Projpass, $projectId, $_wbId);
    }
    //WB -> View Objects
    function wb_view_objects($_accessToken) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbObjectModule->wb_view_objects($this->pdo, $projectId);
    }
    //WB -> View Object
    function wb_view_object($_accessToken, $_wbId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbObjectModule->wb_view_object($this->pdo, $_wbId);
    }
    //WB -> Object --------------------------------
    //WB -> Create Metaphysic
    function wb_create_metaphysic($_accessToken, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbMetaphysicsModule->wb_create_metaphysic($this->pdo, $projectId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth);
    }
    //WB -> Update Metaphysic
    function wb_update_metaphysic($_accessToken, $_wbId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbMetaphysicsModule->wb_update_metaphysic($this->pdo, $_wbId, $_Concept, $_ReasonOfConcept, $_Title, $_Summary, $_Description, $_Cause, $_Purpose, $_Myth);
    }
    //WB -> Delete Metaphysic
    function wb_delete_metaphysic($_accessToken, $_Projpass, $_wbId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbMetaphysicsModule->wb_delete_metaphysic($this->pdo, $_Projpass, $projectId, $_wbId);
    }
    //WB -> View Metaphysics
    function wb_view_metaphysics($_accessToken) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbMetaphysicsModule->wb_view_metaphysics($this->pdo, $projectId);
    }
    //WB -> View Metaphysic
    function wb_view_metaphysic($_accessToken, $_wbId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbMetaphysicsModule->wb_view_metaphysic($this->pdo, $_wbId);
    }
    //WB -> General --------------------------------
    //WB -> Get WB Id By Name
    function wb_getwbid_byname($_accessToken, $_Title) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->wbGeneralModule->wb_getwbid_byname($this->pdo, $projectId, $_Title);   
    }
    //Timeline ----------------------------------------------------------------
    //S -> Create Timeline
    function s_create_timeline($_accessToken, $_Timename, $_Summary, $_Description) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->timelineModule->s_create_timeline($this->pdo, $projectId, $_Timename, $_Summary, $_Description);
    }
    //S -> Update Timeline
    function s_update_timeline($_accessToken, $_timelineId, $_Timename, $_Summary, $_Description) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->timelineModule->s_update_timeline($this->pdo, $projectId, $_timelineId, $_Timename, $_Summary, $_Description);
    }
    //S -> Delete Timeline
    function s_delete_timeline($_accessToken, $_timelineId, $_Projpass) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->timelineModule->s_delete_timeline($this->pdo, $projectId, $_timelineId, $_Projpass);
    }
    //S -> View Timelines
    function s_view_timelines($_accessToken) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->timelineModule->s_view_timelines($this->pdo, $projectId);
    }
    //S -> View Timeline
    function s_view_timeline($_accessToken, $_timelineId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->timelineModule->s_view_timeline($this->pdo, $projectId, $_timelineId);
    }
	//S -> Get Timeline Id By Name
    function s_gettimelineid_byname($_accessToken, $_Timename) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->timelineModule->s_gettimelineid_byname($this->pdo, $projectId, $_Timename);
    }
    //Arc ----------------------------------------------------------------
    //S -> Create Arc
    function s_create_arc($_accessToken, $_timelineId, $_Arcname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->arcModule->s_create_arc($this->pdo, $projectId, $_timelineId, $_Arcname, $_Summary, $_Description, $_Realtime, $_Screentime);
    }
    //S -> Update Arc
    function s_update_arc($_accessToken, $_arcId, $_Arcname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->arcModule->s_update_arc($this->pdo, $projectId, $_arcId, $_Arcname, $_Summary, $_Description, $_Realtime, $_Screentime);
    }
	//S -> Update Arc Time
    function s_update_arctime($_accessToken, $_arcId, $_Realtime, $_Screentime) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->arcModule->s_update_arctime($this->pdo, $projectId, $_arcId, $_Realtime, $_Screentime);
    }
    //S -> Delete Arc
    function s_delete_arc($_accessToken, $_arcId, $_Projpass) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->arcModule->s_delete_arc($this->pdo, $projectId, $_arcId, $_Projpass);
    }
    //S -> View Arcs
    function s_view_arcs($_accessToken, $_timelineId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->arcModule->s_view_arcs($this->pdo, $projectId, $_timelineId);
    }
    //S -> View Arc
    function s_view_arc($_accessToken, $_arcId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->arcModule->s_view_arc($this->pdo, $projectId, $_arcId);
    }
    //S -> Get Arc Id By Name
    function s_getarcid_byname($_accessToken, $_Arcname) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->arcModule->s_getarcid_byname($this->pdo, $projectId, $_Arcname);
    }
    //Act ----------------------------------------------------------------
    //S -> Create Act
    function s_create_act($_accessToken, $_arcId, $_Actname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actModule->s_create_act($this->pdo, $projectId, $_arcId, $_Actname, $_Summary, $_Description, $_Realtime, $_Screentime);
    }
    //S -> Update Act
    function s_update_act($_accessToken, $_actId, $_Actname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actModule->s_update_act($this->pdo, $projectId, $_actId, $_Actname, $_Summary, $_Description, $_Realtime, $_Screentime);
    }
	//S -> Update Act Time
    function s_update_acttime($_accessToken, $_actId, $_Realtime, $_Screentime) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actModule->s_update_acttime($this->pdo, $projectId, $_actId, $_Realtime, $_Screentime);
    }
    //S -> Delete Act
    function s_delete_act($_accessToken, $_actId, $_Projpass) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actModule->s_delete_act($this->pdo, $projectId, $_actId, $_Projpass);
    }
    //S -> View Acts
    function s_view_acts($_accessToken, $_arcId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actModule->s_view_acts($this->pdo, $projectId, $_arcId);
    }
    //S -> View Act
    function s_view_act($_accessToken, $_actId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actModule->s_view_act($this->pdo, $projectId, $_actId);
    }
    //S -> Get Act Id By Name
    function s_getactid_byname($_accessToken, $_Actname) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actModule->s_getactid_byname($this->pdo, $projectId, $_Actname);
    }
    //Action ----------------------------------------------------------------
    //S -> Create Action
    function s_create_action($_accessToken, $_actId, $_Actionname, $_Summary, $_Description, $_Realtime, $_Screentime, $_WBsIds, $_bookmarksIds, $_argumentsIds) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actionModule->s_create_action($this->pdo, $projectId, $_actId, $_Actionname, $_Summary, $_Description, $_Realtime, $_Screentime, $_WBsIds, $_bookmarksIds, $_argumentsIds);
    }
    //S -> Update Action
    function s_update_action($_accessToken, $_actionId, $_Actionname, $_Summary, $_Description, $_Realtime, $_Screentime) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actionModule->s_update_action($this->pdo, $projectId, $_actionId, $_Actionname, $_Summary, $_Description, $_Realtime, $_Screentime);
    }
	//S -> Update Action Time
    function s_update_actiontime($_accessToken, $_actionId, $_Realtime, $_Screentime) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actionModule->s_update_actiontime($this->pdo, $projectId, $_actionId, $_Realtime, $_Screentime);
    }
    //S -> Delete Action
    function s_delete_action($_accessToken, $_actionId, $_Projpass) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actionModule->s_delete_action($this->pdo, $projectId, $_actionId, $_Projpass);
    }
    //S -> View Actions
    function s_view_actions($_accessToken, $_actId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actionModule->s_view_actions($this->pdo, $projectId, $_actId);
    }
    //S -> View Action
    function s_view_action($_accessToken, $_actionId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actionModule->s_view_action($this->pdo, $projectId, $_actionId);
    }
    //S -> Get Action Id By Name
    function s_getactionid_byname($_accessToken, $_Actioname) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->actionModule->s_getactionid_byname($this->pdo, $projectId, $_Actioname);
    }
    //Bookmark ----------------------------------------------------------------
    //B -> Create Bookmark
    function b_create_bookmark($_accessToken, $_Bookmarkname, $_Summary, $_Description) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_create_bookmark($this->pdo, $projectId, $_Bookmarkname, $_Summary, $_Description);
    }
    //B -> Update Bookmark
    function b_update_bookmark($_accessToken, $_bookmarkId, $_Bookmarkname, $_Summary, $_Description) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_update_bookmark($this->pdo, $projectId, $_bookmarkId, $_Bookmarkname, $_Summary, $_Description);
    }
    //B -> Delete Bookmark
    function b_delete_bookmark($_accessToken, $_bookmarkId, $_Projpass) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_delete_bookmark($this->pdo, $projectId, $_bookmarkId, $_Projpass);
    }
    //B -> View Bookmarks
    function b_view_bookmarks($_accessToken) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_view_bookmarks($this->pdo, $projectId);
    }
	//B -> View Bookmark
    function b_view_bookmark($_accessToken, $_bookmarkId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_view_bookmark($this->pdo, $projectId, $_bookmarkId);
    }
    //B -> View Bookmarks In Action
    function b_view_bookmarks_inaction($_accessToken, $_actionId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_view_bookmarks_inaction($this->pdo, $projectId, $_actionId);
    }
    //B -> Relate Bookmark To Action
    function b_relate_bookmark_toaction($_accessToken, $_bookmarkId, $_actionId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_relate_bookmark_toaction($this->pdo, $projectId, $_bookmarkId, $_actionId);
    }
    //B -> Unrelate Bookmark To Action
    function b_unrelate_bookmark_toaction($_accessToken, $_bookmarkId, $_actionId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_unrelate_bookmark_toaction($this->pdo, $projectId, $_bookmarkId, $_actionId);
    }
    //B -> Get Bookmark Id By Name
    function b_getbookmarkid_byname($_accessToken, $_Bookmarkname) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_getbookmarkid_byname($this->pdo, $projectId, $_Bookmarkname);
    }
    //B -> View Bookmarked Actions
    function b_view_bookmarked_actions($_accessToken, $_bookmarkId, $_actionId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_view_bookmarked_actions($this->pdo, $projectId, $_bookmarkId, $_actionId);
    }
	//B -> Analyse Bookmarked Actions
    function b_analyse_bookmarked_actions($_accessToken, $_bookmarkId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->bookmarkModule->b_analyse_bookmarked_actions($this->pdo, $projectId, $_bookmarkId);
    }
    //Argument ----------------------------------------------------------------
    //A -> Create Argument
    function a_create_argument($_accessToken, $_Id_Evoker, $_Id_Target, $_Argumentname, $_Argument) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->argumentModule->a_create_argument($this->pdo, $_Id_Evoker, $_Id_Target, $_Argumentname, $_Argument);
    }
    //A -> Update Argument
    function a_update_argument($_accessToken, $_Id_Evoker, $_Id_Target, $_Argumentname, $_Argument) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->argumentModule->a_update_argument($this->pdo, $_Id_Evoker, $_Id_Target, $_Argumentname, $_Argument);
    }
    //A -> Delete Argument
    function a_delete_argument($_accessToken, $_Projpass) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->argumentModule->a_delete_argument($this->pdo, $_Projpass);
    }
    //A -> View Arguments
    function a_view_arguments($_accessToken) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->argumentModule->a_view_arguments($this->pdo);
    }
    //Relation ----------------------------------------------------------------
    //R -> R -> View WBs
    function r_view_wbs($_accessToken) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->relationModule->r_view_wbs($this->pdo, $projectId);
    }
    //R -> View WBs In Action
    function r_view_wbs_inaction($_accessToken, $_actionId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->relationModule->r_view_wbs_inaction($this->pdo, $projectId, $_actionId);
    }
    //R -> Relate WB To Action
    function r_relate_wb_toaction($_accessToken, $_wbId, $_actionId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->relationModule->r_relate_wb_toaction($this->pdo, $projectId, $_wbId, $_actionId);
    }
    //R -> Unrelate WB To Action
    function r_unrelate_wb_toaction($_accessToken, $_wbId, $_actionId) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->relationModule->r_unrelate_wb_toaction($this->pdo, $projectId, $_wbId, $_actionId);
    }
	//R -> Analyse WBs
    function r_analyse_wbs($_accessToken, $_wbsIds) {
        $projectId = $this->isInProject($this->pdo, $_accessToken);
        if ($projectId) $this->relationModule->r_analyse_wbs($this->pdo, $projectId, $_wbsIds);
    }
}
?>