@php
  /** @var \App\Models\User|null $viewer */
  $viewer = auth()->user();
  $avatarUrl = $viewer?->avatar !== null
      ? asset('storage/' . $viewer->avatar)
      : asset('img/userpic.jpg');
  $profileUrl = $viewer !== null ? route('users.show', $viewer) : route('login');
  $isPopular = request()->routeIs('popular');
  $isFeed = request()->routeIs('feed');
  $isMessages = request()->routeIs('messages');
  $isAddingPost = request()->routeIs('adding-post');
  $isLogin = ($activeGuestPage ?? null) === 'login';
  $isRegistration = ($activeGuestPage ?? null) === 'registration';
@endphp

<header class="header">
  <div class="header__wrapper container">
    <div class="header__logo-wrapper">
      <a class="header__logo-link" href="{{ route('main') }}">
        <img class="header__logo" src="{{ asset('img/logo.svg') }}" alt="Логотип readme" width="128" height="24">
      </a>
      <p class="header__topic">
        micro blogging
      </p>
    </div>
    <form class="header__search-form form" action="{{ route('search-results') }}" method="get">
      <div class="header__search">
        <label class="visually-hidden" for="header-search">Поиск</label>
        <input
          class="header__search-input form__input"
          id="header-search"
          type="search"
          name="query"
          value="{{ $searchQuery ?? '' }}"
        >
        <button class="header__search-button button" type="submit">
          <svg class="header__search-icon" width="18" height="18">
            <use xlink:href="#icon-search"></use>
          </svg>
          <span class="visually-hidden">Начать поиск</span>
        </button>
      </div>
    </form>
    <div class="header__nav-wrapper">
      <nav class="header__nav">
        <ul class="header__my-nav">
          <li class="header__my-page header__my-page--popular">
            <a class="header__page-link{{ $isPopular ? ' header__page-link--active' : '' }}" href="{{ route('popular') }}" title="Популярный контент">
              <span class="visually-hidden">Популярный контент</span>
            </a>
          </li>
          <li class="header__my-page header__my-page--feed">
            <a class="header__page-link{{ $isFeed ? ' header__page-link--active' : '' }}" href="{{ route('feed') }}" title="Моя лента">
              <span class="visually-hidden">Моя лента</span>
            </a>
          </li>
          <li class="header__my-page header__my-page--messages">
            <a class="header__page-link{{ $isMessages ? ' header__page-link--active' : '' }}" href="{{ route('messages') }}" title="Личные сообщения">
              <span class="visually-hidden">Личные сообщения</span>
            </a>
          </li>
        </ul>

        @if ($viewer !== null)
          <ul class="header__user-nav">
            <li class="header__profile">
              <a class="header__profile-link" href="{{ $profileUrl }}">
                <div class="header__avatar-wrapper">
                  <img class="header__profile-avatar" src="{{ $avatarUrl }}" alt="Аватар профиля">
                </div>
                <div class="header__profile-name">
                  <span>{{ $viewer->login }}</span>
                  <svg class="header__link-arrow" width="10" height="6">
                    <use xlink:href="#icon-arrow-right-ad"></use>
                  </svg>
                </div>
              </a>
              <div class="header__tooltip-wrapper">
                <div class="header__profile-tooltip">
                  <ul class="header__profile-nav">
                    <li class="header__profile-nav-item">
                      <a class="header__profile-nav-link" href="{{ $profileUrl }}">
                        <span class="header__profile-nav-text">
                          Мой профиль
                        </span>
                      </a>
                    </li>
                    <li class="header__profile-nav-item">
                      <a class="header__profile-nav-link" href="{{ route('messages') }}">
                        <span class="header__profile-nav-text">
                          Сообщения
                        </span>
                      </a>
                    </li>
                    <li class="header__profile-nav-item">
                      <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="header__profile-nav-link button" type="submit">
                          <span class="header__profile-nav-text">
                            Выход
                          </span>
                        </button>
                      </form>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
            <li>
              <a class="header__post-button{{ $isAddingPost ? ' header__post-button--active' : '' }} button button--transparent" href="{{ $isAddingPost ? route('main') : route('adding-post') }}">
                {{ $isAddingPost ? 'Закрыть' : 'Пост' }}
              </a>
            </li>
          </ul>
        @else
          <ul class="header__user-nav">
            <li class="header__authorization">
              <a class="header__user-button{{ $isLogin ? ' header__user-button--active header__authorization-button' : ' header__authorization-button' }} button" href="{{ route('login') }}">Вход</a>
            </li>
            <li>
              <a class="header__user-button{{ $isRegistration ? ' header__user-button--active header__register-button' : ' header__register-button' }} button" href="{{ route('registration') }}">Регистрация</a>
            </li>
          </ul>
        @endif
      </nav>
    </div>
  </div>
</header>
