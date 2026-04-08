@php
  $type = $post->contentType->icon_class ?? 'text';
  $author = $post->author;
  $authorAvatar = $author?->avatar !== null ? asset('storage/' . $author->avatar) : asset('img/userpic-larisa-small.jpg');
  $imageUrl = $post->image !== null ? asset('storage/' . $post->image) : asset('img/rock-medium.jpg');
  $linkPreview = $post->link_preview !== null ? asset('storage/' . $post->link_preview) : asset('img/logo-vita.jpg');
  $linkHost = $post->link !== null ? parse_url($post->link, PHP_URL_HOST) : null;
  $videoPreview = asset('img/coast-medium.jpg');
  if (is_string($post->video) && preg_match('~(?:v=|youtu\.be/|embed/)([^&?/]+)~', $post->video, $matches) === 1) {
      $videoPreview = 'https://img.youtube.com/vi/' . $matches[1] . '/mqdefault.jpg';
  }
  $createdAt = $post->created_at?->locale('ru');
@endphp

<article class="popular__post post post-{{ $type }}">
  <header class="post__header">
    <h2><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
  </header>
  <div class="post__main">
    @if ($type === 'photo')
      <div class="post-photo__image-wrapper">
        <img src="{{ $imageUrl }}" alt="Фото от пользователя" width="360" height="240">
      </div>
    @elseif ($type === 'video')
      <div class="post-video__block">
        <div class="post-video__preview">
          <img src="{{ $videoPreview }}" alt="Превью к видео" width="360" height="188">
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
          <svg class="post-video__play-big-icon" width="14" height="14">
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
          <div class="post-link__info-wrapper">
            <div class="post-link__icon-wrapper">
              <img src="{{ $linkPreview }}" alt="Иконка">
            </div>
            <div class="post-link__info">
              <h3>{{ $post->title }}</h3>
              @if ($linkHost !== null)
                <p>{{ $linkHost }}</p>
              @endif
            </div>
          </div>
          @if ($linkHost !== null)
            <span>{{ $linkHost }}</span>
          @endif
        </a>
      </div>
    @else
      <p>
        {{ \Illuminate\Support\Str::limit((string) $post->text_content, 220) }}
      </p>
      @if (\Illuminate\Support\Str::length((string) $post->text_content) > 220)
        <div class="post-text__more-link-wrapper">
          <a class="post-text__more-link" href="{{ route('posts.show', $post) }}">Читать далее</a>
        </div>
      @endif
    @endif
  </div>
  <footer class="post__footer">
    <div class="post__author">
      <a class="post__author-link" href="{{ route('users.show', $author) }}" title="Автор">
        <div class="post__avatar-wrapper">
          <img class="post__author-avatar" src="{{ $authorAvatar }}" alt="Аватар пользователя">
        </div>
        <div class="post__info">
          <b class="post__author-name">{{ $author->login }}</b>
          <time class="post__time" datetime="{{ $post->created_at?->toDateString() }}">{{ $createdAt?->diffForHumans() }}</time>
        </div>
      </a>
    </div>
    <div class="post__indicators">
      @include('pages.partials.post-actions', [
          'post' => $post,
          'showRepost' => false,
      ])
    </div>
  </footer>
</article>
