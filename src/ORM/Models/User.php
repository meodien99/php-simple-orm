<?php
namespace ORM\Models;

use ORM\Entities\Entity;

class User extends Entity{

    protected  $table = "users";
    protected $primary_keys = ['id'];
    protected $fields = ['id','username','email','age','gender'];


    public function info(){
        return "#". $this->id .":". $this->username ." | ". $this->email ." | ". $this->age ." | ". $this->gender;
    }
} 