$(document).ready(function() {
    // Aumentar quantidade
    $(".aumentar").click(function() {
        let idCarrinho = $(this).data("id");
        let quantidadeSpan = $(this).siblings(".quantidade");
        
        $.post("../functions/atualizar_carrinho.php", { id: idCarrinho, operacao: "aumentar" }, function(response) {
            if (response.success) {
                quantidadeSpan.text(response.nova_quantidade);
            }
        }, "json");
    });

    // Diminuir quantidade
    $(".diminuir").click(function() {
        let idCarrinho = $(this).data("id");
        let quantidadeSpan = $(this).siblings(".quantidade");
        
        $.post("../functions/atualizar_carrinho.php", { id: idCarrinho, operacao: "diminuir" }, function(response) {
            if (response.success) {
                quantidadeSpan.text(response.nova_quantidade);
            }
        }, "json");
    });

    // Remover item do carrinho
    $(".remover-item").click(function() {
        let idCarrinho = $(this).data("id");
        let itemCarrinho = $(this).closest(".item-carrinho");

        $.post("../functions/remover_item.php", { id: idCarrinho }, function(response) {
            if (response.success) {
                itemCarrinho.remove();
            }
        }, "json");
    });
});
