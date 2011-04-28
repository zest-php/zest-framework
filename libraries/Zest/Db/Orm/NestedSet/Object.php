<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Orm
 */
class Zest_Db_Orm_NestedSet_Object extends Zest_Db_Object{
	
	/**
	 * @var array
	 */
	protected static $__get = array(
		'root',
		'parent', 'parents', 'children',
		'firstChild', 'lastChild',
		'next', 'prev', 'siblings'
	);
	
	/**
	 * @var array
	 */
	protected static $_memoization = array();
	
	/**
	 * @var array
	 */
	protected static $_index = array();
	
	// ROOT
	
	/**
	 * @param array $data
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function createRoot(array $data = array()){
		$table = $this->getMapper()->getDbTable();
		$select = $table->select();
		
		// construction du select
		$select
			->from($table->info(Zest_Db_Table::NAME), 'rgt')
			->order('rgt DESC')
			->limit(1);
			
		// envoi de la requête
		$rowSet = $table->fetchAll($select)->toArray();
		$max = reset($rowSet);
		if($max){
			$lft = $max['rgt'] + 1;
			$rgt = $lft + 1;
		}
		else{
			$lft = 1;
			$rgt = 2;
		}
		
		$object = new $this(array_merge($data, array('lft'  => $lft, 'rgt' => $rgt)));
		$object->save();
		
		$this->_resetMemoize('roots');
		return $object;
	}
	
	/**
	 * @return array
	 */
	public function getRoots(){
		if(!is_null($roots = $this->_getMemoize('roots'))){
			return $roots;
		}
		
		$table = $this->getMapper()->getDbTable();
		$select = $table->select();
		
		// construction du select
		$select
			->from($table->info(Zest_Db_Table::NAME), $table->info(Zest_Db_Table::COLS))
			->where('parent_id IS NULL');
			
		// envoi de la requête
		$arrayObjects = $this->_fetchAll($select);
		
		$this->_setMemoize('roots', $arrayObjects);
		return $arrayObjects;
	}
	
	/**
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function getRoot(){
		$parents = $this->getParentsAndSelf();
		if($parents){
			return end($parents);
		}
		return null;
	}
	
	/**
	 * @return boolean
	 */
	public function isRoot(){
		return !$this->hasParent();
	}
	
	// PARENT
	
	/**
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function getParent(){
		$parents = $this->getParents();
		if($parents){
			return reset($parents);
		}
		return null;
	}
	
	/**
	 * @return boolean
	 */
	public function hasParent(){
		return $this->parent_id ? true : false;
	}
	
	/**
	 * @return array
	 */
	public function getParents(){
		$parents = $this->getParentsAndSelf();
		array_shift($parents);
		return $parents;
	}
	
	/**
	 * @return array
	 */
	public function getParentsAndSelf(){
		if(!is_null($parentsAndSelf = $this->_getMemoize('parents_and_self'))){
			return $parentsAndSelf;
		}
		
		if($this->hasParent()){
			$table = $this->getMapper()->getDbTable();
			$select = $table->select();
			
			// construction du select
			$cols = array_combine($table->info(Zest_Db_Table::COLS), $table->info(Zest_Db_Table::COLS));
			$select
				->from(array('p' => $table->info(Zest_Db_Table::NAME)), $cols)
				->from(array('n' => $table->info(Zest_Db_Table::NAME)), array())
				->where('n.id = ? AND n.lft BETWEEN p.lft AND p.rgt', $this->id, 'integer')
				->order('p.lft DESC');
				
			// envoi de la requête
			$arrayObjects = $this->_fetchAll($select);
		}
		else{
			$arrayObjects = array($this);
		}
		
		$this->_setMemoize('parents_and_self', $arrayObjects);
		return $arrayObjects;
	}
	
	// CHILDREN
	
