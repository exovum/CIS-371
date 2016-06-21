<?php

//
// Connect to the database so it can be queried and accessed.
//
function connect_DB() {
    //echo "Connecting...";
    $connection = new mysqli("127.0.0.1", "stevecal", "stevecal9889");

    // Complain if the connection fails. 
    if (!$connection || $connection->connect_error) {
        die('Unable to connect to database [' . $conection->connect_error . ']');
    }
    if (!$connection->select_db("stevecal")) {
        die ("Unable to seelct database: [" . $connection->error . "]");
    }

    //echo "Connected!" . "<br>";
    return $connection;
}

//
//
// Create the table for the accounts users can use to sign in to access
// various information.
function create_accounts_DB($c) {
    $sql = "CREATE TABLE IF NOT EXISTS story_accounts( ".
        "account_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,".
        "name VARCHAR(50) NOT NULL,".
        "password VARCHAR(50) NOT NULL,".
        "superuser BOOLEAN);";
    $return_val = $c->query($sql);
    //echo "Created table";
    if(!$return_val) {
        die("Could not create the story_accounts table [" . $c->error . "]");
    }
    //$sql = "INSERT IGNORE INTO  friendAccounts (name, password, superuser) VALUES ('testaccount','abcd123','1');";
    //$return_val = $c->query($sql);
    //if(!$return_val) {
    //    die("Could not insert into friendAccounts table [" . $c->error . "]");
    //}
    return $return_val;
            
}

//
// Create the table for storying the general base Story elements
// including a title, a short description, and the longer plot description.
function create_story_DB($c) {
    $sql = "CREATE TABLE IF NOT EXISTS story( ".
        "story_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
        "title VARCHAR (50),".
        "short_desc VARCHAR (50),".
        "long_desc VARCHAR (512),".
        "start_id INT,".
        "curr_id INT,".
        "FOREIGN KEY (start_id) REFERENCES event(event_id),";
        "FOREIGN KEY (curr_id) REFERENCES event(event_id));";
    $return_val = $c->query($sql);
    //echo "Created table";
    if(!$return_val) {
        die("Could not create the story table [" . $c->error . "]");
    }
    //$sql = "INSERT IGNORE INTO  friendAccounts (name, password, superuser) VALUES ('testaccount','abcd123','1');";
    //$return_val = $c->query($sql);
    //if(!$return_val) {
    //    die("Could not insert into friendAccounts table [" . $c->error . "]");
    //}


    //read_from_file($c, "frienddata.txt");

    return $return_val;
            
}

//
// Create the table for the various Events aspects for each story.
function create_event_DB($c) {
    $sql = "CREATE TABLE IF NOT EXISTS event( ".
		"event_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
		"description VARCHAR (128),".
		"result VARCHAR (256),".
		"choice_a INT NOT NULL,".
		"choice_b INT NOT NULL,".
		"FOREIGN KEY (choice_a) REFERENCES event(event_id),".
		"FOREIGN KEY (choice_b) REFERENCES event(event_id)); "
	;
    $return_val = $c->query($sql);
    //echo "Created table";
    if(!$return_val) {
        die("Could not create the event table [" . $c->error . "]");
    }
    return $return_val;
}

//
// Creates the table for connecting the Story and Event tables
// so each story_id has a 'list' of events tied to it.
function create_story_event_DB($c) {
    $sql = "CREATE TABLE IF NOT EXISTS story_event ( ".
		"story_id INT NOT NULL,".
		"event_id INT NOT NULL,".
		"FOREIGN KEY (story_id) REFERENCES story(story_id),".
		"FOREIGN KEY (event_id) REFERENCES event(event_id),".
		"PRIMARY KEY (story_id, event_id));"
	;
    $return_val = $c->query($sql);
    //echo "Created table";
    if(!$return_val) {
        die("Could not create the story_event table [" . $c->error . "]");
    }
    return $return_val;
}

