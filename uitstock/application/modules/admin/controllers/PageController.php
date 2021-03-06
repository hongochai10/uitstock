<?php
class Admin_PageController extends ZendStock_Controller_Action {
	public $config;		
	public $request;
	public $templateMapper;
	public $themeMapper;
	public $pageMapper;
	public $privileges;
	public $privilegeTypeMapper;
	public $entry;
    
	public function init() {  
		 if (!isset($_SESSION['log']))
		 	$this->_redirect('admin/index/error');
		 			  			 
		 $this->privileges = $_SESSION['privilege'];		 
		 $flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 67) $flag = true; 
		 }
	 	 if (!$flag) 
			$this->_redirect('admin/index/error');		 
		 	
		 $this->templateMapper = new Cloud_Model_Template_CloudTemplateMapper();	  		
		 $this->themeMapper = new Cloud_Model_Theme_CloudThemeMapper(); 
		 $this->pageMapper = new Cloud_Model_Page_CloudPageMapper(); 			     	           
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
	
	public function listAction() {
		$this->view->headTitle($this->config['title']['page']);		
		$_SESSION['temp'] = $_SERVER['REQUEST_URI'];
		
		$componentMapper = new Cloud_Model_Component_CloudComponentMapper();
		$components = $componentMapper->fetchAllByFront();
		$component_1 = $components[0];										
		
		$component_id = $this->request->getParam('component');	
		
		if (null == $component_id) $c = $component_1->id;
		else $c = $component_id;

		$title = $this->request->getParam('title');
		if (null == $title) 
			$pages = $this->pageMapper->getPageByComponent($c);					    		
		else
			$pages = $this->pageMapper->searchPage($title, $c);

		$this->view->assign(array(
				'c' => $c,
				'components' => $components,
				'pages' => $pages,
				'button1' => $this->privilegeTypeMapper->getButton1ById($this->entry, 17),
		));										  		     		     		
	}  
	
	public function addAction() 
	{	
		 $flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 69) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 				
		$this->view->headTitle($this->config['title']['addPage']);
		
		$componentMapper = new Cloud_Model_Component_CloudComponentMapper();
		$components = $componentMapper->fetchAllByFront();		
		$componentMaxOrder = $this->pageMapper->getMaxOrder();		
				
		$form = new Cloud_Form_Admin_Page_Add(array(
							'components' => $components,
							'page' => $componentMaxOrder
		));				
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->request->getPost())) {			
				$values = $form->getValues();
				$page = new Cloud_Model_Page_CloudPage($values);
				$component_id = $values['component_id'];																	
				$this->pageMapper->savePage($page, $component_id);
				$this->view->message = 'Đã thêm page: ' . $page->getName();
			}
		}
		
		$this->view->form = $form;						
	}
	
	public function editAction() {		
		 $flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 70) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->view->headTitle($this->config['title']['editPage']); 
		    		
		if ($this->request->getParam('id') != null) {
			$id = $this->request->getParam('id');
			
			$componentMapper = new Cloud_Model_Component_CloudComponentMapper();
			$components = $componentMapper->fetchAll();	
			$currentPage = new Cloud_Model_Page_CloudPage();		 		
			$this->pageMapper->find($id, $currentPage);					
			
			$form = new Cloud_Form_Admin_Page_Edit(array(
			               'page' => $currentPage,
			               'components' => $components,		               
			));									
			
			if ($this->getRequest()->isPost()) {
				if ($form->isValid($this->request->getPost())) {			
					$page = new Cloud_Model_Page_CloudPage($form->getValues());																							
					$this->pageMapper->save($page);
					$this->view->message = 'Đã sửa page: ' . $currentPage->getName();
				}
			}
			
			$this->view->form = $form;	
		}							
	}
	
	public function publishAction()
	{
		 $flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 70) $flag = true; 
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
			if ($privilege['id'] == 70) $flag = true; 
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
			if ($privilege['id'] == 70) $flag = true; 
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
	
	public function deleteAction()
	{
		 $flag = false;		
		 foreach ($this->privileges as $privilege) {
			if ($privilege['id'] == 71) $flag = true; 
		 }
	 	 if (!$flag) {
			$this->_redirect('admin/index/error');
		 } 
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);		
						
		if ($this->request->getParam('listid') != null) {			
			$listid = $this->request->getParam('listid');
			$this->pageMapper->deleteAll($listid);
		}								
	}		
	
	public function autoSuggestionPageAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);	
				
		$title = $this->request->getParam('name');		
		$component_id = $this->request->getParam('component_id');
		$result = $this->pageMapper->autoSuggestionPage($title, $component_id);					
		if ($result) {			
			echo '<ul>';
			foreach ($result as $row) {
				echo '<li onClick="fill(\''.$row->title.'\');">'.$row->title.'</li>';	
			}
			echo '</ul>';
		} 
	}		
	
	public function getPageByComponentAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$componentId = $this->request->getParam('Id');	
		$pages = $this->pageMapper->getPageByComponent($componentId);
		
		echo '<input id="select_all" type="checkbox" />Check all<br/>';
		foreach ($pages as $page) {
			echo '<input name="select" type="checkbox" value="'.$page->id . '">' .$page->title . '<br/>';
		}
	}
	
	
	
}