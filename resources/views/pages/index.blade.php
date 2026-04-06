<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>readme: блог, каким он должен быть</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  </head>
  <body>
    <ul>
      <li>
        <a href="{{ route('main') }}">Главная</a>
      </li>
      <li>
        <a href="{{ route('login') }}">Вход</a>
      </li>
      <li>
        <a href="{{ route('feed') }}">Моя лента</a>
      </li>
      <li>
        <a href="{{ route('no-content') }}">[Демонстрация] Моя лента (нет публикаций)</a>
      </li>
      <li>
        <a href="{{ route('messages') }}">Личные сообщения</a>
      </li>
      <li>
        <a href="{{ route('no-results') }}">Результаты поиска (нет результатов)</a>
      </li>
      <li>
        <a href="{{ route('popular') }}">Популярное</a>
      </li>
      <li>
        <a href="{{ route('post-details') }}">Публикация</a>
      </li>
      <li>
        <a href="{{ route('profile') }}">Профиль</a>
      </li>
      <li>
        <a href="{{ route('search-results') }}">Результаты поиска</a>
      </li>
      <li>
        <a href="{{ route('registration') }}">Регистрация</a>
      </li>
      <li>
        <a href="{{ route('adding-post') }}">Форма добавления поста</a>
      </li>
      <li>
        <a href="{{ route('modal') }}">[Демонстрация] Модальное окно</a>
      </li>
      <li>
        <a href="{{ route('reg-validation') }}">[Демонстрация] Непройденная валидация (форма регистрации)</a>
      </li>
      <li>
        <a href="{{ route('login-validation') }}">[Демонстрация] Непройденная валидация (форма авторизации на главной)</a>
      </li>
    </ul>
  </body>
</html>