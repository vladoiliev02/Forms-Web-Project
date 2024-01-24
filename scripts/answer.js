window.onload = async function () {
  const user = await getAuthenticatedUser();

  if (user == null) {
    window.location.href = '../index.html';
  }

  const urlParams = new URLSearchParams(window.location.search);
  const formId = urlParams.get('formId');

  if (!formId) {
    window.location.href = './404.php';
  }

  fetchForm(formId)
    .then(form => {
      document.getElementById('form-title').innerHTML = form.title;
      for (const question of form.questions) {
        createQuestion(document.getElementById('questions'), question);
      }
      document.getElementById('submit').style.display = 'block';
    });

  document.getElementById('submit').addEventListener('click', function () {
    const questionsDiv = document.getElementById('questions');
    console.log(questionsDiv)
    if (!checkEmptyInputs(questionsDiv.querySelectorAll('.question'))) {
      return
    }

    const inputs = document.querySelectorAll('.question input');
    const answers = [];
    for (const input of inputs) {
      const questionId = input.parentElement.getAttribute('data-question-id');
      answers.push({ questionId: parseInt(questionId), userId: user.id, value: input.value });
    }

    console.log(JSON.stringify(answers));

    fetchWithErrorHandling('../php/forms.php', {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(answers),
    })
      .then(_ => {
        window.location.href = '../index.html';
      });
  });
};

function fetchForm(formId) {
  return fetchWithErrorHandling(`../php/forms.php?formId=${formId}`)
    .then(response => response.json());
}

function createQuestion(container, question) {
  const questionDiv = document.createElement('div');
  questionDiv.className = 'question';
  questionDiv.setAttribute('data-question-id', question.id);

  const questionP = document.createElement('p');
  questionP.textContent = question.value;
  questionDiv.appendChild(questionP);

  const input = document.createElement('input');
  input.type = 'text';
  questionDiv.appendChild(input);

  container.appendChild(questionDiv);
}