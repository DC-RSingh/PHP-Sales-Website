<?php
    /*
        Raje Singh
        WEBD3201 
        October 1, 2020
    */
    $author = "Raje Singh";
	$title = "Logout";
	$file = "logout.php";
	$description = "This is the logout page of the Lab 1 assignment for WEBD3201";
	$date = "September 25, 2020";
	$banner = "Log Out";
    include "./includes/header.php";
    
    //  // If the user is not signed in, redirect them back to sign-in.php
    // if (!isLoggedIn())
    // {
    //     redirect("./sign-in.php");
    // }

    // Log the user signout, destroy the current session, create a new session and redirect the user
    if(isLoggedIn()) {
        log_activity("User ".$_SESSION['user_email']." has signed out");
        session_unset();
        session_destroy();
        session_start();
        setMessage("You have successfully logged out!");
    }

    redirect("./sign-in.php");
?>

<?php include "./includes/footer.php"?>

