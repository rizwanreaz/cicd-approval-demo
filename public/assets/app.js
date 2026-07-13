// Small vanilla-JS progressive enhancements shared by login.php and register.php.
// No frameworks, no build step - this file is served as-is.

document.addEventListener('DOMContentLoaded', function () {
  // Show/hide password toggles: any button.password-toggle flips the type
  // of the <input> inside its .password-field wrapper.
  document.querySelectorAll('.password-toggle').forEach(function (button) {
    button.addEventListener('click', function () {
      var input = button.parentElement.querySelector('input');
      var showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      button.textContent = showing ? 'Show' : 'Hide';
    });
  });

  // Disable the submit button while the form is processing, so a slow
  // request (or an eager double-click) doesn't fire twice.
  document.querySelectorAll('form.js-form').forEach(function (form) {
    form.addEventListener('submit', function () {
      var submitButton = form.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Please wait...';
      }
    });
  });

  // Inline hints on the registration form. These mirror the server-side
  // rules for instant feedback only - the server remains the source of
  // truth and re-validates everything.
  var usernameInput = document.getElementById('username');
  var emailInput = document.getElementById('email');
  var passwordInput = document.getElementById('password');
  var confirmInput = document.getElementById('password_confirm');

  function setHint(input, message, isInvalid) {
    if (!input) return;
    var hint = document.querySelector('[data-hint-for="' + input.id + '"]');
    if (!hint) return;
    hint.textContent = message;
    hint.classList.toggle('invalid', !!isInvalid);
  }

  if (usernameInput && usernameInput.dataset.registerField) {
    usernameInput.addEventListener('input', function () {
      var value = usernameInput.value.trim();
      if (value.length === 0) {
        setHint(usernameInput, '3-30 characters: letters, numbers, underscores.', false);
      } else if (value.length < 3 || value.length > 30) {
        setHint(usernameInput, 'Must be 3-30 characters.', true);
      } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
        setHint(usernameInput, 'Only letters, numbers, and underscores allowed.', true);
      } else {
        setHint(usernameInput, 'Looks good.', false);
      }
    });
  }

  if (emailInput && emailInput.dataset.registerField) {
    emailInput.addEventListener('input', function () {
      var value = emailInput.value.trim();
      if (value.length === 0) {
        setHint(emailInput, 'We will only use this for your account.', false);
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        setHint(emailInput, 'Enter a valid email address.', true);
      } else {
        setHint(emailInput, 'Looks good.', false);
      }
    });
  }

  if (passwordInput && passwordInput.dataset.registerField) {
    passwordInput.addEventListener('input', function () {
      var value = passwordInput.value;
      if (value.length === 0) {
        setHint(passwordInput, 'At least 8 characters.', false);
      } else if (value.length < 8) {
        setHint(passwordInput, 'At least 8 characters.', true);
      } else {
        setHint(passwordInput, 'Looks good.', false);
      }
      if (confirmInput && confirmInput.value.length > 0) {
        confirmInput.dispatchEvent(new Event('input'));
      }
    });
  }

  if (confirmInput && confirmInput.dataset.registerField) {
    confirmInput.addEventListener('input', function () {
      var value = confirmInput.value;
      if (value.length === 0) {
        setHint(confirmInput, '', false);
      } else if (passwordInput && value !== passwordInput.value) {
        setHint(confirmInput, 'Passwords do not match.', true);
      } else {
        setHint(confirmInput, 'Passwords match.', false);
      }
    });
  }
});
