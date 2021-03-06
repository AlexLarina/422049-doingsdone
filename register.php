<?
require_once('functions.php');
require_once('data.php');
require_once('init.php');
require_once ('mysql_helper.php');

$data = [];
$session = null;

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

        if(!empty($sign_up['email'])){
            $user = searchUserByEmailInDB($db_link, $sign_up['email']);
            if($user){
                $errors['email'] = 'Пользователь с таким email уже существует';
            }
        }

        if (!empty($sign_up['email']) && !filter_var($sign_up['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный формат email';
        }

        if (count($errors)) {
            $data =  [
                'errors' => $errors,
                'classname' => $classname,
                'err_message' => $err_message,
                'sign_up' => $sign_up
            ];
        } else {
            $password = password_hash($sign_up['password'], PASSWORD_DEFAULT);
            $sql = 'INSERT INTO users (email, password, name, dt_reg) VALUES(?, ?, ?, NOW())';
            $stmt = db_get_prepare_stmt($db_link, $sql, [$sign_up['email'], $password, $sign_up['name']]);
            $result = mysqli_stmt_execute($stmt);

            $new_email = mysqli_real_escape_string($db_link, $sign_up['email']);
            $id_query = mysqli_query($db_link, "SELECT id FROM users WHERE email = '".$new_email."'");
            $user_id = mysqli_fetch_assoc($id_query);
            $default_project = mysqli_query($db_link, "INSERT INTO projects 
                                            SET name = 'Входящие', user_id = '".$user_id['id']."'");

            if($result){
                header('Location: index.php?login');
            } else {
                echo mysqli_error($db_link);
                exit();
            }
        }
    }
}

$signup_form = include_template('templates/signup_form.php', $data);
$signup_page = include_template('templates/register.php', [
    'signup_form' => $signup_form,
    'session' => $session,
    'title' => 'Дела в порядке'
]);
print($signup_page);
?>

