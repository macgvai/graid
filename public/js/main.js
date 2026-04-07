'use strict';

'use script';

//табы
(function () {
  var switchTabs = function (block) {
    var tabsList = block.querySelector('.tabs__list');
    var tabElements = tabsList.querySelectorAll('.tabs__item');
    var tabContentSections = block.querySelectorAll('.tabs__content');
    var activeTabIndex = 0;
    var initialized = false;

    var initializeSwitch = function () {
      if (!initialized) {
        var detected = false;
        initialized = true;

        for (var i = 0; i < tabElements.length; i++) {
          var tab = tabElements[i];
          if (detected && tab.classList.contains('tabs__item--active')) {
            detected = true;
            activeTabIndex = i;
          }
          addClickHandle(tab, i);
        }
      }
    };

    var addClickHandle = function (tab, index) {
      tab.addEventListener('click', function (evt) {
        evt.preventDefault();
        goToTab(index);
      });
    };

    var goToTab = function (index) {
      if (index !== activeTabIndex) {
        tabElements[activeTabIndex].classList.remove('tabs__item--active');
        tabElements[index].classList.add('tabs__item--active');
        tabContentSections[activeTabIndex].classList.remove('tabs__content--active');
        tabContentSections[index].classList.add('tabs__content--active');
        if (tabElements[index].classList.contains('filters__button')) {
          var activeFilter;
          activeFilter = tabElements[index].parentNode.parentNode.querySelector('.filters__button--active');
          activeFilter.classList.remove('filters__button--active');
          activeFilter = tabElements[index];
          activeFilter.classList.add('filters__button--active');
        }
        if (tabElements[index].classList.contains('messages__contacts-tab')) {
          var activeContact;
          activeContact = tabElements[index].parentNode.parentNode.querySelector('.messages__contacts-tab--active');
          activeContact.classList.remove('messages__contacts-tab--active');
          activeContact = tabElements[index];
          activeContact.classList.add('messages__contacts-tab--active');
        }
        activeTabIndex = index;
      }
    };

    initializeSwitch();

    return {
      init: initializeSwitch,
      goToTab: goToTab
    };
  }

  var addingPostTabs = document.querySelector('.adding-post__tabs-wrapper');
  var profileTabs = document.querySelector('.profile__tabs-wrapper');
  var messagesTabs = document.querySelector('.messages');

  if (addingPostTabs) {
    var addingPostCollback = switchTabs(addingPostTabs);
  }

  if (profileTabs) {
    var profileCollback = switchTabs(profileTabs);
  }

  if (messagesTabs) {
    var messagesCollback = switchTabs(messagesTabs);
  }
})();
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
'use script';

(function () {
  var ESC_KEYCODE = 27;

  window.util = {
    isEscEvent: function (evt, cb) {
      if (evt.keyCode === ESC_KEYCODE) {
        cb();
      }
    },

    getScrollbarWidth: function () {
      return window.innerWidth - document.documentElement.clientWidth;
    }
  }
})();
'use script';

