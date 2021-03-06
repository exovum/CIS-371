
<!-- This file demonstrates
    (1) How to set up a simple HTML form
    (2) How to use PHP to access the data
    It will be used to collect information about a new friend,
    including their First Name, Last Name, Phone Number, and Age.
-->
<?php error_reporting(E_ALL); 
//require('friendDB.php');
session_start();
if (! isset($_SESSION["username"])) {
    header("Location: index.php");
}
$username = $_SESSION["username"];
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Friend Form - Read</title>
    <style type="text/css">
        #post {
            vertical-align: top;
        }
        #ReadTable table td, #ReadTable table th, #ReadTable table {
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

<?php
    include 'accountDB.php';
    $c = connect_accounts_DB();
    if(!has_permission($c, $username)) {
        // user does not have permission to add friends
        echo "<h3> Sorry, you don't have permission to add friends.</h3>";
    } else {
        // Otherwise user is a superuser

?>

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
                    include_once 'accountDB.php';
                    try {
                        $c = connect_accounts_DB();
                        //display_friends($c);
                        read_from_file_accounts($c, $_POST["filename"], $username); 
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
    include_once 'accountDB.php';
    $c = connect_accounts_DB();
    create_accounts_DB($c);
    display_my_friends($c, $username);
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

<p id="demo"></p>

<script src="sortProfs.js"></script>
<script>
    var tname = "ReadTable";
    var table_html = document.getElementById(tname);
    console.log(table_html);

    // Note: I know there is a better way to add same action to multiple
    // objects where they don't all change/mess with the params of the
    // other listeners, yet this is sufficient for now.
    // TODO: use a loop to add the listeners (in case more cols are added).
    var headers = table_html.getElementsByTagName("th");
    console.log(headers);
    var h0 = headers[0].innerHTML;
    headers[0].addEventListener("click", function(){
            testing(tname, h0, 0);
    });
    var h1 = headers[1].innerHTML;
    headers[1].addEventListener("click", function(){
            testing(tname, h1, 1);
    });
    var h2 = headers[2].innerHTML;
    headers[2].addEventListener("click", function(){
            testing(tname, h2, 2);
    });
    var h3 = headers[3].innerHTML;
    headers[3].addEventListener("click", function(){
            testing(tname, h3, 3);
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

<?php
    } //end else for if user has permission
?>

<h4><a href="index.php">Home<a></h4>
<a href="logout.php">Logout</a>

</body>
</html>
