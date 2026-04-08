@php
  $avatarUrl = $subscriptionUser->avatar !== null ? asset('storage/' . $subscriptionUser->avatar) : asset('img/userpic-petro.jpg');
  $memberFor = $subscriptionUser->created_at?->locale('ru');
@endphp

<li class="post-mini post-mini--photo post user">
  <div class="post-mini__user-info user__info">
    <div class="post-mini__avatar user__avatar">
      <a class="user__avatar-link" href="{{ route('users.show', $subscriptionUser) }}">
        <img class="post-mini__picture user__picture" src="{{ $avatarUrl }}" alt="Аватар пользователя">
      </a>
    </div>
    <div class="post-mini__name-wrapper user__name-wrapper">
      <a class="post-mini__name user__name" href="{{ route('users.show', $subscriptionUser) }}">
        <span>{{ $subscriptionUser->login }}</span>
      </a>
      <time class="post-mini__time user__additional" datetime="{{ $subscriptionUser->created_at?->toDateString() }}">{{ $memberFor?->diffForHumans(null, true) }} на сайте</time>
    </div>
  </div>
  <div class="post-mini__rating user__rating">
    <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
      <span class="post-mini__rating-amount user__rating-amount">{{ (int) ($subscriptionUser->posts_count ?? 0) }}</span>
      <span class="post-mini__rating-text user__rating-text">публикаций</span>
    </p>
    <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
      <span class="post-mini__rating-amount user__rating-amount">{{ (int) ($subscriptionUser->followers_count ?? 0) }}</span>
      <span class="post-mini__rating-text user__rating-text">подписчиков</span>
    </p>
  </div>
  @if (($showAction ?? false) === true)
    <div class="post-mini__user-buttons user__buttons">
      @if (($subscribed ?? false) === true)
        <form action="{{ route('subscriptions.destroy', $subscriptionUser) }}" method="post">
          @csrf
          @method('DELETE')
          <button class="post-mini__user-button user__button user__button--subscription button button--quartz" type="submit">Отписаться</button>
        </form>
      @else
        <form action="{{ route('subscriptions.store', $subscriptionUser) }}" method="post">
          @csrf
          <button class="post-mini__user-button user__button user__button--subscription button button--main" type="submit">Подписаться</button>
        </form>
      @endif
    </div>
  @endif
</li>
