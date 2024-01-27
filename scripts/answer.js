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
    if (!checkEmptyInputs(questionsDiv.querySelectorAll('.question'))) {
      return
    }

    const inputs = document.querySelectorAll('.question input');
    const answers = [];
    for (const input of inputs) {
      const questionId = input.parentElement.getAttribute('data-question-id');
      if (input.type === 'radio' || input.type === 'checkbox') {
        if (input.checked) {
          const label = input.nextElementSibling;
          const value = label.textContent;
          answers.push({ questionId: parseInt(questionId), userId: user.id, value: value, type: input.type });
        }
      } else {
        answers.push({ questionId: parseInt(questionId), userId: user.id, value: input.value, type: input.type});
      }
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

  createAnswerInput(questionDiv, question);

  container.appendChild(questionDiv);
}

function createAnswerInput(container, question) {
  const input = document.createElement('input');
  switch (question.type) {
    case 'text':
      input.type = question.type;
      input.placeholder = 'Your answer';
      container.appendChild(input);
      break;
    case 'number':
      input.type = question.type;
      input.placeholder = 'Your answer';
      input.min = question.min;
      input.max = question.max;
      input.step = question.step;
      container.appendChild(input);
      break;
    case 'date':
      input.type = question.type;
      input.placeholder = 'Your answer';
      input.min = question.min;
      input.max = question.max;
      container.appendChild(input);
      break;
    case 'checkbox':
    case 'radio':
      const options = question.values;
      const optionsDiv = document.createElement('div');
      optionsDiv.className = 'options';

      for (const option of options) {
        const singleOption = document.createElement('div');
        singleOption.setAttribute('data-question-id', question.id);
        singleOption.className = 'single-option';
        const optionInput = document.createElement('input');
        optionInput.type = question.type;
        optionInput.name = question.id;
        optionInput.id = `option-${option}`;

        const label = document.createElement('label');
        label.textContent = option;
        label.htmlFor = optionInput.id

        singleOption.appendChild(optionInput);
        singleOption.appendChild(label);
        optionsDiv.appendChild(singleOption);
      }
      container.appendChild(optionsDiv);
      break;
  }
}