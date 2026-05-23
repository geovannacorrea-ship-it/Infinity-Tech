
document.addEventListener('DOMContentLoaded', () => {
    // --- Configurações Iniciais e Estado ---
    const STORAGE_KEY = 'estoque_refrigeracao';
    const ITEMS_PER_PAGE = 10;
    const CRITICAL_THRESHOLD = 5; // Itens com essa quantidade ou menos são "críticos"

    let inventory = JSON.parse(localStorage.getItem(STORAGE_KEY)) || getMockData();
    let currentPage = 1;
    let currentFilter = 'all'; // all, critical, normal
    let editingId = null;

    // --- Elementos do DOM ---
    const tbody = document.getElementById('estoqueTbody');
    const modal = document.getElementById('itemModal');
    const form = document.getElementById('itemForm');
    
    // Botões
    const btnNovo = document.getElementById('novoItemBtn');
    const btnSave = document.getElementById('saveItem');
    const btnCancel = document.getElementById('cancelModal');
    const btnCloseModal = document.getElementById('closeModal');
    const btnExport = document.getElementById('exportBtn');
    
    // Busca e Filtros
    const searchInput = document.getElementById('searchInput');
    const btnClearSearch = document.getElementById('clearSearch');
    const filterChips = document.querySelectorAll('.filter-chip');
    
    // Paginação
    const btnPrev = document.getElementById('prevPage');
    const btnNext = document.getElementById('nextPage');
    const spanCurrentPage = document.getElementById('currentPage');
    const spanShowingCount = document.getElementById('showingCount');
    const spanTotalCount = document.getElementById('totalCount');

    // --- Inicialização ---
    init();

    function init() {
        setupEventListeners();
        setupPriceMask();
        renderTable();
    }

    // --- Event Listeners ---
    function setupEventListeners() {
        // Modal
        btnNovo.addEventListener('click', () => openModal());
        btnCloseModal.addEventListener('click', closeModal);
        btnCancel.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
        btnSave.addEventListener('click', handleSaveItem);

        // Busca
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
        });
        btnClearSearch.addEventListener('click', () => {
            searchInput.value = '';
            currentPage = 1;
            renderTable();
        });

        // Filtros (Chips)
        filterChips.forEach(chip => {
            chip.addEventListener('click', (e) => {
                filterChips.forEach(c => c.classList.remove('active'));
                e.target.classList.add('active');
                currentFilter = e.target.dataset.filter;
                currentPage = 1;
                renderTable();
            });
        });

        // Paginação
        btnPrev.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
        btnNext.addEventListener('click', () => {
            const maxPage = Math.ceil(getFilteredData().length / ITEMS_PER_PAGE);
            if (currentPage < maxPage) {
                currentPage++;
                renderTable();
            }
        });

        // Delegação de eventos para botões da tabela (Editar/Excluir)
        tbody.addEventListener('click', (e) => {
            const btnEdit = e.target.closest('.btn-edit');
            const btnDelete = e.target.closest('.btn-delete');
            
            if (btnEdit) {
                const id = btnEdit.dataset.id;
                openModal(id);
            } else if (btnDelete) {
                const id = btnDelete.dataset.id;
                deleteItem(id);
            }
        });

        // Exportar
        btnExport.addEventListener('click', exportToCSV);
    }

    // --- Lógica Principal de Renderização ---
    function renderTable() {
        let filteredData = getFilteredData();
        updateStats(filteredData);

        // Lógica de Paginação
        const totalItems = filteredData.length;
        const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;
        
        if (currentPage > totalPages) currentPage = totalPages;
        
        const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
        const endIndex = startIndex + ITEMS_PER_PAGE;
        const paginatedData = filteredData.slice(startIndex, endIndex);

        // Renderizar Linhas
        tbody.innerHTML = '';
        if (paginatedData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 2rem;">Nenhum item encontrado.</td></tr>`;
        } else {
            paginatedData.forEach(item => {
                const isCritical = item.quantidade <= CRITICAL_THRESHOLD;
                const tr = document.createElement('tr');
                if(isCritical) tr.classList.add('critical-row'); // Caso queira estilizar a linha no CSS
                
                tr.innerHTML = `
                    <td><strong>${item.codigo}</strong></td>
                    <td>${item.descricao}</td>
                    <td>${item.marca || '-'}</td>
                    <td>${item.especificacao || '-'}</td>
                    <td>
                        <span class="status-badge ${isCritical ? 'critical' : 'normal'}">
                            ${item.quantidade} un.
                        </span>
                    </td>
                    <td>${item.preco || 'R$ 0,00'}</td>
                    <td>${item.localizacao || '-'}</td>
                    <td class="actions-col">
                        <button class="btn-icon btn-edit" data-id="${item.id}" aria-label="Editar" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon btn-delete" data-id="${item.id}" aria-label="Excluir" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Atualizar Controles de Paginação
        spanCurrentPage.textContent = currentPage;
        spanTotalCount.textContent = totalItems;
        spanShowingCount.textContent = totalItems === 0 ? 0 : `${startIndex + 1}-${Math.min(endIndex, totalItems)}`;
        
        btnPrev.disabled = currentPage === 1;
        btnNext.disabled = currentPage === totalPages;
    }

    function getFilteredData() {
        const query = searchInput.value.toLowerCase().trim();
        
        return inventory.filter(item => {
            // Filtro de Busca Texto
            const matchesSearch = 
                item.codigo.toLowerCase().includes(query) ||
                item.descricao.toLowerCase().includes(query) ||
                (item.marca && item.marca.toLowerCase().includes(query)) ||
                (item.especificacao && item.especificacao.toLowerCase().includes(query));

            // Filtro de Chips (Status)
            let matchesStatus = true;
            if (currentFilter === 'critical') {
                matchesStatus = item.quantidade <= CRITICAL_THRESHOLD;
            } else if (currentFilter === 'normal') {
                matchesStatus = item.quantidade > CRITICAL_THRESHOLD;
            }

            return matchesSearch && matchesStatus;
        });
    }

    function updateStats(dataToCalculate) {
        // O valor total deve sempre ser calculado com base em todo o estoque, não só no filtro ativo
        // Mas para manter as estatísticas dinâmicas baseadas na visualização atual (opcional):
        const totalItems = inventory.length; 
        const criticalItems = inventory.filter(i => i.quantidade <= CRITICAL_THRESHOLD).length;
        
        let totalValue = inventory.reduce((acc, item) => {
            const priceNum = parseCurrencyToNumber(item.preco);
            return acc + (priceNum * parseInt(item.quantidade));
        }, 0);

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('criticalItems').textContent = criticalItems;
        document.getElementById('totalValue').textContent = formatNumberToCurrency(totalValue);

        // Alerta visual no painel de crítico
        const critCard = document.getElementById('criticalItems');
        if(criticalItems > 0) {
            critCard.classList.add('warning');
            critCard.style.color = '#ef4444'; // Vermelho
        } else {
            critCard.classList.remove('warning');
            critCard.style.color = 'inherit';
        }
    }

    // --- Operações CRUD ---
    function openModal(id = null) {
        editingId = id;
        form.reset();
        document.getElementById('modalTitle').textContent = id ? 'Editar Item' : 'Novo Item';

        if (id) {
            const item = inventory.find(i => i.id === id);
            if (item) {
                document.getElementById('codigo').value = item.codigo;
                document.getElementById('descricao').value = item.descricao;
                document.getElementById('marca').value = item.marca;
                document.getElementById('especificacao').value = item.especificacao;
                document.getElementById('quantidade').value = item.quantidade;
                document.getElementById('preco').value = item.preco;
                document.getElementById('localizacao').value = item.localizacao;
                document.getElementById('categoria').value = item.categoria;
                document.getElementById('observacao').value = item.observacao;
            }
        } else {
            document.getElementById('preco').value = 'R$ 0,00';
            document.getElementById('quantidade').value = 0;
        }

        modal.style.display = 'flex';
        modal.classList.add('show');
    }

    function closeModal() {
        modal.style.display = 'none';
        modal.classList.remove('show');
        editingId = null;
        form.reset();
    }

    function handleSaveItem(e) {
        e.preventDefault();
        
        // Validação Simples
        const codigo = document.getElementById('codigo').value.trim();
        const descricao = document.getElementById('descricao').value.trim();
        const quantidade = parseInt(document.getElementById('quantidade').value, 10);

        if (!codigo || !descricao || isNaN(quantidade) || quantidade < 0) {
            showToast('Preencha os campos obrigatórios corretamente!', 'error');
            return;
        }

        const newItem = {
            id: editingId || generateId(),
            codigo,
            descricao,
            marca: document.getElementById('marca').value.trim(),
            especificacao: document.getElementById('especificacao').value.trim(),
            quantidade,
            preco: document.getElementById('preco').value.trim(),
            localizacao: document.getElementById('localizacao').value.trim(),
            categoria: document.getElementById('categoria').value,
            observacao: document.getElementById('observacao').value.trim()
        };

        if (editingId) {
            const index = inventory.findIndex(i => i.id === editingId);
            if (index !== -1) inventory[index] = newItem;
            showToast('Item atualizado com sucesso!', 'success');
        } else {
            // Checar se código já existe
            if(inventory.some(i => i.codigo.toLowerCase() === codigo.toLowerCase())) {
                showToast('Já existe um item com este Código SKA!', 'error');
                return;
            }
            inventory.push(newItem);
            showToast('Novo item adicionado com sucesso!', 'success');
        }

        saveToLocalStorage();
        closeModal();
        renderTable();
    }

    function deleteItem(id) {
        if (confirm('Tem certeza que deseja excluir este item do estoque?')) {
            inventory = inventory.filter(i => i.id !== id);
            saveToLocalStorage();
            
            // Ajustar página se excluir o último item da página atual
            if (getFilteredData().length % ITEMS_PER_PAGE === 0 && currentPage > 1) {
                currentPage--;
            }
            
            renderTable();
            showToast('Item excluído com sucesso!', 'success');
        }
    }

    // --- Utilitários ---
    function saveToLocalStorage() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(inventory));
    }

    function generateId() {
        return '_' + Math.random().toString(36).substr(2, 9);
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast show ${type}`; // classes css: .toast.success ou .toast.error
        
        // Estilização injetada dinamicamente caso o CSS do toast não contemple as cores
        toast.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
        toast.style.color = 'white';
        toast.style.padding = '1rem 2rem';
        toast.style.borderRadius = '0.5rem';
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';

        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
            toast.style.display = 'none';
        }, 3000);
        toast.style.display = 'block';
    }

    // --- Máscaras e Formatação Monetária ---
    function setupPriceMask() {
        const precoInput = document.getElementById('preco');
        precoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
            if(value === '') {
                e.target.value = 'R$ 0,00';
                return;
            }
            value = (parseInt(value, 10) / 100).toFixed(2); // Divide por 100 para ter os centavos
            e.target.value = formatNumberToCurrency(parseFloat(value));
        });
        
        // Selecionar texto ao focar
        precoInput.addEventListener('focus', function(e) {
            setTimeout(() => e.target.select(), 50);
        });
    }

    function parseCurrencyToNumber(currencyStr) {
        if (!currencyStr) return 0;
        let numericStr = currencyStr.replace(/[R$\s\.]/g, '').replace(',', '.');
        return parseFloat(numericStr) || 0;
    }

    function formatNumberToCurrency(number) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(number);
    }

    // --- Exportação ---
    function exportToCSV() {
        if (inventory.length === 0) {
            showToast('Não há dados para exportar.', 'error');
            return;
        }

        const headers = ['Código SKA', 'Descrição', 'Marca', 'Especificação', 'Quantidade', 'Preço Unitário', 'Localização', 'Categoria'];
        const rows = inventory.map(item => [
            item.codigo,
            `"${item.descricao}"`, // Escapado caso haja vírgulas
            `"${item.marca}"`,
            `"${item.especificacao}"`,
            item.quantidade,
            `"${item.preco}"`,
            `"${item.localizacao}"`,
            item.categoria
        ]);

        const csvContent = "data:text/csv;charset=utf-8," 
            + headers.join(';') + '\n' 
            + rows.map(e => e.join(';')).join('\n');

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `estoque_refrigeracao_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // --- Mock Data Inicial ---
    function getMockData() {
        return [
            { id: '1a2b3c4d', codigo: 'COMP-101', descricao: 'Compressor Inverter 12k', marca: 'LG', especificacao: '12.000 BTU R410a', quantidade: 8, preco: 'R$ 850,00', localizacao: 'Prateleira A1', categoria: 'Compressor', observacao: '' },
            { id: '5e6f7g8h', codigo: 'PL-INV-01', descricao: 'Placa Condensadora', marca: 'Samsung', especificacao: '9.000 a 12.000 BTU', quantidade: 3, preco: 'R$ 620,00', localizacao: 'Armário B2', categoria: 'Eletrônica', observacao: 'Verificar lote' },
            { id: '9i0j1k2l', codigo: 'GAS-410A', descricao: 'Gás Refrigerante R410a', marca: 'Freon', especificacao: 'Cilindro 11.3kg', quantidade: 15, preco: 'R$ 410,50', localizacao: 'Chão Corredor 1', categoria: 'Gás', observacao: '' },
            { id: '3m4n5o6p', codigo: 'MT-VEN-05', descricao: 'Motor Ventilador Evap.', marca: 'Midea', especificacao: 'Eixo curto 18k', quantidade: 1, preco: 'R$ 180,00', localizacao: 'Prateleira C4', categoria: 'Motor', observacao: 'Fazer pedido urgente' }
        ];
    }
});
