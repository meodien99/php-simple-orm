<?php

require_once __DIR__."/./vendor/autoload.php";

use ORM\DB\DBContext;
use ORM\Models\User;

$db = new DBContext();

$queries = [
    "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username VARCHAR(255), email VARCHAR(255), age INTEGER(11) NOT NULL, gender VARCHAR(255))",
    "INSERT INTO users(username, email, age, gender) VALUES('username1', 'email1@mail.test.com', 21, 'male')",
    "INSERT INTO users(username, email, age, gender) VALUES('username2', 'email2@mail.test.com', 31, 'male')",
    "INSERT INTO users(username, email, age, gender) VALUES('username3', 'email3@mail.test.com', 41, 'female')",
];

$db->init($queries);
/****ADD NEW USER****/
###### NEW I ######
$user1 = new User();
$user1->username = "User 1";
$user1->email = "User1@google.mail.com";
$user1->age = 31;
$user1->gender = "male";
$db->add($user1)->save();

###### NEW II ######
$user2 = new User();
$data = [
    'username' => 'User 2',
    'email'    => 'User2@google.mail.com',
    'age'      => 21,
    'gender'   => 'female'
];
$user2->add($data);

/*********UPDATE**********/
######### EDIT I #########
$user_edit1 = $db->find("User", ['id'=>'3']);
$user_edit1->email = "User1.updated@google.mail.com";
$db->update($user_edit1)->save();

######### EDIT II #########
$user_edit2 = $db->find("User", ['id'=>'2']);
$data = [
    'email' => 'User2.updated@updated.com',
    'username' => 'username2'
];
//$user_edit2->update($data);

/**********DELETE***********/
######### DELETE I #########

//$db->remove($user_edit1)->save();

######### DELETE II ##########

//$user_edit2->remove();
/****Output****/
$users = $db->all("User");

foreach($users as $user){
    echo $user->info()."\n";
}

/**********CLEAN *************/
$queries = "DROP TABLE users";
$db->init($queries);