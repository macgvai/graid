@php
  $type = $post->contentType->icon_class ?? 'text';
  $imageUrl = $post->image !== null ? asset('storage/' . $post->image) : asset('img/rock.jpg');
  $createdAt = $post->created_at?->locale('ru');
  $originalAuthor = $post->originalAuthor;
@endphp

<article class="profile__post post post-{{ $type }}">
  <header class="post__header">
    @if ($post->is_repost && $originalAuthor !== null)
      @php
        $originalAvatar = $originalAuthor->avatar !== null ? asset('storage/' . $originalAuthor->avatar) : asset('img/userpic-tanya.jpg');
      @endphp
      <div class="post__author">
        <a class="post__author-link" href="{{ route('users.show', $originalAuthor) }}" title="Автор">
          <div class="post__avatar-wrapper post__avatar-wrapper--repost">
            <img class="post__author-avatar" src="{{ $originalAvatar }}" alt="Аватар пользователя">
          </div>
          <div class="post__info">
            <b class="post__author-name">Репост: {{ $originalAuthor->login }}</b>
            <time class="post__time" datetime="{{ $post->created_at?->toAtomString() }}">{{ $createdAt?->diffForHumans() }}</time>
          </div>
        </a>
      </div>
    @else
      <h2><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
    @endif
  </header>
  <div class="post__main">
    @if ($type !== 'photo' && $type !== 'link')
      <h2><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
    @endif

    @if ($type === 'photo')
      <div class="post-photo__image-wrapper">
        <img src="{{ $imageUrl }}" alt="Фото от пользователя" width="760" height="396">
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
      @php
        $linkPreview = $post->link_preview !== null ? asset('storage/' . $post->link_preview) : asset('img/logo-vita.jpg');
        $linkHost = $post->link !== null ? parse_url($post->link, PHP_URL_HOST) : null;
      @endphp
      <div class="post-link__wrapper">
        <a class="post-link__external" href="{{ $post->link }}" title="Перейти по ссылке" target="_blank" rel="noopener noreferrer">
          <div class="post-link__icon-wrapper">
            <img src="{{ $linkPreview }}" alt="Иконка">
          </div>
          <div class="post-link__info">
            <h3>{{ $post->title }}</h3>
            @if ($linkHost !== null)
              <span>{{ $linkHost }}</span>
            @endif
          </div>
          <svg class="post-link__arrow" width="11" height="16">
            <use xlink:href="#icon-arrow-right-ad"></use>
          </svg>
        </a>
      </div>
    @else
      @if ($type === 'video')
        <p>
          <a href="{{ $post->video }}" target="_blank" rel="noopener noreferrer">{{ $post->video }}</a>
        </p>
      @else
        <p>
          {{ \Illuminate\Support\Str::limit((string) $post->text_content, 450) }}
        </p>
      @endif
      @if ($type === 'text' && \Illuminate\Support\Str::length((string) $post->text_content) > 450)
        <a class="post-text__more-link" href="{{ route('posts.show', $post) }}">Читать далее</a>
      @endif
    @endif
  </div>
  <footer class="post__footer">
    <div class="post__indicators">
      @include('pages.partials.post-actions', [
          'post' => $post,
          'showComments' => false,
      ])
      <time class="post__time" datetime="{{ $post->created_at?->toAtomString() }}">{{ $createdAt?->diffForHumans() }}</time>
    </div>
    @if ($post->hashtags->isNotEmpty())
      <ul class="post__tags">
        @foreach ($post->hashtags as $tag)
          <li><a href="{{ route('search-results', ['query' => '#' . $tag->name]) }}">#{{ $tag->name }}</a></li>
        @endforeach
      </ul>
    @endif
  </footer>
  <div class="comments">
    <a class="comments__button button" href="{{ route('posts.show', $post) }}">Показать комментарии</a>
  </div>
</article>
