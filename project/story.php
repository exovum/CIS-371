
<!-- This file demonstrates
    1.) Displaying the current story id data -> $_GET for story_id
    2.) Display the current event data -> $_POST for event_id
        so users can't see other options
    3.) Accept votes for what option to choose
-->
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
        <?php error_reporting(E_ALL); 
        include "storyDB.php";
        $c = connect_DB();
        session_start();
        //TODO: redirect the user if they are not signed in? or something else
        /*if (! isset($_SESSION["username"])) {
            header("Location: index.php");
        }
        $username = $_SESSION["username"];
        */
        if(isset($_GET['story'])) {
            // if the query string is set, use the specified id
            $story_id = $_GET['story'];
            // but, before that, we also need to check if there is a story
            // with that ID in the database...
            $story_result = get_story_by_id($c, $story_id);
            if (!$story_result) {
                echo "<h3>Sorry, that story doesn't exist yet.</h3>";
                echo "<p>You can also start a new story!</p>";
                echo "<hr>";
                echo "<p><a href='viewStories.php'>Home</a></p>";
                echo "<p>Create Account / Sign In / Logout</p>";
                echo "<!--<h4><a href='index.php'>Home<a></h4>";
                echo "<a href='logout.php'>Logout</a>-->";
                echo "<title>Story Unavailable</title>";
                exit;
            } else {
                /*
                echo "<h3>Found the story with id of $story_id</h3>";
                $story_title = get_story_data_value($c, $story_id, 'title');
                $story_row = get_story_data($c, $story_id);
                echo "<h2>$story_title</h2>";
                $short = $story_row['short_desc'];
                echo "<p>$short</p>";
                echo "<p>" . $story_row['long_desc'] . "</p>";
                */
            }
            
        } else {
            // "Using default story_id";
            $story_id = 9; // by default, use the starting Ye Dungeon story (id=9)
        }
        $story_row = get_story_data($c, $story_id);
        $title = $story_row['title'];
        $short_desc = $story_row['short_desc'];
        $long_desc = $story_row['long_desc'];

        
        ?>
    <title>Story - <?php echo $title ?></title>
    <style type="text/css">
        #post {
            vertical-align: top;
            }
        #LISTTABLE table td, #LISTTABLE table th, #LISTTABLE table,
        #LinkTable table, #LinkTable table td{
            border: 1px solid gray;
            text-align: center;
        }


    </style>
</head>
<body>

<h1>Story -  
<?php echo $title; ?></h1>

<div id="story_info">
<p>
<?php echo $story_row['short_desc']  ?>
</p>
<p>
<?php echo $story_row['long_desc']  ?>
</p>
</div>

<p id="demo"></p>

<script>
/*
    var table_html = document.getElementById("LISTTABLE");
    console.log(table_html);

    // Note: I know there is a better way to add same action to multiple
    // objects where they don't all change/mess with the params of the
    // other listeners, yet this is sufficient for now.
    // TODO: use a loop to add the listeners (in case more cols are added).
    var headers = table_html.getElementsByTagName("th");
    console.log(headers);
    var h0 = headers[0].innerHTML;
    headers[0].addEventListener("click", function(){
            testing("LISTTABLE", h0, 0);
    });
    var h1 = headers[1].innerHTML;
    headers[1].addEventListener("click", function(){
            testing("LISTTABLE", h1, 1);
    });
    var h2 = headers[2].innerHTML;
    headers[2].addEventListener("click", function(){
            testing("LISTTABLE", h2, 2);
    });
    var h3 = headers[3].innerHTML;
    headers[3].addEventListener("click", function(){
            testing("LISTTABLE", h3, 3);
    });

    /*for ( i = 0; i < headers.length; i++) {
        console.log("headers[" + i + "]: " + headers[i]);
        var hh = headers[i].innerHTML;
        console.log("headers[" + i + "] html: " + headers[i].innerHTML);
        headers[i].addEventListener("click", function(){
            testing("LISTTABLE", hh, i);
        });
    }
    */
</script>

<p><a href='viewStories.php'>Home</a></p>
<p>Logout</p>
<!--<h4><a href="index.php">Home<a></h4>
<a href="logout.php">Logout</a>-->


</body>
</html>
