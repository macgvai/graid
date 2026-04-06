<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>readme: регистрация</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  </head>
  <body class="page">
    <header class="header">
      <div class="header__wrapper container">
        <div class="header__logo-wrapper">
          <a class="header__logo-link" href="{{ route('main') }}">
            <img class="header__logo" src="{{ asset('img/logo.svg') }}" alt="Логотип readme" width="128" height="24">
          </a>
          <p class="header__topic">
            micro blogging
          </p>
        </div>
        <div class="header__nav-wrapper">
          <nav class="header__nav">
            <ul class="header__user-nav">
              <li class="header__authorization">
                <a class="header__user-button header__authorization-button button" href="{{ route('login') }}">Вход</a>
              </li>
              <li>
                <a class="header__user-button header__user-button--active header__register-button button">Регистрация</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </header>

    <main class="page__main page__main--registration">
      <div class="container">
        <h1 class="page__title page__title--registration">Регистрация</h1>
      </div>
      <section class="registration container">
        <h2 class="visually-hidden">Форма регистрации</h2>
        <form class="registration__form form" action="{{ route('registration.store') }}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
              <div class="registration__input-wrapper form__input-wrapper">
                <label class="registration__label form__label" for="registration-email">Электронная почта <span class="form__input-required">*</span></label>
                <div class="form__input-section @error('email') form__input-section--error @enderror">
                  <input class="registration__input form__input" id="registration-email" type="email" name="email" value="{{ old('email') }}" placeholder="Укажите эл.почту">
                  @error('email')
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                    <div class="form__error-text">
                      <h3 class="form__error-title">Ошибка в поле</h3>
                      <p class="form__error-desc">{{ $message }}</p>
                    </div>
                  @enderror
                </div>
              </div>

              <div class="registration__input-wrapper form__input-wrapper">
                <label class="registration__label form__label" for="registration-login">Логин <span class="form__input-required">*</span></label>
                <div class="form__input-section @error('login') form__input-section--error @enderror">
                  <input class="registration__input form__input" id="registration-login" type="text" name="login" value="{{ old('login') }}" placeholder="Укажите логин">
                  @error('login')
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                    <div class="form__error-text">
                      <h3 class="form__error-title">Ошибка в поле</h3>
                      <p class="form__error-desc">{{ $message }}</p>
                    </div>
                  @enderror
                </div>
              </div>

              <div class="registration__input-wrapper form__input-wrapper">
                <label class="registration__label form__label" for="registration-password">Пароль <span class="form__input-required">*</span></label>
                <div class="form__input-section @error('password') form__input-section--error @enderror">
                  <input class="registration__input form__input" id="registration-password" type="password" name="password" placeholder="Придумайте пароль">
                  @error('password')
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                    <div class="form__error-text">
                      <h3 class="form__error-title">Ошибка в поле</h3>
                      <p class="form__error-desc">{{ $message }}</p>
                    </div>
                  @enderror
                </div>
              </div>

              <div class="registration__input-wrapper form__input-wrapper">
                <label class="registration__label form__label" for="registration-password-repeat">Повтор пароля <span class="form__input-required">*</span></label>
                <div class="form__input-section @error('password') form__input-section--error @enderror">
                  <input class="registration__input form__input" id="registration-password-repeat" type="password" name="password_confirmation" placeholder="Повторите пароль">
                  @error('password')
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                    <div class="form__error-text">
                      <h3 class="form__error-title">Ошибка в поле</h3>
                      <p class="form__error-desc">{{ $message }}</p>
                    </div>
                  @enderror
                </div>
              </div>
            </div>

            @if ($errors->any())
              <div class="form__invalid-block">
                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                <ul class="form__invalid-list">
                  @foreach ($errors->all() as $error)
                    <li class="form__invalid-item">{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>

            <div class="registration__input-file-container form__input-container form__input-container--file">
                <div class="registration__input-file-wrapper form__input-file-wrapper">
                    <div class="registration__file-zone form__file-zone dropzone">
                        <input class="registration__input-file form__input-file" id="userpic-file" type="file" name="avatar" title=" ">
                        <div class="form__file-zone-text">
                            <span>Перетащите фото сюда</span>
                        </div>
                    </div>
                    <button class="registration__input-file-button form__input-file-button button" type="button">
                        <span>Выбрать фото</span>
                        <svg class="registration__attach-icon form__attach-icon" width="10" height="20">
                            <use xlink:href="#icon-attach"></use>
                        </svg>
                    </button>
                </div>
                <div class="registration__file form__file dropzone-previews">

                </div>
            </div>

          <button class="registration__submit button button--main" type="submit">Отправить</button>
        </form>
      </section>
    </main>

    <footer class="footer">
      <div class="footer__wrapper">
        <div class="footer__container container">
          <div class="footer__site-info">
            <p class="footer__license">
              При использовании любых материалов с сайта обязательно указание Readme в качестве источника.
            </p>
          </div>
          <div class="footer__my-info">
            <ul class="footer__my-pages">
              <li class="footer__my-page footer__my-page--feed">
                <a class="footer__page-link" href="{{ route('feed') }}">Моя лента</a>
              </li>
              <li class="footer__my-page footer__my-page--popular">
                <a class="footer__page-link" href="{{ route('popular') }}">Популярный контент</a>
              </li>
              <li class="footer__my-page footer__my-page--messages">
                <a class="footer__page-link" href="{{ route('messages') }}">Личные сообщения</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
  </body>
</html>
