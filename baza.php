<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$baza = "korisnici";    

$conn = new mysqli($host, $user, $pass, $baza);

// Provjera veze
if ($conn->connect_error) {
    die("Greška pri spajanju na bazu: " . $conn->connect_error);
}
?>
