#!/usr/bin/php
<?php
	if (count($argv) < 4)
	{	echo "Usage: {$argv[0]} EMAIL PASSWORD FULLNAME [SCREEN_VALUES_WITH_SINGLE_QUOTES={0, 1}]\n
			The script is a console driven appendix to angstrem-students project.\n
			It is used to prepare staff user to insertion to database.\n
			The result is produced in csv format with a single string to stdout.";
			exit(0);
	}
	else
	{
		if (!filter_var($argv[1], FILTER_VALIDATE_EMAIL))
		{
			echo "The email is invalid";
			exit(1);
		}
		if (strlen($argv[2]) > 72 || $argv[2] == "")
		{
			echo "The password length is invalid";
			exit(2);
		}
		if (preg_match("_~!@#\$%\^&\*\_=\+\{\}\\\"\/\|\.><\?:;_", $argv[3]))
		{
			echo "The name contains forbidden symbols";
			exit(3);
		}
		$argv[2] = password_hash($argv[2], PASSWORD_BCRYPT);
		if (isset($argv[4]) && $argv[4] !== "")
		{
			foreach ($argv as $i=>$value)
				$argv[$i] = '\''.$value.'\'';
		}
		$output = $argv[1].",".$argv[2].",".$argv[3]."\n";
		echo $output;
		exit(0);
	}
?>
