<?php
$vote = $_POST['vote'];
$couple = $_POST['ID'];
$name = $_POST['name'];
$servername = "";
$username = "";
$password = "";
// Create connection
$conn = new mysqli($servername, $username, $password,"",3306);
$scorequery = $conn->prepare("SELECT Votes FROM Names WHERE Couples_idCouples=?");
$scorequery->bind_param("s",$couple);
$scorequery->execute();
$scorequery->store_result();
if($scorequery->num_rows>0)
{
    $scorequery->bind_result($scor);
    $scorequery->fetch();
    $scorequery->free_result();
    $scorequery->close();
    if(strcasecmp($vote,"ship")==0)
    {
        $update = $conn->prepare("UPDATE `Names` SET Votes=? WHERE Couples_idCouples=?");
        $val = intval($scor)+1;
        $update->bind_param("is",$val,$couple);
        $update->execute();
        $update->close();
    }
    else if(strcasecmp($vote,"sink")==0)
    {
        $update = $conn->prepare("UPDATE `Names` SET Votes=? WHERE Couples_idCouples=?");
        $val = intval($scor)-1;
        $update->bind_param("is",$val,$couple);
        $update->execute();
        $update->close();
    }
    else{
        echo "fuck off";
    }
}
else{
    if(strcasecmp($vote,"ship")==0)
    {
        $insert = "INSERT INTO `Names` VALUES (?,?,?)";
        $val = intval($scor)+1;
        $insert->bind_param("sis",$name,$val,$couple);
        $insert->execute();
        $insert->close();
    }
     else if(strcasecmp($vote,"sink")==0)
    {
        $insert = "INSERT INTO `Names` VALUES (?,?,?)";
        $val = intval($scor)-1;
        $insert->bind_param("sis",$name,$val,$couple);
        $insert->execute();
        $insert->close();
    }
    else{
        echo "fuck off";
    }
    $conn->close();
}
?>