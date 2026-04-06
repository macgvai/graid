'use script';

(function () {
  var filters = document.querySelector('.filters');

  if (filters) {
    var filtersButtons = filters.querySelectorAll('.filters__button:not(.tabs__item)');
  }

  if (filtersButtons) {
    var filtersButtonActive = filters.querySelector('.filters__button--active');

    var onFiltersButtonClick = function (evt) {
      evt.preventDefault();
      if (evt.currentTarget !== filtersButtonActive) {
        filtersButtonActive.classList.remove('filters__button--active');
        evt.currentTarget.classList.add('filters__button--active');
        filtersButtonActive = evt.currentTarget;
      }
    }

    var addFiltersListener = function (filtersButton) {
      filtersButton.addEventListener('click', onFiltersButtonClick);
    }

    for (var i = 0; i < filtersButtons.length; i++) {
      addFiltersListener(filtersButtons[i]);
    }
  }
})();
