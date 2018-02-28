<?
require_once('functions.php');

$signup_form = include_template('templates/signup_form.php', [
   /*'errors' => [],
    'classname' => '',
    'err_message' => '',*/
]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup'])) {
        $sign_up = $_POST;
        $required = ['email', 'password', 'name'];
        $errors = [];
        $classname = '';
        $err_message = '';

        foreach ($required as $value) {
            if (empty($sign_up[$value])) {
                $errors[$value] = 'Это поле надо заполнить';
            }
        }

        if (count($errors)) {
            $signup_form = include_template('templates/signup_form.php', [
                'errors' => $errors,
                'classname' => $classname,
                'err_message' => $err_message,
            ]);
            print_r('validation failed');
        } else {
            print_r('validation completed');
        }
    }
}

$signup_page = include_template('templates/register.php', [
    'signup_form' => $signup_form,
    'title' => 'Дела в порядке'
]);
print($signup_page);
?>

