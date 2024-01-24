window.onload = async function () {
  const user = await getAuthenticatedUser();

  
  if (user != null) {
    document.getElementById("logout-button").style.display = "block";
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

  let buttonDiv = document.createElement('div');

  let viewResultsButton = document.createElement('button');
  viewResultsButton.textContent = 'View Results';
  viewResultsButton.classList.add('invertedbutton');
  viewResultsButton.addEventListener('click', function (event) {
    event.stopPropagation();
    window.location.href = `./views/form.php?id=${form.id}`;
  });
  buttonDiv.appendChild(viewResultsButton);

  let answerButton = document.createElement('button');
  answerButton.textContent = 'Answer';
  answerButton.classList.add('invertedbutton');
  answerButton.addEventListener('click', function (event) {
    event.stopPropagation();
    window.location.href = `./views/answer.php?formId=${form.id}`;
  });
  buttonDiv.appendChild(answerButton);

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
  buttonDiv.appendChild(deleteButton);

  formElement.appendChild(buttonDiv);
  document.getElementById(formContainerId).appendChild(formElement);
}
