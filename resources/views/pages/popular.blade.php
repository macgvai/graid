@extends('layouts.app', [
    'title' => 'популярное',
    'bodyClass' => 'page',
])

@section('content')
  <section class="page__main page__main--popular">
    <div class="container">
      <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
      <div class="popular__filters-wrapper">
        <div class="popular__sorting sorting">
          <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
          <ul class="popular__sorting-list sorting__list">
            <li class="sorting__item sorting__item--popular">
              <a class="sorting__link{{ $sort === 'popular' ? ' sorting__link--active' : '' }}" href="{{ route('popular', array_filter(['sort' => 'popular', 'type' => $activeType?->value])) }}">
                <span>Популярность</span>
                <svg class="sorting__icon" width="10" height="12">
                  <use xlink:href="#icon-sort"></use>
                </svg>
              </a>
            </li>
            <li class="sorting__item">
              <a class="sorting__link{{ $sort === 'likes' ? ' sorting__link--active' : '' }}" href="{{ route('popular', array_filter(['sort' => 'likes', 'type' => $activeType?->value])) }}">
                <span>Лайки</span>
                <svg class="sorting__icon" width="10" height="12">
                  <use xlink:href="#icon-sort"></use>
                </svg>
              </a>
            </li>
            <li class="sorting__item">
              <a class="sorting__link{{ $sort === 'date' ? ' sorting__link--active' : '' }}" href="{{ route('popular', array_filter(['sort' => 'date', 'type' => $activeType?->value])) }}">
                <span>Дата</span>
                <svg class="sorting__icon" width="10" height="12">
                  <use xlink:href="#icon-sort"></use>
                </svg>
              </a>
            </li>
          </ul>
        </div>
        <div class="popular__filters filters">
          <b class="popular__filters-caption filters__caption">Тип контента:</b>
          <ul class="popular__filters-list filters__list">
            <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
              <a class="filters__button filters__button--ellipse filters__button--all{{ $activeType === null ? ' filters__button--active' : '' }}" href="{{ route('popular', ['sort' => $sort]) }}">
                <span>Все</span>
              </a>
            </li>
            @foreach (\App\Enums\PostType::cases() as $type)
              <li class="popular__filters-item filters__item">
                <a class="filters__button filters__button--{{ $type->iconClass() }} button{{ $activeType === $type ? ' filters__button--active' : '' }}" href="{{ route('popular', ['sort' => $sort, 'type' => $type->value]) }}">
                  <span class="visually-hidden">{{ $type->label() }}</span>
                  <svg class="filters__icon" width="{{ $type === \App\Enums\PostType::Video ? 24 : ($type === \App\Enums\PostType::Text ? 20 : ($type === \App\Enums\PostType::Quote ? 21 : ($type === \App\Enums\PostType::Link ? 21 : 22))) }}" height="{{ $type === \App\Enums\PostType::Photo ? 18 : ($type === \App\Enums\PostType::Video ? 16 : ($type === \App\Enums\PostType::Text ? 21 : ($type === \App\Enums\PostType::Quote ? 20 : 18))) }}">
                    <use xlink:href="#icon-filter-{{ $type->iconClass() }}"></use>
                  </svg>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="popular__posts">
        @foreach ($posts as $post)
          @include('pages.partials.post-compact', ['post' => $post])
        @endforeach
      </div>
      @if ($posts->previousPageUrl() !== null || $posts->nextPageUrl() !== null)
        <div class="popular__page-links">
          @if ($posts->previousPageUrl() !== null)
            <a class="popular__page-link popular__page-link--prev button button--gray" href="{{ $posts->previousPageUrl() }}">Предыдущая страница</a>
          @endif
          @if ($posts->nextPageUrl() !== null)
            <a class="popular__page-link popular__page-link--next button button--gray" href="{{ $posts->nextPageUrl() }}">Следующая страница</a>
          @endif
        </div>
      @endif
    </div>
  </section>
@endsection
