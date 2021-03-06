
<!-- This file demonstrates
    (1) How to set up a simple HTML form
    (2) How to use PHP to access the data
    It will be used to collect information about a new friend,
    including their First Name, Last Name, Phone Number, and Age.
-->
<?php error_reporting(E_ALL); 
//require('friendDB.php');
?>
<html>
<head>
    <title>Friend Form - Read</title>
    <style type="text/css">
        #post {
            vertical-align: top;
        }
        #ReadTable table td, #ReadTable table {
            border: 1px solid gray;
            text-align: center;
        }

    </style>
</head>
<body>

<h1>Friend Form - Read</h1>

<h3>Other pages to update your friends list</h3>
<ul>
<li><a href="friendView.php">View a list of all your friends.</a></li>
<li><a href="friendAdd.php">Add another friend's info by hand.</a></li>
</ul>

<h3>Please enter a file to read new friends from</h3>

<table>
    <tr>
        <td id="post">

            <fieldset>
                <legend>Information</legend>
                <form action="friendRead.php" method="post">
                    Filename: 
                    <input type="text" name="filename" value="friends.txt"/><br/>
                    <br/>
                    <input type="submit" name="postSubmit" value="Read Data"/>


                </form>
            </fieldset>

                <?php
                    $datafile = fopen("frienddata.txt", "a") or die("Unable to open file");
                foreach ($_POST as $key => $value) {
                    $printMe = $value;
                    if (is_array($value)) {
                        $printMe = "[" . implode($value, ", ") . "]";
                    }
                    //echo "<tr><td>$key</td><td>$printMe</td></tr>\n";
                }
                // If the filename is set in POST,
                // call the 'read_from_file' function from friendDB.php
                // to update the database based on the file entered.
                if (isset($_POST["filename"])) {
                    include_once 'friendDB.php';
                    try {
                        $c = connect_DB();
                        //display_friends($c);
                        read_from_file($c, $_POST["filename"]); 
                        $c->close();
                    } catch (Exception $e) {
                        echo 'Sorry, we ran into a problem: ',
                            $e->getMessage();
                    }
                }
                fclose($datafile);
                ?>


        </td>
</table>

<?php

//$fp = fopen("php://input", 'r+');
//echo stream_get_contents($fp);
//$datafile = fopen("frienddata.txt", "a") or die("Unable to open file");

?>

<hr>

<div id="ReadTable">
<table>
    
    <?php
    include_once 'friendDB.php';
    $c = connect_DB();
    create_DB($c);
    display_friends($c);
    /*
    $create_result = create_DB($c);
    $result = get_all($c);
    // iterate over each record in the result.
    // Each record will be one row in the table, beginning with <tr> 
    foreach ($result as $row) {
        echo "<tr>";
        $keys = array("fname", "lname", "phone", "age");
        // iterate over all the columns.  Each column is a <td> element.
        foreach ($keys as $key) {
            echo "<td>" . $row[$key] . "</td>";
        }
        echo "</tr>\n";
    }
    */
    //$c->close();
                                                                                ?> 

</table>
</div>


</body>
</html>
