<?php
//Проверяем пришли ли данные
if(isset($_POST['login']) && isset($_POST['password']))
{
//Записываем все в переменные
    $login=htmlspecialchars(trim($_POST['login']));
    $password=htmlspecialchars(trim($_POST['password']));
    $name=htmlspecialchars(trim($_POST['name']));
//Проверяем на пустоту
    if($login=="" || $password=="" || $name=="")
    {
        die("Заполните все поля!");
    }
//Подключаем базу данных
    $mysql = new mysqli("localhost", "root", "", "mycite");
//Достаем из БД информацию о введенном логине
    $res = $mysql ->query("SELECT `login` FROM `users` WHERE `login`='$login'");
    $data = $res ->fetch_array();
//Если он не пуст, то значит такой уже есть
    if(!empty($data['login']))
    {
        die("Такой логин уже существует!");
    }
//Проверяем длину пароля
    if(strlen($password)<3)
    {
        die("Длина пароля не может быть меньше 3 символов!");
    }
//Вставляем данные в БД
    $result = $mysql->query("INSERT INTO `users` (`login`,`password`,`name`) VALUES('$login','$password','$name') ");
//Если данные успешно занесены в таблицу
    if($result)
    {
        ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <style>
            div{
                height: 100vh;
                display: flex;
                flex-direction: column;
                flex-wrap: nowrap;
                justify-content: center;
                align-items: center;
                align-content: center;
                gap: 1em;
            }
            #button{
                width: 150px;
            }
        </style>
        <div>
            <table>
                <tr>
                    <h3>
                        Вы успешно зарегистрированы!
                    </h3>
                </tr>
                <tr>
                    <form action="/autorization.php">
                        <input type="submit" value="Вход" class="btn btn-success" id="button">
                    </form>
                </tr>
            </table>
        </div>
        <?php
    }
//Если не так, то выводим ошибку
    else
    {
        echo "Error! ----> ". mysqli_error($mysql); //mysql_error();
    }
}
