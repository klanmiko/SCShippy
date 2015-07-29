<?php
if(isset($_GET['user'])
{
    $user = $_GET['user'];
    $servername = "";
    $username = "";
    $password = "";
    // Create connection
    $conn = new mysqli($servername, $username, $password,"",3306);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $query = "SELECT banned FROM Bans WHERE Photoscol=".$user;
    $result = $conn->query($query);
    if($result->num_rows>0)
    {
        $row=$result->fetch_assoc();
        header("content-type:application/json");
        $return = array("ban" => $row['banned']);
        json_encode($return);
    }
    else{
        echo "not banned";
    }
}
?>