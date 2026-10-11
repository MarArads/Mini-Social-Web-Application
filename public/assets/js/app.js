(function () {
  'use strict';

  var USERNAME_RE = /^[A-Za-z0-9_]{3,50}$/;
  var ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

  
  function field(form, name) {
    return form.elements[name] || null;
  }

  function clearError(input) {
    input.classList.remove('input-invalid');
    var scope = input.form || document;
    var old = scope.querySelector('.field-error[data-for="' + (input.name || '') + '"]');
    if (old) old.remove();
  }

  function showError(input, message) {
    clearError(input);
    input.classList.add('input-invalid');
    var note = document.createElement('div');
    note.className = 'field-error';
    note.setAttribute('role', 'alert');
    note.setAttribute('data-for', input.name || '');
    note.textContent = message;
    var row = input.closest('.form-actions');
    if (row) {
      
      row.insertAdjacentElement('beforebegin', note);
    } else {
      input.insertAdjacentElement('afterend', note);
    }
  }

  /
  function checkImage(input) {
    clearError(input);
    if (!input.files || !input.files.length) return true;

    var file = input.files[0];
    var maxMb = parseFloat(input.getAttribute('data-max-mb')) || 5;

    if (ALLOWED_TYPES.indexOf(file.type) === -1) {
      showError(input, 'Please choose a JPG, PNG, GIF, or WEBP image.');
      input.value = '';
      return false;
    }
    if (file.size > maxMb * 1024 * 1024) {
      showError(input, 'That image is too big. The limit is ' + maxMb + ' MB.');
      input.value = '';
      return false;
    }
    return true;
  }

  document.querySelectorAll('input[type="file"]').forEach(function (input) {
    input.addEventListener('change', function () { checkImage(input); });
  });

  // ---------- per-form rules ----------
  // Each rule returns true when the form is fine, and shows messages when it is not.
  var rules = {
    login: function (form) {
      var ok = true;
      var user = field(form, 'username');
      var pass = field(form, 'password');
      if (!user.value.trim()) { showError(user, 'Please enter your username.'); ok = false; }
      if (!pass.value) { showError(pass, 'Please enter your password.'); ok = false; }
      return ok;
    },

    register: function (form) {
      var ok = true;
      var name = field(form, 'full_name');
      var user = field(form, 'username');
      var pass = field(form, 'password');
      var confirm = field(form, 'confirm_password');
      var bio = field(form, 'bio');

      if (!name.value.trim()) {
        showError(name, 'Please enter your full name.'); ok = false;
      } else if (name.value.length > 100) {
        showError(name, 'Full name must be 100 characters or fewer.'); ok = false;
      }

      if (!USERNAME_RE.test(user.value.trim())) {
        showError(user, 'Username must be 3-50 characters: letters, numbers, and underscores only.'); ok = false;
      }

      if (pass.value.length < 6) {
        showError(pass, 'Password must be at least 6 characters.'); ok = false;
      } else if (pass.value.length > 72) {
        showError(pass, 'Password must be 72 characters or fewer.'); ok = false;
      }

      if (confirm.value !== pass.value) {
        showError(confirm, 'Passwords do not match.'); ok = false;
      }

      if (bio.value.length > 255) {
        showError(bio, 'Bio must be 255 characters or fewer.'); ok = false;
      }

      var photo = field(form, 'profile_image');
      if (photo && !checkImage(photo)) ok = false;
      return ok;
    },

    profile: function (form) {
      var ok = true;
      var name = field(form, 'full_name');
      var bio = field(form, 'bio');

      if (!name.value.trim()) {
        showError(name, 'Please enter your full name.'); ok = false;
      } else if (name.value.length > 100) {
        showError(name, 'Full name must be 100 characters or fewer.'); ok = false;
      }
      if (bio.value.length > 255) {
        showError(bio, 'Bio must be 255 characters or fewer.'); ok = false;
      }

      var photo = field(form, 'profile_image');
      if (photo && !checkImage(photo)) ok = false;
      return ok;
    },

    post: function (form) {
      var ok = true;
      var content = field(form, 'content');
      var max = parseInt(content.getAttribute('maxlength'), 10) || 500;

      if (!content.value.trim()) {
        showError(content, 'Please write something first.'); ok = false;
      } else if (content.value.length > max) {
        showError(content, 'Please keep it to ' + max + ' characters or fewer.'); ok = false;
      }

      var photo = field(form, 'image');
      if (photo && !checkImage(photo)) ok = false;
      return ok;
    },

    comment: function (form) {
      var content = field(form, 'content');
      if (!content.value.trim()) {
        showError(content, 'Please write a comment first.');
        return false;
      }
      return true;
    }
  };

  document.querySelectorAll('form[data-validate]').forEach(function (form) {
    var rule = rules[form.getAttribute('data-validate')];
    if (!rule) return;

    // Remove an error as soon as the person starts fixing the field.
    form.addEventListener('input', function (event) {
      if (event.target && event.target.name) clearError(event.target);
    });

    form.addEventListener('submit', function (event) {
      form.querySelectorAll('.field-error').forEach(function (n) { n.remove(); });
      form.querySelectorAll('.input-invalid').forEach(function (n) { n.classList.remove('input-invalid'); });

      if (!rule(form)) {
        event.preventDefault();
        var firstBad = form.querySelector('.input-invalid');
        if (firstBad) firstBad.focus();
        return;
      }

      
      var button = form.querySelector('button[type="submit"]');
      if (button) {
        button.disabled = true;
        setTimeout(function () { button.disabled = false; }, 4000);
      }
    });
  });
})();