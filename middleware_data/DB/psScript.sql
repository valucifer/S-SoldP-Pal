
-- -----------------------------------------------------
-- Table `prestashop`.`ps_tmp_image`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestashop`.`ps_tmp_image` (
  `ps_id` INT NOT NULL,
  `old_path` VARCHAR(45) NULL,
  `new_path` VARCHAR(45) NULL,
  `md5_digest` LONGTEXT NULL,
  `color_analysis` LONGTEXT NULL,
  `status` INT NULL,
  `fk_ps_id` INT NOT NULL,
  PRIMARY KEY (`ps_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prestashop`.`ps_buffer_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestashop`.`ps_buffer_product` (
  `reference` VARCHAR(100) NOT NULL,
  `attivo` INT NULL,
  `categoria` VARCHAR(200) NULL,
  `prezzo` FLOAT NULL,
  `supplier` VARCHAR(45) NULL,
  `manufacture` VARCHAR(45) NULL,
  `qta` FLOAT NULL,
  `qta_min` FLOAT NULL,
  `lunghezza` FLOAT NULL,
  `altezza` FLOAT NULL,
  `larghezza` FLOAT NULL,
  `colore` VARCHAR(45) NULL,
  `quantita` VARCHAR(45) NULL,
  `taglia` VARCHAR(45) NULL,
  `nome` VARCHAR(100) NULL,
  `modello` VARCHAR(45) NULL,
  `linea` VARCHAR(45) NULL,
  `codice_colore` VARCHAR(10) NULL,
  `codice_taglia` VARCHAR(10) NULL,
  `url` VARCHAR(700) NULL,
  `immagine` VARCHAR(700) NULL)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prestashop`.`ps_tmp_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestashop`.`ps_tmp_product` (
  `ps_id` INT NOT NULL,
  `reference` VARCHAR(100) NOT NULL,
  `attivo` INT NULL,
  `categoria` VARCHAR(200) NULL,
  `prezzo` FLOAT NULL,
  `supplier` VARCHAR(45) NULL,
  `manufacture` VARCHAR(45) NULL,
  `qta` FLOAT NULL,
  `qta_min` FLOAT NULL,
  `lunghezza` FLOAT NULL,
  `altezza` FLOAT NULL,
  `larghezza` FLOAT NULL,
  `colore` VARCHAR(45) NULL,
  `quantita` VARCHAR(45) NULL,
  `taglia` VARCHAR(45) NULL,
  `nome` VARCHAR(100) NULL,
  `modello` VARCHAR(45) NULL,
  `linea` VARCHAR(45) NULL,
  `codice_colore` VARCHAR(10) NULL,
  `codice_taglia` VARCHAR(10) NULL,
  `url` VARCHAR(700) NULL,
  `immagine` VARCHAR(700) NULL)
ENGINE = InnoDB;

USE `prestashop` ;

-- -----------------------------------------------------
-- Placeholder table for view `prestashop`.`products_differences`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestashop`.`products_differences` (`ps_id` INT, `reference` INT, `attivo` INT, `categoria` INT, `prezzo` INT, `supplier` INT, `manufacture` INT, `qta` INT, `qta_min` INT, `lunghezza` INT, `altezza` INT, `larghezza` INT, `colore` INT, `quantita` INT, `taglia` INT, `nome` INT, `modello` INT, `linea` INT, `codice_colore` INT, `url` INT, `immagine` INT, `codice_taglia` INT);

-- -----------------------------------------------------
-- Placeholder table for view `prestashop`.`new_products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestashop`.`new_products` (`ps_id` INT, `reference` INT, `attivo` INT, `categoria` INT, `prezzo` INT, `supplier` INT, `manufacture` INT, `qta` INT, `qta_min` INT, `lunghezza` INT, `altezza` INT, `larghezza` INT, `colore` INT, `quantita` INT, `taglia` INT, `nome` INT, `modello` INT, `linea` INT, `codice_colore` INT, `url` INT, `immagine` INT, `codice_taglia` INT);

-- -----------------------------------------------------
-- View `prestashop`.`products_differences`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `prestashop`.`products_differences`;
USE `prestashop`;
CREATE  OR REPLACE VIEW `products_differences` AS SELECT ps_tmp_product.ps_id,ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product, ps_tmp_product WHERE ((ps_buffer_product.reference = ps_tmp_product.reference
AND ps_buffer_product.colore = ps_tmp_product.colore AND ps_buffer_product.taglia = ps_tmp_product.taglia )AND( ps_buffer_product.attivo <> ps_tmp_product.attivo 
OR ps_buffer_product.prezzo <> ps_tmp_product.prezzo OR ps_buffer_product.qta <> ps_tmp_product.qta OR ps_buffer_product.qta_min <> ps_tmp_product.qta_min 
OR ps_buffer_product.lunghezza <> ps_tmp_product.lunghezza OR ps_buffer_product.altezza <> ps_tmp_product.altezza
OR ps_buffer_product.larghezza <> ps_tmp_product.larghezza OR ps_buffer_product.quantita <> ps_tmp_product.quantita
OR ps_buffer_product.nome <> ps_tmp_product.nome OR ps_buffer_product.modello <> ps_tmp_product.modello
OR ps_buffer_product.linea <> ps_tmp_product.linea ));

-- -----------------------------------------------------
-- View `prestashop`.`new_products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `prestashop`.`new_products`;
USE `prestashop`;
CREATE  OR REPLACE VIEW `new_products` AS SELECT ps_tmp_product.ps_id, ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product LEFT JOIN ps_tmp_product ON ps_buffer_product.reference = ps_tmp_product.reference WHERE( ps_tmp_product.ps_id IS NULL);


