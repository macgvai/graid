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
