window.onload = function () {
  const user = getAuthenticatedUser();

  if (user != null) {
    document.getElementById('annonymous-section').style.display = 'none';
    document.getElementById('forms-section').style.display = 'flex';

    let userForms = fetchUsersForms(user.id);
    
    userForms.forEach(form => {
      let formElement = document.createElement('div');
      formElement.classList.add('form');
      formElement.innerHTML = `
        <h3>${form.title}</h3>
      `;

      formElement.addEventListener('click', function (event) {
        window.location.href = `./views/form.php?id=${form.id}`;
      });

      document.getElementById('forms').appendChild(formElement);
    });
  }
};

function fetchUsersForms(userId) {
  // return fetch(`/php/forms.php?userId=${userId}`)
  //   .then(response => response.json())
  //   .then(forms => forms);
    return [
      {
        id: 1,
        title: 'Form 1'
      },
      {
        id: 2,
        title: 'Form 2'
      },
      {
        id: 3,
        title: 'Form 3'
      }
    ]
}