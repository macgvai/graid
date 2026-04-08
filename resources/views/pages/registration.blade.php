@extends('layouts.app', [
    'title' => 'регистрация',
    'bodyClass' => 'page',
    'activeGuestPage' => 'registration',
])

@section('content')
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
              <div class="form__input-section{{ $errors->has('email') ? ' form__input-section--error' : '' }}">
                <input class="registration__input form__input" id="registration-email" type="email" name="email" value="{{ old('email') }}" placeholder="Укажите эл.почту">
                @include('pages.partials.form-field-error', ['field' => 'email'])
              </div>
            </div>
            <div class="registration__input-wrapper form__input-wrapper">
              <label class="registration__label form__label" for="registration-login">Логин <span class="form__input-required">*</span></label>
              <div class="form__input-section{{ $errors->has('login') ? ' form__input-section--error' : '' }}">
                <input class="registration__input form__input" id="registration-login" type="text" name="login" value="{{ old('login') }}" placeholder="Укажите логин">
                @include('pages.partials.form-field-error', ['field' => 'login'])
              </div>
            </div>
            <div class="registration__input-wrapper form__input-wrapper">
              <label class="registration__label form__label" for="registration-password">Пароль<span class="form__input-required">*</span></label>
              <div class="form__input-section{{ $errors->has('password') ? ' form__input-section--error' : '' }}">
                <input class="registration__input form__input" id="registration-password" type="password" name="password" placeholder="Придумайте пароль">
                @include('pages.partials.form-field-error', ['field' => 'password'])
              </div>
            </div>
            <div class="registration__input-wrapper form__input-wrapper">
              <label class="registration__label form__label" for="registration-password-repeat">Повтор пароля<span class="form__input-required">*</span></label>
              <div class="form__input-section{{ $errors->has('password_confirmation') ? ' form__input-section--error' : '' }}">
                <input class="registration__input form__input" id="registration-password-repeat" type="password" name="password_confirmation" placeholder="Повторите пароль">
                @include('pages.partials.form-field-error', ['field' => 'password_confirmation'])
              </div>
            </div>
          </div>

          @include('pages.partials.form-errors')
        </div>
        <div class="registration__input-file-container form__input-container form__input-container--file">
          <div class="registration__input-file-wrapper form__input-file-wrapper">
            <div class="registration__file-zone form__file-zone">
              <input class="registration__input-file form__input-file" id="userpic-file" type="file" name="avatar" accept="image/png,image/jpeg,image/gif" title=" ">
              <div class="form__file-zone-text">
                <span>Перетащите фото сюда</span>
              </div>
            </div>
            <label class="registration__input-file-button form__input-file-button button" for="userpic-file">
              <span>Выбрать фото</span>
              <svg class="registration__attach-icon form__attach-icon" width="10" height="20">
                <use xlink:href="#icon-attach"></use>
              </svg>
            </label>
          </div>
          <div class="registration__file form__file dropzone-previews">
          </div>
        </div>
        <button class="registration__submit button button--main" type="submit">Отправить</button>
      </form>
    </section>
  </main>
@endsection

@push('scripts')
  <script>
    (function () {
      var input = document.getElementById('userpic-file');
      var previews = document.querySelector('.registration__file.dropzone-previews');

      if (!input || !previews) {
        return;
      }

      input.addEventListener('change', function () {
        previews.innerHTML = '';

        if (!input.files || !input.files[0]) {
          return;
        }

        var file = input.files[0];
        var reader = new FileReader();

        reader.addEventListener('load', function (event) {
          previews.innerHTML =
            '<div class="registration__image-wrapper form__file-wrapper">' +
              '<img class="form__image" src="' + event.target.result + '" alt="Превью аватара">' +
            '</div>' +
            '<div class="registration__file-data form__file-data">' +
              '<span class="registration__file-name form__file-name">' + file.name + '</span>' +
            '</div>';
        });

        reader.readAsDataURL(file);
      });
    })();
  </script>
@endpush
