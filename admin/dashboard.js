/* dashboard.js — cria gráficos com Chart.js
   Espera um objecto window.DASH_DATA = { pedidos: [...], vendas: [...], produtos: [...] }
*/

(function() {
    if (!window.DASH_DATA) {
        console.warn("DASH_DATA não encontrado. Insira no dashboard.php: window.DASH_DATA = { pedidos: ..., vendas: ..., produtos: ... }");
        return;
    }

    const pedidos = window.DASH_DATA.pedidos || [];
    const vendas = window.DASH_DATA.vendas || [];
    const produtos = window.DASH_DATA.produtos || [];

    function labelFromMes(m) {
        const meses = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
        return meses[(m - 1 + 12) % 12] || m;
    }

    function getValue(item, keys) {
        for (let k of keys) if (item[k] !== undefined) return item[k];
        return 0;
    }

    function mapMeses(data, valueKeys) {
        return data.map(item => ({
            mes: labelFromMes(Number(getValue(item, ['mes','month','m']))),
            valor: Number(getValue(item, valueKeys)) || 0
        }));
    }

    // Processar pedidos e vendas para labels e valores
    const pedidosMap = mapMeses(pedidos, ['total','pedidos','count']);
    const vendasMap  = mapMeses(vendas,  ['total','vendas','sum']);

    // Preparar arrays para Chart.js (preservar ordem original)
    const pedidosLabels = pedidosMap.map(p => p.mes);
    const pedidosValues = pedidosMap.map(p => p.valor);

    const vendasLabels = vendasMap.map(v => v.mes);
    const vendasValues = vendasMap.map(v => v.valor);

    // Top produtos (espera campo 'nome' e 'total' ou 'total_vendido')
    const produtoLabels = produtos.map(p => p.nome || p.label || "Item");
    const produtoValues = produtos.map(p => Number(p.total || p.total_vendido || p.valor || 0));

    // criar gráfico só se o elemento existir
    if (document.getElementById("graficoPedidos")) {
        new Chart(document.getElementById("graficoPedidos"), {
            type: "line",
            data: {
                labels: pedidosLabels,
                datasets: [{
                    label: "Pedidos",
                    data: pedidosValues,
                    fill: false,
                    tension: 0.3,
                    borderWidth: 2
                }]
            }
        });
    }

    if (document.getElementById("graficoVendas")) {
        new Chart(document.getElementById("graficoVendas"), {
            type: "bar",
            data: {
                labels: vendasLabels,
                datasets: [{
                    label: "Vendas (€)",
                    data: vendasValues,
                    borderWidth: 1
                }]
            }
        });
    }

    if (document.getElementById("graficoProdutos")) {
        new Chart(document.getElementById("graficoProdutos"), {
            type: "doughnut",
            data: {
                labels: produtoLabels,
                datasets: [{
                    label: "Mais vendidos",
                    data: produtoValues
                }]
            }
        });
    }
})();
