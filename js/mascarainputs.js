document.getElementById('valor_produto').addEventListener('input', function(event) {
    let value = event.target.value;

    // Remove tudo o que não for número ou vírgula
    value = value.replace(/\D/g, '');

    // Adiciona o ponto para os centavos
    value = value.replace(/(\d)(\d{2})$/, '$1,$2');

    // Adiciona o separador de milhar
    value = value.replace(/(?=(\d{3})+(\.))/g, '$1.');

    // Se o valor não começar com "R$", adiciona a moeda no início
    if (value) {
        value = 'R$ ' + value;
    }

    // Atualiza o valor do input com a máscara
    event.target.value = value;
});
document.getElementById('peso_produto').addEventListener('input', function(event) {
    let value = event.target.value;

    // Remove tudo o que não for número ou ponto
    value = value.replace(/[^0-9,\.]/g, '');

    // Substitui vírgula por ponto (para manter a consistência)
    value = value.replace(',', '.');

    // Adiciona o ponto como separador de casas decimais (se houver mais de 2 casas)
    if (value.includes('.')) {
        let parts = value.split('.');
        parts[1] = parts[1].substring(0, 2);  // Limita a 2 casas decimais
        value = parts.join('.');
    }

    // Atualiza o valor do input com a máscara
    event.target.value = value;
});