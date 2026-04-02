// Declarando variáveis Exercicio 1
const nomeProduto = "Cadeira Gamer"; // String
let qtdEstoque = { produto: "Cadeira Gamer", quantidade: 20 };
const Produtoativo = "true";
let nome = ""; //string
//Exercicio 2

function saudarCliente(nome) {
    const mensagem = (`Olá, ${ nome }!Bem - vindo à nossa loja.`);
    console.log(mensagem);
}

//Exercicio 3 
function formatarMoedaBRL(valor) {
    return "R$" + valor.toFixed(2).replace(".", ",");
}

// Desconto para Funcionario 4
function calcularDesconto(precoOriginal, isFuncionario) {

    if (isFuncionario) { return precoOriginal * 0.7; } else { return precoOriginal }
}

// Estrutura de Produto5
const produto = {

    id: "920",
    nome: "Inserir Texto",
    preco: "300",
    categoria: "Sem"

}


// Validar Senha 6
function validarSenha(senha) {

    if (senha.length >= 8, senha !== "12345678", senha !== "senha") { return True } else { return False }
}

//7
function fecharCarrinho(valorProduto, quantidade, valorFrete) {
    let subtotal = valorProduto * quantidade;
    if (subtotal > 200) {
        valorFrete = 0;
    }
    const valorTotalFinal = subtotal + valorFrete;
    return valorTotalFinal;
}
console.log(fecharCarrinho(50, 2, 20));
console.log(fecharCarrinho(150, 2, 20));

//8
function validarTamanhoCPF(cpf) {

    const cpfLimpo = cpf.trim();
    const temOnzeCaracteres = cpfLimpo.length === 11;

    const ehNumerico = !isNaN(cpfLimpo);

    return temOnzeCaracteres && ehNumerico;
}

console.log(validarTamanhoCPF(" 12345678901 "));
console.log(validarTamanhoCPF("12345"));
console.log(validarTamanhoCPF("1234567890a"));

//9
function validarCampoVazio(valor) {

    if (valor === null || valor === undefined || valor === "") {
        return false;
    }
    return true;
}
console.log(validarCampoVazio("João"));
console.log(validarCampoVazio(""));
console.log(validarCampoVazio(null));
console.log(validarCampoVazio(undefined));

//10 Função principal solicitada
function gerarResumo(nomeCliente, totalCompra) {
    const valorFormatado = formatarMoedaBRL(totalCompra);
    return `Cliente: $ { nomeCliente }, Total a Pagar: $ { valorFormatado }`;
}
const resumo = gerarResumo("Ana Silva", 1550.50);
console.log(resumo);