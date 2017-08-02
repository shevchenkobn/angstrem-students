<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 09.07.17
 * Time: 12:25
 */
class PDOMySQLConection implements IDBConnection
{
    public static function GetInstance()
    {
        if (!self::$instance)
            self::$instance = new PDOMySQLConection(self::SERVER, self::DATABASE,
                self::USER, self::PASSWORD);
        return self::$instance;
    }
    private static $instance;

    const SERVER = "localhost";
    const USER = "students";
    const PASSWORD = "shevchenkobn@gmail.com";
    const DATABASE = "students";

    private $server;
    private $database;
    private $user;
    private $password;

    private $handle;
    private $PDOFetchMode;

    private function __construct($server, $database, $user, $password)
    {
        if (!preg_match("%^[a-zA-Z0-9](\.[a-zA-Z0-9])*%", $server))
            throw new InvalidArgumentException("$server is not a URL");
        $this->server = $server;
        if (!preg_match("%^[a-zA-Z0-9_$]+$%", $database))
            throw new InvalidArgumentException("$database is not a database name");
        $this->database = $database;
        if (!preg_match("%^[a-zA-Z0-9_$]+$%", $user))
            throw new InvalidArgumentException("$user is not a user name");
        $this->user = $user;
        $this->password = $password;
        $this->CreatePDO();
        $this->PDOFetchMode = PDO::FETCH_ASSOC;
    }

    private function CreatePDO()
    {
        try
        {
            $this->handle = new PDO("mysql:dbname=" . $this->database . ";host=" . $this->server,
                $this->user, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

            // ensure that PDO::prepare returns false when passed invalid SQL
            $this->handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (Exception $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
            exit;
        }
    }
    public function Query($sql)
    {
        $argv = func_get_args();
        $parameters = null;
        if (count($argv) >= 1)
        {
            $sql = $argv[0];
            if (count($argv) == 2 && is_array($argv[1]))
            {
                $parameters = $argv[1];
            }
            elseif (count($argv) !== 1)
                $parameters = array_slice(func_get_args(), 1);
        }
        else
            throw new InvalidArgumentException("Insufficient arguments");
        $statement = $this->handle->prepare($sql);
        if ($statement === false)
        {
            trigger_error($this->handle->errorInfo()[2], E_USER_ERROR);
            exit;
        }
        $results = $statement->execute($parameters);

        if ($results !== false)
        {
            return $statement->fetchAll($this->PDOFetchMode);
        }
        else
        {
            return false;
        }
    }
    public function QueryWithBinding($sql, $parameters)
    {
        $sql_pieces = ["SET ", " = ", ", ", ";"];
        $count = count($parameters);
        $i = 0;
        $set_query = $sql_pieces[0];
        foreach ($parameters as $name => $value)
        {
            $set_query .= str_replace(":", "@", $name) . $sql_pieces[1] . $name;

            if ($i < $count - 1)
            {
                $set_query .= $sql_pieces[2];
            }
            else
                $set_query .= $sql_pieces[3];
            $i++;
        }
        $statement = $this->handle->prepare($set_query);
        if ($statement === false)
        {
            trigger_error($this->handle->errorInfo()[2], E_USER_ERROR);
            exit;
        }
        foreach ($parameters as $name => &$value)
            $statement->bindParam($name, $value);

        $statement->execute();

        $sql = str_replace(":", "@", $sql);
        $statement = $this->handle->prepare($sql);
        if ($statement === false)
        {
            trigger_error($this->handle->errorInfo()[2], E_USER_ERROR);
            exit;
        }
        $results = $statement->execute();
        if ($results !== false)
        {
            return $statement->fetchAll($this->PDOFetchMode);
        }
        else
        {
            return false;
        }
    }
    public function SetPDOFetchMode($pdo_constant)
    {
        if (is_int($pdo_constant) && $pdo_constant >= 0)
            $this->PDOFetchMode = $pdo_constant;
    }
}