// Seleciona os elementos
const perfilIcon = document.getElementById("perfil");
const modal = document.getElementById("modal-perfil");

let hideTimeout; // Variável para armazenar o timeout

// Exibe o modal com animação de entrada
perfilIcon.addEventListener("mouseenter", () => {
    clearTimeout(hideTimeout); // Cancela o fechamento do modal, caso esteja agendado
    modal.classList.add("show"); // Adiciona a classe para mostrar o modal
    modal.classList.remove("closing"); // Remove a classe de fechamento, se presente
});

// Oculta o modal com animação de saída após 3 segundos quando o mouse sai do ícone
perfilIcon.addEventListener("mouseleave", () => {
    hideTimeout = setTimeout(() => {
        modal.classList.add("closing"); // Adiciona a classe de fechamento
        setTimeout(() => {
            modal.classList.remove("show", "closing"); // Remove classes após animação
        }, 500); // Tempo da animação de fechamento (0.5s)
    }, 3000); // Tempo de espera (3 segundos)
});

// Cancela o fechamento do modal se o mouse entrar no modal
modal.addEventListener("mouseenter", () => {
    clearTimeout(hideTimeout); // Cancela o fechamento agendado
});

// Oculta o modal com animação de saída após 0.5 segundos quando o mouse sai do modal
modal.addEventListener("mouseleave", () => {
    hideTimeout = setTimeout(() => {
        modal.classList.add("closing"); // Adiciona a classe de fechamento
        setTimeout(() => {
            modal.classList.remove("show", "closing"); // Remove classes após animação
        }, 500); // Tempo da animação de fechamento (0.5s)
    }, 500); // Tempo de espera reduzido para o modal (0.5 segundos)
});
