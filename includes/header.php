<!doctype html>
<html lang="en">
  <head>
<?php 
/*
    Raje Singh
    WEBD3201 
    December 15, 2020
*/
	session_start();
    ob_start();
    require_once "./includes/constants.php";
    require_once "./includes/functions.php";
    require_once "./includes/db.php";

    $home = isLoggedIn() ? "Hi, ".$_SESSION['user_first_name'] : "Homepage";
    
?>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content="<?php echo $description;?>"/>
    <meta name="author" content="<?php echo $author;?>"/>
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico"/>

    <!--
    Filename    : <?php echo $file . "\n";?>
    Date Created: <?php echo $date . "\n";?>
    -->

    <title>WEBD3201 - <?php echo $title;?></title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet"/>

    <!-- Custom styles for this template -->
    <link href="./css/styles.css" rel="stylesheet"/>
	
  </head>
  <body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="./index.php"><?php echo $home?></a>
        <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <!-- Dynamic based on if user is signed in or out -->
            <a class="nav-link" href="<?php echo $status = isLoggedIn() ? "./logout.php": "./sign-in.php";?>">
                <?php echo $status = isLoggedIn() ? "Sign Out" : "Sign In"; ?>
            </a>
        </li>
        </ul>
    </nav>
    <div class="container-fluid">
      <div class="row">
        
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <!-- Firefox extra nav item -->
                <li class="nav-item">
                    <a class="nav-link" href="./index.php">
                        Home
                    </a>
                </li>
                <!-- EOF extra item -->
                <!-- Dynamic nav bar elements -->
                
                <?php echo $dashboard = isLoggedIn() ? '<li class="nav-item">
                <a class="nav-link active" href="./dashboard.php">
                     <span data-feather="home"></span>
                     Dashboard <span class="sr-only">(current)</span>
                </a>
                </li>'. "\n" : "";

                if(isset($_SESSION['user_type']))
                {
                    echo $salespeople_access = $_SESSION['user_type'] == ADMIN ? '<li class="nav-item">
                    <a class="nav-link active" href="./salespeople.php">
                     <span data-feather="home"></span>
                     Salespeople <span class="sr-only">(current)</span>
                    </a>
                    </li>' ."\n" : ""; 

                    echo $clients_access = $_SESSION['user_type'] == ADMIN || $_SESSION['user_type'] == SALESPERSON ?
                    '<li class="nav-item">
                    <a class="nav-link active" href="./clients.php">
                     <span data-feather="home"></span>
                     Clients <span class="sr-only">(current)</span>
                    </a>
                    </li>' ."\n" : ""; 

                    echo $calls_access = $_SESSION['user_type'] == ADMIN || $_SESSION['user_type'] == SALESPERSON ?
                    '<li class="nav-item">
                    <a class="nav-link active" href="./calls.php">
                     <span data-feather="home"></span>
                     Calls <span class="sr-only">(current)</span>
                    </a>
                    </li>' ."\n" : ""; 
                }

                echo $change_password_access = isLoggedIn() ? '<li class="nav-item">
                <a class="nav-link active" href="./change_password.php">
                     <span data-feather="home"></span>
                     Change Password <span class="sr-only">(current)</span>
                </a>
                </li>'. "\n" : ""; 
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="./privacy.php">
                        Privacy Policy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./acceptableuse.php">Acceptable Use Policy</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./privacy.php#cookiepolicy">Cookie Policy</a>
                </li>
            </ul>
            </div> 
        </nav>

        <main class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">