DROP DATABASE IF EXISTS proyectoutu;

CREATE DATABASE proyectoutu;

USE proyectoutu;



CREATE TABLE  categorias (
   id_categoria  INT(5)  NOT NULL AUTO_INCREMENT ,
   descripcion  VARCHAR(45) NOT NULL,
   estado  BOOLEAN NOT NULL,
  PRIMARY KEY ( id_categoria ));


CREATE TABLE  productos (
   id_producto  INT(5) NOT NULL AUTO_INCREMENT ,
   nombre  VARCHAR(45) NULL,
   id_categoria  INT NOT NULL,
   codigo_barras  VARCHAR(150) NULL,
   precio_venta  DECIMAL(16,2) NULL,
   cantidad_stock  INT NOT NULL,
   estado  BOOLEAN NULL,
  PRIMARY KEY ( id_producto ),
  CONSTRAINT  fk_PRODUCTOS_CATEGORIAS 
    FOREIGN KEY ( id_categoria )
    REFERENCES categorias ( id_categoria )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE  clientes (
   id  VARCHAR(20) NOT NULL,
   nombre  VARCHAR(40) NULL,
   apellidos  VARCHAR(100) NULL,
   celular  VARCHAR(20) NULL,
   direccion  VARCHAR(80) NULL,
   correo_electronico  VARCHAR(70) NULL,
  PRIMARY KEY ( id ));


CREATE TABLE  compras (
   id_compra  int(5) NOT NULL AUTO_INCREMENT,
   id_cliente  VARCHAR(20) NOT NULL,
   fecha  TIMESTAMP NULL,
   medio_pago  CHAR(1) NULL,
   comentario  VARCHAR(300) NULL,
   estado  CHAR(1) NULL,
  PRIMARY KEY ( id_compra ),
  CONSTRAINT  fk_COMPRAS_CLIENTES1 
    FOREIGN KEY ( id_cliente )
    REFERENCES clientes ( id )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE  compras_productos (
   id_compra  INT(5) NOT NULL,
   id_producto  INT(5) NOT NULL,
   cantidad  INT(5) NULL,
   total  DECIMAL(16,2) NULL,
   estado  BOOLEAN NULL,
  PRIMARY KEY ( id_compra ,  id_producto ),
  CONSTRAINT  fk_COMPRAS_PRODUCTOS_PRODUCTOS1 
    FOREIGN KEY ( id_producto )
    REFERENCES productos ( id_producto )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT  fk_COMPRAS_PRODUCTOS_COMPRAS1 
    FOREIGN KEY ( id_compra )
    REFERENCES compras ( id_compra )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
