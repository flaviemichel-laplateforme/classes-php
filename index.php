<?php
require_once "./UserPdo.php";

$userPdo = new UserPdo();


// $user = $userPdo->register("flavie", "aaaaaa", "flavie@gmail.com", "flavie", "michel");
// var_dump($user);

$userConnect = $userPdo->connect("flavie", "aaaaaa");
var_dump($userConnect);

echo $userPdo->getId();
