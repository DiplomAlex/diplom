<?php

class Shop_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    
    const MENU_TOP = 'top';
    const MENU_BREADCRUMBS = 'breadcrumbs';
    const MENU_BOTTOM = 'bottom';
    const MENU_LEFT = 'left';
    
    protected $_menuTopPages = NULL;
    protected $_menuBottomPages = NULL;
    protected $_menuBreadcrumbsPages = NULL;
    protected $_menuLeftPages = NULL;
    
    protected static $_filterCCTD = NULL;
    
    /**
     * @param string $type is one of top|bottom|breadcrumbs
     * @return string 
     */
    public function menu($type = NULL)
    {
        if ($type === NULL) {
            return $this;
        }
        else if (in_array($type, array(self::MENU_TOP, self::MENU_BREADCRUMBS, self::MENU_BOTTOM, self::MENU_LEFT))) {
            return $this->{'renderMenu'.ucfirst($type)}();
        }
        else {
            $this->_throwException('wrong $type in method '.__FUNCTION__);
        }
    }

    
    
    /**
     * @param string $msg
     * @throws Zend_View_Exception
     */
    protected function _throwException($msg)
    {
        throw new Zend_View_Exception(__CLASS__.' throws: '.$msg);
    }
    
    
    
    public function __call($method, $params)
    {
        /* 7 - length of "addMenu", "setMenu", "getMenu",
         * 5 - length of "Pages" 
         * so methods can be used: addMenuNamePages, setMenuNamePages, getMenuNamePages, renderMenuNamePages 
         * */
        $start7 = substr($method, 0, 7);
        $end5 = substr($method, strlen($method) - 5);
        $end4 = substr($method, strlen($method) - 4);
        $menuName_Pages = substr($method, 7, strlen($method) - 5 - 7);
        $menuName_Page = substr($method, 7, strlen($method) - 4 - 7);
        
        /* 10 - length of "renderMenu" to use methods "renderMenuName" */
        $start10 = substr($method, 0, 10);
        $menuName_Render = substr($method, 10);
        
        if (($start7 == 'addMenu') AND ($end5 == 'Pages')) {
            $result = $this->_addMenuPages($menuName_Pages, $params[0]);
        }
        else if (($start7 == 'addMenu') AND ($end4 == 'Page')) {
            $result = $this->_addMenuPage($menuName_Page, $params[0]);            
        }
        else if (($start7 == 'setMenu') AND ($end5 == 'Pages')) {
            $result = $this->_setMenuPages($menuName_Pages, $params[0]);
        }
        else if (($start7 == 'setMenu') AND ($end4 == 'Page')) {
            $result = $this->_setMenuPage($menuName_Page, $params[0], $params[1]);
        }
        else if ($start7 == 'getMenu') {
            $result = $this->_getMenuPages($menuName_Pages);
        }
        else if ($start10 == 'renderMenu') {
            $result = $this->_renderMenu($menuName_Render);
        }        
        
        return $result;
    }
    
    
    protected function _filterCamelCaseToDashLower($value)
    {
        if (self::$_filterCCTD === NULL) {
            self::$_filterCCTD = new Zend_Filter_Word_CamelCaseToDash;
        }
        $result = strtolower(self::$_filterCCTD->filter($value));        
        return $result;
    }
    
    
    /**
     * menu rendering
     * @param
     * @return string html
     */
    protected function _renderMenu($menuName)
    {
        $this->view->menuPages = new Zend_Navigation($this->{'getMenu'.$menuName.'Pages'}());
        $html = $this->view->render('menu/'.$this->_filterCamelCaseToDashLower($menuName).'.phtml');
        return $html;
    }
    
    /**
     * @param string $menuName
     * @return array
     */
    protected function _getMenuPages($menuName)
    {
        $property = '_menu'.$menuName.'Pages';
        if ( ! $this->{$property}) {
            $this->{$property} = $this->{'_getDefaultMenu'.$menuName.'Pages'}();
        }
        return $this->{$property};
    }
    
    /**
     * @param string $menuName
     * @param array
     * @return $this
     */
    protected function _setMenuPages($menuName, array $pages)
    {
        $this->{'_menu'.$menuName.'Pages'} = $pages;
        return $this;
    }
    
    /**
     * @param string $menuName
     * @param string $index
     * @param array $page
     * @return $this
     */
    protected function _setMenuPage($menuName, $index, array $page)
    {
        $property = '_menu'.$menuName.'Pages';
        if (array_key_exists($index, $this->{$property})) {
            $this->{$property}[$index] = $page;
        }
        else {
            $this->_throwException(__FUNCTION__.': no such index "'.$index.'" in $this->'.$property);
        }
        return $this;
    } 
    
    /**
     * @param string $menuName
     * @param array
     * @return $this
     */    
    protected function _addMenuPages($menuName, array $pages)
    {
        $property = '_menu'.$menuName.'Pages';
        $currPages = $this->_getMenuPages($menuName);
        foreach ($pages as $page) {
            $currPages []= $page;
        }
        $this->{$property} = $currPages;
        return $this;
    }

    /**
     * @param string $menuName
     * @param array
     * @return $this
     */    
    protected function _addMenuPage($menuName, array $page)
    {
        $property = '_menu'.$menuName.'Pages';
        $pages = $this->_getMenuPages($menuName);
        $pages[]= $page;
        $this->{$property} = $pages;
        return $this;
    }
        
        
    protected function _getDefaultMenuTopPages()
    {
      
        $pages = array(
            array(
                'label' => $this->view->translate('Главная'),
                'route' => 'lab-index',
				'params' => array(
					'seo_id' => 'glav'
				)
            ),
        );  
        return $pages;
    }


    protected function _getDefaultMenuBottomPages()
    {
        $pages = array(                    
        );
        return $pages;
    }
    
    
    protected function _getDefaultMenuLeftPages()
    {
        $pages = array();
        return $pages;
    }
    
    
    protected function _getDefaultMenuBreadcrumbsPages()
    {
        $pages = array(
            array(
                'label' => $this->view->translate('Главная'),
                'route' => 'lab-index',
            ),        
        );
        return $pages;
    }
    
        
}
