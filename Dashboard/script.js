const dados = {
  orcamentos: 200,
  vendas: 60,
  receita: 18000
};

function calcularConversao(vendas, orcamentos) {
  if (orcamentos === 0) return 0;
  return (vendas / orcamentos) * 100;
}

// preenchendo os cards
document.getElementById("orcamentos").innerText = dados.orcamentos;
document.getElementById("vendas").innerText = dados.vendas;
document.getElementById("receita").innerText = `R$ ${dados.receita}`;

const conversao = calcularConversao(dados.vendas, dados.orcamentos);
document.getElementById("conversao").innerText = `${conversao.toFixed(1)}%`;

const ctx = document.getElementById('graficoVendas');

new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Fev', 'Mar', 'Abr'],
    datasets: [
      {
      label: 'Vendas',
      data: [21, 45, 60, 80],
      borderColor: 'blue',
      fill: false,
      tension: 0.4,
      pointRadius: 3
      
      }
      ,
      {
      label: 'Orçamentos',
      data: [70, 84, 90, 100],
      borderColor: 'red',
      fill: false,
      tension: 0.4,
      pointRadius: 3
      
    }

    ]
    
  },
  options: {
  scales: {
    y: {
      beginAtZero: true,
      grace: '10%' 
    }
  }
}
  
});