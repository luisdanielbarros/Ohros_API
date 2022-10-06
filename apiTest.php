<?php 
    //Dependencies
    require "dbConnection.php";
    $conn = new databaseConnection();

    //Variables
    ////Dynamic variable
    $step = 0;

    //Users
    ////Dynamic variable
    $userId = 1;
    $Username = 'John Doe';
    $Password = 'johndoe';
    $Email = 'ohrosteam@gmail.com';
    ////Dynamic variable
    $confirmationCode = '7f59302aa4411338cdd09b8308722e0dbd5170e5ed24b5ec4d4a4dab1b09cc7ab3d88107ab32b8591566f9b9f69328ed35958e1404d9ffd9e747b7ceb3b75451722ffc9f9c8c27a33d1acf4e834f36dcafd572265246cb569dd2a24f131efe68510ce335461c271d380ba502269dabaab224cc4b6cb63bc8394136d618305fc2';
    ////Dynamic variable
    $accessToken = 'd3340ae43fa703345d7b09890e946bffe72fdf270d7a03a37c0d6fb4cb5a36bd453aa7dff0a9a50b529ca0d5c1a8be8282fb62a7ac7b70c3d042d034dcc3ef51cd60f993b3ce21a0d69869e81aa3e5bee87c800b3d32f33e42d7b7c8875805fc3a57e2cd3c561962bf229bcfb79250c36307fdb677ccfe528a857948113b73e4';
    
    //Projects
    ////Dynamic variable
    $projectId = 1;
    $projectName = 'Lifelane';
    $projectPassword = 'lifelane';
    
    //Time
    ////Timelines
    $timelineId = 1;
    ////Arcs
    $arcTheCallingId = 1;
    $arcWantedId = 2;
    $arcFriendsId = 2;
    $arcTragedyId = 4;
    $arcTheGodsThatWalkedId = 5;
    $arcALesserEvil = 6;
    ////Acts
    $actIntroductionId = 1;
    $actEveryoneDiesSomedayId = 13;
    $actAllInId = 14;
    $actYouTooId = 15;
    $actSellYourSoulId = 16;
    $actTheEndId = 17;
    $actOminousDreamId = 18;
    $actSoulBoundId = 19;
    $actTheBaredevilId = 20;
    $actLifeFindsAWayId = 21;
    $actHuntTheDeadId = 22;
    $actACertainWordId = 23;
    $actTheThingId = 24;
    $actAlienBridgesId = 25;
    $actBabelId = 26;
    $actDifferencesId = 27;
    $actAxisMundiId = 28;
    $actTragedyQMId = 29;
    
    //WBs
    ////Characters
    $samuelLenId = 1;
    $marieWalshId = 2;
    $joanJId = 3;
    $chikaSunsonId = 4;
    $isabellaAellaId = 5;
    $theYellowBeastId = 6;
    $merchantOfDeathId = 7;
    $animaMundiId = 8;
    $theThingId = 9;
    ////Locations
    $cityDeadendId = 10;

    //The Meat
    $conn->Connect();
    //Account
    if ($step == 0) {
        $conn->u_register($Username, $Password, $Email);
        exit;
    }
    else if ($step == 1) {
        $conn->u_confirm_account($userId, $confirmationCode);
        $conn->u_login($Username, $Password);
    }
    else if ($step == 2) {
        //Project
        $conn->u_create_project($accessToken, $projectName, 'No summary yet.', 'No description yet.', $projectPassword);
        $conn->u_open_project($accessToken, $projectId, $projectPassword);

        //World-Building
        ////Characters
        $Characters = array();
        //////Samuel Len
        array_push($Characters, array( 
            'Concept' => 'A rebel teenager inspired mainly by the Jesus of Suburbia music video by the Green Day, rage incarnate, the embodiment of will to power however with a tint of empathy and sense of justice, the typical herculean hero of a japanese shonen but tainted with all those darker tones of life we often see in modernity.',
            'reasonOfConcept' => 'There is in modernity a sensation that one\'s powerless and irrelevant, constrained by an artifical technological system that crunches the organic nature of the individual into arbitrary metrics and soulless tasks. The inability of modern man to engage in what Kaczynski described as the power process usually reflects in a pitiful, unconscious want for rebellion, this character seeks to bring forth empathy and interest by expressing this collectivized, repressed, tortured will to be, not in the pathetic, popular, good-christian/nihilistic sense but in the total opposite of that, by displaying a will to power seemingly capable of boiling seas. Here the dark principle of the shadow overlaps with the heroic quest for golden goose, perhaps an argument is made that chaos justifies itself. As can be supposed, the erotic principle is contained with this character.',
            'WBName' => 'Samuel Len',
            'Summary' => 'Broken home guy, raised in a wasteland, emigrated into a city to start a new life, story starts a few weeks after he settled down in an worn-out apartment with a crappy job.',
            'Description' => 'Born in a town noone cared to name, no outsider either cared or dared to map it, home broken, raised by the streets to the hymn of imminent destruction of old school rock, if seen justice only appeared at vigilante\'s hands. Reaching your teenage years earned you an award for street-smarts and strong punches. Best keep those dear to you at arm\'s length and burn the rest of the world, lest it manage to get your back. Left his hometown around his 20s to "start something" wherever the road out took him.',
            'Cause' => 'Tough love at home, raised rough on the streets, learned what a man\'s gotta learn to survive in a land where all laws were written by someone\'s blood.',
            'Purpose' => 'To find a purpose in life.',
            'Myth' => 'The trickster-hero myth. The man who ate the world.',
            'oceanModel' => '75;25;75;37;62;',
            'jungModel' => '75;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        //////Marie Walsh
        array_push($Characters, array( 
            'Concept' => 'An hard-working top student at a priviledged university and overlooked part-time night-shift worker who grinds each day \"until her nails fall off\", inspired by the korean novel Seo-Young, My Daughter. A person solely commanded by her sense of responsibility and justice, an idealist who would consider it a fair death to martyr her life away for a few words. A bookworm who\'s struggled all her life for an ounce of power to mend this broken world, and once she gets it (later in the story) struggles not to be changed by it into the greedy, tyranical evil she swore she\'d erase. Archetypally, she represents the intellect, the logos, gnosis, and is endangered by all the immature (however justified) emotions that surface, like the typical scholar who\'s built his knowledge at the sake of wisdom, a head troubled by a vortex of emotions, a developed mind within a body too frail to act on them, layers of cemented agony laid on the sun for two decades.',
            'reasonOfConcept' => 'While Samuel Len seeks to answer the problem of lack of practical freedom in today\'s society from a disagreeable perspective, Marie represents an agreeable and cooperative attempt to arrive at a solution. In her the salient aspects are the over-development of the intellect in modern humans at the cost of its physical counterpart, her trust in the "system" and other people contrasts Samuel\'s interrogating demeanor. In all the aspects that she and Samuel contradict each other a somewhat compensatory middle ground is reached, underneath which lies in an implicit manner a rich philosophical debate, one of those that can never be definitively answered, rather concluded at the whim of personal preference.',
            'WBName' => 'Marie Walsh',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
            'oceanModel' => '50;50;50;50;50;',
            'jungModel' => '50;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        //////Joan J
        array_push($Characters, array( 
            'Concept' => 'Once a God existed that had everything imaginable at a whim, he had all things but one, he could not understand those pity humans that fought so fervishly for their own microscopic endeavours. So he made a pact with the prince of an ancient kingdom to trade everything that they possess with one another, from material goods to advocating his own immortality. He by his own will erased his own memories and suppressed his divine powers, he ran away from the castle onto the wilderness and there waited inside a cave for thousands of years, he was to come out again sometime in the 21st century. This is Joshua, an amnesiac trope. He wonders why he can speak to animals and hear the sounds of the stars talking to each other, he doesn\'t know why or how he knows what\'s behind the curtain of every horizon, he doesn\'t knows what he knows or is, and so he walks the lands looking to rediscover his own identity, in the end unknowningly transforming his journey into a tragedy by going full circle, before he realized it he was happy, passionate and fulfilled yet now he\'s numb as all Gods are.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'Joan J(?)',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
            'oceanModel' => '50;50;50;50;50;',
            'jungModel' => '50;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        //////Chika Sunson
        array_push($Characters, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'Chika Sunson',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
            'oceanModel' => '50;50;50;50;50;',
            'jungModel' => '50;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        //////Isabella Aella
        array_push($Characters, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'Isabella Aella',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
            'oceanModel' => '50;50;50;50;50;',
            'jungModel' => '50;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        //////The Yellow Beast
        array_push($Characters, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'The Yellow Beast',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
            'oceanModel' => '50;50;50;50;50;',
            'jungModel' => '50;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        //////Merchant of Death
        array_push($Characters, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'Merchant of Death',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
            'oceanModel' => '50;50;50;50;50;',
            'jungModel' => '50;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        //////Anima Mundi
        array_push($Characters, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'Anima Mundi',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
            'oceanModel' => '50;50;50;50;50;',
            'jungModel' => '50;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        //////The Thing
        array_push($Characters, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'The Thing',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
            'oceanModel' => '50;50;50;50;50;',
            'jungModel' => '50;50;50;50;50;',
            'Ego' => 'No ego yet.',
            'Complexes' => 'No complexes yet.',
            'Persona' => 'No persona yet.',
            'Anima' => 'No anima yet.',
            'Shadow' => 'No shadow yet.',
            'Self' => 'No self yet.',
            'psychicQuirks' => '',
            'physicQuirks' => ''
        ));
        foreach ($Characters as &$Character) {
            $conn->wb_create_character($accessToken, $Character['Concept'], $Character['reasonOfConcept'], 
            $Character['WBName'], $Character['Summary'], $Character['Description'], 
            $Character['Cause'], $Character['Purpose'], $Character['Myth'], 
            $Character['oceanModel'], $Character['jungModel'], $Character['Ego'], $Character['Complexes'], 
            $Character['Persona'], $Character['Anima'], $Character['Shadow'], $Character['Self'], 
            $Character['psychicQuirks'], $Character['physicQuirks']);
        }
        $Characters = null;

        ////Locations
        $Locations = array();
        //////City, Deadend
        array_push($Locations, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'City, Deadend',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
        ));
        //////City, Main Plaza
        array_push($Locations, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'City, Main Plaza',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
        ));
        //////City, BonaVista Market
        array_push($Locations, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'City, BonaVista Market',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
        ));
        //////City, Metro Station A
        array_push($Locations, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'City, Metro Station A',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
        ));
        //////City, Uni Campus
        array_push($Locations, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'City, Uni Campus',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
        ));
        foreach ($Locations as &$Location) {
            $conn->wb_create_location($accessToken, $Location['Concept'], $Location['reasonOfConcept'], 
            $Location['WBName'], $Location['Summary'], $Location['Description'], 
            $Location['Cause'], $Location['Purpose'], $Location['Myth']);
        }
        $Locations = null;

        ////Objects
        $Objects = array();
        //////Odd, Metal Junk
        array_push($Objects, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'Odd, Metal Junk',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
        ));
        foreach ($Objects as &$Object) {
            $conn->wb_create_object($accessToken, $Object['Concept'], $Object['reasonOfConcept'], 
            $Object['WBName'], $Object['Summary'], $Object['Description'], 
            $Object['Cause'], $Object['Purpose'], $Object['Myth']);
        }
        $Objects = null;

        ////Metaphysics
        $Metaphysics = array();
        //////Word Sunson
        array_push($Metaphysics, array( 
            'Concept' => 'No concept yet.',
            'reasonOfConcept' => 'No reason of concept yet.',
            'WBName' => 'Word Sunson',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Cause' => 'No cause yet.',
            'Purpose' => 'No purpose yet.',
            'Myth' => 'No myth yet.',
        ));
        foreach ($Metaphysics as &$Metaphysic) {
            $conn->wb_create_metaphysic($accessToken, $Metaphysic['Concept'], $Metaphysic['reasonOfConcept'], 
            $Metaphysic['WBName'], $Metaphysic['Summary'], $Metaphysic['Description'], 
            $Metaphysic['Cause'], $Metaphysic['Purpose'], $Metaphysic['Myth']);
        }
        $Metaphysics = null;

        //Time
        ////Timelines
        $Timelines = array();
        //////Main
        array_push($Timelines, array( 
            'Title' => 'No title yet.',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.'
        ));
        foreach ($Timelines as &$Timeline) {
            $conn->s_create_timeline($accessToken, $Timeline['Title'], $Timeline['Summary'], $Timeline['Description']);
        }
        $Timelines = null;

        ////Arcs
        $Arcs = array();
        $Time = 1;
        //////The Calling
        array_push($Arcs, array( 
            'Id_Timeline' => $timelineId,
            'Title' => 'The Calling',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        //////Wanted
        array_push($Arcs, array( 
            'Id_Timeline' => $timelineId,
            'Title' => 'Wanted',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        //////Friends
        array_push($Arcs, array( 
            'Id_Timeline' => $timelineId,
            'Title' => 'Friends',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        //////Tragedy
        array_push($Arcs, array( 
            'Id_Timeline' => $timelineId,
            'Title' => 'Tragedy',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        //////The Gods that Walked
        array_push($Arcs, array( 
            'Id_Timeline' => $timelineId,
            'Title' => 'The Gods that Walked',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        //////A Lesser Evil
        array_push($Arcs, array( 
            'Id_Timeline' => $timelineId,
            'Title' => 'A Lesser Evil',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        foreach ($Arcs as &$Arc) {
            $conn->s_create_arc($accessToken, $Arc['Id_Timeline'], $Arc['Title'], $Arc['Summary'], $Arc['Description'], $Arc['Realtime'], $Arc['Screentime']);
        }
        $Arcs = null;

        ////Acts
        $Acts = array();
        $Time = 1;
        //////Arc The Calling
        ////////Arc The Calling -> Act Introduction
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Introduction',
            'Summary' => 'No summary yet.',
            'Description' => 'Introducing the audience to the main predicament.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act The Face of Evil
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'The Face of Evil',
            'Summary' => 'No summary yet.',
            'Description' => 'The moment the hero first faces evil and, unable to ignore it, chooses to step into the hellmouth.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act Survival
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Survival',
            'Summary' => 'No summary yet.',
            'Description' => 'Development of the main goal of surviving in an post-apocalyptic world.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act Pairs
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Pairs',
            'Summary' => 'No summary yet.',
            'Description' => 'Acceptance of the basic fact that allies and human relationships are necessary for survival in a pure anarchy.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act The Calling
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'The Calling',
            'Summary' => 'No summary yet.',
            'Description' => 'A mysterious voice is broadcasted everywhere at once setting up a meeting place at a specific time, it promises to rescue everyone it finds "back to the normal world".',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act Arguments
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Arguments',
            'Summary' => 'No summary yet.',
            'Description' => 'Heated arguments discussing whether to trust the broadcast or if it\'s just the most obvious ambush in history.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act Call to Action
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Call to Action',
            'Summary' => 'No summary yet.',
            'Description' => 'MC and Marie choose to go for it with a small group of spur-of-the-moment allies.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act Obstacles
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Obstacles',
            'Summary' => 'No summary yet.',
            'Description' => 'Group attempts to avoid obstacles and faces dangers in the form of delinquents, criminals and monsters.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act Station
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Station',
            'Summary' => 'No summary yet.',
            'Description' => 'Group board a train at a strange metro station submersed in a other-wordly atmosphere.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Calling -> Act Ride
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Ride',
            'Summary' => 'No summary yet.',
            'Description' => 'Group faces more dangers now in the form of "magic" as well, seeing a "magic-user" in action.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));      
        ////////Arc The Calling -> Act The Promised Place
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'The Promised Place',
            'Summary' => 'No summary yet.',
            'Description' => 'The broadcaster was honest, but the meeting was still ambushed anyways.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));       
        ////////Arc The Calling -> Act Wake Up
        array_push($Acts, array( 
            'Id_Arc' => $arcTheCallingId,
            'Title' => 'Wake Up',
            'Summary' => 'No summary yet.',
            'Description' => 'MC wakes up in his bedroom, he was saved. When he blinks his eyes he\'s back in the "dangerous side". This time with a bounty on his head. Everyone he met before wants him dead.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        //////Arc Tragedy
        $Time = 1;
        ////////Arc Tragedy -> Act Everyone Dies Someday
        array_push($Acts, array( 
            'Id_Arc' => $arcTragedyId,
            'Title' => 'Everyone Dies Someday',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc Tragedy -> Act All-in
        array_push($Acts, array( 
            'Id_Arc' => $arcTragedyId,
            'Title' => 'All-in',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc Tragedy -> Act You Too
        array_push($Acts, array( 
            'Id_Arc' => $arcTragedyId,
            'Title' => 'You Too',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc Tragedy -> Act Sell Your Soul
        array_push($Acts, array( 
            'Id_Arc' => $arcTragedyId,
            'Title' => 'Sell Your Soul',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc Tragedy -> The End
        array_push($Acts, array( 
            'Id_Arc' => $arcTragedyId,
            'Title' => 'The End',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        //////Arc The Gods that Walked
        $Time = 1;
        ////////Arc The Gods that Walked -> Act Ominous Dream
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Ominous Dream',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act Soul-Bound
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Soul-Bound',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act The Baredevil
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'The Baredevil',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act Life Finds A Way
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Life Finds A Way',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act Hunt The Dead
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Hunt The Dead',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act A Certain Word
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'A Certain Word',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act The Thing
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'The Thing',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act Alien Bridges
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Alien Bridges',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act Babel
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Babel',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act Differences
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Differences',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act Axis Mundi
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Axis Mundi',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        ////////Arc The Gods that Walked -> Act Tragedy(?)
        array_push($Acts, array( 
            'Id_Arc' => $arcTheGodsThatWalkedId,
            'Title' => 'Tragedy(?)',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++
        ));
        foreach ($Acts as &$Act) {
            $conn->s_create_act($accessToken, $Act['Id_Arc'], $Act['Title'], $Act['Summary'], $Act['Description'], $Act['Realtime'], $Act['Screentime']);
        }
        $Acts = null;

        ////Actions
        $Actions = array();
        $Time = 1;
        //////Act Introduction
        ////////Arc The Calling -> Act Introduction -> Action Mysterious conversation
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'Mysterious conversation',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($isabellaAellaId, $theYellowBeastId)
        ));
        ////////Arc The Calling -> Act Introduction -> Action Introduction
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'Introduction to a normal, urban life',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId)
        ));
        ////////Arc The Calling -> Act Introduction -> Action Questionnaire
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'Questionnaire',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array()
        ));
        ////////Arc The Calling -> Act Introduction -> Action MC wakes up
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'MC wakes up',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $cityDeadendId)
        ));
        ////////Arc The Calling -> Act Introduction -> Action MC is confused
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'MC is confused',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $cityDeadendId)
        ));
        ////////Arc The Calling -> Act Introduction -> Action MC comments on the irregular landscape
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'MC comments on the irregular landscape',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $cityDeadendId)
        ));
        ////////Arc The Calling -> Act Introduction -> Action MC walks through
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'MC walks through',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $cityDeadendId)
        ));
        ////////Arc The Calling -> Act Introduction -> Action MC watches an out-of-this-world news broadcast
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'MC watches an out-of-this-world news broadcast',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId)
        ));
        ////////Arc The Calling -> Act Introduction -> Action MC walks for what feels like hours
        array_push($Actions, array( 
            'Id_Act' => $actIntroductionId,
            'Title' => 'MC walks for what feels like hours',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $cityDeadendId)
        ));
        ////////Arc The Gods that Walked -> Act Ominous Dream -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actOminousDreamId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $animaMundiId)
        ));
        ////////Arc The Gods that Walked -> Act Soul-Bound -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actSoulBoundId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $chikaSunsonId)
        ));
        ////////Arc The Gods that Walked -> Act The Baredevil -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actTheBaredevilId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $chikaSunsonId)
        ));
        ////////Arc The Gods that Walked -> Act Life Finds A Way -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actLifeFindsAWayId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($marieWalshId, $joanJId)
        ));
        ////////Arc The Gods that Walked -> Act Hunt The Dead -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actHuntTheDeadId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $chikaSunsonId)
        ));
        ////////Arc The Gods that Walked -> Act A Certain Word -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actACertainWordId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($marieWalshId, $joanJId)
        ));
        ////////Arc The Gods that Walked -> Act The Thing -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actTheThingId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $chikaSunsonId)
        ));
        ////////Arc The Gods that Walked -> Act Alien Bridges -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actAlienBridgesId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $chikaSunsonId)
        ));
        ////////Arc The Gods that Walked -> Act Babel -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actBabelId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($marieWalshId, $joanJId)
        ));
        ////////Arc The Gods that Walked -> Act Differences -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actDifferencesId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $chikaSunsonId)
        ));
        ////////Arc The Gods that Walked -> Act Axis Mundi -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actAxisMundiId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $marieWalshId, $joanJId, $chikaSunsonId)
        ));
        ////////Arc The Gods that Walked -> Act Tragedy(?) -> Full Action
        array_push($Actions, array( 
            'Id_Act' => $actTragedyQMId,
            'Title' => 'Full Action',
            'Summary' => 'No summary yet.',
            'Description' => 'No description yet.',
            'Realtime' => $Time,
            'Screentime' => $Time++,
            'WBs' => array($samuelLenId, $marieWalshId, $joanJId, $chikaSunsonId)
        ));
        $actionId = 1;
        foreach ($Actions as &$Action) {
            $conn->s_create_action($accessToken, $Action['Id_Act'], $Action['Title'], $Action['Summary'], $Action['Description'], $Action['Realtime'], $Action['Screentime'], '', '', '');
            foreach ($Action['WBs'] as &$WB) {
                $conn->r_relate_wb_toaction($accessToken, $WB, $actionId);
            }
            $actionId++;

        }
        $Actions = null;

        ////////Arc Tragedy -> Act Everyone Dies Someday -> Action ???

}


    $conn->Disconnect();
?>