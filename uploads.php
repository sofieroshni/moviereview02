<?php
global $pdo;
require "connect.php";
if(isset($_POST['submit'])){ //tjekker om skemaet bliver sendt ind via POST-metoden
    $file = $_FILES['file']; //vi gav det navnet filen
    $fileName = $_FILES['file']['name']; //henter navn på fil som blev lastet op
    $fileTmpName = $_FILES['file']['tmpName']; //henter midlertidlig placering. Brusen til at flytte fil fra mildertidlig placering til permenent.
    $fileSize = $_FILES['file']['size']; //henter størrelsen, giver information om str. og tjekker om den er for stor
    $fileError= $_FILES['file']['error']; //variabel bruges til at tjekke op på om der opstod problemer i oplastningsprocsessen
    $fileType= $_FILES['file']['type'];
// hvilke typer filer jeg  gerne vil havde
    //uploadet//
$fileExt = explode('.', $fileName); //splitter filnavn via punktum. image.jpg. $fileExt bliver til arrayet ["image", "jpg"] giver adgang til information om hvilke filtype
$fileActualExt = strtolower(end($fileExt)); //konventerer til små bogstaver. henter det sidste element i arrayet $fileExt. gør det letterer at sammenligne filtyper

    $allowed =array('jpg', 'jpeg','png', 'pdf'); //hvilke filtyper
if(in_array($fileActualExt, $allowed)){
if($fileError === 0){
if($fileSize<1000000){

    $fileNameNew = uniqid('',true).".".$fileActualExt; //uniqid() indbyggret php-funktion. generer en unik streg. Hver gang funktionen kaldels vil det retunerer en forskellig værdi, noget som gør den egnet til at skabe unikke navne.
    $fileDestination = 'uploads/'.$fileNameNew; // filen mappe + filensnyenavn;

//checker om directory er findes
    if (!is_dir('moviereview')) {
        mkdir('moviereview', 0777, true); //
    }

    move_uploaded_file($fileTmpName, $fileDestination);

    $sql = "INSERT INTO movies(movie_img,movieTitle) VALUES (:movie_img, movieTitle)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':movie_img' => $fileDestination]);


    header("Location:index.php?uploadsucess");
    echo "sucess";
}else{
    echo "filen er for stor";
}
}else{
    echo "<h1>".'fejl i at uploade din fil'."</h1>";

}
}else{
    echo "du kan ikke uploade filer i den type";
}
}

