<?php
$servername="";
$username="";
$password="";
$conn = new mysqli($servername,$username,$password,"",3306);
if(isset($_POST['newname'],$_POST['coupleid']))
{
    if($insertquery = $conn->prepare("INSERT INTO `Names` VALUES (?,0,?)"))
    {
        $val = $_POST['newname'];
        $valb = $_POST['coupleid'];
        $insertquery->bind_param('si',$val,$valb);
        $insertquery->execute();
    }
}
$conn->close();
?>