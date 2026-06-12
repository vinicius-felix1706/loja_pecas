-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/06/2026 às 16:37
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `loja_pecas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome_categoria` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `nome_categoria`, `descricao`) VALUES
(1, 'Motor', 'Pecas internas e estruturais do motor');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `role` enum('cliente','admin') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `nome`, `email`, `telefone`, `senha`, `role`) VALUES
(1, 'Administrador de teste', 'admteste@gmail.com', '+5535984258856', '$2y$10$yiZBf3SQHAiS1YPBBiUOc.egJs6qrG7u0cqUCM7B9nOiJhUa1qkMG', 'admin'),
(2, 'Usuario de teste', 'userteste@gmail.com', '+5535943678841', '$2y$10$1HhaQJMBT0Lvfj4HRKxwfOoYt0dRBpPGLgYUVyXwq7IfbOwQCmOjG', 'cliente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `item_pedido`
--

CREATE TABLE `item_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `data_pedido` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id`, `nome`, `preco`, `estoque`, `id_categoria`) VALUES
(1, 'Bloco do Motor', 1890.00, 20, 1),
(2, 'Cabecote', 1240.00, 20, 1),
(3, 'Pistao', 186.90, 20, 1),
(4, 'Anel de Pistao', 72.50, 20, 1),
(5, 'Biela', 298.00, 20, 1),
(6, 'Virabrequim', 980.00, 20, 1),
(7, 'Comando de Valvulas', 455.00, 20, 1),
(8, 'Valvula de Admissao', 64.90, 20, 1),
(9, 'Valvula de Escape', 69.90, 20, 1),
(10, 'Tucho', 42.00, 20, 1),
(15, 'Junta do cabecote', 89.90, 20, 1),
(16, 'Pino do pistao', 48.90, 20, 1),
(17, 'Bronzina de biela', 58.00, 20, 1),
(18, 'Bronzina de mancal', 64.00, 20, 1),
(19, 'Volante do motor', 520.00, 20, 1),
(20, 'Varetas', 55.00, 20, 1),
(21, 'Balancins', 86.00, 20, 1),
(22, 'Molas de valvula', 38.00, 20, 1),
(23, 'Guias de valvula', 46.00, 20, 1),
(24, 'Retentores de valvula', 34.00, 20, 1),
(25, 'Correia dentada', 115.00, 20, 1),
(26, 'Corrente de comando', 135.00, 20, 1),
(27, 'Tensor da correia', 92.00, 20, 1),
(28, 'Polia do virabrequim', 128.00, 20, 1),
(29, 'Polia do comando', 118.00, 20, 1),
(30, 'Tampa de valvulas', 155.00, 20, 1),
(31, 'Carter', 310.00, 20, 1),
(32, 'Junta do carter', 42.00, 20, 1),
(33, 'Bomba de oleo', 265.00, 20, 1),
(34, 'Pescador de oleo', 76.00, 20, 1),
(35, 'Filtro de oleo', 39.90, 20, 1),
(36, 'Bomba d\'agua', 280.00, 20, 1),
(37, 'Termostato', 74.00, 20, 1),
(38, 'Radiador', 390.00, 20, 1),
(39, 'Ventoinha', 180.00, 20, 1),
(40, 'Coletor de admissao', 340.00, 20, 1),
(41, 'Coletor de escape', 360.00, 20, 1),
(42, 'Corpo de borboleta', 420.00, 20, 1),
(43, 'Injetores de combustivel', 210.00, 20, 1),
(44, 'Bomba de combustivel', 310.00, 20, 1),
(45, 'Filtro de combustivel', 49.90, 20, 1),
(46, 'Velas de ignicao', 36.00, 20, 1),
(47, 'Cabos de vela', 95.00, 20, 1),
(48, 'Bobina de ignicao', 160.00, 20, 1),
(49, 'Distribuidor', 240.00, 20, 1),
(50, 'Sensor de rotacao', 145.00, 20, 1),
(51, 'Sensor de fase', 135.00, 20, 1),
(52, 'Sensor MAP', 190.00, 20, 1),
(53, 'Sensor MAF', 220.00, 20, 1),
(54, 'Sensor de temperatura', 84.00, 20, 1),
(55, 'Sensor de oxigenio', 180.00, 20, 1),
(56, 'Unidade de controle do motor (ECU)', 890.00, 20, 1),
(57, 'Alternador', 620.00, 20, 1),
(58, 'Motor de partida', 480.00, 20, 1),
(59, 'Correia de acessorios', 120.00, 20, 1),
(60, 'Tensor da correia de acessorios', 98.00, 20, 1),
(61, 'Turbocompressor', 2450.00, 20, 1),
(62, 'Intercooler', 730.00, 20, 1),
(63, 'Valvula wastegate', 410.00, 20, 1),
(64, 'Valvula EGR', 360.00, 20, 1),
(65, 'Retentor do virabrequim', 78.00, 20, 1),
(66, 'Retentor do comando', 82.00, 20, 1),
(67, 'Coxim do motor', 190.00, 20, 1),
(68, 'Tampa frontal do motor', 230.00, 20, 1),
(69, 'Tampa traseira do motor', 240.00, 20, 1),
(70, 'Galeria de oleo', 110.00, 20, 1),
(71, 'Galeria de agua', 115.00, 20, 1),
(72, 'Camara de combustao', 95.00, 20, 1),
(73, 'Camisas dos cilindros', 210.00, 20, 1),
(74, 'Prisioneiros do cabecote', 65.00, 20, 1),
(75, 'Parafusos do cabecote', 58.00, 20, 1),
(76, 'Respiro do carter', 48.00, 20, 1),
(77, 'Separador de oleo', 72.00, 20, 1),
(78, 'Sensor de detonacao', 135.00, 20, 1),
(79, 'Sensor de pressao do oleo', 125.00, 20, 1),
(80, 'Sensor de nivel de oleo', 118.00, 20, 1),
(81, 'Bujao do carter', 22.00, 20, 1),
(82, 'Tampa do oleo', 36.00, 20, 1),
(83, 'Vareta de oleo', 28.00, 20, 1),
(84, 'Junta da tampa de valvulas', 54.00, 20, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `item_pedido`
--
ALTER TABLE `item_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_itempedido_pedido` (`id_pedido`),
  ADD KEY `fk_itempedido_produto` (`id_produto`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_cliente` (`id_cliente`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_categoria` (`id_categoria`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `item_pedido`
--
ALTER TABLE `item_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `item_pedido`
--
ALTER TABLE `item_pedido`
  ADD CONSTRAINT `fk_itempedido_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `fk_itempedido_produto` FOREIGN KEY (`id_produto`) REFERENCES `produto` (`id`);

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_pedido_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id`);

--
-- Restrições para tabelas `produto`
--
ALTER TABLE `produto`
  ADD CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
