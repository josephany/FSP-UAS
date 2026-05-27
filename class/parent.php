<?php
require_once("data.php");

class OrangTua
{
    protected $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);
    }

    public function __destruct()
    {
        $this->mysqli->close();
    }
}
