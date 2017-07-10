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
        if (self::$instance)
            self::$instance->instance = new StudentsDBConnection();
        return self::$instance->instance;
    }
    private static $instance;

    const SERVER = "mysql.hostinger.com.ua";
    const DATABASE = "u139489065_istud";
    const USER = "u139489065_odmen";
    const PASSWORD = "avengersStudy";

    private $dbConnection;

    private function __construct()
    {
        $this->dbConnection = new PDOMySQLConection(self::SERVER, self::DATABASE,
            self::USER, self::PASSWORD);
    }
    public function Query($sql/*, args*/)
    {
        return $this->dbConnection->Query($sql, array_slice(func_get_args(), 1));
    }
}