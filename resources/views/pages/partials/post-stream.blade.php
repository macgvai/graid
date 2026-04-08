@php
  $type = $post->contentType->icon_class ?? 'text';
  $author = $post->author;
  $authorAvatar = $author?->avatar !== null ? asset('storage/' . $author->avatar) : asset('img/userpic.jpg');
  $imageUrl = $post->image !== null ? asset('storage/' . $post->image) : asset('img/rock.jpg');
  $linkPreview = $post->link_preview !== null ? asset('storage/' . $post->link_preview) : asset('img/logo-vita.jpg');
  $linkHost = $post->link !== null ? parse_url($post->link, PHP_URL_HOST) : null;
  $videoPreview = asset('img/coast.jpg');
  if (is_string($post->video) && preg_match('~(?:v=|youtu\.be/|embed/)([^&?/]+)~', $post->video, $matches) === 1) {
      $videoPreview = 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
  }
  $createdAt = $post->created_at?->locale('ru');
@endphp

<article class="{{ trim(($className ?? '') . ' post post-' . $type) }}">
  <header class="post__header post__author">
    <a class="post__author-link" href="{{ route('users.show', $author) }}" title="Автор">
      <div class="post__avatar-wrapper">
        <img class="post__author-avatar" src="{{ $authorAvatar }}" alt="Аватар пользователя" width="60" height="60">
      </div>
      <div class="post__info">
        <b class="post__author-name">{{ $author->login }}</b>
        <span class="post__time">{{ $createdAt?->diffForHumans() }}</span>
      </div>
    </a>
  </header>
  <div class="post__main">
    <h2><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>

    @if ($type === 'photo')
      <div class="post-photo__image-wrapper">
        <img src="{{ $imageUrl }}" alt="Фото от пользователя" width="760" height="396">
      </div>
    @elseif ($type === 'video')
      <div class="post-video__block">
        <div class="post-video__preview">
          <img src="{{ $videoPreview }}" alt="Превью к видео" width="760" height="396">
        </div>
        <div class="post-video__control">
          <button class="post-video__play post-video__play--paused button button--video" type="button"><span class="visually-hidden">Запустить видео</span></button>
          <div class="post-video__scale-wrapper">
            <div class="post-video__scale">
              <div class="post-video__bar">
                <div class="post-video__toggle"></div>
              </div>
            </div>
          </div>
          <button class="post-video__fullscreen post-video__fullscreen--inactive button button--video" type="button"><span class="visually-hidden">Полноэкранный режим</span></button>
        </div>
        <a class="post-video__play-big button" href="{{ $post->video }}" target="_blank" rel="noopener noreferrer">
          <svg class="post-video__play-big-icon" width="27" height="28">
            <use xlink:href="#icon-video-play-big"></use>
          </svg>
          <span class="visually-hidden">Запустить проигрыватель</span>
        </a>
      </div>
    @elseif ($type === 'quote')
      <blockquote>
        <p>
          {{ $post->text_content }}
        </p>
        @if ($post->quote_author !== null)
          <cite>{{ $post->quote_author }}</cite>
        @endif
      </blockquote>
    @elseif ($type === 'link')
      <div class="post-link__wrapper">
        <a class="post-link__external" href="{{ $post->link }}" title="Перейти по ссылке" target="_blank" rel="noopener noreferrer">
          <div class="post-link__icon-wrapper">
            <img src="{{ $linkPreview }}" alt="Иконка">
          </div>
          <div class="post-link__info">
            <h3>{{ $post->title }}</h3>
            @if ($linkHost !== null)
              <p>{{ $linkHost }}</p>
              <span>{{ $linkHost }}</span>
            @endif
          </div>
          <svg class="post-link__arrow" width="11" height="16">
            <use xlink:href="#icon-arrow-right-ad"></use>
          </svg>
        </a>
      </div>
    @else
      <p>
        {{ \Illuminate\Support\Str::limit((string) $post->text_content, 320) }}
      </p>
      @if (\Illuminate\Support\Str::length((string) $post->text_content) > 320)
        <a class="post-text__more-link" href="{{ route('posts.show', $post) }}">Читать далее</a>
      @endif
    @endif
  </div>
  <footer class="post__footer post__indicators">
    @include('pages.partials.post-actions', ['post' => $post])
  </footer>
</article>
