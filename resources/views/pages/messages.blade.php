@extends('layouts.app', [
    'title' => 'личные сообщения',
    'bodyClass' => 'page',
])

@section('content')
  @php
    $viewer = auth()->user();
    $viewerAvatar = $viewer?->avatar !== null ? asset('storage/' . $viewer->avatar) : asset('img/userpic.jpg');
  @endphp

  <main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
      <h2 class="visually-hidden">Сообщения</h2>
      <div class="messages__contacts">
        <ul class="messages__contacts-list tabs__list">
          @foreach ($contacts as $contact)
            @php
              $contactUser = $contact['user'];
              $lastMessage = $contact['last_message'];
              $contactAvatar = $contactUser->avatar !== null ? asset('storage/' . $contactUser->avatar) : asset('img/userpic-larisa.jpg');
              $isActive = $selectedUser !== null && $selectedUser->is($contactUser);
            @endphp
            <li class="messages__contacts-item">
              <a class="messages__contacts-tab{{ $isActive ? ' messages__contacts-tab--active' : '' }}" href="{{ route('messages', ['user' => $contactUser->id]) }}">
                <div class="messages__avatar-wrapper">
                  <img class="messages__avatar" src="{{ $contactAvatar }}" alt="Аватар пользователя">
                </div>
                <div class="messages__info">
                  <span class="messages__contact-name">
                    {{ $contactUser->login }}
                  </span>
                  <div class="messages__preview">
                    <p class="messages__preview-text">
                      {{ \Illuminate\Support\Str::limit((string) ($lastMessage?->content ?? 'Нет сообщений'), 28) }}
                    </p>
                    @if ($lastMessage !== null)
                      <time class="messages__preview-time" datetime="{{ $lastMessage->created_at?->toAtomString() }}">
                        {{ $lastMessage->created_at?->locale('ru')->diffForHumans() }}
                      </time>
                    @endif
                  </div>
                </div>
              </a>
            </li>
          @endforeach
        </ul>
      </div>
      <div class="messages__chat">
        <div class="messages__chat-wrapper">
          <ul class="messages__list tabs__content tabs__content--active">
            @forelse ($conversation as $message)
              @php
                $isOwnMessage = $viewer !== null && $message->sender_id === $viewer->id;
                $messageAuthor = $isOwnMessage ? $viewer : $message->sender;
                $messageAvatar = $messageAuthor?->avatar !== null ? asset('storage/' . $messageAuthor->avatar) : ($isOwnMessage ? $viewerAvatar : asset('img/userpic-larisa-small.jpg'));
              @endphp
              <li class="messages__item{{ $isOwnMessage ? ' messages__item--my' : '' }}">
                <div class="messages__info-wrapper">
                  <div class="messages__item-avatar">
                    <a class="messages__author-link" href="{{ route('users.show', $messageAuthor) }}">
                      <img class="messages__avatar" src="{{ $messageAvatar }}" alt="Аватар пользователя">
                    </a>
                  </div>
                  <div class="messages__item-info">
                    <a class="messages__author" href="{{ route('users.show', $messageAuthor) }}">
                      {{ $messageAuthor->login }}
                    </a>
                    <time class="messages__time" datetime="{{ $message->created_at?->toAtomString() }}">
                      {{ $message->created_at?->locale('ru')->diffForHumans() }}
                    </time>
                  </div>
                </div>
                <p class="messages__text">
                  {{ $message->content }}
                </p>
              </li>
            @empty
              <li class="messages__item">
                <p class="messages__text">Выберите диалог или начните новую переписку.</p>
              </li>
            @endforelse
          </ul>
        </div>
        @if ($selectedUser !== null)
          <div class="comments">
            <form class="comments__form form" action="{{ route('messages.store') }}" method="post">
              @csrf
              <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
              <div class="comments__my-avatar">
                <img class="comments__picture" src="{{ $viewerAvatar }}" alt="Аватар пользователя">
              </div>
              <div class="form__input-section{{ $errors->has('content') ? ' form__input-section--error' : '' }}">
                <textarea class="comments__textarea form__textarea form__input" name="content" placeholder="Ваше сообщение">{{ old('content') }}</textarea>
                <label class="visually-hidden">Ваше сообщение</label>
                @include('pages.partials.form-field-error', ['field' => 'content'])
              </div>
              <button class="comments__submit button button--green" type="submit">Отправить</button>
            </form>
          </div>
        @endif
      </div>
    </section>
  </main>
@endsection