	/**
	 * @return array
	 */
	public function getChildren(){
		if(!is_null($children = $this->_getMemoize('children'))){
			return $children;
		}
		
		if($this->hasChildren()){
			$table = $this->getMapper()->getDbTable();
			$select = $table->select();
			
			// construction du select
			$cols = array_combine($table->info(Zest_Db_Table::COLS), $table->info(Zest_Db_Table::COLS));
			$select
				->from(array('n' => $table->info(Zest_Db_Table::NAME)), $cols)
				->from(array('p' => $table->info(Zest_Db_Table::NAME)), array())
				->where('p.id = ? AND n.lft BETWEEN p.lft AND p.rgt', $this->id, 'integer')
				->group('n.lft')
				->order('n.lft ASC');
				
			// envoi de la requête
			$arrayObjects = $this->_fetchAll($select);
			$arrayObjects = $this->_buildTree($arrayObjects);
			
			if($arrayObjects){
				$arrayObjects = reset($arrayObjects)->_getMemoize('children');
			}
		}
		else{
			$arrayObjects = array();
		}
		
		$this->_setMemoize('children', $arrayObjects);
		return $arrayObjects;
	}
	
	/**
	 * @return boolean
	 */
	public function hasChildren(){
		return !$this->rgt || !$this->lft || ($this->rgt == $this->lft + 1) ? false : true;
	}
	
	/**
	 * @return array
	 */
	public function recursiveGetChildren(){
		$allChildren = array();
		foreach($this->getChildren() as $child){
			$allChildren[] = $child;
			$allChildren = array_merge($allChildren, $child->recursiveGetChildren());
		} 
		return $allChildren;
	}
	
	/**
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function getFirstChild(){
		$children = $this->getChildren();
		if($children){
			return reset($children);
		}
		return null;
	}
	
	/**
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function getLastChild(){
		$children = $this->getChildren();
		if($children){
			return end($children);
		}
		return null;
	}
	
	// NEXT
	
	/**
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function getNext(){
		$next = $this->getNextAll();
		if($next){
			return reset($next);
		}
		return null;
	}
	
	/**
	 * @return boolean
	 */
	public function hasNext(){
		if($parent = $this->getParent()){
			return $this->rgt == $parent->rgt - 1 ? false : true;
		}
		return false;
	}
	
	/**
	 * @return array
	 */
	public function getNextAll(){
		if($this->hasNext()){
			$next = null;
			foreach($this->getSiblingsAndSelf() as $sibling){
				if(is_array($next)){
					$next[] = $sibling;
				}
				if($sibling->id == $this->id){
					$next = array();
				}
			}
			if(is_null($next)){
				$next = array();
			}
			return $next;
		}
		return array();
	}
	
	// PREV
	
	/**
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function getPrev(){
		$prev = $this->getPrevAll();
		if($prev){
			return end($prev);
		}
		return null;
	}
	
	/**
	 * @return boolean
	 */
	public function hasPrev(){
		if($parent = $this->getParent()){
			return $this->lft == $parent->lft + 1 ? false : true;
		}
		return false;
	}
	
	/**
	 * @return array
	 */
	public function getPrevAll(){
		if($this->hasPrev()){
			$prev = array();
			foreach($this->getSiblingsAndSelf() as $sibling){
				if($sibling->id == $this->id){
					break;
				}
				$prev[] = $sibling;
			}
			return $prev;
		}
		return array();
	}
	
	// SIBLINGS
	
	/**
	 * @return array
	 */
	public function getSiblings(){
		$siblings = array_filter($this->getSiblingsAndSelf(), array($this, '_rejectSelf'));
		return array_values($siblings);
	}
	
	/**
	 * @return boolean
	 */
	public function hasSiblings(){
		return $this->hasPrev() || $this->hasNext();
	}
	
	/**
	 * @return array
	 */
	public function getSiblingsAndSelf(){
		if($parent = $this->getParent()){
			return $parent->getChildren();
		}
		return array($this);
	}
	
	// NODE
	
	/**
	 * @param Zest_Db_Orm_NestedSet_Object $moved
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function append($moved){
		$this->_moveTo($moved, '_appendLftRgtParentId');
		return $this;
	}
	
	/**
	 * @return array
	 */
	protected function _appendLftRgtParentId(){
		return array($this->rgt, $this->rgt + 1, $this->id);
	}
	
	/**
	 * @param Zest_Db_Orm_NestedSet_Object $moved
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function prepend($moved){
		$this->_moveTo($moved, '_prependLftRgtParentId');
		return $this;
	}
	
	/**
	 * @return array
	 */
	protected function _prependLftRgtParentId(){
		return array($this->lft + 1, $this->lft + 2, $this->id);
	}
	
