
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
    <title>Friend Form - View</title>
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

<h1>Friend Form - View</h1>

<h3>Other pages to update your friends list</h3>
<ul>
<li><a href="friendRead.php">Update friends from a file.</a></li>
<li><a href="friendAdd.php">Add another friend's info by hand.</a></li>
</ul>


<h3>Here is all of your friends</h3>
<div id="LISTTABLE">
<table>
    <?php
    include 'friendDB.php';
    $c = connect_DB();
    display_friends($c);
    $c->close();
    ?>
</table>
</div>


</body>
</html>
