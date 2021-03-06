<?php
class Admin_ArticleController extends ZendStock_Controller_Action {
	public $config;		
	public $request;
	public $templateMapper;
	public $themeMapper;
	public $articleMapper;
	public $contentCategoryMapper;
	public $privileges;
	public $privilegeTypeMapper;
	public $entry;
    
	public function init() {  		
		 if (!isset($_SESSION['log']))
		 	$this->_redirect('admin/index/error');
		
		 $this->privileges = $_SESSION['privilege'];		 
		 $flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 25) $flag = true; 
		 }
		 
	 	 if (!$flag) 
			$this->_redirect('admin/index/error');
		  		 			
		  	  		
		 $this->templateMapper = new Cloud_Model_Template_CloudTemplateMapper();	  		
		 $this->themeMapper = new Cloud_Model_Theme_CloudThemeMapper(); 
		 $this->articleMapper = new Cloud_Model_Article_CloudArticleMapper();		
		 $this->contentCategoryMapper = new Cloud_Model_ContentCategory_CloudContentCategoryMapper();			     	           
	     $dirTemplate = $this->templateMapper->getTemplateDefault(1); 
		 $dirTheme = $this->themeMapper->getThemeDefault(1);		     	           
	     $this->config = $this->createLayout($dirTemplate, $dirTheme);	    
	        	    				 
		 $this->request = $this->getRequest();		 
		 		 
		 $this->privilegeTypeMapper = new Cloud_Model_PrivilegeType_CloudPrivilegeTypeMapper();
		 $this->view->privileges = $this->privileges;	
		 
		 $this->entry = "";
		 foreach ($this->privileges as $privilege) {
			$this->entry = $this->entry . "," . $privilege['id']; 
		 }
		 $this->entry = substr($this->entry, 1);
		 
		 $this->view->assign(array(	    	
		 	'userMapper' => new Cloud_Model_User_CloudUserMapper(),
		 	'session' => new Cloud_Model_UserSession_CloudUserSessionMapper(),	    	   		    
	    ));		 
	}	
	
	public function listCategoryAction()
	{		
		$flag = false;		
		foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 30) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->view->headTitle($this->config['title']['category']);	
		$_SESSION['temp'] = $_SERVER['REQUEST_URI'];	

		$name = $this->request->getParam('name');
		$page = $this->_getParam('page', 1);			

		if (null == $name) {
			$parents = $this->contentCategoryMapper->fetchParentByPage($page);
			$subs = $this->contentCategoryMapper->fetchAllSub();
		} else {
			$parents = $this->contentCategoryMapper->searchCat($name);
		}
		 		 	
	    $this->view->assign(array(
	    		'parents' => $parents,
	    		'subs' => $subs,
	            'button1' => $this->privilegeTypeMapper->getButton1ById($this->entry, 10),	            
	    ));
	}
	
	public function viewCatAction()
	{
		$flag = false;		
		foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 30) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->view->headTitle($this->config['title']['viewCategory']);
		
		if (null != $this->request->getParam('id')) {
			$id = $this->request->getParam('id');
			$currentCategory = new Cloud_Model_ContentCategory_CloudContentCategory();
			$this->contentCategoryMapper->find($id, $currentCategory);
			
			$sub = $this->contentCategoryMapper->getSubNameById($currentCategory->parent_id);
			if (null != $sub->name) $sub = $sub->name;
								
			$this->view->assign(array(			
	    		'category' => $currentCategory,
	    		'sub' => $sub,
			    'button2' => $this->privilegeTypeMapper->getButton2ById($this->entry, 10),
	    	));
		}		
	}
	
	public function addCatAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 32) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->view->headTitle($this->config['title']['addCategory']);		  
	    
	    $form = new Cloud_Form_Admin_ContentCategory_Add(array(
			'parents' => $this->contentCategoryMapper->fetchAllParent(),						
		));

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->request->getPost())) {			
				$values = $form->getValues();
				$category = new Cloud_Model_ContentCategory_CloudContentCategory($values);
				$newId = $this->contentCategoryMapper->save($category);
				$this->contentCategoryMapper->updateAlias($newId, $values['name']);																	
				$this->view->message = 'Đã thêm loại tin: ' . $values['name'];
			}
		}
		
		$this->view->form = $form;
	}
	
	public function editCatAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 33) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->view->headTitle($this->config['title']['editCategory']);

		if (null != $this->request->getParam('id')) {
			$id = $this->request->getParam('id');
			$currentCategory = new Cloud_Model_ContentCategory_CloudContentCategory();
			$this->contentCategoryMapper->find($id, $currentCategory);
		
			$form = new Cloud_Form_Admin_ContentCategory_Edit(array(
			  'category' => $currentCategory,		
			  'parents' => $this->contentCategoryMapper->fetchAllParent(),				
			));
			
			if ($this->getRequest()->isPost()) {
				if ($form->isValid($this->request->getPost())) {			
					$values = $form->getValues();
					$category = new Cloud_Model_ContentCategory_CloudContentCategory($values);
					$this->contentCategoryMapper->save($category);
					$this->contentCategoryMapper->updateAlias($values['id'], $values['name']);																	
					$this->view->message = 'Đã sửa loại tin: ' . $values['name'];
				}
			}
			
			$this->view->form = $form;
		}		
	}
	
	public function deleteCatAction()
	{
		 $flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 34) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if (null != $this->request->getParam('id')) {			
			$id = $this->request->getParam('id');				
			$this->contentCategoryMapper->delete($id);
		}
	}
		
	public function publishAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 33) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
				 
		if ($this->request->getParam('listid') != null) {			
			$listid = explode(",", $this->request->getParam('listid'));
			$mapper = $this->request->getParam('mapper');
			$dbTable = $this->$mapper->getDbTable();			
			Cloud_Model_Utli_CloudUtli::setPublish($dbTable, $listid);			
		}	
		else 
			echo 'error';							
	}
	
	public function unpublishAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 33) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->request->getParam('listid') != null) {				 			
			$listid = explode(",", $this->request->getParam('listid'));					 			
			$mapper = $this->request->getParam('mapper');
			$dbTable = $this->$mapper->getDbTable();
			Cloud_Model_Utli_CloudUtli::setUnPublish($dbTable, $listid);	
		}		
		else 
			echo 'error';						
	}	
	
	public function setPublishAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 33) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->request->getParam('id') != null) {							
			$id = $this->request->getParam('id');		 
			$mapper = $this->request->getParam('mapper');
			$dbTable = $this->$mapper->getDbTable();
			Cloud_Model_Utli_CloudUtli::setPublishAjax($dbTable, $id);															 						
		}		
		else 
			echo 'error';
	}
	
	public function autoSuggestionCatAction()
	{
		$flag = false;		
		foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 30) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);	
				
		$name = $this->request->getParam('name');				
		$result = $this->contentCategoryMapper->autoSuggestionCat($name);
		if ($result) {			
			echo '<ul>';
			foreach ($result as $row) {
				echo '<li onClick="fill(\''.$row->name.'\');">'.$row->name.'</li>';	
			}
			echo '</ul>';
		} 
	}	
	
	public function listArticleAction()
	{
		$this->view->headTitle($this->config['title']['article']);	
		$_SESSION['temp'] = $_SERVER['REQUEST_URI'];	
		
		$page = $this->_getParam('page', 1);
		
		$title = $this->request->getParam('title');
		$id = $this->request->getParam('id');
		$pid = $this->request->getParam('pid');
		if (null == $title && null == $pid && null == $id) 
			$articles = $this->articleMapper->fetchArticleByPage($page);					
		if (null != $id) 
			$articles = $this->articleMapper->fetchArticleById($this->request->getParam('id'));	
		if (null != $pid) 
			$articles = $this->articleMapper->fetchArticleBySub($this->request->getParam('pid'));
		if (null != $title) 
			$articles = $this->articleMapper->searchArticle($title);								
				
		$this->view->assign(array(				    		
	    		'articles' => $articles,
		        'button1' => $this->privilegeTypeMapper->getButton1ById($this->entry, 9),			    
	    	));	
	}
	
	public function viewArticleAction()
	{
		$this->view->headTitle($this->config['title']['viewArticle']);
		
		if (null != $this->request->getParam('id')) {
			$id = $this->request->getParam('id');
			$article = $this->articleMapper->getArticleById($id);

			$entries = $this->articleMapper->fetchAll();
			
			$this->view->assign(array(			
	    		'entries' => $entries,
	    		'article' => $article,		
			    'button2' => $this->privilegeTypeMapper->getButton2ById($this->entry, 9),	    
	    	));			
		}		
	}
	
	public function addArticleAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 27) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->view->headTitle($this->config['title']['addArticle']);		  
	    
	    $form = new Cloud_Form_Admin_Article_Add(array(
	    	'categories' => $this->contentCategoryMapper->fetchAllSub(),
	    ));	    
	    
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->request->getPost())) {			
				$values = $form->getValues();	
				$article = new Cloud_Model_Article_CloudArticle($values);
				$newId = $this->articleMapper->save($article);
				$this->articleMapper->updateImage($newId, $values['path']);
				$this->articleMapper->updateAlias($newId, $values['title']);
				$this->articleMapper->updateRelative($newId, $values['listid_relative']);																					
				$this->view->message = 'Đã thêm tin: ' . $values['title'];
			}
		}
		$this->view->form = $form;	
	}
	
	public function editArticleAction()
	{
		 $flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 28) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->view->headTitle($this->config['title']['editArticle']);

		if (null != $this->request->getParam('id')) {
			$id = $this->request->getParam('id');
			$currentArticle = new Cloud_Model_Article_CloudArticle();
			$this->articleMapper->find($id, $currentArticle);						
		
			$form = new Cloud_Form_Admin_Article_Edit(array(
				'article' => $currentArticle,
			  	'categories' => $this->contentCategoryMapper->fetchAllSub(),
			    'entries' => $this->articleMapper->fetchAll(),							
			));
			
			if ($this->getRequest()->isPost()) {
				if ($form->isValid($this->request->getPost())) {								
					$values = $form->getValues();
					$id = $values['id'];	
					$image = $currentArticle->getImage();
					$article = new Cloud_Model_Article_CloudArticle($values);
					$this->articleMapper->save($article);
					if ($values['up'] == 1)
						$this->articleMapper->updateImage($id, $values['path']);
					else
						$this->articleMapper->updateImage2($id, $image, $values['path'], $values['fileName']);
					$this->articleMapper->updateAlias($id, $values['title']);
					$this->articleMapper->updateRelative($id, $values['listid_relative']);																					
					$this->view->message = 'Đã sửa tin: ' . $values['title'];
				}
			}
			
			$this->view->form = $form;
		}		
	}
	
	public function setImportantAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 28) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 }
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);										
							
		$listid = $this->request->getParam('listid');							
			
		$article = new Cloud_Model_Article_CloudArticle();		 		
		$this->articleMapper->find($listid, $article);	
							
		if (null == $article) echo 'error';
		else {
			$this->articleMapper->setImportant($listid);
		}
	}
	
	public function setNormalAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 28) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 }
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);										
							
		$listid = $this->request->getParam('listid');							
			
		$article = new Cloud_Model_Article_CloudArticle();		 		
		$this->articleMapper->find($listid, $article);	
							
		if (null == $article) echo 'error';
		else {
			$this->articleMapper->setNormal($listid);
		}
	}
	
	public function deleteArticleAction()
	{
		$flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 29) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 }
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if (null != $this->request->getParam('listid')) {			
			$listid = $this->request->getParam('listid');				
			$this->articleMapper->delete($listid);
		}
	}
	
	public function autoSuggestionArticleAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);	
				
		$title = $this->request->getParam('name');				
		$result = $this->articleMapper->autoSuggestionArticle($title);
		if ($result) {			
			echo '<ul>';
			foreach ($result as $row) {
				echo '<li onClick="fill(\''.$row->title.'\');">'.$row->title.'</li>';	
			}
			echo '</ul>';
		} 
	}
			
}