(function () {
  var activeModal = document.querySelector('.modal--active');
  var modal = document.querySelector('.modal');
  var modalAdding = document.querySelector('.modal--adding');
  var addingPostSubmit = document.querySelector('.adding-post__submit');
  var scrollbarWidth = window.util.getScrollbarWidth() + 'px';
  var pageMainSection = document.querySelector('.page__main-section');
  var footerWrapper = document.querySelector('.footer__wrapper');

  var showModal = function (openButton, modal) {
    var closeButton = modal.querySelector('.modal__close-button');

    var onPopupEscPress = function (evt) {
      window.util.isEscEvent(evt, closeModal);
    };

    var closeModal = function (evt) {
      modal.classList.remove('modal--active');
      activeModal = false;
      document.removeEventListener('keydown', onPopupEscPress);
      document.documentElement.style.overflowY = 'auto';
      pageMainSection.style.paddingRight = '0';
      footerWrapper.style.paddingRight = '0';
    }

    var openModal = function (evt) {
      if (activeModal) {
        activeModal.classList.remove('modal--active');
      }

      modal.classList.add('modal--active');
      activeModal = modal;
      document.documentElement.style.overflowY = 'hidden';
      pageMainSection.style.paddingRight = scrollbarWidth;
      footerWrapper.style.paddingRight = scrollbarWidth;
      closeButton.focus();

      closeButton.addEventListener('click', function (evt) {
        evt.preventDefault();
        closeModal();
      });

      modal.addEventListener('click', function (evt) {
        if (evt.target === modal) {
          closeModal();
        }
      })

      document.addEventListener('keydown', onPopupEscPress);
    }

    openButton.addEventListener('click', function (evt) {
      openModal();
    });
  }

  // if (modal) {
  //   showModal(addingPostSubmit, modalAdding);
  // }
})();
(function () {
  var dropzone = document.querySelector('dropzone');
  var registrationFileZone = document.querySelector('.registration__file-zone');
  var addingPostPhotoFileZone = document.querySelector('.adding-post__file-zone--photo');
  var addingPostVideoFileZone = document.querySelector('.adding-post__file-zone--video');

  var inputsFile = document.querySelectorAll('input[type="file"]');

  if (inputsFile) {
    var addClickListener = function (inputFile) {
      inputFile.addEventListener('click', function (evt) {
        evt.preventDefault();
      });
    }

    for (var i = 0; i < inputsFile.length; i++) {
      addClickListener(inputsFile[i]);
    }
  }

  Dropzone.autoDiscover = false;

  if (registrationFileZone) {
    var regDropzone = new Dropzone('.registration__file-zone', {
      url: '#',
      maxFiles: 1,
      init: function() {
        this.on("addedfile", function() {
          if (this.files[1]!=null){
            this.removeFile(this.files[0]);
          }
        });
      },
      clickable: '.form__input-file-button',
      maxFilesize: null,
      maxThumbnailFilesize: 50,
      thumbnailWidth: null,
      thumbnailHeight: null,
      previewsContainer: '.dropzone-previews',
      acceptedFiles: 'image/*',
      parallelUploads: 1,
      autoProcessQueue: false,
      previewTemplate: '<div class="dz-preview dz-file-preview"><div class="registration__image-wrapper form__file-wrapper"><img class="form__image" src="" alt="" data-dz-thumbnail></div><div class="registration__file-data form__file-data"><span class="registration__file-name form__file-name dz-filename" data-dz-name></span><button class="registration__delete-button form__delete-button button" type="button" data-dz-remove><span>Удалить</span><svg class="registration__delete-icon form__delete-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" width="12" height="12"><path d="M18 1.3L16.7 0 9 7.7 1.3 0 0 1.3 7.7 9 0 16.7 1.3 18 9 10.3l7.7 7.7 1.3-1.3L10.3 9z"/></svg></button></div></div>'
    });
  }

  if (addingPostPhotoFileZone) {
    var addingPhotoDropzone = new Dropzone('.adding-post__file-zone--photo', {
      url: '#',
      maxFiles: 1,
      init: function() {
        this.on("addedfile", function() {
          if (this.files[1]!=null){
            this.removeFile(this.files[0]);
          }
        });
      },
      clickable: '.form__input-file-button--photo',
      maxFilesize: null,
      maxThumbnailFilesize: 50,
      thumbnailWidth: null,
      thumbnailHeight: null,
      previewsContainer: '.adding-post__file--photo',
      acceptedFiles: 'image/*',
      parallelUploads: 1,
      autoProcessQueue: false,
      previewTemplate: '<div class="dz-preview dz-file-preview"><div class="adding-post__image-wrapper form__file-wrapper"> <img class="form__image" src="" alt="" data-dz-thumbnail> </div> <div class="adding-post__file-data form__file-data"> <span class="adding-post__file-name form__file-name dz-filename" data-dz-name></span> <button class="adding-post__delete-button form__delete-button button" type="button" data-dz-remove> <span>Удалить</span> <svg class="adding-post__delete-icon form__delete-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" width="12" height="12"><path d="M18 1.3L16.7 0 9 7.7 1.3 0 0 1.3 7.7 9 0 16.7 1.3 18 9 10.3l7.7 7.7 1.3-1.3L10.3 9z"/></svg> </button> </div></div>'
    });
  }

  if (addingPostVideoFileZone) {
    var addingVideoDropzone = new Dropzone('.adding-post__file-zone--video', {
      url: '#',
      maxFiles: 1,
      init: function() {
        this.on("addedfile", function() {
          if (this.files[1]!=null){
            this.removeFile(this.files[0]);
          }
        });
      },
      clickable: '.form__input-file-button--video',
      maxFilesize: null,
      maxThumbnailFilesize: 50,
      thumbnailWidth: null,
      thumbnailHeight: null,
      previewsContainer: '.adding-post__file--video',
      acceptedFiles: 'image/*',
      parallelUploads: 1,
      autoProcessQueue: false,
      previewTemplate: '<div class="dz-preview dz-file-preview"><div class="adding-post__video-wrapper form__file-wrapper form__file-wrapper--video"> <img class="form__image" src="" alt="" data-dz-thumbnail> </div> <div class="adding-post__file-data form__file-data"> <span class="adding-post__file-name form__file-name dz-filename" data-dz-name></span> <button class="adding-post__delete-button form__delete-button button" type="button" data-dz-remove> <span>Удалить</span> <svg class="adding-post__delete-icon form__delete-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" width="12" height="12"><path d="M18 1.3L16.7 0 9 7.7 1.3 0 0 1.3 7.7 9 0 16.7 1.3 18 9 10.3l7.7 7.7 1.3-1.3L10.3 9z"/></svg> </button> </div></div>'
    });
  }
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiIiwic291cmNlcyI6WyJtYWluLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIid1c2Ugc3RyaWN0JztcblxuLy89IHRlbXBsYXRlcy90YWJzLmpzXG4vLz0gdGVtcGxhdGVzL3NvcnRpbmcuanNcbi8vPSB0ZW1wbGF0ZXMvZmlsdGVycy5qc1xuLy89IHRlbXBsYXRlcy91dGlsLmpzXG4vLz0gdGVtcGxhdGVzL21vZGFsLmpzXG4vLz0gdGVtcGxhdGVzL2Ryb3B6b25lLXNldHRpbmdzLmpzXG4iXSwiZmlsZSI6Im1haW4uanMifQ==
