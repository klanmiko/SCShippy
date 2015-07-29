<?php
$servername = "";
$username = "";
$password = "";
// Create connection
$conn = new mysqli($servername, $username, $password,"",3306);
//given starting index 1
$randomquery = "SELECT * FROM Couples";
$result = $conn->query($randomquery);
if($result->num_rows>0)
{
    $end=$result->num_rows;
    $final = array();
    for($i=0;$i<$end;$i++)
    {
        $aggregatearray = $result->fetch_assoc();
        $boy="";
        $girl="";
        $namearray = array();
        if($aggregatearray['Both']==0)
        {
            $boyquery=$conn->prepare("SELECT Name_char FROM Individuals WHERE ID=?");
            $val = intval($aggregatearray['Boy_ID']);
            $boyquery->bind_param("i",$val);
            $boyquery->execute();
            $boyquery->store_result();
            if($boyquery->num_rows>0)
            {
                $boyquery->bind_result($boy);
                $boyquery->fetch();
            }
            $boyquery->free_result();
            $boyquery->close();
            $girlquery=$conn->prepare("SELECT Name_char FROM Individuals WHERE ID=?");
            $val=intval($aggregatearray['Girl_ID']);
            $girlquery->bind_param("i",$val);
            $girlquery->execute();
            $girlquery->store_result();
            if($girlquery->num_rows>0)
            {
                $girlquery->bind_result($girl);
                $girlquery->fetch();
            }
            $girlquery->free_result();
            $girlquery->close();
        }
        $namesquery = $conn->prepare("SELECT `Name` FROM `Names` WHERE Couples_idCouples=?");
        $val = intval($aggregatearray['idCouples']);
        $namesquery->bind_param("i",$val);
        $namesquery->execute();
        $namesquery->store_result();
        if($namesquery->num_rows>0)
        {
            $namesquery->bind_result($nameresults);
            for($j=0;$j<$namesquery->num_rows;$j++)
            {
                $namesquery->fetch();
                $namearray[$j]=$nameresults;
            }
        }
        $final[$i]=array(
            "ID" => $aggregatearray['idCouples'],
            "boy" => $boy,
            "girl" => $girl,
            "name" => $namearray
        );
    }
    $conn->close();
    header("content-type:application/json");
    echo json_encode($final);
    
}
else{
    echo "0 results";
}
?>