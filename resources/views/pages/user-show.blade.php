@extends('layouts.app', [
    'title' => 'профиль',
    'bodyClass' => 'page',
])

@section('content')
  @php
    $viewer = auth()->user();
    $avatarUrl = $profileUser->avatar !== null ? asset('storage/' . $profileUser->avatar) : asset('img/userpic-medium.jpg');
    $memberFor = $profileUser->created_at?->locale('ru');
    $isOwnProfile = $viewer !== null && $viewer->is($profileUser);
  @endphp

  <main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--default">
      <div class="profile__user-wrapper">
        <div class="profile__user user container">
          <div class="profile__user-info user__info">
            <div class="profile__avatar user__avatar">
              <img class="profile__picture user__picture" src="{{ $avatarUrl }}" alt="Аватар пользователя">
            </div>
            <div class="profile__name-wrapper user__name-wrapper">
              <span class="profile__name user__name">{{ $profileUser->login }}</span>
              <time class="profile__user-time user__time" datetime="{{ $profileUser->created_at?->toDateString() }}">{{ $memberFor?->diffForHumans(null, true) }} на сайте</time>
            </div>
          </div>
          <div class="profile__rating user__rating">
            <p class="profile__rating-item user__rating-item user__rating-item--publications">
              <span class="user__rating-amount">{{ (int) ($profileUser->posts_count ?? 0) }}</span>
              <span class="profile__rating-text user__rating-text">публикаций</span>
            </p>
            <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
              <span class="user__rating-amount">{{ (int) ($profileUser->followers_count ?? 0) }}</span>
              <span class="profile__rating-text user__rating-text">подписчиков</span>
            </p>
          </div>
          @if (!$isOwnProfile)
            <div class="profile__user-buttons user__buttons">
              @if ($isSubscribed)
                <form action="{{ route('subscriptions.destroy', $profileUser) }}" method="post">
                  @csrf
                  @method('DELETE')
                  <button class="profile__user-button user__button user__button--subscription button button--quartz" type="submit">Отписаться</button>
                </form>
              @else
                <form action="{{ route('subscriptions.store', $profileUser) }}" method="post">
                  @csrf
                  <button class="profile__user-button user__button user__button--subscription button button--main" type="submit">Подписаться</button>
                </form>
              @endif
              <a class="profile__user-button user__button user__button--writing button button--green" href="{{ route('messages', ['user' => $profileUser->id]) }}">Сообщение</a>
            </div>
          @endif
        </div>
      </div>
      <div class="profile__tabs-wrapper tabs">
        <div class="container">
          <div class="profile__tabs filters">
            <b class="profile__tabs-caption filters__caption">Показать:</b>
            <ul class="profile__tabs-list filters__list tabs__list">
              <li class="profile__tabs-item filters__item">
                <a class="profile__tabs-link filters__button{{ $activeTab === 'posts' ? ' filters__button--active' : '' }} button" href="{{ route('users.show', ['user' => $profileUser, 'tab' => 'posts']) }}">Посты</a>
              </li>
              <li class="profile__tabs-item filters__item">
                <a class="profile__tabs-link filters__button{{ $activeTab === 'likes' ? ' filters__button--active' : '' }} button" href="{{ route('users.show', ['user' => $profileUser, 'tab' => 'likes']) }}">Лайки</a>
              </li>
              <li class="profile__tabs-item filters__item">
                <a class="profile__tabs-link filters__button{{ $activeTab === 'subscriptions' ? ' filters__button--active' : '' }} button" href="{{ route('users.show', ['user' => $profileUser, 'tab' => 'subscriptions']) }}">Подписки</a>
              </li>
            </ul>
          </div>
          <div class="profile__tab-content">
            <section class="profile__posts tabs__content{{ $activeTab === 'posts' ? ' tabs__content--active' : '' }}">
              <h2 class="visually-hidden">Публикации</h2>
              @if ($posts !== null && $activeTab === 'posts')
                @foreach ($posts as $post)
                  @include('pages.partials.post-profile', ['post' => $post])
                @endforeach
              @endif
            </section>

            <section class="profile__likes tabs__content{{ $activeTab === 'likes' ? ' tabs__content--active' : '' }}">
              <h2 class="visually-hidden">Лайки</h2>
              @if ($posts !== null && $activeTab === 'likes')
                <ul class="profile__likes-list">
                  @foreach ($posts as $post)
                    @include('pages.partials.post-mini-liked', ['post' => $post])
                  @endforeach
                </ul>
              @endif
            </section>

            <section class="profile__subscriptions tabs__content{{ $activeTab === 'subscriptions' ? ' tabs__content--active' : '' }}">
              <h2 class="visually-hidden">Подписки</h2>
              @if ($subscriptions !== null && $activeTab === 'subscriptions')
                <ul class="profile__subscriptions-list">
                  @foreach ($subscriptions as $subscriptionUser)
                    @include('pages.partials.subscription-card', [
                        'subscriptionUser' => $subscriptionUser,
                        'showAction' => $isOwnProfile,
                        'subscribed' => $isOwnProfile,
                    ])
                  @endforeach
                </ul>
              @endif
            </section>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
