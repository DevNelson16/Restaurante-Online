/* ============================================================
   SCRIPTS.JS – SISTEMA AJAX PARA RESTAURANTE
   Uso: api/estoque.php, api/pedidos.php, api/cardapio.php
   Retorno esperado: JSON
   ============================================================ */

async function ajax(url, method = "POST", data = {}) {
    const response = await fetch(url, {
        method: method,
        body: method === "POST" ? new URLSearchParams(data) : null,
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        }
    });
    // tentar parse seguro
    try {
        return await response.json();
    } catch (e) {
        return { success: false, message: "Resposta inválida do servidor" };
    }
}

/* =========================
   ATUALIZAR ESTOQUE (ADMIN)
   ========================= */
function updateStock(itemId, newQty) {
    // garantir inteiro/numero
    newQty = parseInt(newQty, 10);
    if (isNaN(newQty) || newQty < 0) {
        alert("Quantidade inválida.");
        return;
    }

    ajax("api/estoque.php", "POST", {
        action: "update",
        id: itemId,
        quantidade: newQty
    })
    .then(res => {
        if (res.success) {
            alert("Estoque atualizado!");
            const el = document.querySelector(`#estoque-${itemId}`);
            if (el) el.textContent = newQty;
        } else {
            alert("Erro: " + (res.message || "Erro desconhecido"));
        }
    })
    .catch(() => alert("Erro de rede ao atualizar estoque."));
}

/* =========================
   LISTAR ESTOQUE (ADMIN)
   ========================= */
function carregarEstoque() {
    ajax("api/estoque.php", "POST", { action: "list" })
    .then(res => {
        if (!res.success) {
            console.warn("Erro ao carregar estoque:", res.message);
            return;
        }
        const tabela = document.querySelector("#tabelaEstoque");
        if (!tabela) return;

        tabela.innerHTML = "";

        res.data.forEach(item => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${escapeHtml(item.nome)}</td>
                <td id="estoque-${item.id}">${item.quantidade}</td>
                <td>
                    <input type="number" id="input-${item.id}" min="0" value="${item.quantidade}" style="width:80px">
                    <button data-id="${item.id}" class="btn-salvar-estoque">Salvar</button>
                </td>
            `;
            tabela.appendChild(row);
        });

        // adicionar eventos aos botões
        document.querySelectorAll(".btn-salvar-estoque").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.getAttribute("data-id");
                const value = document.querySelector(`#input-${id}`).value;
                updateStock(id, value);
            });
        });
    })
    .catch(() => console.error("Erro de rede ao listar estoque"));
}

/* =========================
   FAZER PEDIDO (PÚBLICO)
   ========================= */
function fazerPedido(produtoId, quantidade = 1) {
    quantidade = parseInt(quantidade, 10);
    if (isNaN(quantidade) || quantidade <= 0) {
        alert("Quantidade inválida.");
        return;
    }

    ajax("api/pedidos.php", "POST", {
        action: "new",
        produto_id: produtoId,
        quantidade: quantidade
    })
    .then(res => {
        if (res.success) {
            alert("Pedido realizado com sucesso!");
            // se existir função para atualizar cardápio, chama
            if (typeof carregarCardapio === "function") carregarCardapio();
        } else {
            alert("Erro ao realizar pedido: " + (res.message || "Erro desconhecido"));
        }
    })
    .catch(() => alert("Erro de rede ao enviar pedido."));
}

/* =========================
   LISTAR PEDIDOS (ADMIN)
   ========================= */
