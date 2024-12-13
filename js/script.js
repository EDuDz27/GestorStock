function showForm(formNumber) {
    const forms = document.querySelectorAll('.container');
    forms.forEach(form => form.classList.remove('active-form'));

    const selectedForm = document.getElementById('form' + formNumber);
    selectedForm.classList.add('active-form');
}

function handleFormSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    formData.append('_method', 'GET');

    fetch('processar.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            document.getElementById('resultados').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('resultados').innerHTML = "<p>Ocorreu um erro. Tente novamente.</p>";
        });
}

function init() {
    showForm(1);

    document.getElementById('formPesquisarProduto').addEventListener('submit', handleFormSubmit);
}
init();