<?php

/**
 * les classes
 * 		DirectoryTest, DirectoryTestMapper, Default_Model_DbTable_DirectoryTest
 * 		FileTest, FileTestMapper, Default_Model_DbTable_FileTest
 * sont déclarées dans le fichier Zest_Db_AbstractTest
 */

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage UnitTests
 */
class Zest_Db_Orm_NestedSet_ObjectTest extends Zest_Db_AbstractTest{
	
	protected $_beforeAll = null;
	
	public static function setUpBeforeClass(){
		if(!defined('ZEST_DB_ABSTRACTTEST_DIR')){
			define('ZEST_DB_ABSTRACTTEST_DIR', Zest_AllTests::getDataDir().'/db');
		}
		
		try{
			self::$_db = Zest_Db::getDbAdapter('nestedset');
		}
		catch(Zest_Db_Exception $e){
			$config = new Zend_Config_Ini(self::_getConfigPathname());
			Zest_Db::setDbConfigs($config->toArray());
			self::$_db = Zest_Db::getDbAdapter('nestedset');
		}
	}
	
	public static function tearDownAfterClass(){
		self::$_db->closeConnection();
		self::$_db = null;
	}
	
	protected function setUp(){
		self::$_db->query('DELETE FROM "directory";');
		self::$_db->query('INSERT INTO "directory" VALUES(1,\'racine\',NULL,1,14);');
		self::$_db->query('INSERT INTO "directory" VALUES(2,\'images\',1,2,7);');
		self::$_db->query('INSERT INTO "directory" VALUES(3,\'videos\',1,8,11);');
		self::$_db->query('INSERT INTO "directory" VALUES(4,\'others\',1,12,13);');
		self::$_db->query('INSERT INTO "directory" VALUES(5,\'landscape\',2,3,4);');
		self::$_db->query('INSERT INTO "directory" VALUES(6,\'traffic\',2,5,6);');
		self::$_db->query('INSERT INTO "directory" VALUES(7,\'train\',3,9,10);');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		
		Zest_Db_Orm_NestedSet_Object::resetMemoization();
	}
	
	public function testCreateRoot(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		
		$racine2 = $racine->createRoot(array('name' => 'racine2'));
		$this->assertEquals(15, $racine2->lft);
		$this->assertEquals(16, $racine2->rgt);
		
		// sélection du rgt max
		// insert de la nouvelle racine
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(2, $numberOfQueries);
	}
	
	public function testGetRoots(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		
		$roots = $racine->getRoots();
		
		// sélection de tous les tuples avec un parent_id à null
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
		
		// contrôle du fonctionnement de l'index
		$this->assertTrue($racine === $roots[1]);
	}
	
	public function testGetRoot(){
		$racine = $this->_getBranch('racine');
		$train = $this->_getBranch('train');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		
		// contrôle du fonctionnement de l'index
		$this->assertTrue($racine === $train->root);
		
		// aucune requête (on va chercher dans l'index tout le temps)
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(0, $numberOfQueries);
	}
	
	public function testIsRoot(){
		$racine = $this->_getBranch('racine');
		$this->assertTrue($racine->isRoot());
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		
		$images = $this->_getBranch('images');
		$this->assertFalse($images->isRoot());
		
		$train = $this->_getBranch('train');
		$this->assertFalse($train->isRoot());
		
		// 2 requêtes qui correspondent au find de la racine dans _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(2, $numberOfQueries);
	}
	
	public function testGetParent(){
		$racine = $this->_getBranch('racine');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertNull($racine->parent);
		
		$train = $this->_getBranch('train');
		$this->assertEquals('videos', $train->parent->name);
		
		// 1 requête qui correspond au find de la racine dans _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
	}
	
	public function testHasParent(){
		$racine = $this->_getBranch('racine');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertFalse($racine->hasParent());
		
		$train = $this->_getBranch('train');
		$this->assertTrue($train->hasParent());
		
		// 1 requête qui correspond au find de la racine dans _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
	}
	
	public function testGetParents(){
		$racine = $this->_getBranch('racine');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertEmpty($racine->parents);
		
		$train = $this->_getBranch('train');
		$names = $this->_getNames($train->parents);
		$this->assertEquals(array('videos', 'racine'), $names);
		
		// 1 requête qui correspond au find de la racine dans _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
	}
	
