const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');

        // Quando o mouse passar por cima do botão (hover)
        togglePasswordButton.addEventListener('mouseenter', () => {
            passwordInput.type = 'text'; // Mostra a senha
        });

        // Quando o mouse sair do botão
        togglePasswordButton.addEventListener('mouseleave', () => {
            passwordInput.type = 'password'; // Oculta a senha novamente
        });
