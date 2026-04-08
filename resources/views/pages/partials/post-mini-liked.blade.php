@php
  $type = $post->contentType->icon_class ?? 'text';
  $author = $post->author;
  $authorAvatar = $author?->avatar !== null ? asset('storage/' . $author->avatar) : asset('img/userpic-petro.jpg');
  $imageUrl = $post->image !== null ? asset('storage/' . $post->image) : asset('img/rock-small.png');
  $previewUrl = $post->link_preview !== null ? asset('storage/' . $post->link_preview) : asset('img/coast-small.png');
  $createdAt = $post->created_at?->locale('ru');
@endphp

<li class="post-mini post-mini--{{ $type }} post user">
  <div class="post-mini__user-info user__info">
    <div class="post-mini__avatar user__avatar">
      <a class="user__avatar-link" href="{{ route('users.show', $author) }}">
        <img class="post-mini__picture user__picture" src="{{ $authorAvatar }}" alt="Аватар пользователя">
      </a>
    </div>
    <div class="post-mini__name-wrapper user__name-wrapper">
      <a class="post-mini__name user__name" href="{{ route('users.show', $author) }}">
        <span>{{ $author->login }}</span>
      </a>
      <div class="post-mini__action">
        <span class="post-mini__activity user__additional">Понравилась публикация</span>
        <time class="post-mini__time user__additional" datetime="{{ $post->created_at?->toAtomString() }}">{{ $createdAt?->diffForHumans() }}</time>
      </div>
    </div>
  </div>
  <div class="post-mini__preview">
    <a class="post-mini__link" href="{{ route('posts.show', $post) }}" title="Перейти на публикацию">
      @if ($type === 'photo')
        <div class="post-mini__image-wrapper">
          <img class="post-mini__image" src="{{ $imageUrl }}" width="109" height="109" alt="Превью публикации">
        </div>
      @elseif ($type === 'video')
        <div class="post-mini__image-wrapper">
          <img class="post-mini__image" src="{{ $previewUrl }}" width="109" height="109" alt="Превью публикации">
          <span class="post-mini__play-big">
            <svg class="post-mini__play-big-icon" width="12" height="13">
              <use xlink:href="#icon-video-play-big"></use>
            </svg>
          </span>
        </div>
      @elseif ($type === 'quote')
        <span class="visually-hidden">Цитата</span>
        <svg class="post-mini__preview-icon" width="21" height="20">
          <use xlink:href="#icon-filter-quote"></use>
        </svg>
      @elseif ($type === 'link')
        <span class="visually-hidden">Ссылка</span>
        <svg class="post-mini__preview-icon" width="21" height="18">
          <use xlink:href="#icon-filter-link"></use>
        </svg>
      @else
        <span class="visually-hidden">Текст</span>
        <svg class="post-mini__preview-icon" width="20" height="21">
          <use xlink:href="#icon-filter-text"></use>
        </svg>
      @endif
    </a>
  </div>
</li>