	public function testGetParentsAndSelf(){
		$racine = $this->_getBranch('racine');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$names = $this->_getNames($racine->getParentsAndSelf());
		$this->assertEquals(array('racine'), $names);
		
		$train = $this->_getBranch('train');
		$names = $this->_getNames($train->getParentsAndSelf());
		$this->assertEquals(array('train', 'videos', 'racine'), $names);
		
		// 1 requête qui correspond au find de la racine dans _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
	}
	
	public function testGetChildren(){
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		
		$dir = new Default_Model_DirectoryNestedSetTest();
		$dir->find(1);
		
		// 1 requête : le select
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
		
		$children = $dir->children;
		$names = $this->_getNames($children);
		$this->assertEquals(array('images', 'videos', 'others'), $names);
		
		// 1 requête : select des enfants
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll - 1;
		$this->assertEquals(1, $numberOfQueries);
		
		/////////////////////////
		
		$dir = new Default_Model_DirectoryNestedSetTest();
		$dir->find(2);
		
		// 1 requête : méthode find
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll - 2;
		$this->assertEquals(1, $numberOfQueries);
		
		$children = $dir->children;
		$names = $this->_getNames($children);
		$this->assertEquals(array('landscape', 'traffic'), $names);
		
		// 0 requête : utilisation de l'index
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll - 3;
		$this->assertEquals(0, $numberOfQueries);
		
		/////////////////////////
		
		$train = $this->_getBranch('train');
		$children = $train->children;
		$this->assertEmpty($children);
		$this->assertEquals('array', gettype($children));
	}
	
	public function testHasChildren(){
		$racine = $this->_getBranch('racine');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertTrue($racine->hasChildren());
		
		// 0 requête : utilisation de lft et rgt pour le calcul
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(0, $numberOfQueries);
		
		$landscape = $this->_getBranch('landscape');
		$this->assertFalse($landscape->hasChildren());
		
		// 1 requête : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
	}
	
	public function testRecursiveGetChildren(){
		$images = $this->_getBranch('images');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$children = $images->recursiveGetChildren();
		
		$names = $this->_getNames($children);
		$this->assertEquals(array('landscape', 'traffic'), $names);
		
		// 0 requête : utilisation de l'index
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(0, $numberOfQueries);
		
		/////////////////////////
		
		$racine = $this->_getBranch('racine');
		$children = $racine->recursiveGetChildren();
		
		$names = $this->_getNames($children);
		$this->assertEquals(array('images', 'landscape', 'traffic', 'videos', 'train', 'others'), $names);
		
		// 1 requête : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
		
		/////////////////////////
		
		$train = $this->_getBranch('train');
		$children = $train->recursiveGetChildren();
		$this->assertEmpty($children);
		$this->assertEquals('array', gettype($children));
	}
	
	public function testGetFirstChild(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertEquals('images', $racine->firstChild->name);

		$images = $this->_getBranch('images');
		$this->assertEquals('landscape', $images->firstChild->name);

		$train = $this->_getBranch('train');
		$this->assertNull($train->firstChild);
		
		// 2 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(2, $numberOfQueries);
	}
	
	public function testGetLastChild(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertEquals('others', $racine->lastChild->name);

		$images = $this->_getBranch('images');
		$this->assertEquals('traffic', $images->lastChild->name);

		$train = $this->_getBranch('train');
		$this->assertNull($train->lastChild);
		
		// 2 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(2, $numberOfQueries);
	}
	
	public function testGetNext(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertNull($racine->next);
		
		$images = $this->_getBranch('images');
		$this->assertEquals('videos', $images->next->name);
		
		$traffic = $this->_getBranch('traffic');
		$this->assertNull($traffic->next);
		
		// 2 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(2, $numberOfQueries);
	}
	
	public function testHasNext(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertFalse($racine->hasNext());
		
		$images = $this->_getBranch('images');
		$this->assertTrue($images->hasNext());
		
		$traffic = $this->_getBranch('traffic');
		$this->assertFalse($traffic->hasNext());
		
		// 2 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(2, $numberOfQueries);
	}
	
