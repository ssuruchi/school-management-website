<?php
include("../config.php");

if (isset($_POST["add_student"])) {
    $name = strtolower($_POST["name"]);
    $email = $_POST["email"];
    $class_id = $_POST["class_id"];
    $gender = strtolower($_POST["gender"]);
    $phone = $_POST["phone"];
    $dob = $_POST["dob"];
    $address = strtolower($_POST["address"]);
    $password = $_POST["password"];

    $find_student = "SELECT * FROM students WHERE email = '$email'";
    $response = mysqli_query($conn, $find_student) or die(mysqli_error($conn));
    if (mysqli_num_rows($response) == 1) {
        echo "Student already registered...";
    } else {
        $password = sha1($password);
        $add_student = "INSERT INTO students (name,email,class_id,gender,phone,dob,address,password) VALUES ('$name','$email','$class_id','$gender','$phone','$dob','$address','$password') ";
        $response = mysqli_query($conn, $add_student) or die(mysqli_error($conn));
        header('Location: admin.php');
    }
}

if (isset($_POST["update_student"])) {
    $student_id = $_GET["student_id"];
    $name = strtolower($_POST["name"]);
    $email = $_POST["email"];
    $class_id = $_POST["class_id"];
    $gender = strtolower($_POST["gender"]);
    $phone = $_POST["phone"];
    $dob = $_POST["dob"];
    $address = strtolower($_POST["address"]);
    $password = $_POST["password"];


    $find_student = "SELECT * FROM students WHERE `email` = '$email'";
    $response = mysqli_query($conn, $find_student) or die(mysqli_error($conn));
    if (mysqli_num_rows($response) == 1) {
        if ($password == NULL) {
            $update_profile = "UPDATE 
                students 
                SET name = '$name', email = '$email', class_id = '$class_id', gender = '$gender', phone = '$phone', dob = '$dob', address = '$address' 
                WHERE student_id = '$student_id'
            ";
        } else {
            $password = sha1($password);
            $update_profile = "UPDATE 
                students 
                SET name = '$name', email = '$email', class_id = '$class_id', gender = '$gender', phone = '$phone', dob = '$dob', address = '$address', password = '$password' 
                WHERE student_id = '$student_id'
            ";
        }
        $response = mysqli_query($conn, $update_profile) or die(mysqli_error($conn));
        header('Location: ./students.php?query=manage');
    } else {
        echo "There was some problem finding the student's account, contact Database Administrator...";
    }
}

if(isset($_POST['importSubmit'])){
   
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
        
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            fgetcsv($csvFile);
            
            while(($line = fgetcsv($csvFile)) !== FALSE){
                $student_id     = $line[0];
                $name           = $line[1];
                $email          = $line[2];
                $class_id       = $line[3];
                $gender         = $line[4];
                $phone          = $line[5];
                $dob            = $line[6];
                $address        = $line[7];
                $password       = $line[8];
                
                // Check whether member already exists in the database with the same email
                $find_student = "SELECT * FROM students WHERE `email` = '$email'";
    $response = mysqli_query($conn, $find_student) or die(mysqli_error($conn));
                
                if(mysqli_num_rows($response) == 1){
                    // Update member data in the database
                    $password = sha1($password);
                    $update_profile= "UPDATE 
                    students 
                    SET name = '$name', email = '$email', class_id = '$class_id', gender = '$gender', phone = '$phone', dob = '$dob', address = '$address', password = '$password' 
                    WHERE student_id = '$student_id'
                ";
                }else{
                    // Insert member data in the database
                    $password = sha1($password);
                    $add_student = "INSERT INTO students (name,email,class_id,gender,phone,dob,address,password) VALUES ('$name','$email','$class_id','$gender','$phone','$dob','$address','$password') ";
                }
                $response = mysqli_query($conn, $add_student) or die(mysqli_error($conn));
            }
            
            // Close opened CSV file
            fclose($csvFile);
            
            $qstring = '?status=succ';
        }else{
            $qstring = '?status=err';
        }
    }else{
        $qstring = '?status=invalid_file';
    }

header("Location: ./students.php?query=manage".$qstring);
}
