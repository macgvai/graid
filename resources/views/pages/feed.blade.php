@extends('layouts.app', [
    'title' => 'моя лента',
    'bodyClass' => 'page',
])

@section('content')
  <main class="page__main page__main--feed">
    <div class="container">
      <h1 class="page__title page__title--feed">Моя лента</h1>
    </div>
    <div class="page__main-wrapper container">
      <section class="feed">
        <h2 class="visually-hidden">Лента</h2>
        <div class="feed__main-wrapper">
          <div class="feed__wrapper">
            @foreach ($posts as $post)
              @include('pages.partials.post-stream', [
                  'post' => $post,
                  'className' => 'feed__post',
              ])
            @endforeach
          </div>
        </div>
        <ul class="feed__filters filters">
          <li class="feed__filters-item filters__item">
            <a class="filters__button{{ $activeType === null ? ' filters__button--active' : '' }}" href="{{ route('feed') }}">
              <span>Все</span>
            </a>
          </li>
          @foreach (\App\Enums\PostType::cases() as $type)
            <li class="feed__filters-item filters__item">
              <a class="filters__button filters__button--{{ $type->iconClass() }} button{{ $activeType === $type ? ' filters__button--active' : '' }}" href="{{ route('feed', ['type' => $type->value]) }}">
                <span class="visually-hidden">{{ $type->label() }}</span>
                <svg class="filters__icon" width="{{ $type === \App\Enums\PostType::Video ? 24 : ($type === \App\Enums\PostType::Text ? 20 : ($type === \App\Enums\PostType::Quote ? 21 : ($type === \App\Enums\PostType::Link ? 21 : 22))) }}" height="{{ $type === \App\Enums\PostType::Photo ? 18 : ($type === \App\Enums\PostType::Video ? 16 : ($type === \App\Enums\PostType::Text ? 21 : ($type === \App\Enums\PostType::Quote ? 20 : 18))) }}">
                  <use xlink:href="#icon-filter-{{ $type->iconClass() }}"></use>
                </svg>
              </a>
            </li>
          @endforeach
        </ul>
      </section>

      @include('pages.partials.promo')
    </div>
  </main>
@endsection
