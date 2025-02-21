-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21-Fev-2025 às 02:27
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `nextcommerce`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `id_carrinho` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `img_produto` varchar(255) NOT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `carrinho`
--

INSERT INTO `carrinho` (`id_carrinho`, `id_usuario`, `id_produto`, `quantidade`, `preco`, `img_produto`, `data_adicao`) VALUES
(5, 3, 6, 1, 999.99, '../images/img1_1738634990.png', '2025-02-18 00:11:14');

-- --------------------------------------------------------

--
-- Estrutura da tabela `dados_adicionais`
--

CREATE TABLE `dados_adicionais` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `estado` varchar(2) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `complemento` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `dados_adicionais`
--

INSERT INTO `dados_adicionais` (`id`, `id_usuario`, `cpf`, `cep`, `rua`, `estado`, `cidade`, `bairro`, `complemento`) VALUES
(1, 1, '096.473.229-76', '88310-693', 'Rua Jaime Fernandes Vieira', 'SC', 'Itajaí', 'Cordeiros', '205 Bloco D');

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id_item` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `itens_pedido` text NOT NULL,
  `valor_pedido` decimal(10,2) NOT NULL,
  `status_pedido` varchar(255) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `cep` varchar(20) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `bairro` varchar(255) NOT NULL,
  `rua` varchar(255) NOT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `data_pedido` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `itens_pedido`, `valor_pedido`, `status_pedido`, `cpf`, `cep`, `cidade`, `bairro`, `rua`, `complemento`, `data_pedido`) VALUES
(76, 1, 'NIKE DUNK LOW REDWOOD (2)', 2009.98, 'Aguardando Pagamento', '096.473.229-76', '88310-693', 'Itajaí', 'Cordeiros', 'Rua Jaime Fernandes Vieira', '205 Bloco D', '2025-02-20 21:35:34'),
(77, 1, 'NIKE DUNK LOW PHOTON DUST (1), NIKE Dunk Low SE Monsoon Blue (1)', 1809.98, 'Aguardando Pagamento', '096.473.229-76', '88310-693', 'Itajaí', 'Cordeiros', 'Rua Jaime Fernandes Vieira', '205 Bloco D', '2025-02-20 22:22:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `nome_produto` varchar(255) NOT NULL,
  `marca_produto` varchar(255) NOT NULL,
  `sku_produto` varchar(255) NOT NULL,
  `cor_produto` varchar(50) DEFAULT NULL,
  `valor_produto` decimal(10,2) NOT NULL,
  `categoria_produto` varchar(255) DEFAULT NULL,
  `peso_produto` decimal(10,2) DEFAULT NULL,
  `quantidade_produto` int(11) DEFAULT 0,
  `descricao_produto` text DEFAULT NULL,
  `material_produto` varchar(255) DEFAULT NULL,
  `img1_produto` varchar(255) DEFAULT NULL,
  `img2_produto` varchar(255) DEFAULT NULL,
  `img3_produto` varchar(255) DEFAULT NULL,
  `img4_produto` varchar(255) DEFAULT NULL,
  `img5_produto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avaliacao_produto` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `nome_produto`, `marca_produto`, `sku_produto`, `cor_produto`, `valor_produto`, `categoria_produto`, `peso_produto`, `quantidade_produto`, `descricao_produto`, `material_produto`, `img1_produto`, `img2_produto`, `img3_produto`, `img4_produto`, `img5_produto`, `created_at`, `avaliacao_produto`) VALUES
(3, 'NIKE DUNK LOW PHOTON DUST', 'NIKE', 'SKU_67A172AAB4623', 'cinza', 799.99, 'roupas', 750.00, 10, 'Tênis Dunk Low Photon Dust\r\n\r\nEnvolva-se no estilo e conforto com o Tênis Dunk Low Photon Dust. Este modelo clássico da Nike combina o icônico design do Dunk com um toque moderno na cor \"Photon Dust\". Feito com materiais premium, o cabedal em couro garante durabilidade e um visual sofisticado. A entressola acolchoada proporciona conforto durante o dia todo, seja para um passeio casual ou para destacar-se no seu look diário.\r\n\r\nDesign Elegante: Cores suaves em \"Photon Dust\" que elevam qualquer visual.\r\n\r\nConforto Excepcional: Entressola acolchoada para suporte e conforto.\r\n\r\nDurabilidade: Cabedal em couro premium para maior resistência.\r\n\r\nVersatilidade: Perfeito para uso casual ou para completar um look mais sofisticado.\r\n\r\nAdicione um toque de elegância e modernidade ao seu guarda-roupa com o Tênis Dunk Low Photon Dust, a escolha ideal para quem busca estilo e conforto em um só produto.', 'COURO', '../images/img1_1738633898.png', '../images/img2_1738633898.png', '../images/img3_1738633898.png', '../images/img4_1738633898.png', '../images/img5_1738633898.png', '2025-02-04 01:51:38', NULL),
(5, 'NIKE Dunk Low SE Monsoon Blue', 'NIKE', 'SKU_67A174C246F71', 'azul', 999.99, 'roupas', 750.00, 10, 'Dunk Low SE Monsoon Blue\r\n\r\nEleve seu estilo com o deslumbrante Dunk Low SE Monsoon Blue. Este modelo especial da Nike combina o design clássico do Dunk com um toque moderno e vibrante na cor \"Monsoon Blue\". Feito com materiais de alta qualidade, o cabedal em couro proporciona durabilidade e um visual sofisticado. A entressola acolchoada garante conforto durante todo o dia, enquanto as cores vibrantes adicionam um toque de ousadia ao seu look.\r\n\r\nDesign Moderno: Cores vibrantes em \"Monsoon Blue\" para um visual deslumbrante.\r\n\r\nConforto Superior: Entressola acolchoada para suporte e conforto durante todo o dia.\r\n\r\nDurabilidade: Cabedal em couro premium para maior resistência.\r\n\r\nVersatilidade: Ideal para uso casual ou para destacar-se em qualquer ocasião.\r\n\r\nAdicione um toque de modernidade e ousadia ao seu guarda-roupa com o Dunk Low SE Monsoon Blue, a escolha perfeita para quem busca estilo e conforto em um só produto.', 'ALCÃNTARA', '../images/img1_1738634434.png', '../images/img2_1738634434.png', '../images/img3_1738634434.png', '../images/img4_1738634434.png', '../images/img5_1738634434.png', '2025-02-04 02:00:34', NULL),
(6, 'NIKE DUNK LOW REDWOOD', 'NIKE', 'SKU_67A176EE8469F', 'vermelho', 999.99, 'roupas', 750.00, 300, 'Dunk Low Redwood\r\n\r\nDescubra a sofisticação e o estilo rústico do Dunk Low Redwood. Este modelo da Nike combina o design clássico do Dunk com um toque natural e elegante na cor \"Redwood\". Feito com materiais de alta qualidade, o cabedal em couro proporciona durabilidade e um visual sofisticado. A entressola acolchoada oferece conforto durante todo o dia, tornando este tênis perfeito para qualquer ocasião, seja um passeio casual ou um evento especial.\r\n\r\nDesign Elegante: Cores quentes em \"Redwood\" que trazem um visual rústico e sofisticado.\r\n\r\nConforto Excepcional: Entressola acolchoada para suporte e conforto durante todo o dia.\r\n\r\nDurabilidade: Cabedal em couro premium para maior resistência.\r\n\r\nVersatilidade: Ideal para uso casual ou para completar um look mais elegante.\r\n\r\nAdicione um toque de elegância rústica ao seu guarda-roupa com o Dunk Low Redwood, a escolha perfeita para quem busca estilo e conforto em um só produto.', 'COURO', '../images/img1_1738634990.png', '../images/img2_1738634990.png', '../images/img3_1738634990.png', '../images/img4_1738634990.png', '../images/img5_1738634990.png', '2025-02-04 02:09:50', 3.70),
(9, 'ADIDAS ADI 2000', 'ADIDAS', 'SKU_67A950DC880BB', 'branco', 799.99, 'roupas', 1000.00, 100, 'Tênis Adidas ADI 2000 – Estilo Retrô e Conforto Moderno\r\n\r\nO Adidas ADI 2000 é a escolha perfeita para quem busca um visual autêntico e cheio de atitude. Inspirado nos tênis icônicos dos anos 2000, este modelo combina o design retrô com a tecnologia moderna, garantindo conforto e durabilidade para o dia a dia.\r\n\r\nCom um cabedal feito em materiais de alta qualidade, o ADI 2000 oferece um ajuste confortável e seguro. Seu solado robusto proporciona excelente aderência, enquanto a entressola macia absorve impactos, garantindo uma caminhada mais leve e estável.\r\n\r\nSeja para um look casual ou para compor um estilo streetwear autêntico, o Adidas ADI 2000 é o tênis ideal. Adquira o seu agora e traga um toque nostálgico e moderno ao seu visual!', '0', '../images/img1_1739149532.png', '../images/img2_1739149532.png', '../images/img3_1739149532.png', '../images/img4_1739149532.png', '../images/img5_1739149532.png', '2025-02-10 01:05:32', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nome_usuario` varchar(255) NOT NULL,
  `email_usuario` varchar(255) NOT NULL,
  `senha_usuario` varchar(255) NOT NULL,
  `email_empresarial_usuario` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `email_usuario`, `senha_usuario`, `email_empresarial_usuario`, `created_at`) VALUES
