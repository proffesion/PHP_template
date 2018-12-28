<?php
include 'core/init.php';

if (!$username = Input::get('user')) {
    Redirect::to('index.php');
} else {
    $user = new User($username);
    if (!$user->exists(404)) {
        Redirect::to(404);
    } else {
        $data = $user->data();
    }
    ?>

    <h3><?php echo e($data->username); ?></h3>
    <p><?php echo e($data->name); ?></p>

    <?php
}