@extends('layouts.app', [
    'title' => 'авторизация',
    'bodyClass' => 'page',
    'activeGuestPage' => 'login',
])

@section('content')
  <main class="page__main page__main--login">
    <div class="container">
      <h1 class="page__title page__title--login">Вход</h1>
    </div>
    <section class="login container">
      <h2 class="visually-hidden">Форма авторизации</h2>

      @if (session('status'))
        <p class="search__no-results-info">{{ session('status') }}</p>
      @endif

      <form class="login__form form" action="{{ route('web.login') }}" method="post">
        @csrf
        <div class="form__text-inputs-wrapper">
          <div class="form__text-inputs">
            <div class="login__input-wrapper form__input-wrapper">
              <label class="login__label form__label" for="login-email">Электронная почта <span class="form__input-required">*</span></label>
              <div class="form__input-section{{ $errors->has('email') ? ' form__input-section--error' : '' }}">
                <input class="login__input form__input" id="login-email" type="email" name="email" value="{{ old('email') }}" placeholder="Укажите эл.почту">
                @include('pages.partials.form-field-error', ['field' => 'email'])
              </div>
            </div>
            <div class="login__input-wrapper form__input-wrapper">
              <label class="login__label form__label" for="login-password">Пароль <span class="form__input-required">*</span></label>
              <div class="form__input-section{{ $errors->has('password') ? ' form__input-section--error' : '' }}">
                <input class="login__input form__input" id="login-password" type="password" name="password" placeholder="Введите пароль">
                @include('pages.partials.form-field-error', ['field' => 'password'])
              </div>
            </div>
          </div>

          @include('pages.partials.form-errors')
        </div>
        <button class="login__submit button button--main" type="submit">Отправить</button>
      </form>
    </section>
  </main>
@endsection
