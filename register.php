<?
require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup'])) {
        $signup = $_POST;
        $required = ['email', 'password', 'name'];
        $errors = [];
        foreach ($required as $value) {
            if (empty($signup[$value])) {
                $errors[$value] = 'Это поле надо заполнить';
            }
        }
        print_r('welcome');
    }
}

$reg_form = include_template('templates/register.php', [
    'errors' => [],
    'classname' => '',
    'err_message' => '',
    'signup' => '',
    'title' => 'Дела в порядке'
]);
print($reg_form);
?>

