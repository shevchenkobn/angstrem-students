<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 09.07.17
 * Time: 13:12
 */
class StudentsDBConnection implements IDBConnection
{
    public static function GetInstance()
    {
        if (!self::$instance)
            self::$instance = new StudentsDBConnection();
        return self::$instance;
    }
    private static $instance;

    const SERVER = "mysql.hostinger.com.ua";
    const DATABASE = "u493075740_istud";
    const USER = "u493075740_odmen";
    const PASSWORD = "avengersStudy";

    private $dbConnection;

    private function __construct()
    {
        $this->dbConnection = new PDOMySQLConection(self::SERVER, self::DATABASE,
            self::USER, self::PASSWORD);
    }
    public function Query($sql/*, args*/)
    {
        $args = func_get_args();
        if (count($args) > 1 && is_array($args[1]))
            return $this->dbConnection->Query($sql, $args[1]);
        else
            return $this->dbConnection->Query($sql, array_slice(func_get_args(), 1));
    }
}