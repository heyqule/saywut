<?php
/**
 * Created by JetBrains PhpStorm.
 * User: heyqule
 * Date: 7/7/13
 * Time: 11:11 AM
 * To change this template use File | Settings | File Templates.
 */
define('PASSWORD','12341234');

session_start();

if(empty($_SESSION['public_key'])) {
    $_SESSION['public_key'] = uniqid();
}

header('Cache-Control: max-age=0');

if($_SESSION['public_key'] == $_POST['public_key']
    && PASSWORD == $_POST['password']) {
    $_SESSION['is_logged'] = 1;
    unset($_SESSION['public_key']);
    header( 'Location: index.php' ) ;
}


?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>
    <h1>Saywut Admin Login</h1>
    <form action="" method="post">
        <input type="hidden" name="public_key" value="<?php echo $_SESSION['public_key']; ?>" />
        <input type="password" name="password" />
        <input type="submit" />
    </form>
</body>