	public function testGetNextAll(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$next = $racine->getNextAll();
		$this->assertEmpty($next);
		$this->assertEquals('array', gettype($next));
		
		$images = $this->_getBranch('images');
		$names = $this->_getNames($images->getNextAll());
		$this->assertEquals(array('videos', 'others'), $names);
		
		$videos = $this->_getBranch('videos');
		$names = $this->_getNames($videos->getNextAll());
		$this->assertEquals(array('others'), $names);
		
		$traffic = $this->_getBranch('traffic');
		$next = $traffic->getNextAll();
		$this->assertEmpty($next);
		$this->assertEquals('array', gettype($next));
		
		// 3 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
	}
	
	public function testGetPrev(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertNull($racine->prev);
		
		$videos = $this->_getBranch('videos');
		$this->assertEquals('images', $videos->prev->name);
		
		$landscape = $this->_getBranch('landscape');
		$this->assertNull($landscape->prev);
		
		// 2 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(2, $numberOfQueries);
	}
	
	public function testHasPrev(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertFalse($racine->hasPrev());
		
		$videos = $this->_getBranch('videos');
		$this->assertTrue($videos->hasPrev());
		
		$landscape = $this->_getBranch('landscape');
		$this->assertFalse($landscape->hasPrev());
		
		// 2 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(2, $numberOfQueries);
	}
	
	public function testGetPrevAll(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$prev = $racine->getPrevAll();
		$this->assertEmpty($prev);
		$this->assertEquals('array', gettype($prev));
		
		$others = $this->_getBranch('others');
		$names = $this->_getNames($others->getPrevAll());
		$this->assertEquals(array('images', 'videos'), $names);
		
		$videos = $this->_getBranch('videos');
		$names = $this->_getNames($videos->getPrevAll());
		$this->assertEquals(array('images'), $names);
		
		$landscape = $this->_getBranch('landscape');
		$prev = $landscape->getPrevAll();
		$this->assertEmpty($prev);
		$this->assertEquals('array', gettype($prev));
		
		// 3 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
	}
	
	public function testGetSiblings(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$siblings = $racine->siblings;
		$this->assertEmpty($siblings);
		$this->assertEquals('array', gettype($siblings));
		
		$others = $this->_getBranch('others');
		$names = $this->_getNames($others->siblings);
		$this->assertEquals(array('images', 'videos'), $names);
		
		$videos = $this->_getBranch('videos');
		$names = $this->_getNames($videos->siblings);
		$this->assertEquals(array('images', 'others'), $names);
		
		$train = $this->_getBranch('train');
		$siblings = $train->siblings;
		$this->assertEmpty($siblings);
		$this->assertEquals('array', gettype($siblings));
		
		// 3 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
	}
	
	public function testHasSiblings(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$this->assertFalse($racine->hasSiblings());
		
		$others = $this->_getBranch('others');
		$this->assertTrue($others->hasSiblings());
		
		$videos = $this->_getBranch('videos');
		$this->assertTrue($videos->hasSiblings());
		
		$train = $this->_getBranch('train');
		$this->assertFalse($train->hasSiblings());
		
		// 3 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
	}
	
	public function testGetSiblingsAndSelf(){
		$racine = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$names = $this->_getNames($racine->getSiblingsAndSelf());
		$this->assertEquals(array('racine'), $names);
		
		$others = $this->_getBranch('others');
		$names = $this->_getNames($others->getSiblingsAndSelf());
		$this->assertEquals(array('images', 'videos', 'others'), $names);
		
		$videos = $this->_getBranch('videos');
		$names = $this->_getNames($videos->getSiblingsAndSelf());
		$this->assertEquals(array('images', 'videos', 'others'), $names);
		
		$train = $this->_getBranch('train');
		$names = $this->_getNames($train->getSiblingsAndSelf());
		$this->assertEquals(array('train'), $names);
		
		// 3 requêtes : find de _getBranch
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
	}
	
