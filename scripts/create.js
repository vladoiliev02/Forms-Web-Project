// DO NOT DELETE THIS VARIABLE !!!!
let form = {
  title: "form1",
  questions: [
    {
      id: 1,
      type: 'text',
      value: 'What is your name?'
    },
    {
      id: 2,
      type: 'radio',
      value: 'What is your gender?',
      values: [
        "male",
        "female"
      ]
    },
    {
      id: 3,
      type: 'checkbox',
      value: 'What is your employment status?',
      values: [
        "student",
        "employed"
      ]
    },
    {
      id: 4,
      type: 'number',
      value: 'What is your age?',
      min: "0",
      max: "100"
    }
  ]
}

window.onload = async function () {
  const user = await getAuthenticatedUser();

  if (user == null) {
    window.location.href = '../index.html';
  }

  const urlParams = new URLSearchParams(window.location.search);
  const formId = urlParams.get('formId');

  if (formId) {
    const response = await fetchWithErrorHandling(`../php/forms.php?formId=${formId}`);
    form = await response.json();

    document.getElementById('create-form').innerHTML = 'Update';
    displayFormHTML(form);
  }

  // DELETE THIS
  // displayFormHTML(form);

  document.getElementById('add-question').addEventListener('click', function () {
    const container = document.getElementById('questions');
    const questionDivs = container.querySelectorAll('.question');
    if (!checkEmptyInputs(questionDivs)) {
      return;
    }

    createQuestion(container);
  });

  document.getElementById('create-form').addEventListener('click', function () {
    const jsonToggle = document.getElementById('jsonToggle');
    if (!jsonToggle.checked) {
      const titleInput = document.getElementById('form-name');
      if (titleInput.value === '') {
        displayError(document.getElementById('form-name-div'), 'Please fill out the title before submitting.');
        return;
      }

      const container = document.getElementById('questions');
      const questionDivs = container.querySelectorAll('.question');

      form = createForm(titleInput.value, questionDivs);
      console.log(form);
      console.log(JSON.stringify(form))
    } else {
      const jsonTextarea = document.getElementById('questions').querySelector('textarea');
      if (jsonTextarea) {
        try {
          const jsonObject = JSON.parse(jsonTextarea.value);
          form = jsonObject;
        } catch (error) {
          displayError(document.getElementById('questions'), 'Invalid JSON');
          return;
        }
      } else {
        displayError(document.getElementById('questions'), 'Please fill out the JSON before submitting.');
        return;
      }
    }

    if (!validateForm(form)) {
      return;
    }

    fetchWithErrorHandling(`../php/forms.php?formId=${formId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(form)
    })
      .then(response => response.json())
      .then(form => {
        window.location.href = `./form.php?id=${form.id}`;
      })
  });

  document.getElementById('jsonToggle').addEventListener('change', function () {
    const questionsSection = document.getElementById('questions');
    const formName = document.getElementById('form-name');

    if (this.checked) {
      formName.style.display = 'none';
      form = createForm(formName.value, document.getElementById('questions').querySelectorAll('.question'));
      questionsSection.innerHTML = '';
      const jsonForm = JSON.stringify(form, null, 2);

      const jsonTextarea = document.createElement('textarea');
      jsonTextarea.value = jsonForm;
      questionsSection.appendChild(jsonTextarea);
      document.getElementById('add-question').style.display = 'none';
    } else {
      formName.style.display = 'block';
      const jsonTextarea = questionsSection.querySelector('textarea');
      if (jsonTextarea) {
        try {
          const jsonObject = JSON.parse(jsonTextarea.value);
          form = jsonObject;
        } catch (error) {
          this.checked = !this.checked;
          displayError(document.getElementById('questions'), 'Invalid JSON');
          return;
        }
        jsonTextarea.remove();
      }

      questionsSection.innerHTML = '';
      document.getElementById('add-question').style.display = 'block';
      displayFormHTML(form);
    }
  });

};

function createQuestion(container, question = {}) {
  const questionDiv = document.createElement('div');
  questionDiv.setAttribute('data-question-id', question.id);
  questionDiv.classList.add('question');

  const newQuestion = document.createElement('input');
  newQuestion.id = 'question-value';
  newQuestion.type = 'text';
  newQuestion.value = question.value || '';
  questionDiv.appendChild(newQuestion);

  createAnswerInput(questionDiv, question);

  const deleteButton = document.createElement('button');
  deleteButton.textContent = 'Delete';
  deleteButton.addEventListener('click', function () {
    questionDiv.remove();
    if (!container.querySelector('.question')) {
      document.getElementById('create-form').style.display = 'none';
    }
  });
  questionDiv.appendChild(deleteButton);

  container.appendChild(questionDiv);
  document.getElementById("create-form").style.display = "inline-block";
}

function createAnswerInput(container, question = {}) {
  if (!question.type) {
    question.type = 'text';
  }

  const inputTypeSelect = document.createElement('select');
  ['text', 'checkbox', 'radio', 'number', 'date'].forEach(type => {
    const option = document.createElement('option');
    option.value = type;
    option.textContent = type;
    if (type === question.type) {
      option.selected = true;
    }
    inputTypeSelect.appendChild(option);
  });
  inputTypeSelect.id = 'input-type';
  container.appendChild(inputTypeSelect);

  const configPanel = document.createElement('div');
  configPanel.className = 'configPanel';
  container.appendChild(configPanel);
  inputTypeSelect.addEventListener('change', function () {
    configPanel.innerHTML = '';

    displayConfigPanel(configPanel, this.value);
  });

  if (question) {
    displayConfigPanel(configPanel, question.type, question);
  }
}

function displayConfigPanel(configPanel, type, question = {}) {
  switch (type) {
    case 'radio':
    case 'checkbox':
      const optionInput = document.createElement('input');
      optionInput.type = 'text';
      optionInput.placeholder = 'Option value';
      configPanel.appendChild(optionInput);

      const optionsContainer = document.createElement('div');

      if (question && question.values) {
        for (const value of question.values) {
          displayNewOption(value, optionInput, optionsContainer);
        }
      }

      const addButton = document.createElement('button');
      addButton.textContent = 'Add Option';
      addButton.addEventListener('click', function () {
        displayNewOption(optionInput.value, optionInput, optionsContainer);
      });

      const inputContainer = document.createElement('div');
      inputContainer.className = 'input-container';
      inputContainer.appendChild(optionInput);
      inputContainer.appendChild(addButton);

      configPanel.appendChild(inputContainer);
      configPanel.appendChild(optionsContainer);
      break;
    case 'number':
      const minLabel = document.createElement('label');
      minLabel.textContent = 'Min value:';
      minLabel.htmlFor = 'minInput';
      configPanel.appendChild(minLabel);

      const minInput = document.createElement('input');
      minInput.type = 'number';
      minInput.placeholder = 'Min value';
      minInput.value = question ? (question.min ? question.min : 1) : 1;
      minInput.id = 'minInput';
      configPanel.appendChild(minInput);

      const maxLabel = document.createElement('label');
      maxLabel.textContent = 'Max value:';
      maxLabel.htmlFor = 'maxInput';
      configPanel.appendChild(maxLabel);

      const maxInput = document.createElement('input');
      maxInput.type = 'number';
      maxInput.placeholder = 'Max value';
      maxInput.value = question ? (question.max ? question.max : 10) : 10;
      maxInput.id = 'maxInput';
      configPanel.appendChild(maxInput);

      const stepLabel = document.createElement('label');
      stepLabel.textContent = 'Step value:';
      stepLabel.htmlFor = 'stepInput';
      configPanel.appendChild(stepLabel);

      const stepInput = document.createElement('input');
      stepInput.type = 'number';
      stepInput.placeholder = 'Step value';
      stepInput.value = question ? (question.step ? question.step : 1) : 1;
      stepInput.id = 'stepInput';
      configPanel.appendChild(stepInput);
      break;
    case 'date':
      const minDateLabel = document.createElement('label');
      minDateLabel.textContent = 'Min date:';
      minDateLabel.htmlFor = 'minDateInput';
      configPanel.appendChild(minDateLabel);

      const minDateInput = document.createElement('input');
      minDateInput.type = 'date';
      minDateInput.placeholder = 'Min date';
      minDateInput.id = 'minDateInput';
      if (question) {
        minDateInput.value = question.min ? question.min : '';
      }
      configPanel.appendChild(minDateInput);

      const maxDateLabel = document.createElement('label');
      maxDateLabel.textContent = 'Max date:';
      maxDateLabel.htmlFor = 'maxDateInput';
      configPanel.appendChild(maxDateLabel);

      const maxDateInput = document.createElement('input');
      maxDateInput.type = 'date';
      maxDateInput.placeholder = 'Max date';
      maxDateInput.id = 'maxDateInput';
      if (question) {
        maxDateInput.value = question.max ? question.max : '';
      }
      configPanel.appendChild(maxDateInput);
      break;
    case 'text':
      break;
  }
}

function displayNewOption(value, optionInput, optionsContainer, question = {}) {
  const option = document.createElement('div');
  option.className = 'option-value';

  const valueSpan = document.createElement('span');
  valueSpan.id = 'optionValue';
  valueSpan.textContent = value;
  option.appendChild(valueSpan);

  const deleteButton = document.createElement('button');
  deleteButton.textContent = 'Delete';
  deleteButton.addEventListener('click', function () {
    option.remove();
  });

  option.appendChild(deleteButton);
  optionsContainer.appendChild(option);
  optionInput.value = '';
}

function createForm(title, questionDivs) {
  const questions = [];

  for (const questionDiv of questionDivs) {
    let id = null;
    if (parseInt(questionDiv.getAttribute('data-question-id'))) {
      id = parseInt(questionDiv.getAttribute('data-question-id'));
    }

    let question = {
      id: id,
      value: '',
      type: '',
      values: [],
      min: '',
      max: '',
      step: ''
    };

    question.value = questionDiv.querySelector('#question-value').value;
    if (!question.value) {
      displayError(questionDiv, 'Please fill out the question before submitting.');
    }

    question.type = questionDiv.querySelector('#input-type').value;

    switch (question.type) {
      case 'radio':
      case 'checkbox':
        const optionDivs = questionDiv.querySelectorAll('.option-value');
        const values = [];
        for (const optionDiv of optionDivs) {
          values.push(optionDiv.querySelector('#optionValue').textContent);
        }
        question.values = values;

        if (question.values.length == 0) {
          displayError(questionDiv, 'Please fill out the options before submitting.');
        }
        break;
      case 'number':
        question.min = questionDiv.querySelector('#minInput').value;
        question.max = questionDiv.querySelector('#maxInput').value;
        question.step = questionDiv.querySelector('#stepInput').value;
        break;
      case 'date':
        question.min = questionDiv.querySelector('#minDateInput').value;
        question.max = questionDiv.querySelector('#maxDateInput').value;
        break;
    }

    questions.push(question);
  }

  const form = {
    title: title,
    questions: questions
  };

  return form;
}

function displayFormHTML(form) {
  document.getElementById('form-name').value = form.title;
  for (const question of form.questions) {
    createQuestion(document.getElementById('questions'), question);
  }
}

function validateForm(form) {
  const container = document.getElementById("json-section");
  if (form.title === '') {
    displayError(container,'Please fill out the title before submitting.');
    return false;
  }

  if (form.questions.length === 0) {
    displayError(container,'Please fill out the questions before submitting.');
    return false;
  }

  for (const question of form.questions) {
    if (question.value === '') {
      displayError(container,'Please fill out the questions before submitting.');
      return false;
    }

    if (question.type === 'radio' || question.type === 'checkbox') {
      if (!question.values || question.values.length === 0) {
        displayError(container,'Please fill out the options before submitting.');
        return false;
      }
    }
  }

  return true;
}