function listarPedidos() {
    ajax("api/pedidos.php", "POST", { action: "list" })
    .then(res => {
        if (!res.success) {
            console.warn("Erro listar pedidos:", res.message);
            return;
        }

        const container = document.querySelector("#listaPedidos");
        if (!container) return;

        container.innerHTML = "";

        res.data.forEach(pedido => {
            // o backend pode retornar 'itens' (HTML) ou um array; tratar ambos
            const itensHtml = pedido.itens ?? (pedido.itens_html ?? "");
            const status = escapeHtml(pedido.status ?? pedido.estado ?? "pendente");
            const total = pedido.total ? Number(pedido.total).toFixed(2) : "0.00";
            const cliente = escapeHtml(pedido.cliente ?? (pedido.cliente_nome ?? "Anónimo"));

            const card = document.createElement("div");
            card.className = "pedido";
            card.innerHTML = `
                <h3>Pedido #${pedido.id}</h3>
                <p><strong>Cliente:</strong> ${cliente}</p>
                <p><strong>Itens:</strong><br>${itensHtml}</p>
                <p><strong>Total:</strong> € ${total}</p>
                <p><strong>Status:</strong> ${status}</p>
                <div class="acoes-pedido">
                    <button class="btn-status" data-id="${pedido.id}" data-status="pendente">Pendente</button>
                    <button class="btn-status" data-id="${pedido.id}" data-status="preparando">Preparando</button>
                    <button class="btn-status" data-id="${pedido.id}" data-status="a caminho">A Caminho</button>
                    <button class="btn-status" data-id="${pedido.id}" data-status="entregue">Entregue</button>
                    <button class="btn-cancel" data-id="${pedido.id}">Cancelar</button>
                </div>
            `;
            container.appendChild(card);
        });

        // eventos para mudar status
        document.querySelectorAll(".btn-status").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.getAttribute("data-id");
                const status = btn.getAttribute("data-status");
                mudarStatus(id, status);
            });
        });

        // eventos para cancelar
        document.querySelectorAll(".btn-cancel").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.getAttribute("data-id");
                cancelarPedido(id);
            });
        });
    })
    .catch(() => console.error("Erro de rede ao listar pedidos"));
}

/* =========================
   MUDAR STATUS (ADMIN)
   ========================= */
function mudarStatus(id, novoStatus) {
    if (!confirm(`Alterar status do pedido #${id} para "${novoStatus}"?`)) return;

    ajax("api/pedidos.php", "POST", {
        action: "update_status",
        id: id,
        status: novoStatus
    })
    .then(res => {
        if (res.success) {
            alert("Status atualizado!");
            listarPedidos();
        } else {
            alert("Erro: " + (res.message || "Erro desconhecido"));
        }
    })
    .catch(() => alert("Erro de rede ao atualizar status."));
}

/* =========================
   CANCELAR PEDIDO
   ========================= */
function cancelarPedido(id) {
    if (!confirm("Deseja cancelar este pedido?")) return;

    ajax("api/pedidos.php", "POST", {
        action: "cancel",
        id: id
    })
    .then(res => {
        if (res.success) {
            alert("Pedido cancelado!");
            listarPedidos();
        } else {
            alert("Erro: " + (res.message || "Erro desconhecido"));
        }
    })
    .catch(() => alert("Erro de rede ao cancelar pedido."));
}

/* =========================
   CARREGAR CARDÁPIO (PÚBLICO)
   ========================= */
function carregarCardapio() {
    ajax("api/cardapio.php", "POST", { action: "list" })
    .then(res => {
        if (!res.success) return;
        const cardapio = document.querySelector("#cardapio");
        if (!cardapio) return;

        cardapio.innerHTML = "";

        res.data.forEach(item => {
            const el = document.createElement("div");
            el.className = "produto";
            el.innerHTML = `
                <img src="${escapeHtml(item.foto ?? 'assets/img/default.png')}" alt="${escapeHtml(item.nome)}" onerror="this.src='assets/img/default.png'">
                <h3>${escapeHtml(item.nome)}</h3>
                <p>${escapeHtml(item.descricao ?? '')}</p>
                <strong>€${Number(item.preco).toFixed(2)}</strong>
                <div>
                    <input type="number" min="1" value="1" class="qtd-prod-${item.id}" style="width:60px">
                    <button class="btn-pedir" data-id="${item.id}">Pedir Agora</button>
                </div>
            `;
            cardapio.appendChild(el);
        });

        // eventos para pedir
        document.querySelectorAll(".btn-pedir").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.getAttribute("data-id");
                const qtd = document.querySelector(`.qtd-prod-${id}`).value || 1;
                fazerPedido(id, qtd);
            });
        });
    })
    .catch(() => console.error("Erro de rede ao carregar cardápio"));
}

/* =========================
   INICIALIZAÇÃO
   ========================= */
document.addEventListener("DOMContentLoaded", () => {
    if (document.querySelector("#tabelaEstoque")) carregarEstoque();
    if (document.querySelector("#listaPedidos")) listarPedidos();
    if (document.querySelector("#cardapio")) carregarCardapio();
});

/* =========================
   UTILS
   ========================= */
function escapeHtml(text) {
    if (text === null || text === undefined) return "";
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
