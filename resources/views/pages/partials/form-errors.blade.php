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
