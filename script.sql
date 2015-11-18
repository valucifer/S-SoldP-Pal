
-- -----------------------------------------------------
-- Table `prestashop`.`ps_tmp_products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestashop`.`ps_tmp_products` (
  `ps_id` INT NOT NULL,
  `id_material` VARCHAR(45) NULL,
  `color` VARCHAR(45) NULL,
  `size` VARCHAR(45) NULL,
  `status` INT NULL,
  `reference` VARCHAR(100) NULL,
  PRIMARY KEY (`ps_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prestashop`.`ps_tmp_image`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestashop`.`ps_tmp_image` (
  `ps_id` INT NOT NULL,
  `old_path` VARCHAR(45) NULL,
  `new_path` VARCHAR(45) NULL,
  `md5_digest` LONGTEXT NULL,
  `color_analysis` LONGTEXT NULL,
  `fk_ps_id` INT NOT NULL,
  `status` INT NULL,
  PRIMARY KEY (`ps_id`, `fk_ps_id`),
  INDEX `fk_ps_tmp_image_ps_tmp_products_idx` (`fk_ps_id` ASC),
  CONSTRAINT `fk_ps_tmp_image_ps_tmp_products`
    FOREIGN KEY (`fk_ps_id`)
    REFERENCES `prestashop`.`ps_tmp_products` (`ps_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

