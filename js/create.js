window.onload = function () {
  const user = getAuthenticatedUser();

  if (user == null) {
    window.location.href = './index.html';
  }

  document.getElementById('add-question').addEventListener('click', function () {
    const container = document.getElementById('questions');
    const questionDivs = container.querySelectorAll('.question');
    if (!checkEmptyInputs(questionDivs)) {
      return;
    }

    createQuestion(container);
  });

  document.getElementById('create-form').addEventListener('click', function () {
    const titleInput = document.getElementById('form-name');
    if (titleInput.value === '') {
      displayError(document.getElementById('form-name-div'), 'Please fill out the title before submitting.');
      return;
    }

    const container = document.getElementById('questions');
    const questionDivs = container.querySelectorAll('.question');
    if (!checkEmptyInputs(questionDivs)) {
      return;
    }
    const questions = [];

    for (const questionDiv of questionDivs) {
      const input = questionDiv.querySelector('input[type="text"]');
      questions.push({ value: input.value });
    }

    const form = {
      title: titleInput.value,
      userId: user.id,
      questions: questions
    };

    console.log(JSON.stringify(form))

    fetchWithErrorHandling(`./php/forms.php`, {
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

};

function createQuestion(container) {
  const questionDiv = document.createElement('div');
  questionDiv.classList.add('question');

  const newQuestion = document.createElement('input');
  newQuestion.type = 'text';
  questionDiv.appendChild(newQuestion);

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
