<?php
$tries = $_GET['tries'];
$servername = "";
$username = "";
$password = "";
// Create connection
$conn = new mysqli($servername, $username, $password,"",3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM Couples";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    if($tries>$result->num_rows)
    {
        $row = (($tries-1)%($result->num_rows))+1;
    }
    else{
        $row = $tries;
    }
    $couple = 0;
    for($x=1;$x<=$row;$x++)
    {
        $couple = $result->fetch_assoc();
    }
    $return = 0;
    $path = 0;
    $names = 0;
    if ($couple['Both']==1)
    {
        $photos = $conn->prepare("SELECT Path FROM Photos WHERE `Index`=?");
        $val = (int)$couple['Photos_Index'];
        $photos->bind_param("i",$val);
        $photos->execute();
        $photos->store_result();
        if ($photos->num_rows>0)
        {
            $photos->bind_result($path);
            $photos->fetch();
        }
        $return = array(
        "ID"=>$couple['idCouples'],
        "Both"=>$couple['Both'],
        "Photo"=>(string)$path);
        $photos->free_result();
        $photos->close();
    }
    else if($couple['Both']==0)
    {
        $return = array(
        "ID"=>$couple['idCouples'],
        "Both"=>$couple['Both']);
        $peoplequery = $conn->prepare("SELECT Photos_Index FROM Individuals WHERE ID IN (?,?)");
        if($peoplequery->error!="")
        {
            echo "People query error ".$peoplequery->error;
        }
        else if($conn->error!="")
        {
            echo "People query conn error ".$conn->error;
        }
        $boy = $couple['Boy_ID'];
        $girl = $couple['Girl_ID'];
        $peoplequery->bind_param("ss",$boy,$girl);
        $peoplequery->execute();
        $peoplequery->store_result();
        $boyinfo=""; $girlinfo="";
        if($peoplequery->num_rows>1)
        {
            $peoplequery->bind_result($boyinfo);
            $peoplequery->fetch();
            $peoplequery->bind_result($girlinfo);
            $peoplequery->fetch();
            $peoplequery->free_result();
            $peoplequery->close();
            $boy = $conn->prepare("SELECT Path FROM Photos Where `Index`=?");
            if($conn->error!="")
    {
        echo "conn error boy ".$conn->error;
    }
    else if($boy->error!="")
        {
            echo "boy error ".$boy->error;
        }
            $boy->bind_param("s",$boyinfo);
            $boy->execute();
            $boy->store_result();
            if($boy->num_rows>0)
            {
                $boy->bind_result($path);
                $boy->fetch();
                $return['Boy']=$path;
            }
            $boy->free_result();
            $boy->close();
            $girl = $conn->prepare("SELECT Path FROM Photos Where `Index`=?");
            if($conn->error!="")
    {
        echo "girl conn error ".$conn->error;
    }
    else if($girl->error!="")
        {
            echo "girl error ".$girl->error;
        }
            $girl->bind_param("s",$girlinfo);
            $girl->execute();
            $girl->store_result();
            if($girl->num_rows>0)
            {
                $girl->bind_result($path);
                $girl->fetch();
                $return['Girl']=$path;
            }
            $girl->free_result();
            $girl->close();
        }
    }
    $names = $conn->prepare("SELECT Name FROM Names WHERE Couples_idCouples=?");
    
    if($conn->error!="")
    {
        echo "names conn error ".$conn->error;
    }
    else if($names->error!="")
        {
            echo "names error ".$names->error;
        }
    $val = (int)$couple['idCouples'];
    $names->bind_param("i",$val);
    $names->execute();
    $names->store_result();
    if($names->num_rows>0)
    {
        $count=0;
        $return['Names']=array();
        $names->bind_result($name);
        while($names->fetch())
        {
            $return['Names'][$count]=$name;
            $count++;
        }
    }
    $names->free_result();
    $names->close();
    header("content-type:application/json");
    echo json_encode($return,JSON_NUMERIC_CHECK);
    $conn->close();
} else {
    echo "0 results";
}
?>

