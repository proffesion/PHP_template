<?php
require_once 'core/init.php';

if (Session::exists('home')) {
    echo '<p>' . Session::flash('home') . '</p>';
}
$user = new User(); // curent user
if ($user->isloggedIn()) {
?>
 
 <p>Hello <a href="profile.php?user=<?php echo e($user->data()->username); ?>"><?php echo e($user->data()->username); ?></a></p>

 <ul>
    <li><a href="logout.php">Logout</a></li>
    <li><a href="update.php">Update Details</a></li>
    <li><a href="changepassword.php">Change Password</a></li>
 </ul>
<?php

if ($user->hasPermission('admin')) {
    echo '<p> You are the administrator </p>';
}

if ($user->hasPermission('moderator')) {
    echo '<p> You are the moderator </p>';
}

} else {
    echo '<p> You need to <a href="login.php"> login </a> or <a href="register.php"> register </a> </p>';
}


// echo $user->data()->username;
// $user = new User(6);










// echo random_bytes(32);
# code...
// $user = DB::getInstance()->update('users', 3, array(
// 	'password' => 'password_new',
// 	'name' 	   => 'jonathan new'
// ));

