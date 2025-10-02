-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/07/2025 às 19:07
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `adopet`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `especies`
--

CREATE TABLE `especies` (
  `id_especie` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `especies`
--

INSERT INTO `especies` (`id_especie`, `nome`, `descricao`) VALUES
(1, 'Cachorro', 'Cães de todas as raças e portes'),
(2, 'Gato', 'Felinos domésticos'),
(3, 'Outro', 'Outras espécies de animais domésticos');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_usuario`
--

CREATE TABLE `tipo_usuario` (
  `id_tipo_usuario` int(11) NOT NULL,
  `descricao` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipo_usuario`
--

INSERT INTO `tipo_usuario` (`id_tipo_usuario`, `descricao`) VALUES
(1, 'ONG'),
(2, 'Pessoa Fisica');

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id_endereco` int(11) NOT NULL,
  `rua` varchar(255) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `cep` varchar(10) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `pais` varchar(100) DEFAULT 'Brasil',
  `complemento` varchar(255) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `id_tipo_usuario` int(11) NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `id_endereco` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('Ativo','Inativo','Bloqueado') DEFAULT 'Ativo',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `telefones`
--

CREATE TABLE `telefones` (
  `id_telefone` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `descricao` varchar(50) DEFAULT NULL,
  `ddd` varchar(3) NOT NULL,
  `numero` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `animais`
--

CREATE TABLE `animais` (
  `id_animais` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `id_especie` int(11) NOT NULL,
  `especie_outro` varchar(100) DEFAULT NULL,
  `raca` varchar(100) DEFAULT NULL,
  `idade` enum('Filhote','Adulto','Idoso') NOT NULL,
  `genero` enum('Macho','Fêmea') NOT NULL,
  `porte` enum('Pequeno','Medio','Grande') NOT NULL,
  `castrado` tinyint(1) DEFAULT NULL,
  `vacinado` tinyint(1) DEFAULT NULL,
  `vermifugado` tinyint(1) DEFAULT NULL,
  `descricao` text NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT 1,
  `status` enum('Ativo','Inativo','Adotado') DEFAULT 'Ativo',
  `id_usuario` int(11) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `interesses_adocao`
--

CREATE TABLE `interesses_adocao` (
  `id_interesses_adocao` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `id_interessado` int(11) NOT NULL,
  `status` enum('Pendente','Em Análise','Aprovado','Rejeitado','Adotado') DEFAULT 'Pendente',
  `mensagem_interessado` text DEFAULT NULL,
  `mensagem_doador` text DEFAULT NULL,
  `data_interesse` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `adocoes`
--

CREATE TABLE `adocoes` (
  `id_adocao` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `data_adocao` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_adocao`
--

CREATE TABLE `historico_adocao` (
  `id_historico_adocao` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `campo_alterado` varchar(100) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_alterado` text DEFAULT NULL,
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `especies`
--
ALTER TABLE `especies`
  ADD PRIMARY KEY (`id_especie`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  ADD PRIMARY KEY (`id_tipo_usuario`),
  ADD UNIQUE KEY `descricao` (`descricao`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id_endereco`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_tipo_usuario` (`id_tipo_usuario`),
  ADD KEY `id_endereco` (`id_endereco`);

--
-- Índices de tabela `telefones`
--
ALTER TABLE `telefones`
  ADD PRIMARY KEY (`id_telefone`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `animais`
--
ALTER TABLE `animais`
  ADD PRIMARY KEY (`id_animais`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_especie` (`id_especie`);

--
-- Índices de tabela `interesses_adocao`
--
ALTER TABLE `interesses_adocao`
  ADD PRIMARY KEY (`id_interesses_adocao`),
  ADD KEY `id_animal` (`id_animal`),
  ADD KEY `id_interessado` (`id_interessado`);

--
-- Índices de tabela `adocoes`
--
ALTER TABLE `adocoes`
  ADD PRIMARY KEY (`id_adocao`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_animal` (`id_animal`);

--
-- Índices de tabela `historico_adocao`
--
ALTER TABLE `historico_adocao`
  ADD PRIMARY KEY (`id_historico_adocao`),
  ADD KEY `id_animal` (`id_animal`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `especies`
--
ALTER TABLE `especies`
  MODIFY `id_especie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  MODIFY `id_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `telefones`
--
ALTER TABLE `telefones`
  MODIFY `id_telefone` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `animais`
--
ALTER TABLE `animais`
  MODIFY `id_animais` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `interesses_adocao`
--
ALTER TABLE `interesses_adocao`
  MODIFY `id_interesses_adocao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `adocoes`
--
ALTER TABLE `adocoes`
  MODIFY `id_adocao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_adocao`
--
ALTER TABLE `historico_adocao`
  MODIFY `id_historico_adocao` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_endereco`) REFERENCES `enderecos` (`id_endereco`) ON DELETE SET NULL;

--
-- Restrições para tabela `telefones`
--
ALTER TABLE `telefones`
  ADD CONSTRAINT `telefones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Restrições para tabela `animais`
--
ALTER TABLE `animais`
  ADD CONSTRAINT `animais_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `animais_ibfk_2` FOREIGN KEY (`id_especie`) REFERENCES `especies` (`id_especie`);

--
-- Restrições para tabela `interesses_adocao`
--
ALTER TABLE `interesses_adocao`
  ADD CONSTRAINT `interesses_adocao_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animais` (`id_animais`),
  ADD CONSTRAINT `interesses_adocao_ibfk_2` FOREIGN KEY (`id_interessado`) REFERENCES `usuarios` (`id_usuario`);

--
-- Restrições para tabela `adocoes`
--
ALTER TABLE `adocoes`
  ADD CONSTRAINT `adocoes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `adocoes_ibfk_2` FOREIGN KEY (`id_animal`) REFERENCES `animais` (`id_animais`) ON DELETE CASCADE;

--
-- Restrições para tabela `historico_adocao`
--
ALTER TABLE `historico_adocao`
  ADD CONSTRAINT `historico_adocao_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animais` (`id_animais`) ON DELETE CASCADE,
  ADD CONSTRAINT `historico_adocao_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;