	/**
	 * @param Zest_Db_Orm_NestedSet_Object $moved
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function after($moved){
		$this->_moveTo($moved, '_afterLftRgtParentId');
		return $this;
	}
	
	/**
	 * @return array
	 */
	protected function _afterLftRgtParentId(){
		return array($this->rgt + 1, $this->rgt + 2, $this->parent_id);
	}
	
	/**
	 * @param Zest_Db_Orm_NestedSet_Object $moved
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function before($moved){
		$this->_moveTo($moved, '_beforeLftRgtParentId');
		return $this;
	}
	
	/**
	 * @return array
	 */
	protected function _beforeLftRgtParentId(){
		return array($this->lft, $this->lft + 1, $this->parent_id);
	}
	
	// OVERRIDE
	
	/**
	 * @param integer|array $primary
	 * @param array $options
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	public function find($primary, array $options = array()){
		parent::find($primary, $options);
		if($primary = $this->id){
			$table = $this->getMapper()->getDbTable()->info(Zest_Db_Table::NAME);
			self::$_index[$table][$primary] = $this;
		}
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		if(in_array($name, self::$__get)){
			return $this->{'get'.ucfirst($name)}();
		}
		return parent::__get($name);
	}
	
	// PROTECTED
	
	/**
	 * @param Zest_Db_Orm_NestedSet_Object $moved
	 * @param string $lftRgtCallback
	 * @return void
	 */
	protected function _moveTo($moved, $lftRgtCallback){
		if($moved->id && $moved->isRoot()){
			throw new Zest_Db_Exception('Impossible de déplacer la racine.');
		}
		
		$table = $moved->getMapper()->getDbTable()->info(Zest_Db_Table::NAME);
		$dbAdapter = $moved->getMapper()->getDbAdapter();
		
		$ids = null;
		if($moved->id){
			// sélection des identifiants pour les mises à jour
			$ids = array($moved->id);
			if($moved->hasChildren()){
				$movedChildren = $moved->recursiveGetChildren();
				foreach($movedChildren as $movedChild){
					$ids[] = $movedChild->id;
				}
			}
			if(in_array($this->id, $ids)){
				throw new Zest_Db_Exception(sprintf('Impossible de déplacer l\'élément "%s" dans l\'élément "%s".', $moved->id, $this->id));
			}
			
			// suppression virtuelle de la portion pour avoir une projection de lft et rgt
			$deleteDiff = $moved->rgt - $moved->lft;
			$query = 'UPDATE '.$table.' SET lft = lft - ('.$deleteDiff.' + 1) WHERE lft > '.$moved->rgt.' AND id NOT IN ('.implode(', ', $ids).');';
			$dbAdapter->query($query);
				
			$query = 'UPDATE '.$table.' SET rgt = rgt - ('.$deleteDiff.' + 1) WHERE rgt > '.$moved->rgt.' AND id NOT IN ('.implode(', ', $ids).');';
			$dbAdapter->query($query);
			
			// rafraichissement de lft et rgt
			if($this->lft > $moved->lft){
				$this->lft = $this->lft - ($deleteDiff + 1);
			}
			if($this->rgt > $moved->rgt){
				$this->rgt = $this->rgt - ($deleteDiff + 1);
			}
		}
		
		// calcul de lft et rgt
		list($lft, $rgt, $parent_id) = $this->$lftRgtCallback();
		if($moved->hasChildren()){
			$count = ($moved->rgt - $moved->lft - 1) / 2;
			$rgt += $count * 2;
		}
		
		// mise à jour des enfants (ancienne position - nouvelle position)
		if($moved->id){
			$updateDiff = $moved->lft - $lft;
			if($moved->hasChildren()){
				$query = 'UPDATE '.$table.' SET lft = lft - ('.$updateDiff.'), rgt = rgt - ('.$updateDiff.') WHERE id IN ('.implode(', ', array_slice($ids, 1)).');';
				$dbAdapter->query($query);
			}
		}
		
		// mise à jour de la branche déplacée
		$moved->setData(array(
			'parent_id' => $parent_id,
			'lft' => $lft,
			'rgt' => $rgt
		))->save();
		
		// mise à jour du reste de l'arbre (par rapport au nombre d'enfants)
		if(is_null($ids)){
			$ids = array($moved->id);
		}
		
		$updateDiff = $rgt - $lft + 1;
		$query = 'UPDATE '.$table.' SET lft = lft + '.$updateDiff.' WHERE lft >= '.$lft.' AND id NOT IN ('.implode(', ', $ids).');';
		$dbAdapter->query($query);
		
		$query = 'UPDATE '.$table.' SET rgt = rgt + '.$updateDiff.' WHERE rgt >= '.$lft.' AND id NOT IN ('.implode(', ', $ids).');';
		$dbAdapter->query($query);
		
		// remise à zéro des tableaux de mémoization
		self::resetMemoization();
	}
	
