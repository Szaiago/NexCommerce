document.addEventListener("DOMContentLoaded", function () {
    const modalEditar = document.querySelector(".modal-editar-item");
    const botoesAbrirModal = document.querySelectorAll(".abrir-modal-editar");

    botoesAbrirModal.forEach(botao => {
        botao.addEventListener("click", function () {
            document.getElementById("id_produto").value = botao.getAttribute("data-id");
            document.getElementById("nome_produto").value = botao.getAttribute("data-nome"); // Adicionado
            document.getElementById("marca_produto").value = botao.getAttribute("data-marca");
            document.getElementById("cor_produto").value = botao.getAttribute("data-cor");
            document.getElementById("categoria_produto").value = botao.getAttribute("data-categoria");
            document.getElementById("peso_produto").value = botao.getAttribute("data-peso");
            document.getElementById("material_produto").value = botao.getAttribute("data-material");
            document.getElementById("valor_produto").value = botao.getAttribute("data-valor");
            document.getElementById("descricao_produto").value = botao.getAttribute("data-descricao");

            modalEditar.style.display = "block";
        });
    });

    // Função para fechar o modal
    window.fecharModal = function () {
        modalEditar.style.display = "none";
    };
});
