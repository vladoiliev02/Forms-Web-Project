window.onload = async function () {
    let result = await getAnswersMap();

    document.getElementById('form-title').innerHTML = result['title'];

    let index = 1;
    for (const [question, answersData] of Object.entries(result['answers'])) {
        canvasId = addCanvas(index);

        drawChart(canvasId, question, answersData['answers'], answersData['answersCount']);

        ++index;
    }
}

async function getAnswersMap() {
    var urlParams = new URLSearchParams(window.location.search);
    var formId = urlParams.get('formId');

    let response = await fetchWithErrorHandling(`../php/chartAnswer.php?formId=${formId}`);

    return await response.json();
}

function addCanvas(canvasId) {
    const canvas = document.createElement('canvas');
    canvas.id = 'canvas' + canvasId;

    document.body.appendChild(canvas);

    return canvas.id;
}


function drawChart(canvasId, question, answers, answersCount) {
    const barColors = "#522e92";

    new Chart(canvasId, {
        type: "bar",
        data: {
            labels: answers,
            datasets: [{
                backgroundColor: barColors,
                data: answersCount
            }]
        },
        options: {
            backgroundColor: "white",
            scales: {
                xAxes: [{
                    gridLines: {
                        color: "#100e14",
                        zeroLineColor: "white"
                    },
                    ticks: {
                        fontColor: "white",
                        fontSize: 15
                    }
                }],
                yAxes: [{
                    gridLines: {
                        //  color: "white",
                        zeroLineColor: "white"
                    },
                    ticks: {
                        fontColor: "white",
                        fontSize: 15,
                        beginAtZero: true,
                        type: 'linear',
                        precision: 0
                    }
                }]
            },
            legend: { display: false },
            title: {
                display: true,
                text: question,
                fontColor: "white",
                fontSize: 18
            }
        }
    });
}