	public function testAppendNew(){
		$moved = new Default_Model_DirectoryNestedSetTest(array('name' => 'append'));
		$videos = $this->_getBranch('videos');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$videos->append($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
		
		// nouvelle branche
		$names = $this->_getNames($videos->children);
		$this->assertEquals(array('train', 'append'), $names);
		
		$this->assertEquals(11, $moved->lft);
		$this->assertEquals(12, $moved->rgt);
		
		// nouveau parent
		$videos = $this->_getBranch('videos');
		$this->assertEquals(8, $videos->lft);
		$this->assertEquals(13, $videos->rgt);
		
		$racine = $this->_getBranch('racine');
		$this->assertEquals(1, $racine->lft);
		$this->assertEquals(16, $racine->rgt);
	}
	
	public function testAppendMoveABranchWithChildrenToABranchWithChildren(){
		$moved = $this->_getBranch('videos');
		$newParent = $this->_getBranch('images');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newParent->append($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(6, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(7, $moved->lft);
		$this->assertEquals(10, $moved->rgt);
		$this->assertEquals($newParent->id, $moved->parent_id);
		
		$movedChild = $this->_getBranch('train');
		$this->assertEquals(8, $movedChild->lft);
		$this->assertEquals(9, $movedChild->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('images');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('landscape', 'traffic', 'videos'), $names);
		$this->assertEquals(2, $newParent->lft);
		$this->assertEquals(11, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('racine');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('images', 'others'), $names);
		$this->assertEquals(1, $oldParent->lft);
		$this->assertEquals(14, $oldParent->rgt);
	}
	
	public function testAppendMoveABranchWithChildrenToABranchWithoutChild(){
		$moved = $this->_getBranch('images');
		$newParent = $this->_getBranch('train');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newParent->append($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(6, $numberOfQueries);
		
		// branche déplacée
		$this->assertEquals(4, $moved->lft);
		$this->assertEquals(9, $moved->rgt);
		$this->assertEquals($newParent->id, $moved->parent_id);
		
		$movedChild = $this->_getBranch('landscape');
		$this->assertEquals(5, $movedChild->lft);
		$this->assertEquals(6, $movedChild->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('train');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('images'), $names);
		$this->assertEquals(3, $newParent->lft);
		$this->assertEquals(10, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('racine');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('videos', 'others'), $names);
		$this->assertEquals(1, $oldParent->lft);
		$this->assertEquals(14, $oldParent->rgt);
	}
	
	public function testAppendMoveABranchWithoutChildToABranchWithChildren(){
		$moved = $this->_getBranch('train');
		$newParent = $this->_getBranch('racine');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newParent->append($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(5, $numberOfQueries);
		
		// branche déplacée
		$this->assertEquals(12, $moved->lft);
		$this->assertEquals(13, $moved->rgt);
		$this->assertEquals($newParent->id, $moved->parent_id);
		
		// nouveau parent
		$newParent = $this->_getBranch('racine');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('images', 'videos', 'others', 'train'), $names);
		$this->assertEquals(1, $newParent->lft);
		$this->assertEquals(14, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('videos');
		$names = $this->_getNames($oldParent->children);
		$this->assertEmpty($names);
		$this->assertEquals(8, $oldParent->lft);
		$this->assertEquals(9, $oldParent->rgt);
	}
	
	public function testAppendMoveABranchWithoutChildToABranchWithoutChild(){
		$moved = $this->_getBranch('landscape');
		$newParent = $this->_getBranch('train');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newParent->append($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(5, $numberOfQueries);
		
		// branche déplacée
		$this->assertEquals(8, $moved->lft);
		$this->assertEquals(9, $moved->rgt);
		$this->assertEquals($newParent->id, $moved->parent_id);
		
		// nouveau parent
		$newParent = $this->_getBranch('train');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('landscape'), $names);
		$this->assertEquals(7, $newParent->lft);
		$this->assertEquals(10, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('images');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('traffic'), $names);
		$this->assertEquals(2, $oldParent->lft);
		$this->assertEquals(5, $oldParent->rgt);
	}
	
	public function testPrependNew(){
		$moved = new Default_Model_DirectoryNestedSetTest(array('name' => 'prepend'));
		$others = $this->_getBranch('others');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$others->prepend($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
		
		// nouvelle branche
		$others = $this->_getBranch('others');
		$names = $this->_getNames($others->children);
		$this->assertEquals(array('prepend'), $names);
		
		$this->assertEquals(13, $moved->lft);
		$this->assertEquals(14, $moved->rgt);
		
		// nouveau parent
		$others = $this->_getBranch('others');
		$this->assertEquals(12, $others->lft);
		$this->assertEquals(15, $others->rgt);
		
		$racine = $this->_getBranch('racine');
		$this->assertEquals(1, $racine->lft);
		$this->assertEquals(16, $racine->rgt);
	}
	
	public function testPrependMoveABranchWithChildrenToABranchWithChildren(){
		$moved = $this->_getBranch('videos');
		$newParent = $this->_getBranch('images');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newParent->prepend($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(6, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(3, $moved->lft);
		$this->assertEquals(6, $moved->rgt);
		$this->assertEquals($newParent->id, $moved->parent_id);
		
		$movedChild = $this->_getBranch('train');
		$this->assertEquals(4, $movedChild->lft);
		$this->assertEquals(5, $movedChild->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('images');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('videos', 'landscape', 'traffic'), $names);
		$this->assertEquals(2, $newParent->lft);
		$this->assertEquals(11, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('racine');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('images', 'others'), $names);
		$this->assertEquals(1, $oldParent->lft);
		$this->assertEquals(14, $oldParent->rgt);
	}
	
	public function testPrependMoveABranchWithChildrenToABranchWithoutChild(){
		$moved = $this->_getBranch('videos');
		$newParent = $this->_getBranch('traffic');

		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newParent->prepend($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(6, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(6, $moved->lft);
		$this->assertEquals(9, $moved->rgt);
		$this->assertEquals($newParent->id, $moved->parent_id);
		
		$movedChild = $this->_getBranch('train');
		$this->assertEquals(7, $movedChild->lft);
		$this->assertEquals(8, $movedChild->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('traffic');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('videos'), $names);
		$this->assertEquals(5, $newParent->lft);
		$this->assertEquals(10, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('racine');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('images', 'others'), $names);
		$this->assertEquals(1, $oldParent->lft);
		$this->assertEquals(14, $oldParent->rgt);
	}
	
	public function testPrependMoveABranchWithoutChildToABranchWithChildren(){
		$moved = $this->_getBranch('landscape');
		$newParent = $this->_getBranch('racine');

		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newParent->prepend($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(5, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(2, $moved->lft);
		$this->assertEquals(3, $moved->rgt);
		$this->assertEquals($newParent->id, $moved->parent_id);
		
		// nouveau parent
		$newParent = $this->_getBranch('racine');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('landscape', 'images', 'videos', 'others'), $names);
		$this->assertEquals(1, $newParent->lft);
		$this->assertEquals(14, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('images');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('traffic'), $names);
		$this->assertEquals(4, $oldParent->lft);
		$this->assertEquals(7, $oldParent->rgt);
	}
	
	public function testPrependMoveABranchWithoutChildToABranchWithoutChild(){
		$moved = $this->_getBranch('train');
		$newParent = $this->_getBranch('traffic');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newParent->prepend($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(5, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(6, $moved->lft);
		$this->assertEquals(7, $moved->rgt);
		$this->assertEquals($newParent->id, $moved->parent_id);
		
		// nouveau parent
		$newParent = $this->_getBranch('traffic');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('train'), $names);
		$this->assertEquals(5, $newParent->lft);
		$this->assertEquals(8, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('videos');
		$names = $this->_getNames($oldParent->children);
		$this->assertEmpty($names);
		$this->assertEquals(10, $oldParent->lft);
		$this->assertEquals(11, $oldParent->rgt);
	}
	
	public function testAfterNew(){
		$moved = new Default_Model_DirectoryNestedSetTest(array('name' => 'after'));
		$landscape = $this->_getBranch('landscape');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$landscape->after($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
		
		// nouvelle branche
		$this->assertEquals(5, $moved->lft);
		$this->assertEquals(6, $moved->rgt);
		$this->assertEquals(2, $moved->parent_id);
		
		// nouvelle parent
		$images = $this->_getBranch('images');
		$names = $this->_getNames($images->children);
		$this->assertEquals(array('landscape', 'after', 'traffic'), $names);
		
		// next
		$traffic = $this->_getBranch('traffic');
		$this->assertEquals(7, $traffic->lft);
		$this->assertEquals(8, $traffic->rgt);
	}
	
	public function testAfterMoveABranchWithChildrenToABranchWithChildren(){
		$moved = $this->_getBranch('images');
		$newSibling = $this->_getBranch('videos');

		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newSibling->after($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(6, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(6, $moved->lft);
		$this->assertEquals(11, $moved->rgt);
		
		$movedChild = $this->_getBranch('landscape');
		$this->assertEquals(7, $movedChild->lft);
		$this->assertEquals(8, $movedChild->rgt);
		
		// nouveau sibling
		$newSibling = $this->_getBranch('videos');
		$this->assertEquals(2, $newSibling->lft);
		$this->assertEquals(5, $newSibling->rgt);
		
		// nouveau parent, ancien parent
		$newParent = $this->_getBranch('racine');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('videos', 'images', 'others'), $names);
		$this->assertEquals(1, $newParent->lft);
		$this->assertEquals(14, $newParent->rgt);
	}
	
	public function testAfterMoveABranchWithChildrenToABranchWithoutChild(){
		$moved = $this->_getBranch('images');
		$newSibling = $this->_getBranch('train');

		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newSibling->after($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(6, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(5, $moved->lft);
		$this->assertEquals(10, $moved->rgt);
		
		$movedChild = $this->_getBranch('traffic');
		$this->assertEquals(8, $movedChild->lft);
		$this->assertEquals(9, $movedChild->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('videos');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('train', 'images'), $names);
		$this->assertEquals(2, $newParent->lft);
		$this->assertEquals(11, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('racine');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('videos', 'others'), $names);
		$this->assertEquals(1, $oldParent->lft);
		$this->assertEquals(14, $oldParent->rgt);
	}
	
	public function testAfterMoveABranchWithoutChildToABranchWithChildren(){
		$moved = $this->_getBranch('train');
		$newSibling = $this->_getBranch('images');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newSibling->after($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(5, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(8, $moved->lft);
		$this->assertEquals(9, $moved->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('racine');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('images', 'train', 'videos', 'others'), $names);
		$this->assertEquals(1, $newParent->lft);
		$this->assertEquals(14, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('videos');
		$this->assertEmpty($oldParent->children);
		$this->assertEquals(10, $oldParent->lft);
		$this->assertEquals(11, $oldParent->rgt);
	}
	
	public function testAfterMoveABranchWithoutChildToABranchWithoutChild(){
		$moved = $this->_getBranch('train');
		$newSibling = $this->_getBranch('landscape');

		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newSibling->after($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(5, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(5, $moved->lft);
		$this->assertEquals(6, $moved->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('images');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('landscape', 'train', 'traffic'), $names);
		$this->assertEquals(2, $newParent->lft);
		$this->assertEquals(9, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('videos');
		$this->assertEmpty($oldParent->children);
		$this->assertEquals(10, $oldParent->lft);
		$this->assertEquals(11, $oldParent->rgt);
	}
	
	public function testBeforeNew(){
		$moved = new Default_Model_DirectoryNestedSetTest(array('name' => 'before'));
		$train = $this->_getBranch('train');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$train->before($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(3, $numberOfQueries);
		
		// nouvelle branche
		$this->assertEquals(9, $moved->lft);
		$this->assertEquals(10, $moved->rgt);
		$this->assertEquals(3, $moved->parent_id);
		
		// nouvelle parent
		$videos = $this->_getBranch('videos');
		$names = $this->_getNames($videos->children);
		$this->assertEquals(array('before','train'), $names);
		
		// next
		$train = $this->_getBranch('train');
		$this->assertEquals(11, $train->lft);
		$this->assertEquals(12, $train->rgt);
	}
	
	public function testBeforeMoveABranchWithChildrenToABranchWithChildren(){
		$moved = $this->_getBranch('images');
		$newSibling = $this->_getBranch('videos');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newSibling->before($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(6, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(2, $moved->lft);
		$this->assertEquals(7, $moved->rgt);
		
		$movedChild = $this->_getBranch('traffic');
		$this->assertEquals(5, $movedChild->lft);
		$this->assertEquals(6, $movedChild->rgt);
		
		// nouveau parent, ancien parent
		$newParent = $this->_getBranch('racine');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('images', 'videos', 'others'), $names);
		$this->assertEquals(1, $newParent->lft);
		$this->assertEquals(14, $newParent->rgt);
	}
	
	public function testBeforeMoveABranchWithChildrenToABranchWithoutChild(){
		$moved = $this->_getBranch('images');
		$newSibling = $this->_getBranch('train');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newSibling->before($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(6, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(3, $moved->lft);
		$this->assertEquals(8, $moved->rgt);
		
		$movedChild = $this->_getBranch('traffic');
		$this->assertEquals(6, $movedChild->lft);
		$this->assertEquals(7, $movedChild->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('videos');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('images', 'train'), $names);
		$this->assertEquals(2, $newParent->lft);
		$this->assertEquals(11, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('racine');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('videos', 'others'), $names);
		$this->assertEquals(1, $oldParent->lft);
		$this->assertEquals(14, $oldParent->rgt);
	}
	
	public function testBeforeMoveABranchWithoutChildToABranchWithChildren(){
		$moved = $this->_getBranch('landscape');
		$newSibling = $this->_getBranch('videos');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newSibling->before($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(5, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(6, $moved->lft);
		$this->assertEquals(7, $moved->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('racine');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(array('images', 'landscape', 'videos', 'others'), $names);
		$this->assertEquals(1, $newParent->lft);
		$this->assertEquals(14, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('images');
		$names = $this->_getNames($oldParent->children);
		$this->assertEquals(array('traffic'), $names);
		$this->assertEquals(2, $oldParent->lft);
		$this->assertEquals(5, $oldParent->rgt);
	}
	
	public function testBeforeMoveABranchWithoutChildToABranchWithoutChild(){
		$moved = $this->_getBranch('train');
		$newSibling = $this->_getBranch('landscape');
		
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		$newSibling->before($moved);
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(5, $numberOfQueries);

		// branche déplacée
		$this->assertEquals(3, $moved->lft);
		$this->assertEquals(4, $moved->rgt);
		
		// nouveau parent
		$newParent = $this->_getBranch('images');
		$names = $this->_getNames($newParent->children);
		$this->assertEquals(2, $newParent->lft);
		$this->assertEquals(9, $newParent->rgt);
		
		// ancien parent
		$oldParent = $this->_getBranch('videos');
		$this->assertEmpty($oldParent->children);
		$this->assertEquals(10, $oldParent->lft);
		$this->assertEquals(11, $oldParent->rgt);
	}

	public function testMoveRootException(){
		$this->setExpectedException('Zest_Db_Exception');
		$racine = $this->_getBranch('racine');
		$images = $this->_getBranch('images');
		$images->append($racine);
	}
	
//	// la méthode __get est testée tout au long des autres tests
//	public function testMagicGet(){
//	}
	
	public function testFind(){
		$racine1 = $this->_getBranch('racine');
		$this->_beforeAll = count(self::$_db->getProfiler()->getQueryProfiles());
		
		$racine2 = $this->_getBranch('racine');
		
		// contrôle du fonctionnement de l'index
		$this->assertTrue($racine1 !== $racine2);
		
		// la méthode find n'utilise pas l'index car elle peuple l'objet sur lequel on la lance
		$numberOfQueries = count(self::$_db->getProfiler()->getQueryProfiles()) - $this->_beforeAll;
		$this->assertEquals(1, $numberOfQueries);
	}
	
	protected function _getNames($array){
		$names = array();
		foreach($array as $object){
			$names[] = $object->name;
		}
		return $names;
	}
	
	/**
	 * @param string $name
	 * @return Zest_Db_Orm_NestedSet_Object
	 */
	protected function _getBranch($name){
		$dir = new Default_Model_DirectoryNestedSetTest();
		$dir->find(1);
		
		$children = array_merge(array($dir), $dir->recursiveGetChildren());
		foreach($children as $child){
			if($child->name == $name){
				return $child;
			}
		}
	}
	
}

class Default_Model_DirectoryNestedSetTest extends Zest_Db_Orm_NestedSet_Object{
}

class Default_Model_DirectoryNestedSetTestMapper extends Zest_Db_Object_Mapper{
}

class Default_Model_DbTable_DirectoryNestedSetTest extends Zest_Db_Table{
	protected $_name = 'directory';
	protected $_adapterConfigName = 'nestedset';
}