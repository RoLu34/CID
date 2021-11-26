<?php

namespace Database;

class Connection{

private $db_name;
private $db_password;
private $db_username;
private $uniqid;

public function __construct($db_name, $db_password, $db_username){

    $this->db_name = $db_name;
    $this->db_password = $db_password;
    $this->db_username = $db_username;
    $this->uniqid = uniqid();

}

}