// Creates the table for keeping track of the votes for a choice_id
// for a story_id and event_id pair.
function create_event_votes_DB($c) {
    $sql = "CREATE TABLE IF NOT EXISTS event_votes (".
        "story_id INT NOT NULL,".
        "event_id INT NOT NULL,".
        "choice_id INT NOT NULL,".
        "num_votes INT DEFAULT 0,".
        "FOREIGN KEY (story_id) REFERENCES story(story_id),".
        "FOREIGN KEY (event_id) REFERENCES event(event_id),".
        "FOREIGN KEY (choice_id) REFERENCES event(event_id),".
        "PRIMARY KEY (story_id, event_id, choice_id) );";
    $result = $c->query($sql);
    if(!$result) {
        die("Could not create the event_choices table [" . $c->error . "]");
    }
    return $result;
}

//
// Functions for making additions to the connected database tables.
//

// TODO: Add a new user account 
function add_account($c, $p_name, $p_pass, $p_superuser) {
    //echo "Adding a friend..." . "<br>";
    if(!$p_name) {
        $msgf = "Sorry, you need to include a user name.<br>";
        echo "add_account error: " . $msgf;
        throw new Exception($msgf);
        //return $p_fname;
    }
    if(!$p_pass) {
        $msgl = "Sorry, you need to include a password.<br>";
        echo "add_account error: " . $msgl;
        throw new Exception($msgl);
        //return $p_lname;
    }
    $set_superuser = $p_superuser;
    if(!$p_superuser) {
        $set_superuser = 0; // if not defined, then set as standard user
    }
    $sql = "INSERT IGNORE INTO story_accounts (name, password, superuser) VALUES ('$p_name', '$p_pass', '$set_superuser');";
    $return_val = $c->query($sql);
    if(!$return_val) {
        die("Could not insert into story_accounts table [" . $c->error . "]");
    }
    //echo "Added a new friend!" . "<br>";
    return $return_val;
}

// Add the basis for a new Story to the database
function add_story($c, $title, $short_desc, $long_desc) {
    $msg = "Sorry, try again. The story is missing a ";
    $error = false;
    if(!$title) {
        $msg += "title.";
        $error = true;
    }
    if(!$short_desc) {
        $msg += "short description.";
        $error = true;
    }
    if(!$long_desc) {
        $msg += "long description.";
        $error = true;
    }
    // If there is an error, throw it with the appropriate message.
    if($error) {
        throw new Exception($msg);
    }

    $sql = "INSERT IGNORE INTO  story (title, short_desc, long_desc)".
        " VALUES ('$title', '$short_desc', '$long_desc');";
    $return_val = $c->query($sql);
    if(!$return_val) {
        die("Could not insert into story table [" . $c->error . "]");
    }
    return $return_val;
}

// Add a new Event or format for an Event to the database
function add_event($c, $desc, $result, $choice_a, $choice_b) {
    $msg = "Sorry, try again. The event is missing a ";
    $error = false;
    if(!$desc) {
        $msg += "description.";
        $error = true;
    }
    if(!$result) {
        $msg += "description for the result.";
        $error = true;
    }
    // choice_a and choice_b CAN be null
    // If there is an error, throw it with the appropriate message.
    if($error) {
        throw new Exception($msg);
    }

    $sql = "INSERT IGNORE INTO  event (description, result, choice_a, choice_b)".
        " VALUES ('$desc', '$result', '$choice_a', '$choice_b');";
    $return_val = $c->query($sql);
    if(!$return_val) {
        die("Could not insert into event table [" . $c->error . "]");
    }
    return $return_val;
}

