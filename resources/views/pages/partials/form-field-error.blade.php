@if ($errors->has($field))
  <button class="form__error-button button" type="button">
    !
    <span class="visually-hidden">Информация об ошибке</span>
  </button>
  <div class="form__error-text">
    <h3 class="form__error-title">{{ $title ?? 'Ошибка валидации' }}</h3>
    <p class="form__error-desc">{{ $errors->first($field) }}</p>
  </div>
@endif
