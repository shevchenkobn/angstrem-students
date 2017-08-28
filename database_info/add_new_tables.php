<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 11.08.17
 * Time: 14:05
 */
/**
 * This file is meant to set columns which must have a value.
 * This feature is not implemented because it's not agreed what columns must be.
 */
return ["students" => ["name", "surname", "second_name", "form_number", "form_letter", "birthday"],
	"contracts_info" => ["conclusion_date"],
	"parents" => ["status", "name", "surname"],
	"students_info" => []
];
?>