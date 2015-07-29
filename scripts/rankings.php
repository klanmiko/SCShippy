<?php
$servername = "";
$username = "";
$password = "";
// Create connection
$conn = new mysqli($servername, $username, $password,"",3306);
//given starting index 1
$start = $_GET['rangestart'];
$end = $_GET['rangeend'];
$randomquery = "SELECT * FROM `Names` ORDER BY `Votes` DESC";
$result = $conn->query($randomquery);
if($result->num_rows>0)
{
        if($end>$result->num_rows)
        {
            $end=$result->num_rows;
        }
        $final = array();
        $result->data_seek($start-1);
        for($i=($start-1);$i<$end;$i++)
        {
            $aggregatearray = $result->fetch_assoc();
            $couplequery = $conn->prepare("SELECT * FROM Couples WHERE `idCouples`=?");
            $couplequery->bind_param("i",$aggregatearray['Couples_idCouples']);
            $couplequery->execute();
            $couplequery->store_result();
            if($couplequery->num_rows>0)
            {
                $couplequery->bind_result($unused,$theboth,$theboy,$thegirl,$thephoto);
                $couplequery->fetch();
                $couplequery->free_result();
                $couplequery->close();
                if(intval($theboth)==1)
                {
                    $photo = $conn->prepare("SELECT Path FROM Photos Where `Index`=?");
                    if($photo->error!="")
                    {
                        echo $photo->error;
                    }
                    else if($conn->error!="")
                    {
                        echo $conn->error;
                    }
                    $photo->bind_param("s",$thephoto);
                    $photo->execute();
                    $photo->store_result();
                    if($photo->num_rows>0)
                    {
                        $photo->bind_result($ph);
                        $photo->fetch();
                        $final[]=array(
                            "name" => $aggregatearray['Name'],
                            "both" => 1,
                            "score" => intval($aggregatearray['Votes']),
                            "photo" => $ph
                        );
                    }
                    $photo->free_result();
                    $photo->close();
                }
                else if(intval($theboth)==0)
                {
                    $peoplequery = $conn->prepare("SELECT Photos_Index FROM Individuals WHERE ID IN (?,?)");
                    $peoplequery->bind_param("ss",$theboy,$thegirl);
                    $peoplequery->execute();
                    $peoplequery->store_result();
                    if($peoplequery->num_rows>1)
                    {
                        $peoplequery->bind_result($boyinfo);
                        $peoplequery->fetch();
                        $peoplequery->bind_result($girlinfo);
                        $peoplequery->fetch();
                        $peoplequery->free_result();
                        $peoplequery->close();
                        $boy = $conn->prepare("SELECT Path FROM Photos Where `Index`=?");
                        $boy->bind_param("s",$boyinfo);
                        $boy->execute();
                        $boy->store_result();
                        $path="";$pathb="";
                        if($boy->num_rows>0)
                        {
                            $boy->bind_result($path);
                            $boy->fetch;
                        }
                        $boy->free_result();
                        $boy->close();
                        $girl = $conn->prepare("SELECT Path FROM Photos Where `Index`=?");
                        $girl->bind_param("s",$girlinfo);
                        $girl->execute();
                        $girl->store_result();
                        if($girl->num_rows>0)
                        {
                            $girl->bind_result($pathb);
                            $girl->fetch();
                        }
                        $girl->free_result();
                        $girl->close();
                        $final[]=array(
                            "name" => $aggregatearray['Name'],
                            "both" => 0,
                            "score" => intval($aggregatearray['Votes']),
                            "boy" => $path,
                            "girl" => $pathb
                        );
                    }
                }
            }
        }
    $conn->close();
        header("content-type:application/json");
        echo json_encode($final);
}
else{
    echo "0 results";
}
?>