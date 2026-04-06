'use script';

(function () {
  var sorting = document.querySelector('.sorting');

  if (sorting) {
    var sortingLinks = sorting.querySelectorAll('.sorting__link');
    var sortingLinkActive = sorting.querySelector('.sorting__link--active');

    var onSortingItemClick = function (evt) {
      evt.preventDefault();
      if (evt.currentTarget === sortingLinkActive) {
        sortingLinkActive.classList.toggle('sorting__link--reverse');
      } else {
        sortingLinkActive.classList.remove('sorting__link--active');
        evt.currentTarget.classList.add('sorting__link--active');
        sortingLinkActive = evt.currentTarget;
      }
    }

    var addSortingListener = function (sortingItem) {
      sortingItem.addEventListener('click', onSortingItemClick);
    }

    for (var i = 0; i < sortingLinks.length; i++) {
      addSortingListener(sortingLinks[i]);
    }
  }
})();
