<?php
$target_dir = "../photos/";
$servir_dir = "photos/";
$namefield = preg_replace('((^\.)|\/|(\.$))', '_', $_POST['namefield']);
$target_file = $target_dir . $namefield . "." . pathinfo($_FILES['fileuser']['name'],PATHINFO_EXTENSION);
$server_file = $servir_dir . $namefield . "." . pathinfo($_FILES['fileuser']['name'],PATHINFO_EXTENSION);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST['submit'])) {
    $check = getimagesize($_FILES['fileuser']['tmp_name']);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES['fileuser']['size'] > 5000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileuser"]["tmp_name"], $target_file)) {
        $servername = "";
        $username = "";
        $password = "";
// Create connection
        $conn = new mysqli($servername, $username, $password,"",3306);
        $photoinsert = $conn->prepare("INSERT INTO Photos (Path) VALUES (?)");
        $photoinsert->bind_param("s",$server_file);
        $photoinsert->execute();
        $index = $photoinsert->insert_id;
        $photoinsert->close();
        if($_POST['number']=="both")
        {
            $insertcouple = $conn->prepare("INSERT INTO Couples (`Both`,Photos_Index) VALUES (1,?)");
            $insertcouple->bind_param("i",$index);
            $insertcouple->execute();
            $coupleid = $insertcouple->insert_id;
            $insertcouple->close();
            $insertname = $conn->prepare("INSERT INTO `Names` VALUES (?,0,?)");
            $val = $_POST['namefield'];
            $insertname->bind_param("si",$val,$coupleid);
            $insertname->execute();
            $insertname->close();
        }
        else if($_POST['number']=="one")
        {
            if($_POST['gender']=="boy")
            {
                $insertboy = $conn->prepare("INSERT INTO Individuals (Gender,Name_char,Photos_Index) VALUES ('M',?,?)");
                            $val = $_POST['namefield'];
                $insertboy->bind_param("si",$val,$index);
                $insertboy->execute();
                $insertboy->close();
            }
            else if($_POST['gender']=="girl")
            {
                $insertgirl = $conn->prepare("INSERT INTO Individuals (Gender,Name_char,Photos_Index) VALUES ('F',?,?)");
                            $val = $_POST['namefield'];

                $insertgirl->bind_param("si",$val,$index);
                $insertgirl->execute();
                $insertgirl->close();
            }
        }
        $conn->close();
    } else {
    }
}
?> 