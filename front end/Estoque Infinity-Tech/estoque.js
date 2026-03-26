const campoBusca = document.querySelector('input[placeholder*="Filtrar"]');
const linhasTabela = document.querySelectorAll('tbody tr');

campoBusca.addEventListener('input', () => {
    const termo = campoBusca.value.toLowerCase();

    linhasTabela.forEach(linha => {
        const conteudoLinha = linha.textContent.toLowerCase();
        if (conteudoLinha.includes(termo)) {
            linha.style.display = "";
        } else {
            linha.style.display = "none";
        }
    });
document.addEventListener('click', (event) => {
    if (event.target.textContent === 'Excluir') {
        const confirmar = confirm("Tem certeza que deseja remover esta peça do estoque?");
        if (confirmar) {
            event.target.closest('tr').remove();
        }
    }
});
});