// Add a new story_event element. This is pairing one story with one event. 
// This function selects the Story based on the story title,
// and chooses the Event based on the description.
// To avoid duplicate/multiple story/event selections, see the method
//      add_story_event_by_id($c, $story_id, $event_id).
function add_story_event_by_title($c, $story_title, $event_desc) {
    $msg = "Sorry, try again. The story_event is missing a ";
    $error = false;
    if(!$story_title) {
        $msg += "title.";
        $error = true;
    }
    if(!$event_desc) {
        $msg += "short event description.";
        $error = true;
    }
    // If there is an error, throw it with the appropriate message.
    if($error) {
        throw new Exception($msg);
    }

    $sql = "INSERT IGNORE INTO  story_event (story_id, event_id) ".
       "VALUES (".
       "(SELECT story_id FROM story WHERE title = '$story_title'),".
       "(SELECT event_id FROM event WHERE description = '$event_desc'));";
    $return_val = $c->query($sql);
    if(!$return_val) {
        die("Could not insert into story_event table by title[" . $c->error . "]");
    }
    return $return_val;
}

// Add a new story_event element. This is pairing one story with one event. 
//      add_story_event_by_id($c, $story_id, $event_id).
function add_story_event_by_id($c, $story_id, $event_id) {
    $msg = "Sorry, try again. The story_event is missing a ";
    $error = false;
    if(!$story_id) {
        $msg += "story id.";
        $error = true;
    }
    if(!$event_id) {
        $msg += "event_id.";
        $error = true;
    }
    // If there is an error, throw it with the appropriate message.
    if($error) {
        throw new Exception($msg);
    }

    // Note: Really, probablly don't need the nested queries..
    $sql = "INSERT IGNORE INTO  story_event (story_id, event_id) ".
       "VALUES (".
       "(SELECT story_id FROM story WHERE story_id = '$story_id'),".
       "(SELECT event_id FROM event WHERE event_id = '$event_id'));";
    $return_val = $c->query($sql);
    if(!$return_val) {
        die("Could not insert into story_event table by id [" . $c->error . "]");
    }
    return $return_val;
}

// TODO: update to work for project accounts
function get_all_accounts($c) {
    //echo "Selecting all user accounts...";
    $sql = "select * from story_accounts";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;
}

// Select all of the created Stories.
function get_all_stories($c) {
    //echo "Selecting all stories...";
    $sql = "select * from story";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;
}

// Select all of the created Events.
function get_all_events($c) {
    //echo "Selecting all events...";
    $sql = "select * from event";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;
}

function get_start_event($c, $start_id) {
    //echo "Selecting all events...";
    $sql = "select * from event WHERE event_id = $start_id";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;
}

function get_story_by_id($c, $story_id){
    $sql = "select * from story where story_id = $story_id";
    $result = $c->query($sql);
    // return the result. If null, then result is null as checked
    // by the calling function, which should display appropriate message.
    if(mysqli_num_rows($result) < 1) {
        $result = false;
        return false;
    } else {
        //echo "result is NOT null";
    }
    //echo "</br>";
    return $result;
}
function get_event_by_id($c, $event_id){
    $sql = "select * from event WHERE event_id = $event_id";
    $result = $c->query($sql);
    // return the result. If null, then result is null as checked
    // by the calling function, which should display appropriate message.
    if(mysqli_num_rows($result) < 1) {
        $result = false;
        return false;
    }
    return $result;
}

// Select all of the created story_event pairs.
function get_all_story_events($c) {
    //echo "Selecting all story_events...";
    $sql = "select * from story_event";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;
}

// Select the end_time of the story_event based on storyID and eventID
function get_story_event_endtime($c, $story_id, $event_id) {
    $sql = "SELECT end_time FROM story_event WHERE story_id = $story_id AND event_id = $event_id;";
    $result = $c->query($sql);
    if(!$result) {
        return false;
    }

    foreach ($result as $row) {
        //echo "<li>";
        $keys = "end_time";
        $timestamp = $row[$keys];
        //echo "<h2>timestamp: $timestamp</h2>";
        //echo "</br>";
        $time = strtotime($timestamp);
        //echo "time: $time";
        //echo "</br>";

        return $timestamp;
        /*$formatTime = date('M j, Y H:i:s O', $time);
        $formatTime2 = date("D M d Y H:i:s O", $time);
        //echo "formatTime: $formatTime";
        //echo "</br>";
        //echo "formatTime2: $formatTime2";
        //echo "</br>";
        return $formatTime;
        //echo "</li>";
        */
    } 
}

