<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 09.07.17
 * Time: 12:25
 */
class PDOMySQLConection implements IDBConnection
{
    private $server;
    private $database;
    private $user;
    private $password;

    private $handle;

    public function __construct($server, $database, $user, $password)
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
        if (!preg_match("%^[a-zA-Z0-9_$]{6,}$%", $password))
            throw new InvalidArgumentException("$password is not a user name");
        $this->password = $password;
        $this->CreatePDO();
    }

    private function CreatePDO()
    {
        try
        {
            $this->handle = new PDO("mysql:dbname=" . $this->database . ";host=" . $this->server,
                $this->user, $this->password);

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
            if (count($argv) == 2)
            {
                if (!is_array($argv[1]))
                    throw new InvalidArgumentException("Invalid query parameters");
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
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return false;
        }
    }
}