(1, 'Iago Spindola de Souza', 'iago@nextcommerce.com', '$2y$10$Uknp345eXmd60ndbuBxQYejFRMy6In4i.GX6eCg7HT/JXgIPAU8re', 'iago@nextcommerce.com', '2025-02-04 01:16:28'),
(2, 'Egislaine Aparecida Neto', 'egislaine@gmail.com', '$2y$10$uXESrGbRS93E7Qp5eVCUL.jqT/fQF10Bl06dBQv5PbbaBQ/Yzxt4K', NULL, '2025-02-05 01:26:44'),
(3, 'Egislaine Aparecida Neto', 'egis@nextcommerce.com', '$2y$10$zX79zO1dArVs.Zer.E6rNO.Cjrur9LH9eMAwZ.KAW1NSuj.BLWqJG', 'egis@nextcommerce.com', '2025-02-18 00:01:58');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id_carrinho`),
  ADD UNIQUE KEY `unique_usuario_produto` (`id_usuario`,`id_produto`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices para tabela `dados_adicionais`
--
ALTER TABLE `dados_adicionais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario` (`id_usuario`);

--
-- Índices para tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices para tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`),
  ADD UNIQUE KEY `sku_produto` (`sku_produto`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email_usuario` (`email_usuario`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `id_carrinho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `dados_adicionais`
--
ALTER TABLE `dados_adicionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carrinho_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `dados_adicionais`
--
ALTER TABLE `dados_adicionais`
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Limitadores para a tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
