@php
  $activePostType = \App\Enums\PostType::tryFrom((int) old('post_type', request('type', \App\Enums\PostType::Photo->value)))
      ?? \App\Enums\PostType::Photo;
@endphp

@extends('layouts.app', [
    'title' => 'добавление публикации',
    'bodyClass' => 'page',
])

@section('content')
  <main class="page__main page__main--adding-post">
    <div class="page__main-section">
      <div class="container">
        <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
      </div>
      <div class="adding-post container">
        <div class="adding-post__tabs-wrapper tabs">
          <div class="adding-post__tabs filters">
            <ul class="adding-post__tabs-list filters__list tabs__list">
              @foreach (\App\Enums\PostType::cases() as $type)
                @php
                  $dimensions = match ($type) {
                      \App\Enums\PostType::Photo => ['width' => 22, 'height' => 18],
                      \App\Enums\PostType::Video => ['width' => 24, 'height' => 16],
                      \App\Enums\PostType::Text => ['width' => 20, 'height' => 21],
                      \App\Enums\PostType::Quote => ['width' => 21, 'height' => 20],
                      \App\Enums\PostType::Link => ['width' => 21, 'height' => 18],
                  };
                @endphp
                <li class="adding-post__tabs-item filters__item">
                  <a
                    class="adding-post__tabs-link filters__button tabs__item{{ $activePostType === $type ? ' tabs__item--active' : '' }} filters__button--{{ $type->iconClass() }}{{ $activePostType === $type ? ' filters__button--active' : '' }}"
                    href="{{ route('adding-post', ['type' => $type->value]) }}"
                    data-tab-target="post-type-{{ $type->value }}"
                  >
                    <svg class="filters__icon" width="{{ $dimensions['width'] }}" height="{{ $dimensions['height'] }}">
                      <use xlink:href="#icon-filter-{{ $type->iconClass() }}"></use>
                    </svg>
                    <span>{{ $type->label() }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
          <div class="adding-post__tab-content">
            <section class="adding-post__photo tabs__content{{ $activePostType === \App\Enums\PostType::Photo ? ' tabs__content--active' : '' }}" data-tab-content="post-type-{{ \App\Enums\PostType::Photo->value }}">
              <h2 class="visually-hidden">Форма добавления фото</h2>
              <form class="adding-post__form form" action="{{ route('posts.store', \App\Enums\PostType::Photo->value) }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="post_type" value="{{ \App\Enums\PostType::Photo->value }}">
                <div class="form__text-inputs-wrapper">
                  <div class="form__text-inputs">
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="photo-heading">Заголовок <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('title') && $activePostType === \App\Enums\PostType::Photo ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="photo-heading" type="text" name="title" value="{{ $activePostType === \App\Enums\PostType::Photo ? old('title') : '' }}" placeholder="Введите заголовок">
                        @if ($activePostType === \App\Enums\PostType::Photo)
                          @include('pages.partials.form-field-error', ['field' => 'title'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
                      <div class="form__input-section{{ $errors->has('image_url') && $activePostType === \App\Enums\PostType::Photo ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="photo-url" type="text" name="image_url" value="{{ $activePostType === \App\Enums\PostType::Photo ? old('image_url') : '' }}" placeholder="Введите ссылку">
                        @if ($activePostType === \App\Enums\PostType::Photo)
                          @include('pages.partials.form-field-error', ['field' => 'image_url'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="photo-tags">Теги</label>
                      <div class="form__input-section{{ $errors->has('tags') && $activePostType === \App\Enums\PostType::Photo ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="photo-tags" type="text" name="tags" value="{{ $activePostType === \App\Enums\PostType::Photo ? old('tags') : '' }}" placeholder="Введите теги">
                        @if ($activePostType === \App\Enums\PostType::Photo)
                          @include('pages.partials.form-field-error', ['field' => 'tags'])
                        @endif
                      </div>
                    </div>
                  </div>

                  @if ($activePostType === \App\Enums\PostType::Photo)
                    @include('pages.partials.form-errors')
                  @endif
                </div>
                <div class="adding-post__input-file-container form__input-container form__input-container--file">
                  <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                    <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone">
                      <input class="adding-post__input-file form__input-file" id="userpic-file-photo" type="file" name="image_file" accept="image/png,image/jpeg,image/gif" title=" ">
                      <div class="form__file-zone-text">
                        <span>Перетащите фото сюда</span>
                      </div>
                    </div>
                    <label class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button" for="userpic-file-photo">
                      <span>Выбрать фото</span>
                      <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                        <use xlink:href="#icon-attach"></use>
                      </svg>
                    </label>
                  </div>
                  <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">
                  </div>
                </div>
                <div class="adding-post__buttons">
                  <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                  <a class="adding-post__close" href="{{ route('main') }}">Закрыть</a>
                </div>
              </form>
            </section>

            <section class="adding-post__video tabs__content{{ $activePostType === \App\Enums\PostType::Video ? ' tabs__content--active' : '' }}" data-tab-content="post-type-{{ \App\Enums\PostType::Video->value }}">
              <h2 class="visually-hidden">Форма добавления видео</h2>
              <form class="adding-post__form form" action="{{ route('posts.store', \App\Enums\PostType::Video->value) }}" method="post">
                @csrf
                <input type="hidden" name="post_type" value="{{ \App\Enums\PostType::Video->value }}">
                <div class="form__text-inputs-wrapper">
                  <div class="form__text-inputs">
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="video-heading">Заголовок <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('title') && $activePostType === \App\Enums\PostType::Video ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="video-heading" type="text" name="title" value="{{ $activePostType === \App\Enums\PostType::Video ? old('title') : '' }}" placeholder="Введите заголовок">
                        @if ($activePostType === \App\Enums\PostType::Video)
                          @include('pages.partials.form-field-error', ['field' => 'title'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('video') && $activePostType === \App\Enums\PostType::Video ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="video-url" type="text" name="video" value="{{ $activePostType === \App\Enums\PostType::Video ? old('video') : '' }}" placeholder="Введите ссылку">
                        @if ($activePostType === \App\Enums\PostType::Video)
                          @include('pages.partials.form-field-error', ['field' => 'video'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="video-tags">Теги</label>
                      <div class="form__input-section{{ $errors->has('tags') && $activePostType === \App\Enums\PostType::Video ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="video-tags" type="text" name="tags" value="{{ $activePostType === \App\Enums\PostType::Video ? old('tags') : '' }}" placeholder="Введите теги">
                        @if ($activePostType === \App\Enums\PostType::Video)
                          @include('pages.partials.form-field-error', ['field' => 'tags'])
                        @endif
                      </div>
                    </div>
                  </div>

                  @if ($activePostType === \App\Enums\PostType::Video)
                    @include('pages.partials.form-errors')
                  @endif
                </div>

                <div class="adding-post__buttons">
                  <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                  <a class="adding-post__close" href="{{ route('main') }}">Закрыть</a>
                </div>
              </form>
            </section>

            <section class="adding-post__text tabs__content{{ $activePostType === \App\Enums\PostType::Text ? ' tabs__content--active' : '' }}" data-tab-content="post-type-{{ \App\Enums\PostType::Text->value }}">
              <h2 class="visually-hidden">Форма добавления текста</h2>
              <form class="adding-post__form form" action="{{ route('posts.store', \App\Enums\PostType::Text->value) }}" method="post">
                @csrf
                <input type="hidden" name="post_type" value="{{ \App\Enums\PostType::Text->value }}">
                <div class="form__text-inputs-wrapper">
                  <div class="form__text-inputs">
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="text-heading">Заголовок <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('title') && $activePostType === \App\Enums\PostType::Text ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="text-heading" type="text" name="title" value="{{ $activePostType === \App\Enums\PostType::Text ? old('title') : '' }}" placeholder="Введите заголовок">
                        @if ($activePostType === \App\Enums\PostType::Text)
                          @include('pages.partials.form-field-error', ['field' => 'title'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__textarea-wrapper form__textarea-wrapper">
                      <label class="adding-post__label form__label" for="post-text">Текст поста <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('text_content') && $activePostType === \App\Enums\PostType::Text ? ' form__input-section--error' : '' }}">
                        <textarea class="adding-post__textarea form__textarea form__input" id="post-text" name="text_content" placeholder="Введите текст публикации">{{ $activePostType === \App\Enums\PostType::Text ? old('text_content') : '' }}</textarea>
                        @if ($activePostType === \App\Enums\PostType::Text)
                          @include('pages.partials.form-field-error', ['field' => 'text_content'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="post-tags">Теги</label>
                      <div class="form__input-section{{ $errors->has('tags') && $activePostType === \App\Enums\PostType::Text ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="post-tags" type="text" name="tags" value="{{ $activePostType === \App\Enums\PostType::Text ? old('tags') : '' }}" placeholder="Введите теги">
                        @if ($activePostType === \App\Enums\PostType::Text)
                          @include('pages.partials.form-field-error', ['field' => 'tags'])
                        @endif
                      </div>
                    </div>
                  </div>

                  @if ($activePostType === \App\Enums\PostType::Text)
                    @include('pages.partials.form-errors')
                  @endif
                </div>
                <div class="adding-post__buttons">
                  <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                  <a class="adding-post__close" href="{{ route('main') }}">Закрыть</a>
                </div>
              </form>
            </section>

            <section class="adding-post__quote tabs__content{{ $activePostType === \App\Enums\PostType::Quote ? ' tabs__content--active' : '' }}" data-tab-content="post-type-{{ \App\Enums\PostType::Quote->value }}">
              <h2 class="visually-hidden">Форма добавления цитаты</h2>
              <form class="adding-post__form form" action="{{ route('posts.store', \App\Enums\PostType::Quote->value) }}" method="post">
                @csrf
                <input type="hidden" name="post_type" value="{{ \App\Enums\PostType::Quote->value }}">
                <div class="form__text-inputs-wrapper">
                  <div class="form__text-inputs">
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="quote-heading">Заголовок <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('title') && $activePostType === \App\Enums\PostType::Quote ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="quote-heading" type="text" name="title" value="{{ $activePostType === \App\Enums\PostType::Quote ? old('title') : '' }}" placeholder="Введите заголовок">
                        @if ($activePostType === \App\Enums\PostType::Quote)
                          @include('pages.partials.form-field-error', ['field' => 'title'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__input-wrapper form__textarea-wrapper">
                      <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('text_content') && $activePostType === \App\Enums\PostType::Quote ? ' form__input-section--error' : '' }}">
                        <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="cite-text" name="text_content" placeholder="Текст цитаты">{{ $activePostType === \App\Enums\PostType::Quote ? old('text_content') : '' }}</textarea>
                        @if ($activePostType === \App\Enums\PostType::Quote)
                          @include('pages.partials.form-field-error', ['field' => 'text_content'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__textarea-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('quote_author') && $activePostType === \App\Enums\PostType::Quote ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="quote-author" type="text" name="quote_author" value="{{ $activePostType === \App\Enums\PostType::Quote ? old('quote_author') : '' }}">
                        @if ($activePostType === \App\Enums\PostType::Quote)
                          @include('pages.partials.form-field-error', ['field' => 'quote_author'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="cite-tags">Теги</label>
                      <div class="form__input-section{{ $errors->has('tags') && $activePostType === \App\Enums\PostType::Quote ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="cite-tags" type="text" name="tags" value="{{ $activePostType === \App\Enums\PostType::Quote ? old('tags') : '' }}" placeholder="Введите теги">
                        @if ($activePostType === \App\Enums\PostType::Quote)
                          @include('pages.partials.form-field-error', ['field' => 'tags'])
                        @endif
                      </div>
                    </div>
                  </div>

                  @if ($activePostType === \App\Enums\PostType::Quote)
                    @include('pages.partials.form-errors')
                  @endif
                </div>
                <div class="adding-post__buttons">
                  <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                  <a class="adding-post__close" href="{{ route('main') }}">Закрыть</a>
                </div>
              </form>
            </section>

            <section class="adding-post__link tabs__content{{ $activePostType === \App\Enums\PostType::Link ? ' tabs__content--active' : '' }}" data-tab-content="post-type-{{ \App\Enums\PostType::Link->value }}">
              <h2 class="visually-hidden">Форма добавления ссылки</h2>
              <form class="adding-post__form form" action="{{ route('posts.store', \App\Enums\PostType::Link->value) }}" method="post">
                @csrf
                <input type="hidden" name="post_type" value="{{ \App\Enums\PostType::Link->value }}">
                <div class="form__text-inputs-wrapper">
                  <div class="form__text-inputs">
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="link-heading">Заголовок <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('title') && $activePostType === \App\Enums\PostType::Link ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="link-heading" type="text" name="title" value="{{ $activePostType === \App\Enums\PostType::Link ? old('title') : '' }}" placeholder="Введите заголовок">
                        @if ($activePostType === \App\Enums\PostType::Link)
                          @include('pages.partials.form-field-error', ['field' => 'title'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__textarea-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="post-link">Ссылка <span class="form__input-required">*</span></label>
                      <div class="form__input-section{{ $errors->has('link') && $activePostType === \App\Enums\PostType::Link ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="post-link" type="text" name="link" value="{{ $activePostType === \App\Enums\PostType::Link ? old('link') : '' }}">
                        @if ($activePostType === \App\Enums\PostType::Link)
                          @include('pages.partials.form-field-error', ['field' => 'link'])
                        @endif
                      </div>
                    </div>
                    <div class="adding-post__input-wrapper form__input-wrapper">
                      <label class="adding-post__label form__label" for="link-tags">Теги</label>
                      <div class="form__input-section{{ $errors->has('tags') && $activePostType === \App\Enums\PostType::Link ? ' form__input-section--error' : '' }}">
                        <input class="adding-post__input form__input" id="link-tags" type="text" name="tags" value="{{ $activePostType === \App\Enums\PostType::Link ? old('tags') : '' }}" placeholder="Введите теги">
                        @if ($activePostType === \App\Enums\PostType::Link)
                          @include('pages.partials.form-field-error', ['field' => 'tags'])
                        @endif
                      </div>
                    </div>
                  </div>

                  @if ($activePostType === \App\Enums\PostType::Link)
                    @include('pages.partials.form-errors')
                  @endif
                </div>
                <div class="adding-post__buttons">
                  <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                  <a class="adding-post__close" href="{{ route('main') }}">Закрыть</a>
                </div>
              </form>
            </section>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    (function () {
      var wrapper = document.querySelector('.adding-post__tabs-wrapper');

      if (wrapper) {
        var tabs = wrapper.querySelectorAll('.tabs__item');
        var contents = wrapper.querySelectorAll('[data-tab-content]');

        var activateTab = function (targetName) {
          for (var i = 0; i < tabs.length; i++) {
            var isActiveTab = tabs[i].getAttribute('data-tab-target') === targetName;
            tabs[i].classList.toggle('tabs__item--active', isActiveTab);
            tabs[i].classList.toggle('filters__button--active', isActiveTab);
          }

          for (var j = 0; j < contents.length; j++) {
            contents[j].classList.toggle('tabs__content--active', contents[j].getAttribute('data-tab-content') === targetName);
          }
        };

        for (var k = 0; k < tabs.length; k++) {
          tabs[k].addEventListener('click', function (event) {
            event.preventDefault();
            activateTab(this.getAttribute('data-tab-target'));
          });
        }
      }

      var input = document.getElementById('userpic-file-photo');
      var previews = document.querySelector('.adding-post__file--photo');

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
            '<div class="adding-post__image-wrapper form__file-wrapper">' +
              '<img class="form__image" src="' + event.target.result + '" alt="Превью изображения">' +
            '</div>' +
            '<div class="adding-post__file-data form__file-data">' +
              '<span class="adding-post__file-name form__file-name">' + file.name + '</span>' +
            '</div>';
        });

        reader.readAsDataURL(file);
      });
    })();
  </script>
@endpush
