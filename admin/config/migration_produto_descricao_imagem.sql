-- Execute para permitir descricao e caminho da imagem nos cards das pecas.
-- Observacao: o upload de imagem ainda nao esta 100% finalizado no sistema.

ALTER TABLE produto
  ADD COLUMN descricao TEXT NULL AFTER nome,
  ADD COLUMN imagem VARCHAR(255) NULL AFTER estoque;
