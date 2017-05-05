<?php

require_once('iTree.php');

class Tree implements iTree
{
    protected $rootNode;

    function __construct(iNode $node) {
        $this->rootNode = $node;
    }

    /*
		@return Node корневой лист дерева, NULL, если нет
	*/
	function getRoot() : iNode{
        return isset($this->rootNode) ? $this->rootNode : null;
    }

	/*
		Достает лист из дерева
		@params string nodeName имя листа для поиска
		@return Node лист с заданным именем, NULL если такого листа нет в дереве
	*/
	function getNode(string $nodeName): iNode {
        if (!isset($this->rootNode)) {
            return null;
        }

        $result = $this->_searchByNameDownTheTree($nodeName, $this->rootNode);
        return $result;
    }

    protected function _searchByNameDownTheTree(string $nodeName, iNode $node) {
        $currentNodeName = $node->getName();
        if (trim($nodeName) === trim($currentNodeName)) {
            return $node;
        }
        $children = $node->getChildren();
        foreach($children as $child) {
            $result = $this->_searchByNameDownTheTree($nodeName, $child);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }

	/*
		Добавляет лист к листу $parent
		@param Node $node лист, который мы добавляем
		@param Node $parent лист-родителm, к которому добавляем
		@return Node лист, который добавили в дерево
		@throws ParentNotFoundException если ролитель не найдет в дереве
	*/
	function appendNode(iNode $node, iNode $parent): iNode {
        if (!$this->contains($parent)) {
            throw new ParentNotFoundException;
        }

        $parent->addChild($node);
        return $node;
    }

    /**
     * @return true, если нода содержится в дереве, false иначе.
     */
    public function contains(iNode $node): bool {
        if (!isset($this->rootNode)) {
            return false;
        }
        $result = $this->_searchByNodeDownTheTree($node, $this->rootNode);
        return $result !== null;
    }

    protected function _searchByNodeDownTheTree(iNode $find, iNode $currentRoot) {
        if ($find === $currentRoot) {
            return $find;
        }
        $children = $currentRoot->getChildren();
        foreach($children as $child) {
            $result = $this->_searchByNodeDownTheTree($find, $child);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }


	/*
		Удаляет лист и всех детей рекурсивно
		@param Node $node лист для удаления
		@throws NodeNotFoundException такой лист не найдет в дереве
	*/
	function deleteNode(iNode $node) {
        if (!$this->contains($node)) {
            throw new NodeNotFoundException;
        }
        $parent = $node->getParent();
        if ($parent) {
            $children = $parent->getChildren();            
            if (($key = array_search($node, $children)) !== false) {                
                $parent->removeChild($key);
                $node->unsetParent();
                // not sure if I have to unset $node itself...            
            }
        }
    }


	 /*
		@return string json представление дерева, вида
		{ root : {
				name : "rootNodeName",
				childs : [
					{
						name : "childOne",
						childs : []
					},
					{
						name : "childTwo",
						childs : []
					}
				]
			}
		}
	 */
	function toJSON(): string {
        $arr = [];
        if (!isset($this->rootNode)) {
            return json_encode(new stdClass);
        }
        $arr['root'] = [
            'name' => $this->rootNode->getName(),
            'childs' => []
        ];
        $children = $this->rootNode->getChildren();
        foreach ($children as $child) {
            $this->_addNodeToJSON($arr['root']['childs'], $child);
        }
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    protected function _addNodeToJSON(array &$arr, iNode $node) {
        $children = $node->getChildren();

        $arr[] = [
            'name' => $node->getName(),
            'childs' => []
        ];
        end($arr);
        $key = key($arr);
        foreach ($children as $child) {
            $this->_addNodeToJSON($arr[$key]['childs'], $child);
        }
    }
}

class ParentNotFoundException extends \Exception
{
}

class NodeNotFoundException extends \Exception
{
}

