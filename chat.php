<!-- Стили для блока с сообщениями -->
<style>
    /* Основной контейнер чата */
    .chat-container {
        display: flex; /* Используем Flexbox для гибкого расположения элементов */
        flex-direction: column; /* Располагаем элементы вертикально */
        height: 80vh; /* Устанавливаем высоту контейнера чата как 80% высоты окна */
        width: 50vw; /* Ширина чата 50% от ширины экрана */
        border: 1px solid silver; /* Граница контейнера */
        margin: 0 auto; /* Центрирование контейнера по горизонтали */
        overflow: hidden; /* Отключаем прокрутку контейнера */
    }

    /* Блок для сообщений */
    #messages {
        flex: 1; /* Занимает всё доступное пространство внутри контейнера */
        overflow-y: auto; /* Прокрутка по вертикали */
        border-bottom: 1px solid silver; /* Нижняя граница блока */
        padding: 10px; /* Внутренние отступы */
        box-sizing: border-box; /* Включаем padding и border в ширину элемента */
    }

    /* Поле ввода сообщения и кнопка отправки */
    #mess_to_send, #submit {
        width: 100%; /* Полная ширина родительского контейнера */
        box-sizing: border-box; /* Включаем padding и border в ширину элемента */
    }

    /* Поле ввода сообщения */
    #mess_to_send {
        height: 36px; /* Высота поля ввода */
        margin-bottom: 10px; /* Отступ между полем ввода и кнопкой отправки */
    }

    /* Кнопка отправки */
    #submit {
        height: 36px; /* Высота кнопки, выравниваем по высоте с полем ввода */
    }

    /* Кнопка выхода */
    #exit {
        margin-bottom: 1em; /* Нижний отступ */
        margin-top: 1em; /* Верхний отступ */
    }

    /* Общий стиль сообщения */
    .message {
        cursor: pointer; /* Курсор изменяется на указатель при наведении */
        padding: 5px; /* Внутренние отступы */
        border-bottom: 1px solid #ddd; /* Нижняя граница сообщения */
        margin-bottom: 5px; /* Отступ снизу */
        display: flex; /* Flexbox для гибкого расположения */
        flex-direction: column; /* Вертикальное расположение элементов */
    }

    /* Контент сообщения */
    .message-content {
        display: flex; /* Flexbox для горизонтального расположения */
        align-items: flex-start; /* Выравнивание по верху */
    }

    /* Логин отправителя сообщения */
    .message-login {
        font-weight: bold; /* Жирный шрифт */
        color: orange; /* Цвет текста */
        margin-right: 5px; /* Отступ справа */
    }

    /* Текст сообщения */
    .message-text {
        word-wrap: break-word; /* Перенос слов для длинных текстов */
    }

    /* Цитируемое сообщение */
    .reply {
        margin-left: 20px; /* Отступ слева */
        font-style: italic; /* Курсив */
        color: #555; /* Цвет текста */
        border-left: 2px solid #555; /* Левая граница */
        padding-left: 10px; /* Отступ слева */
        margin-bottom: 5px; /* Отступ снизу */
    }

    /* Блок для цитаты */
    #quote {
        margin-bottom: 10px; /* Отступ снизу */
        padding: 10px; /* Внутренние отступы */
        border-left: 2px solid #555; /* Левая граница */
        background-color: #f9f9f9; /* Фоновый цвет */
        max-height: 100px; /* Максимальная высота */
        overflow: auto; /* Прокрутка при переполнении */
        display: none; /* Скрыт по умолчанию */
    }
