window.onload = function () {
  const user = getAuthenticatedUser();

  if (user != null) {
    document.getElementById('annonymous-section').style.display = 'none';
    document.getElementById('forms-section').style.display = 'flex';

    fetchUsersForms(user.id)
      .then(forms => {
        forms.forEach(form => {
          displayForm('forms', form);
        });
      });
  }
};

function fetchUsersForms(userId) {
  return fetchWithErrorHandling(`./php/forms.php?userId=${userId}`)
    .then(response => response.json());
}

function displayForm(formContainerId, form) {
  let formElement = document.createElement('div');
  formElement.classList.add('form');
  formElement.innerHTML = `
    <h3>${form.title}</h3>
  `;

  let deleteButton = document.createElement('button');
  deleteButton.textContent = 'Delete';
  deleteButton.classList.add('delete-button');
  deleteButton.addEventListener('click', function (event) {
    event.stopPropagation();
    fetchWithErrorHandling(`./php/forms.php?formId=${form.id}`, {
      method: 'DELETE',
    }).then(response => {
      if (response.ok) {
        formElement.remove();
      }
    });
  });
  formElement.appendChild(deleteButton);

  formElement.addEventListener('click', function (event) {
    window.location.href = `./views/form.php?id=${form.id}`;
  });

  document.getElementById(formContainerId).appendChild(formElement);
}
