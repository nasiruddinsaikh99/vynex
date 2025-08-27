<?php
ini_set('display_errors', 1);
require_once('config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];

	$sql = "INSERT INTO `contacts`(`name`, `email`, `phone`, `subject`, `message`) VALUES ('$name','$email', '$phone', '$subject' ,'$message')";

	if (mysqli_query($conn, $sql)) {
	  echo "New record created successfully";
	  header("location: contact.php?alert=1");
	  exit();
	} else {
	  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
}

?>
