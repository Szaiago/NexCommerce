-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04-Fev-2025 às 02:31
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
(1, 'NIKE DUNK LOW PANDA', 'NIKE', 'SKU_67A16D4F333A1', 'branco', 799.99, 'roupas', 750.00, 0, 'TÊNIS NIKE DUNK LOW PANDA!', 'COURO', '../images/img1_1738632527.jpg', '../images/img2_1738632527.png', '../images/img3_1738632527.png', '../images/img4_1738632527.png', '../images/img5_1738632527.png', '2025-02-04 01:28:47', NULL);

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
(1, 'Iago Spindola de Souza', 'iago@nextcommerce.com', '$2y$10$Uknp345eXmd60ndbuBxQYejFRMy6In4i.GX6eCg7HT/JXgIPAU8re', 'iago@nextcommerce.com', '2025-02-04 01:16:28');

--
-- Índices para tabelas despejadas
--

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
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
