// Função para mostrar o formulário correto
function showForm(formNumber) {
    // Esconde todos os formulários
    const forms = document.querySelectorAll('.container');
    forms.forEach(form => form.classList.remove('active-form'));

    // Mostra o formulário selecionado
    const selectedForm = document.getElementById('form' + formNumber);
    selectedForm.classList.add('active-form');
}
// Exibe o primeiro formulário por padrão
showForm(1);


