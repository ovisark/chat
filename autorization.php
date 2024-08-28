<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">-->
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
            /*gap: 1em;*/
        }
        #submit {
            margin-top: 10px;
            margin-right: 2px;
            margin-left: 2px;
            width: 100px;
        }
    </style>
</head>
<body>
<?php
//Запускаем сессию для работы с куки файлами
session_start();
//если отсутствует логин и айди то форма авторизации регистрации

if(!isset($_SESSION['login']) || !isset($_SESSION['id']))
{
    ?>
    <div>
        <table>
            <tr>
                <form action="/login.php" method="POST">
                    <h1>
                        Вход
                    </h1>
            </tr>
            <tr>
                Логин:
                <label>
                    <input type="text" name="login" class="form-control">
                </label>
            </tr>
            <tr>
                Пароль:
                <label>
                    <input type="password" name="password" class="form-control">
                </label>
            </tr>
            <tr>
                <td>

                    <input type="submit" value="Вход" class="btn btn-success" id="submit">
                    </form>
                </td>
                <td>
                    <form action="/index.php">
                        <input type="submit" value="Назад" class="btn btn-success" id="submit">
                    </form>
                </td>
            </tr>
        </table>
    </div>

    <?php
}
//Если сессия запущена, то есть присутствуют файлы
//кукис, и в них есть логин и айди
//то отображаем профиль пользователя
//Для этого достаем из базы все данные по логину
//и выводим их на странице
if(isset($_SESSION['login']) && isset($_SESSION['id']))
{
    $mysql = new mysqli("localhost", "root", "", "mycite"); //создаем подключение к базе данных
    $user=$_SESSION['login'];
    $res = $mysql-> query("SELECT * FROM `users` WHERE `login`='$user'"); //запрос в базу данных
    $user_data = $res->fetch_array(); //помещает в переменную user_data данные пользователя из запроса в бд
    echo "<div style=\"text-align: center;\">";
    echo "Ваш логин: <b>". $user_data['login']."</b><br>";
    echo "Ваше имя: <b>". $user_data['name']."</b><br>";
    echo "<a href='exit.php'>Выход</a>";
    include("chat.php");//вставка файла
    echo "</center>";
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>