	/**
	 * @return void
	 */
	public static function resetMemoization(){
		self::$_index = array();
		self::$_memoization = array();
	}
	
	/**
	 * @param string $name
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	protected function _getMemoize($name){
		$primary = $this->id;
		if(isset(self::$_memoization[$primary][$name])){
			return self::$_memoization[$primary][$name];
		}
		return null;
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	protected function _setMemoize($name, $value){
		$primary = $this->id;
		self::$_memoization[$primary][$name] = $value;
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	protected function _resetMemoize($name = null){
		$primary = $this->id;
		if(is_null($name)){
			self::$_memoization[$primary] = array();
		}
		else{
			unset(self::$_memoization[$primary][$name]);
		}
		return $this;
	}
	
	/**
	 * @param Zest_Db_Table_Select $select
	 * @return array
	 */
	protected function _fetchAll(Zest_Db_Table_Select $select){
		$table = $this->getMapper()->getDbTable()->info(Zest_Db_Table::NAME);
		if(!isset(self::$_index[$table])){
			self::$_index[$table] = array();
		}
		
		$arrayObjects = array();
		$rowSet = $this->getMapper()->getDbTable()->fetchAll($select)->toArray();
		foreach($rowSet as $row){
			$primary = $row['id'];
			if(isset(self::$_index[$table][$primary])){
				$arrayObjects[$primary] = self::$_index[$table][$primary];
			}
			else{
				$arrayObjects[$primary] = self::$_index[$table][$primary] = $this->getMapper()->toObject($row);
			}
		}
		return $arrayObjects;
	}
	
	/**
	 * @param array $objects
	 * @return array
	 */
	protected function _buildTree($objects){
		if(!$objects) return $objects;
		
		$parents = null;
		
		$parent = null;
		$children = array();
		
		foreach($objects as $key => $object){
			$object->_setMemoize('children', array());
			if(is_null($parent)){
				$parent = $object;
			}
			else{
				if($object->lft > $parent->lft && $object->rgt < $parent->rgt){
					$object->_setMemoize('parents_and_self', array_merge(array($object), $parent->getParentsAndSelf()));
					$children[] = $object;
					unset($objects[$key]);
				}
				else{
					$parent->_setMemoize('children', $this->_buildTree($children));
					$parents[] = $parent;
					
					$parent = $object;
					$children = array();
				}
			}
		}
		$parent->_setMemoize('children', $this->_buildTree($children));
		$parents[] = $parent;
		return $parents;
	}
	
	/**
	 * @param Zest_Db_Orm_NestedSet_Object $object
	 * @return boolean
	 */
	protected function _rejectSelf($object){
		return $object->id != $this->id;
	}
	
//	protected function _index($object, $objects){
//		return array_search($object, $objects);
//	}
//	
//	protected function _add($objects, $index, $object, $after = false){
//		if(is_object($index)){
//			$index = $this->_index($index, $objects);
//		}
//		if($after){
//			$index++;
//		}
//		array_splice($objects, $index, 0, array($object));
//		return $objects;
//	}
//	
//	protected function _remove($objects, $index){
//		if(is_object($index)){
//			$index = $this->_index($index, $objects);
//		}
//		if(is_int($index)){
//			unset($objects[$index]);
//			$objects = array_values($objects);
//		}
//		return $objects;
//	}
	
}