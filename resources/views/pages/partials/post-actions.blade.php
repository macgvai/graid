@php
  $likesCount = (int) ($post->likes_count ?? 0);
  $commentsCount = (int) ($post->comments_count ?? 0);
  $repostsCount = (int) ($post->reposts_count ?? 0);
@endphp

<div class="post__buttons">
  <form action="{{ route('likes.store', $post) }}" method="post" style="display: inline;">
    @csrf
    <button class="post__indicator post__indicator--likes button" type="submit" title="Лайк">
      <svg class="post__indicator-icon" width="20" height="17">
        <use xlink:href="#icon-heart"></use>
      </svg>
      <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
        <use xlink:href="#icon-heart-active"></use>
      </svg>
      <span>{{ $likesCount }}</span>
      <span class="visually-hidden">количество лайков</span>
    </button>
  </form>

  @if (($showComments ?? true) === true)
    <a class="post__indicator post__indicator--comments button" href="{{ route('posts.show', $post) }}#comments" title="Комментарии">
      <svg class="post__indicator-icon" width="19" height="17">
        <use xlink:href="#icon-comment"></use>
      </svg>
      <span>{{ $commentsCount }}</span>
      <span class="visually-hidden">количество комментариев</span>
    </a>
  @endif

  @if (($showRepost ?? true) === true)
    <form action="{{ route('posts.repost', $post) }}" method="post" style="display: inline;">
      @csrf
      <button class="post__indicator post__indicator--repost button" type="submit" title="Репост">
        <svg class="post__indicator-icon" width="19" height="17">
          <use xlink:href="#icon-repost"></use>
        </svg>
        <span>{{ $repostsCount }}</span>
        <span class="visually-hidden">количество репостов</span>
      </button>
    </form>
  @endif
</div>
