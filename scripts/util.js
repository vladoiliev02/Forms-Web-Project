let prefix;

if (window.location.href.includes('/views/')) {
  prefix = '..';
} else {
  prefix = '.';
}

async function getAuthenticatedUser() {
  let response = await fetchWithErrorHandling(`${prefix}/php/auth.php`);
  let user = await response.json();
  console.log(user);

  return user;
}

function fetchWithErrorHandling(uri, options) {
  return fetch(uri, options)
    .then(response => {
      if (!response.ok) {
        if (response.status === 404) {
          window.location.href = prefix + '/views/404.php';
          return response;
        }
        throw response;
      }
      return response;
    })
    .catch(err => {
      err.text().then(errorMessage => {
        try {
          const errorData = JSON.parse(errorMessage);
          showErrorMessage(errorData.error);
          console.log(errorData);
        } catch (error) {
          console.log('Error:', errorMessage);
        }
      });
    });
}

function showErrorMessage(message) {
  const modal = document.createElement('div');
  modal.style.position = 'fixed';
  modal.style.top = '0';
  modal.style.left = '0';
  modal.style.width = '100%';
  modal.style.height = '100%';
  modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
  modal.style.color = 'white';
  modal.style.display = 'flex';
  modal.style.justifyContent = 'center';
  modal.style.flexDirection = 'column';
  modal.style.alignItems = 'center';
  modal.style.zIndex = '1000';
  modal.textContent = message;

  modal.addEventListener('click', function () {
    document.body.removeChild(modal);
  });

  const button = document.createElement('button');
  button.textContent = 'Close';
  button.addEventListener('click', function () {
    document.body.removeChild(modal);
  });
  modal.appendChild(button);

  document.body.appendChild(modal);
}

function checkEmptyInputs(container) {
  for (const div of container) {
    const input = div.querySelector('input[type="text"]');
    if (input && input.value === '') {
      displayError(div, 'Please fill out this question.')
      return false;
    }
  }

  return true;
}

function displayError(container, errorMessage) {
  if (!container.querySelector('.error')) {
    const message = document.createElement('div');
    message.classList.add('error');
    message.textContent = errorMessage;
    container.appendChild(message);

    setTimeout(function () {
      container.removeChild(message);
    }, 2000);
  }
}
