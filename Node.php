<?php

require_once('iNode.php');

class Node implements iNode 
{
    protected $name;
    protected $children;
    protected $_parent;
    
    function __construct($name) {
        $this->setName($name);
        $this->children = [];
    }    
    
    /*
        @return string имя листа, если есть, иначе NULL
    */
    function getName() : string {
        return $this->name;
    }

    /*
        Изменить имя листа
        @param string $name имя листа, если есть, иначе NULL
    */
    function setName(string $name) {
        $this->name = $name;
    }

    /*
        @return array массив из Node которые являются дочерними по отношениею к текущему листу, иначе пустой массив
    */
    function getChildren() : array {
        return $this->children;
    }

    /*
        Добавляет дочерний лист
        @param iNode $child дочерний лист
    */
    function addChild(iNode $child) {        
        $this->children[] = $child;
        $child->setParent($this);
    }
    
    /*
        @return Node родительский лист, если нет, то NULL
    */
    function getParent() : iNode {
        return isset($this->_parent) ? $this->_parent : null;            
    }

    /*
        Устанавливает лист-родитель
        @param iNode $parent лист родитель
    */
    function setParent(iNode $parent) {
        $this->_parent = $parent;
    }
    
    function unsetParent(){
        $this->_parent = null;
    }
    
    function removeChild($key) {
        unset($this->children[$key]);
    }
}
