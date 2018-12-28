<?php
require_once 'core/init.php';

$user = new User();

if (!$user->isLoggedIn()) {
    Redirect::to('index.php');
}

if (Input::exists()) {
    if (Token::check(Input::get('token'))) {
        // validate
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'name' => array(
                'required' => true,
                'max'      => 50,
                'min'      => 4
            )
        ));

        if ($validation->passed()) {
            # Update
            try {
                
                $user->update(array(
                    'name' => Input::get('name')
                ));
                /*
                $user->update(array(
                    'name' => Input::get('name')
                ) $id); // update user
                */
                
                Session::flash('home', 'Your details has been updated');
                Redirect::to('index.php');

            } catch (Exception $e) {
                die($e->getMessage());
            }

        } else {
            foreach ($validation->errors() as $error) {
                echo $error, '<br>';
            }
        }
        
    }
}
?>

<form action="" method="post">
    <div class="field">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?php echo e($user->data()->name); ?>">

    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    <input type="submit" value="Update">

    </div>
</form>