INSERT INTO `prestashop`.`ps_buffer_product` ( `reference`, `attivo`, `categoria`, `prezzo`, `supplier`, `manufacture`, `qta`, `qta_min`, `lunghezza`, `altezza`, `larghezza`, `colore`, `quantita`, `taglia`, `nome`, `modello`, `linea`, `codice_colore`, `codice_taglia`) VALUES ( 'esiste e non diverso', '0', 'asda', '213.3', 'sda', 'da', '12.3', '21.4', '25.2', '23.2', '344.1', 'blue', '324.3', 'xl', NULL, NULL, NULL, NULL, NULL);

INSERT INTO `prestashop`.`ps_buffer_product` ( `reference`, `attivo`, `categoria`, `prezzo`, `supplier`, `manufacture`, `qta`, `qta_min`, `lunghezza`, `altezza`, `larghezza`, `colore`, `quantita`, `taglia`, `nome`, `modello`, `linea`, `codice_colore`, `codice_taglia`) VALUES ( 'esiste e diverso', '0', 'asda', '213.3', 'sda', 'da', '12.3', '21.4', '25.2', '23.2', '344.1', 'blue', '324.3', 'xl', NULL, NULL, NULL, NULL, NULL);

INSERT INTO `prestashop`.`ps_buffer_product` ( `reference`, `attivo`, `categoria`, `prezzo`, `supplier`, `manufacture`, `qta`, `qta_min`, `lunghezza`, `altezza`, `larghezza`, `colore`, `quantita`, `taglia`, `nome`, `modello`, `linea`, `codice_colore`, `codice_taglia`) VALUES ( 'non esiste', '0', 'asda', '213.3', 'sda', 'da', '12.3', '21.4', '25.2', '23.2', '344.1', 'red', '324.3', 'xl', NULL, NULL, NULL, NULL, NULL);



INSERT INTO `prestashop`.`ps_tmp_product` ( `ps_id`,`reference`, `attivo`, `categoria`, `prezzo`, `supplier`, `manufacture`, `qta`, `qta_min`, `lunghezza`, `altezza`, `larghezza`, `colore`, `quantita`, `taglia`, `nome`, `modello`, `linea`, `codice_colore`, `codice_taglia`) VALUES ( '1','esiste e non diverso', '0', 'asda', '213.3', 'sda', 'da', '12.3', '21.4', '25.2', '23.2', '344.1', 'blue', '324.3', 'xl', NULL, NULL, NULL, NULL, NULL);

INSERT INTO `prestashop`.`ps_tmp_product` ( `ps_id`, `reference`, `attivo`, `categoria`, `prezzo`, `supplier`, `manufacture`, `qta`, `qta_min`, `lunghezza`, `altezza`, `larghezza`, `colore`, `quantita`, `taglia`, `nome`, `modello`, `linea`, `codice_colore`, `codice_taglia`) VALUES ( '2','esiste e diverso', '0', 'asda', '21.3', 'sda', 'da', '12.3', '21.4', '25.2', '23.2', '344.1', 'blue', '324.3', 'xl', NULL, NULL, NULL, NULL, NULL);
