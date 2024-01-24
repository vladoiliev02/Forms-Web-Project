function getAuthenticatedUser() {
  // Dancho...
  return { id: 1 }
}



function fetchWithErrorHandling(uri, options) {
  return fetch(uri, options)
    .then(response => {
      if (!response.ok) {
        throw response;
      }
      return response;
    })
    .catch(err => {
      err.text().then(errorMessage => {
        const errorData = JSON.parse(errorMessage);
        showErrorMessage(errorData.error);
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