</style>
<!-- HTML структура чата -->
<div class="chat-container">
    <div>
        <!-- Dropdown для выбора комнаты -->
        <label for="room_select">Выберите комнату:</label>
        <select id="room_select" onchange="changeRoom(this.value)">
            <?php
            $mysql = new mysqli("localhost", "root", "", "mycite");
            $result = $mysql->query("SELECT * FROM rooms");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            ?>
        </select>
    </div>

    <div id="messages"></div> <!-- Блок для сообщений -->
    <div id="quote"></div> <!-- Блок для цитируемого сообщения -->
    <button id="cancel_quote" onclick="cancelQuote()" style="display:none;">Отменить цитирование</button> <!-- Кнопка отмены цитирования -->
    <form action="javascript:send();"> <!-- Форма отправки сообщения -->
        <input type="hidden" id="reply_to" name="reply_to" value=""> <!-- Поле для ID цитируемого сообщения -->
        <input type="text" id="mess_to_send"> <!-- Поле ввода сообщения -->
        <input type="submit" value="Отправить" id="submit" class="btn btn-success"> <!-- Кнопка отправки сообщения -->
    </form>
</div>
<!-- Форма для выхода -->
<form action="/exit.php">
    <input type="submit" value="Выход" id="exit" class="btn btn-danger"> <!-- Кнопка выхода -->
</form>

<!-- Подключаем jQuery -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script type="text/javascript">
    function cancelQuote() {
        $("#reply_to").val(''); // Очищаем поле для ID цитируемого сообщения
        $("#quote").hide(); // Скрываем блок цитаты
        $("#cancel_quote").hide(); // Скрываем кнопку отмены цитирования
        adjustChatHeight(); // Адаптируем высоту чата после отмены цитирования
    }
    // Текущая комната
    let currentRoom = $("#room_select").val(); 

    function changeRoom(roomId) {
        currentRoom = roomId; // Обновляем текущую комнату
        load_messes(); // Перезагружаем сообщения для выбранной комнаты
    }

    // Функция для отправки сообщения
    function send() {
    let mess = $("#mess_to_send").val();
    let replyTo = $("#reply_to").val();

    $.ajax({
        type: "POST",
        url: "add_mess.php",
        data: {
            mess: mess,
            reply_to: replyTo,
            room_id: currentRoom // Передаем ID комнаты
        },
        success: function () {
            console.log("Сообщение отправлено успешно"); // Логируем успех
            load_messes(); // Загружаем сообщения
            $("#mess_to_send").val('');
            $("#reply_to").val('');
            $("#quote").hide();
            $("#cancel_quote").hide();
            adjustChatHeight();
        },
        error: function (xhr, status, error) {
            console.error("Ошибка при отправке сообщения: " + error); // Логируем ошибку
        }
    });
}

    // Обновляем функцию загрузки сообщений для работы с комнатами
    function load_messes() {
        $.ajax({
            type: "POST",
            url: "/load_messes.php",
            data: {
                room_id: currentRoom // Передаем ID комнаты
            },
            dataType: "json",
            success: function (messages) {
                $("#messages").empty();

                messages.forEach(function (message) {
                    let messageHtml = "<div class='message' data-id='" + message.id + "' data-login='" + message.login + "' data-text='" + message.message + "'>";

                    if (message.reply_to) {
                        messageHtml += "<div class='reply'><b><span>" + message.reply_login + ": </span></b>" + message.reply_message + "</div>";
                    }

                    messageHtml += "<div class='message-content'><span class='message-login'>" + message.login + ":</span><span class='message-text'>" + message.message + "</span></div></div>";

                    $("#messages").append(messageHtml);
                });

                $(".message").on("click", function () {
                    let messageId = $(this).data("id");
                    let messageText = $(this).data("text");
                    let messageLogin = $(this).data("login");
                    $("#reply_to").val(messageId);
                    $("#quote").html('<b>' + messageLogin + ':</b> ' + messageText).show();
                    $("#cancel_quote").show();
                    adjustChatHeight();
                });
            }
        });
    }

    $(document).ready(function () {
        load_messes(); // Загружаем сообщения при старте
        setInterval(load_messes, 3000); // Обновляем сообщения каждые 3 секунды
    });
</script>