// Select all of the chosen story_event pairs for given @id
function get_story_event_results($c, $id) {
    $sql = "SELECT event_id FROM story_event WHERE story_id = $id;";
    //$sql = "SELECT result FROM event WHERE event_id = (SELECT event_id FROM story_event WHERE story_id = $id);";
    $result = $c->query($sql);
    if (!$result) {
        //echo "NO RESULT";
        die("Unable to get story_event ids [".$c->error."]");
        //return false;
    }
    $event_results = [];
    foreach($result as $row) {
       //echo "found a row"; 
       $event_id = $row['event_id'];
       $sql_2 = "SELECT result FROM event WHERE event_id = $event_id;";
       $result_2 = $c->query($sql_2);
       if(!$result_2) {
           die("Unable to get results [".$c->error."]");
       }
       //echo "SUCCESS</br>";
       foreach($result_2 as $row_2) {
           $event_result = $row_2['result'] . "</br>";
           //echo "RESULT: " .$event_result;
           array_push($event_results, $event_result);
           //echo "</br>";
       }
    }
    return $event_results;
}

//TODO: add similar functions for reading new story/event info from files.
// This function may not be necessary for friendAccounts,
// but it is left here in case it should be updated to
// add accounts from a text file.

/*function read_from_file_accounts($c, $filename, $username) {

    if(!has_permission($c, $username)) {
        echo "Sorry, you do not have permission to add friends.";
        return false;
    }
    
    //echo "Reading friend data from a file" . "<br>";
    
    $datafile = fopen($filename, "r") or die("Unable to open file for reading");
    //echo fread($datafile, filesize($filename));
    // output one line until end-of-file
    while(!feof($datafile)) {
        $line = fgets($datafile);
        //echo "<h4>debug: line=$line</h4>";
        
        // get first name from the current line
        $token = strtok($line, ",");
        $p_fname = $token;
        // get last name from the current line
        $token = strtok(",");
        $p_lname = $token;
        // get phone number from the current line
        $token = strtok(",");
        $p_phone = $token;
        $new_p_phone = str_replace("-", "", $p_phone);
        // get age from the current line
        $token = strtok(",");
        $p_age = $token;
        //echo "Read: $p_fname::$p_lname::$p_phone/$new_p_phone::$p_age<br>";
        if($p_fname) {
            add_my_friend($c, $username, $p_fname, $p_lname, $new_p_phone, $p_age);
        }
    }
    
    fclose($datafile);

    //echo "Done reading friend data!" . "<br>";
}
* end read_from_file_accounts() 
*/

