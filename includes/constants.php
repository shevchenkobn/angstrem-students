<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 27.06.17
 * Time: 11:42
 */
define("RELATIVE_DOCUMENT_ROOT", "public/");
define("REAL_DOCUMENT_ROOT", "/".RELATIVE_DOCUMENT_ROOT);

define("STAFF_DB_SERVER", "mysql.hostinger.com.ua");
define("STAFF_DB_NAME", "u139489065_staff");
define("STAFF_DB_USER", "u139489065_admin");
define("STAFF_DB_PASS", "avengersStudy");

define("STUDENTS_DB_SERVER", STAFF_DB_SERVER);
define("STUDENTS_DB_NAME", "u139489065_istud");
define("STUDENTS_DB_USER", "u139489065_odmen");
define("STUDENTS_DB_PASS", STAFF_DB_PASS);

define("STUDENTS_DB_MONTHLY_FEE", 650);

define("STUDENT_DB_STRUCTURE_JSON", realpath(__DIR__."/../database_info/db_structure.json"));
define("STUDENT_DB_DICTIONARY_PHP", realpath(__DIR__."/../database_info/db_structure.json"));
?>