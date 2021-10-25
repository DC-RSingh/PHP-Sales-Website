<?php
 /*
    Raje Singh
    WEBD3201 
    November 18, 2020
*/

/******* COOKIES *******/
define("COOKIE_LIFESPAN", "2592000");

/******* USER TYPES ********/
define("ADMIN", 'a');
define("CLIENT", 'c');
define("SALESPERSON", 's');
define("PENDING", 'p');
define("DISABLED", 'd');

/******* DATABASE CONSTANTS ********/
define("DB_HOST", "127.0.0.1");
define("DATABASE", "singhr_db");
define("DB_ADMIN", "singhr");
define("DB_PORT", "5432");
define("DB_PASSWORD", "100776793");

/******* MISCELLANEOUS ********/
// In case browser does not support type="email" and the type falls back to "text"
define("HTML_EMAIL_REGEX", "[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,64}");
define("UPLOAD_DIR", "uploads/");
define("MAX_FILE_SIZE", 500000);
define("RECORDS_PER_PAGE", 10);
define("HTML_NAME_REGEX", "^[0-9a-zA-Z]+$");
define("PHP_NAME_REGEX", "/^[0-9a-zA-Z]+$/");

define("WEBSITE_EMAIL", "singhr@opentech.durhamcollege.org");

// Could be changed with an if but I'm lazy
// Change if on localhost
define("WEBSITE_URL", "http://opentech.durhamcollege.org/webd3201/singhr/");
define("TEMPLATE_DIR", "templates/");
define("OPENTECH_FOLDER_ROOT", "/var/www/html/webd3201/singhr/");
?>