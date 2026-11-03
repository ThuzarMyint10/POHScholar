document.addEventListener('DOMContentLoaded', function () {
  var toastTimer;

  function fieldLabel(input) {
    if (input.id) {
      var label = document.querySelector('label[for="' + CSS.escape(input.id) + '"]');
      if (label) return label.textContent.trim().replace(/\s+/g, ' ');
    }
    return input.getAttribute('aria-label') || input.name || 'This field';
  }

  function showToast(message, input) {
    var toast = input && input.parentElement.querySelector('[data-form-toast]');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'form-toast';
      toast.setAttribute('role', 'alert');
      toast.setAttribute('aria-live', 'assertive');
      toast.dataset.formToast = '';
      if (input) input.insertAdjacentElement('afterend', toast);
      else document.body.appendChild(toast);
    }
    if (input && input.hasAttribute('data-incomplete-submit')) {
      toast.classList.add('submission-toast');
      var buttonRect = input.getBoundingClientRect();
      var toastWidth = Math.min(360, window.innerWidth - 24);
      toast.style.width = toastWidth + 'px';
      toast.style.left = Math.min(
        Math.max(buttonRect.left + (buttonRect.width / 2), toastWidth / 2 + 12),
        window.innerWidth - toastWidth / 2 - 12
      ) + 'px';
      toast.style.top = Math.max(buttonRect.top - 8, 12) + 'px';
    } else {
      toast.classList.remove('submission-toast');
      toast.style.removeProperty('width');
      toast.style.removeProperty('left');
      toast.style.removeProperty('top');
    }
    toast.textContent = message;
    toast.classList.add('is-visible');
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () {
      toast.classList.remove('is-visible');
    }, 3600);
  }

  function clearToast(input) {
    var toast = input.parentElement.querySelector('[data-form-toast]');
    if (toast) toast.remove();
  }

  function invalidMessage(input) {
    var label = fieldLabel(input);
    if (input.hasAttribute('data-digits-only')) return label + ' accepts numbers only.';
    if (input.validity.valueMissing) return label + ' is required.';
    if (input.validity.typeMismatch) return 'Please enter a valid ' + label.toLowerCase() + '.';
    if (input.validity.badInput) return 'Please enter a valid value for ' + label.toLowerCase() + '.';
    if (input.validity.rangeUnderflow || input.validity.rangeOverflow) return label + ' is outside the allowed range.';
    if (input.validity.tooShort) return label + ' is too short.';
    if (input.validity.tooLong) return label + ' is too long.';
    if (input.validity.patternMismatch) return label + ' has an invalid format.';
    return 'Please check ' + label.toLowerCase() + '.';
  }

  function validateField(input) {
    if (input.disabled || input.readOnly) return;
    if (input.checkValidity()) {
      clearToast(input);
      return;
    }
    showToast(invalidMessage(input), input);
  }

  document.addEventListener('invalid', function (event) {
    if (event.target.matches('input, select, textarea')) showToast(invalidMessage(event.target), event.target);
  }, true);

  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('mainNav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  document.querySelectorAll('input, select, textarea').forEach(function (input) {
    input.addEventListener('blur', function () {
      validateField(input);
    });
    input.addEventListener('input', function () {
      if (input.checkValidity()) clearToast(input);
    });
  });

  document.querySelectorAll('[data-digits-only]').forEach(function (input) {
    input.addEventListener('input', function () {
      var original = input.value;
      input.value = original.replace(/[^0-9]/g, '');
      if (input.value !== original) showToast(fieldLabel(input) + ' accepts numbers only.', input);
    });
  });

  document.querySelectorAll('[data-incomplete-submit]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      showToast('Complete every checklist section above before submitting.', button);
    });
  });
});
