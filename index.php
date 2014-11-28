<?php

require_once __DIR__."/./vendor/autoload.php";

use ORM\DB\DBContext;
use ORM\Models\User;

$db = new DBContext();

/****ADD NEW USER****/
###### I ######
$user1 = new User();
$user1->username = "User 1";
$user1->email = "User1@google.mail.com";
$user1->age = 31;
$user1->gender = "male";
#WORKED !>> $db->add($user1)->save();

###### II ######
$user2 = new User();
$data = [
    'username' => 'User 2',
    'email'    => 'User2@google.mail.com',
    'age'      => 21,
    'gender'   => 'female'
];
#WORKED !>> $user2->add($data);

/*********UPDATE**********/
######### I #########
$user_edit1 = $db->find("User", ['id'=>'3']);
$user_edit1->email = "User1.updated@google.mail.com";
#WORKED !>> $db->update($user)->save();

######### II #########
$user_edit2 = $db->find("User", ['id'=>'2']);
$data = [
    'email' => 'User2.updated@updated.com',
    'username' => 'username2'
];
#WORKED !>> $user_edit2->update($data);

/**********DELETE***********/
######### I #########

#WORKED !>>$db->remove($user_edit1)->save();

######### II ##########

#WORKED !>>$user_edit2->remove();
/****Output****/
$users = $db->all("User");

foreach($users as $user){
    echo $user->info()."\n";
}
