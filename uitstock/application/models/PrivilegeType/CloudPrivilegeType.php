<?php
	/**
	 * Class        : Privilege Type Model
	 * Description  :
	 * Author       : Vita - Nguyen Ngoc Linh
	 * Student ID   : 07520194
	 * Faculty      : IS
	 */
	class Cloud_Model_PrivilegeType_CloudPrivilegeType
	{
		protected $id;
		protected $name;
		protected $description;
		protected $published;									
		protected $button1;
		protected $button2;
		
	public function __construct(array $options = null)
		{
			if (is_array($options)) {
				$this->setOptions($options);
			}
		}
	
		public function __set($name, $value)
		{
			$method = 'set' . $name;
			if (('mapper' == $name) || !method_exists($this, $method)) {
				throw new Exception('Invalid widget property');
			}
			$this->$method($value);
		}
		
		public function __get($name)
		{
			$method = 'get' . $name;
			if (('mapper' == $name) || !method_exists($this, $method)) {
				throw new Exception('Invalid widget property');
			}
			return $this->$method();	
		}
		
		public function setOptions(array $options)
		{
			$methods = get_class_methods($this);
			foreach ($options as $key => $value) {
				$method = 'set' . ucfirst($key);
				if (in_array($method, $methods)) {				 
					$this->$method($value);
				}
			}
			return $this;
		}
		
			/**
		 * @return the $id
		 */
		public function getId() {
			return $this->id;
		}
	
			/**
		 * @return the $name
		 */
		public function getName() {
			return $this->name;
		}
	
			/**
		 * @return the $description
		 */
		public function getDescription() {
			return $this->description;
		}
	
			/**
		 * @return the $published
		 */
		public function getPublished() {
			return $this->published;
		}
	
			/**
		 * @return the $button1
		 */
		public function getButton1() {
			return $this->button1;
		}
	
			/**
		 * @return the $button2
		 */
		public function getButton2() {
			return $this->button2;
		}
	
			/**
		 * @param $id the $id to set
		 */
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
	
			/**
		 * @param $name the $name to set
		 */
		public function setName($name) {
			$this->name = $name;
			return $this;
		}
	
			/**
		 * @param $description the $description to set
		 */
		public function setDescription($description) {
			$this->description = $description;
			return $this;
		}
	
			/**
		 * @param $published the $published to set
		 */
		public function setPublished($published) {
			$this->published = $published;
			return $this;
		}
	
			/**
		 * @param $button1 the $button1 to set
		 */
		public function setButton1($button1) {
			$this->button1 = $button1;
			return $this;
		}
	
			/**
		 * @param $button2 the $button2 to set
		 */
		public function setButton2($button2) {
			$this->button2 = $button2;
			return $this;
		}															
	}			