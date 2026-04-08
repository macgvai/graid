@extends('layouts.app', [
    'title' => 'публикация',
    'bodyClass' => 'page',
])

@section('content')
  @php
    $author = $post->author;
    $viewer = auth()->user();
    $authorAvatar = $author->avatar !== null ? asset('storage/' . $author->avatar) : asset('img/userpic-elvira.jpg');
    $viewerAvatar = $viewer?->avatar !== null ? asset('storage/' . $viewer->avatar) : asset('img/userpic.jpg');
    $memberFor = $author->created_at?->locale('ru');
    $type = $post->contentType->icon_class ?? 'text';
    $imageUrl = $post->image !== null ? asset('storage/' . $post->image) : asset('img/rock-default.jpg');
    $linkPreview = $post->link_preview !== null ? asset('storage/' . $post->link_preview) : asset('img/logo-vita.jpg');
    $linkHost = $post->link !== null ? parse_url($post->link, PHP_URL_HOST) : null;
    $visibleComments = $post->comments->take(2);
    $isOwnProfile = $viewer !== null && $viewer->is($author);
    $isSubscribedToAuthor = !$isOwnProfile && $viewer !== null
        ? $viewer->followedUsers()->whereKey($author->id)->exists()
        : false;
  @endphp

  <main class="page__main page__main--publication">
    <div class="container">
      <h1 class="page__title page__title--publication">{{ $post->title }}</h1>
      <section class="post-details">
        <h2 class="visually-hidden">Публикация</h2>
        <div class="post-details__wrapper post-{{ $type }}">
          <div class="post-details__main-block post post--details">
            @if ($type === 'photo')
              <div class="post-details__image-wrapper post-photo__image-wrapper">
                <img src="{{ $imageUrl }}" alt="Фото от пользователя" width="760" height="507">
              </div>
            @elseif ($type === 'video')
              <div class="post-details__image-wrapper">
                @if ($youtubeEmbedUrl !== null)
                  <iframe src="{{ $youtubeEmbedUrl }}" width="760" height="507" allowfullscreen title="{{ $post->title }}"></iframe>
                @else
                  <a href="{{ $post->video }}" target="_blank" rel="noopener noreferrer">{{ $post->video }}</a>
                @endif
              </div>
            @elseif ($type === 'quote')
              <blockquote>
                <p>{{ $post->text_content }}</p>
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
                      <span>{{ $linkHost }}</span>
                    @endif
                  </div>
                  <svg class="post-link__arrow" width="11" height="16">
                    <use xlink:href="#icon-arrow-right-ad"></use>
                  </svg>
                </a>
              </div>
            @else
              <div class="post-details__text-wrapper">
                <p>{{ $post->text_content }}</p>
              </div>
            @endif

            <div class="post__indicators">
              @include('pages.partials.post-actions', ['post' => $post])
              <span class="post__view">{{ $post->views }} просмотров</span>
            </div>
            <div class="comments" id="comments">
              <form class="comments__form form" action="{{ route('comments.store', $post) }}" method="post">
                @csrf
                <div class="comments__my-avatar">
                  <img class="comments__picture" src="{{ $viewerAvatar }}" alt="Аватар пользователя">
                </div>
                <div class="form__input-section{{ $errors->has('content') ? ' form__input-section--error' : '' }}">
                  <textarea class="comments__textarea form__textarea form__input" name="content" placeholder="Ваш комментарий">{{ old('content') }}</textarea>
                  <label class="visually-hidden">Ваш комментарий</label>
                  @include('pages.partials.form-field-error', ['field' => 'content'])
                </div>
                <button class="comments__submit button button--green" type="submit">Отправить</button>
              </form>
              <div class="comments__list-wrapper">
                <ul class="comments__list">
                  @foreach ($visibleComments as $comment)
                    @php
                      $commentAvatar = $comment->author->avatar !== null ? asset('storage/' . $comment->author->avatar) : asset('img/userpic-larisa.jpg');
                    @endphp
                    <li class="comments__item user">
                      <div class="comments__avatar">
                        <a class="user__avatar-link" href="{{ route('users.show', $comment->author) }}">
                          <img class="comments__picture" src="{{ $commentAvatar }}" alt="Аватар пользователя">
                        </a>
                      </div>
                      <div class="comments__info">
                        <div class="comments__name-wrapper">
                          <a class="comments__user-name" href="{{ route('users.show', $comment->author) }}">
                            <span>{{ $comment->author->login }}</span>
                          </a>
                          <time class="comments__time" datetime="{{ $comment->created_at?->toAtomString() }}">{{ $comment->created_at?->locale('ru')->diffForHumans() }}</time>
                        </div>
                        <p class="comments__text">
                          {{ $comment->content }}
                        </p>
                      </div>
                    </li>
                  @endforeach
                </ul>
                @if (($post->comments_count ?? 0) > $visibleComments->count())
                  <a class="comments__more-link" href="{{ route('posts.show', $post) }}">
                    <span>Показать все комментарии</span>
                    <sup class="comments__amount">{{ $post->comments_count }}</sup>
                  </a>
                @endif
              </div>
            </div>
          </div>
          <div class="post-details__user user">
            <div class="post-details__user-info user__info">
              <div class="post-details__avatar user__avatar">
                <a class="post-details__avatar-link user__avatar-link" href="{{ route('users.show', $author) }}">
                  <img class="post-details__picture user__picture" src="{{ $authorAvatar }}" alt="Аватар пользователя">
                </a>
              </div>
              <div class="post-details__name-wrapper user__name-wrapper">
                <a class="post-details__name user__name" href="{{ route('users.show', $author) }}">
                  <span>{{ $author->login }}</span>
                </a>
                <time class="post-details__time user__time" datetime="{{ $author->created_at?->toDateString() }}">{{ $memberFor?->diffForHumans(null, true) }} на сайте</time>
              </div>
            </div>
            <div class="post-details__rating user__rating">
              <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                <span class="post-details__rating-amount user__rating-amount">{{ (int) ($author->followers_count ?? 0) }}</span>
                <span class="post-details__rating-text user__rating-text">подписчиков</span>
              </p>
              <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                <span class="post-details__rating-amount user__rating-amount">{{ (int) ($author->posts_count ?? 0) }}</span>
                <span class="post-details__rating-text user__rating-text">публикаций</span>
              </p>
            </div>
            @if (!$isOwnProfile)
              <div class="post-details__user-buttons user__buttons">
                @if ($isSubscribedToAuthor)
                  <form action="{{ route('subscriptions.destroy', $author) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="user__button user__button--subscription button button--quartz" type="submit">Отписаться</button>
                  </form>
                @else
                  <form action="{{ route('subscriptions.store', $author) }}" method="post">
                    @csrf
                    <button class="user__button user__button--subscription button button--main" type="submit">Подписаться</button>
                  </form>
                @endif
                <a class="user__button user__button--writing button button--green" href="{{ route('messages', ['user' => $author->id]) }}">Сообщение</a>
              </div>
            @endif
          </div>
        </div>
      </section>
    </div>
  </main>
@endsection