// Collect all of the accounts info, then return a html table with the data.
function display_accounts($c) {
    //echo "Displaying friends...";
    $result = get_all_accounts($c); 

    // iterate over each record in the result.
    // Each record will be one row in the table, beginning with <tr> 
    echo "<table>";
    echo "<tr><th>User Name</th><th>Superuser</th>";
    foreach ($result as $row) {
        echo "<tr>";
        $keys = array("name", "superuser");
        // iterate over all the columns.  Each column is a <td> element.
        foreach ($keys as $key) {
            echo "<td>" . $row[$key] . "</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>" . "<br>";
    //echo "Done displaying friends!<br>";
}

// Collect all of the stories, then return a html table with the data.
// The display method for this is very basic, showing only title and 
// the short description.
// TODO: mainly used for testing, so update this to display more nicely.
function display_stories_basic($c, $with_links) {
    //echo "Displaying friends...";
    $result = get_all_stories($c); 

    // iterate over each record in the result.
    // Each record will be one row in the table, beginning with <tr> 
    echo "<table id='view_stories'>";
    echo "<tr><th>Story Title</th><th>Short Description</th><th>Current Event</th><th>Voting Ends In</th></tr>";
    foreach ($result as $row) {
        $row_id = $row['story_id'];
        $event_id = $row['curr_id'];
        echo "<tr id='$row_id-$event_id'>";
        echo "<td class='data_story_id' id='data_story_id' style='display:none'>$row_id</td>";
        echo "<td class='data_event_id' id='data_event_id' style='display:none'>$event_id</td>";
        $keys = array("title", "short_desc"); //, "long_desc", );
        // iterate over all the columns.  Each column is a <td> element.
        if(!$with_links) {
            echo "<td class='title'>" . $row['title'] . "</td>";
        }else {
            echo "<td class='title'><a href='story.php?id=$row_id'>".
                $row['title'] ."</a></td>";
        }
        echo "<td class='short_desc'>" . $row['short_desc'] . "</td>";
        //echo "<td class='curr_desc'>" . $row['curr_id'] . "</td>";
        $event_data = get_event_data($c, $row['curr_id']);
        echo "<td class='curr_desc'>" . $event_data['description'] . "</td>";
        $timer_id = "textTimer:".$row_id.":".$event_id;
        //echo "<td class='curr_endtime'><p id='$timer_id'></p></td>";
        echo "<td class='curr_endtime'><p id='defaultCountdown'></p></td>";
        //"textTimer:"+s_id+":"+e_id
        foreach($event_data as $event_item) {
            //echo "<td class='curr_desc'>" . $event_item['description'] . "</td>";
        }
        //foreach ($keys as $key) {
            //echo "<td>" . $row[$key] . "</td>";
        //}

        echo "</tr>\n";
    }
    echo "</table>" . "<br>";
}
// Collect all of the stories, then return a html table with the data.
// TODO: mainly used for testing, so update this to display more nicely.
function display_stories($c) {
    //echo "Displaying friends...";
    $result = get_all_stories($c); 

    // iterate over each record in the result.
    // Each record will be one row in the table, beginning with <tr> 
    echo "<h3>Stories</h3>";
    echo "<table>";
    echo "<tr><th>Story Title</th><th>Short Description</th><th>Long Description</th><th>Start Event</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        $keys = array("title", "short_desc", "long_desc", );
        // iterate over all the columns.  Each column is a <td> element.
        foreach ($keys as $key) {
            echo "<td>" . $row[$key] . "</td>";
        }
        // query and retrieve the 'start event' for the current story row
        echo "<td>";
        //echo "start_id: " . $row['start_id'];
        //$event_result = "";
        $event_result = get_start_event($c, $row['start_id']); 
        echo "<table class='start_event_table'";
        echo "<tr><th>ID</th><th>Description</th><th>Result</th></tr>";
        foreach ($event_result as $event_row) {
            echo "<tr>";
            $event_keys = array("event_id", "description", "result");
            // iterate over all the selected event's columns
            foreach ($event_keys as $event_key) {
                echo "<td>" . $event_row[$event_key] . "</td>";
            }
            echo "</tr>\n";
        }
        echo "</table>";
        echo "</td>";


        echo "</tr>\n";
    }
    echo "</table>" . "<br>";
}

// Collect all of the events, then return a html table with the data.
// TODO: mainly used for testing, so update this to display more nicely.
function display_events($c, $with_id) {
    //echo "Displaying friends...";
    $result = get_all_events($c); 

    // iterate over each record in the result.
    // Each record will be one row in the table, beginning with <tr> 
    echo "<h3>Events</h3>";
    echo "<table><tr>";
    if($with_id){
        echo "<th>ID</th>";

    }
    echo "<th>Description</th><th>Result Text</th><th>Choice A</th><th>Choice B</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        if($with_id) {
            $keys = array("event_id", "description", "result", "choice_a", "choice_b");
        } else {
            $keys = array("description", "result", "choice_a", "choice_b");
        }
    
        // iterate over all the columns.  Each column is a <td> element.  
        foreach ($keys as $key) {
            echo "<td>" . $row[$key] . "</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>" . "<br>";
}


// Collect all of the story_events, then return a html table with the data.
// TODO: mainly used for testing, so update this to display more nicely.
function display_story_events($c) {
    //echo "Displaying friends...";
    $result = get_all_story_events($c); 

    // iterate over each record in the result.
    // Each record will be one row in the table, beginning with <tr> 
    echo "<h3>Story Events</h3>";
    echo "<table>";
    echo "<tr><th>Story ID</th><th>Event ID</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        $keys = array("story_id", "event_id");
        // iterate over all the columns.  Each column is a <td> element.
        foreach ($keys as $key) {
            echo "<td>" . $row[$key] . "</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>" . "<br>";
}

// Displays the chosen event history for a given story ID in a list
function display_story_history($c, $id) {
    // retrieve a 'list' of the the chosen results
    $result = get_story_event_results($c, $id);
    echo "<h3>Full Story</h3>";
    // NOTE: By including the current event in the list of History,
    //      the History will NEVER be empty because the start event
    //      is always set.
    if(!$result)  {
        echo "<p>Your story has just begun!</p>";
    }
    //echo "<ol>";
    foreach ($result as $row) {
        //echo "<li>";
        echo "<p>";
        $keys = "result";
        //echo $row[$keys];
        echo $row;
        echo "</p>";
        //echo "</li>";
    } 
    //echo "</ol></br>";
    echo "</br>";
}

// This function queries the story table for row data with $story_id.
function get_story_data($c, $id) {
    $sql = "SELECT * FROM story WHERE story_id = $id";
    $result = $c->query($sql);
    foreach ($result as $row) {
        return $row;
    }
}

// This function queries the story table for $value with $story_id.
function get_story_data_value($c, $id, $value) {
    $sql = "SELECT $value FROM story WHERE story_id = $id";
    $result = $c->query($sql);
    foreach ($result as $row) {
        return $row[$value];
    }
    /*foreach ($result as $row) {
        echo "<tr>";
        $keys = array("story_id", "event_id");
        // iterate over all the columns.  Each column is a <td> element.
        foreach ($keys as $key) {
            echo "<td>" . $row[$key] . "</td>";
        }
        echo "</tr>\n";
    }
    */
}

// This function queries the event table for row data with $event_id.
function get_event_data($c, $id) {
    $sql = "SELECT * FROM event WHERE event_id = $id";
    $result = $c->query($sql);
    foreach ($result as $row) {
        return $row;
    }
}

// This function returns the descriptions for choice_a and choice_b
function get_event_choices($c, $event_id) {
    $sqla = "SELECT description FROM event WHERE event_id = (SELECT choice_a FROM event WHERE event_id = $event_id);";
    $sqlb = "SELECT description FROM event WHERE event_id = (SELECT choice_b FROM event WHERE event_id = $event_id);";

    $choices = [];
    $resulta = $c->query($sqla);
    // If the query is not successful, add a 'false' element.
    // If the query is successful, add the description to the array.
    if(!$resulta) {
        //array_push($choices, "Nothing for a"); 
        die("Unable to get choices [".$c->error."]");
    } else {
        if($resulta->num_rows > 0) {
            foreach($resulta as $rowa) {
                array_push($choices, $rowa['description']); 
            }
        } else {
            array_push($choices, "This is not much of a choice.");
        }
    }

    $resultb = $c->query($sqlb);
    if(!$resultb) {
        array_push($choices, "Nothing for b");
        die("Unable to get choices [".$c->error."]");
    } else {
        if($resultb->num_rows > 0) {
            foreach($resultb as $rowb) {
                array_push($choices, $rowb['description']); 
            }
        } else {
            array_push($choices, "This is not a good Plan B.");
        }
        //array_push($choices, $resultb['description']); 
    }

    return $choices;

}

function submit_vote($c, $story_id, $event_id, $choice_id) {
    $sql = "INSERT INTO event_votes (story_id, event_id, choice_id, num_votes)". 
    "VALUES ($story_id, $event_id, $choice_id, 1)".
    "ON DUPLICATE KEY UPDATE num_votes=num_votes+1;";
    /*
    $sql = "INSERT INTO event_votes (story_id, event_id, choice_id, num_votes) ".
        "VALUES ($story_id, $event_id, $choice_id, 1) ".
        "ON DUPLICATE KEY UPDATE num_votes=num_votes+1;";
    */
    $result = $c->query($sql);
    if(!$result) {
        die("Unable to submit vote [".$c->error."]");
    }
    return $result;
}

function calculate_votes($c, $story_id, $event_id) {
    $sql = "SELECT choice_id,num_votes FROM event_votes ".
        "WHERE story_id=$story_id AND event_id=$event_id;";
    $result = $c->query($sql);
    if(!$result) {
        die("Unable to view the number of votes: [".$c->error."]");
    }

    $total_votes = 0;
    $votes = [];
    $choice_ids = [];
    $choices = [];
    $voted_options = 0;
    // read and gather all of the vote counts for the choices
    while($row = mysqli_fetch_assoc($result)) {
        //echo $row['choice_id'] . " (" . $row['num_votes'];
        array_push($votes, $row['num_votes']);
        array_push($choice_ids, $row['choice_id']);
        $total_votes += $row['num_votes'];
        $voted_options ++;
    }

    if($votes[0] > $votes[1]) {
        $winner_id = $choice_ids[0];
        //echo "choice A/0 wins: id is";
    } else {
        //echo "choice B/1 wins";
        $winner_id = $choice_ids[1];
    }
    //echo "</br>Winner ID is: $winner_id</br>";
    // Now, query the database to update story_id's current_event
    $sql = "UPDATE story SET curr_id = $winner_id WHERE story_id = $story_id";
    $result = $c->query($sql);
    if(!$result) {
        die("Unable to view the number of votes: [".$c->error."]");
    }
    //echo "Updated curr_id</br>";
    return $winner_id;
}

function display_event_votes($c, $story_id, $event_id) {
    $sql = "SELECT num_votes FROM event_votes ".
        "WHERE story_id=$story_id AND event_id=$event_id;";
    $result = $c->query($sql);
    if(!$result) {
        die("Unable to view the number of votes: [".$c->error."]");
    }

    $total_votes = 0;
    $votes = [];
    $choices = [];
    $voted_options = 0;
    // read and gather all of the vote counts for the choices
    while($row = mysqli_fetch_assoc($result)) {
        //echo $row['choice_id'] . " (" . $row['num_votes'];
        array_push($votes, $row['num_votes']);
        $total_votes += $row['num_votes'];
        $voted_options ++;
    }

    //echo $voted_options;
    $percent_votes = [];
    foreach($votes as $vote) {
        $percent_v = $vote / $total_votes;
        $percent_vote = number_format($percent_v * 100, 2) . '%';
        array_push($percent_votes, $percent_vote);
        //echo "PERCENT VOTE: " + $percent_vote;
        //echo "<li>$percent_vote</li>";
    }


    //echo "<ul>";
    echo "<p>A total of $total_votes votes</p>";
    echo "<table>";
    $choices = get_event_choices($c, $event_id);
    //echo "CHOICE 0: " . $choices[0];
    for($i = 0; $i < 2; $i++) {
        if(!$choices[$i]) {
            $disp_choice = "Nothing $i"; 
        } else {
            $disp_choice = $choices[$i];
        }
        echo "<tr><td>".$disp_choice."</td><td>".$percent_votes[$i]."</td></tr>";
        //echo "<li>" . $choices[$i] . "\t" .  $percent_votes[$i]."</li></br>";
    }
    echo "</table>";
    //echo "</ul>";
    //echo "TESTING?";

}

//TODO: update this function to work with user accounts not friend accs.
function has_permission($c, $username) {
    $sql = "SELECT superuser FROM story_accounts WHERE name = '$username';";
    if(!$result = $c->query($sql)) {
        die("Unable to process permissions query [".$c->error."]");
    }
    //echo "permissions result = $result";
    echo "<br/>";
    while($row = $result->fetch_assoc()) {
        $permission = $row['superuser'];
        //echo $row['superuser'] . '<br/>';
    }
    return $permission;
}

//
// The following functions test inserting new stories/events to the db.
//

function test_insert_story($c, $title, $short_desc, $long_desc) {
    $sql = "REPLACE INTO story (title, short_desc, long_desc) ".
        "VALUES ('$title', '$short_desc', '$long_desc');";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;

}
// The following functions test inserting new events to the db.
function test_insert_event($c, $desc, $result, $choice_a, $choice_b) {
    if(!$choice_a)
        $choice_a = "NULL";
    if(!$choice_b)
        $choice_b = "NULL";

    echo "</br>";
    echo "choice_a: " . $choice_a;
    echo "choice_b: " . $choice_b;
    echo "</br>";

    $sql = "REPLACE INTO event (description, result, choice_a, choice_b) ".
        "VALUES ('$desc', '$result', $choice_a, $choice_b);";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;

}

// The following functions test inserting new events to the db.
function insert_story_event($c, $story_id, $event_id) {
    $sql = "INSERT INTO story_event (story_id, event_id) ".
        "VALUES ('$story_id', '$event_id');";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;
}

function test_insert_account($c, $name, $password, $isSuper) {
    $sql = "INSERT INTO story_accounts (name, password, superuser)".
        "VALUES ('$name', '$password', '$isSuper');";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;
}

function test_insert_account2($c, $name) {
    $password = $name ."1234";
    $sql = "INSERT INTO story_accounts (name, password, superuser)".
        "VALUES ('$name', '$password', '0');";
    $result = $c->query($sql);
    if (!$result) {
        die ("Query was unsuccessful: [" . $c->error ."]");
    }
    return $result;
}
?>


<?php
//$c = connect_DB();
//$create_result = create_DB($c);
//$result = get_all($c);
//$c->close();


$debug = 0;
$c = connect_DB();
if($debug) {
    create_accounts_DB($c);
    create_story_DB($c);
    create_event_DB($c);
    create_story_event_DB($c);
    create_event_votes_DB($c);
    echo "<hr>";
    display_stories($c);
    echo "<hr>";
    // if second parameter is true, then the event_ids will also be shown.
    display_events($c, 1);
    echo "<hr>";
    display_story_events($c);
    //display_friends($c);

    // * SUCCESS *
    // Stories can be successfully inserted into the database.
    // HOWEVER, duplicates are not ignored -> should look into.
    //test_insert_story($c, 'Dungeon 1', 'Ye find yeself in a dark dungeon',
    //    'Ye find yeself in a dark dungeon room with a grimy gray floor. Ye see a flask on the floor next to ye. There is one exit to ye right.');
    echo "<hr>";
    // TODO: figure out how to insert choices for an event.
    echo "PRE-TEST_INSERT_EVENT";
    //test_insert_event($c, 'Pick up the flask', 'Ye pick up ye flask. Inside is a dull orange liquid. There is one exit to ye right.', NULL, NULL);
    //test_insert_event($c, 'Ye enter ye dungeon.', 'Ye find yeself in a dark dungeon room with a grimy gray floor. Ye see a flask on the floor next to ye. There is one exit to ye right.', NULL, NULL);
    //test_insert_event($c, 'Go through the exit.', 'Ye approach the exit. As you get closer ye hear a faint clicking sound further down the tunnel. Perhaps it is some fowl creature? or merely the ambiant noise of a dark tunnel.', NULL, NULL);
    //test_insert_event($c, 'Go through the exit.', 'Ye approach the exit. As you get closer ye hear a faint clicking sound further down the tunnel. Perhaps it\'s some fowl creature? or merely some ambiant noise of a tunnel.', NULL, NULL);
    //insert_story_event($c, 9, 30);
    echo "<hr>";
    display_stories($c);
    echo "<hr>";
    //test_insert_account($c, 'stevecal', 'exo88*', '1');
    //test_insert_account2($c, 'stevejar');

    echo "<hr>";
    display_accounts($c);
    echo "<hr>";
    echo "<hr>";
    echo "<hr>";

} // end if(debug)

?>
