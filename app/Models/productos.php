<?php

namespace  \App\Models;

class  productos{

    private $id_producto;
    private $nombre;
    private $id_categoria;
    private $codigo_barras;
    private $precio_venta;
    private $cantidad_stock;
    private $estado;

function __construct($id_producto,$nombre,$id_categoria,$codigo_barras,$precio_venta,$cantidad_stock,$id_estado){      
    $this->id_producto = $id_producto;
    $this->nombre = $nombre;
    $this->id_categoria = $id_categoria;
        $this->codigo_barras = $codigo_barras;
        $this->precio_venta = $precio_venta;
        $this->cantidad_stock = $cantidad_stock;
        $this->id_estado = $id_estado;
}

function getId_Producto(){
return $this->id_producto;
}
function setId_Producto($id_producto){
    $this->id_producto = $id_producto;
}
function getNombre(){
return $this->nombre;
}
function setNombre($nombre){
    $this->nombre = $nombre;
}
function getId_Categoria(){
    return $this->id_categoria;
}
function setId_Categoria($id_categoria){
    $this->id_categoria = $id_categoria;
}
function getCodigo_barras(){
    return $this->codigo_barras;
}
function setCodigo_barras($codigo_barras){
    $this->codigo_barras = $codigo_barras;
}
function getPrecio_venta(){
    return $this->precio_venta;
}
function setPrecio_venta($precio_venta){
    $this->precio_venta = $precio_venta;
}
function  getCantidad_stock(){
    return $this->cantidad_stock;
}
function setCantidad_stock($cantidad_stock){
    $this->cantidad_stock = $cantidad_stock;
}
function setEstado($estado){
    $this->estado = $estado;
}
function getEstado(){
    return $this->estado;
}
}