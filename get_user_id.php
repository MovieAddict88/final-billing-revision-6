<?php
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

$users = $admins->fetchAdmin(100);
foreach ($users as $user) {
    if ($user->user_name == 'testemployer') {
        echo $user->user_id;
        break;
    }
}
