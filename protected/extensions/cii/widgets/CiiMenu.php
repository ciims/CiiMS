<?php
Yii::import('zii.widgets.CMenu');
class CiiMenu extends CMenu
{
    /**
     * @var array
     * Default items to populate CiiMenu With
     */
    public $defaultItems = array(
        array('label' => 'Blog', 'url' => array('/blog'), 'active' => false),
        array('label' => 'Admin', 'url' => array('/admin'), 'active' => false),
    );
    
    /**
     * @var array
     * CiiMenu Items
     */
    public $CiiMenuItems = array();
    /**
     * Run function, this is what is called when we want to call the widget
     */
    public function run()
    {
        // Offer the option to allow CiiMenu to behave exactly like CMenu. If it's just an empty array however
        if (empty($this->items))
            $this->items = $this->getCiiMenu();
        
        parent::run();
    }
    
    /**
     * Retrieves the CiiMenuItems from the configuration. If the items are not populated, then it 
     * builds them out from CiiMenu::$defaultItems
     */
    private function getCiiMenu()
    {
        // Retrieve the item from cache since we're going to have to build this out manually
        $items = Yii::app()->cache->get('CiiMenuItems');
        if ($items === false)
        {
            // Get the menu items from Configuration
            $menuRoutes = Cii::get(Configuration::model()->findByAttributes(array('key' => 'menu')), 'value', NULL);
            
            // If the configuration is not provided, then set this to our defualt items
            if ($menuRoutes == NULL)
                $items = $this->defaultItems;
            else
            {
                $fullRoutes = explode('|', $menuRoutes);
                foreach ($fullRoutes as $route)
                {
                    if ($route == "")
                        continue;
                    $items[] = array('label' => ucwords(str_replace('-', ' ', $route)), 'url' => Yii::app()->createUrl('/' . $route), 'active' => false);
                }
            }
            Yii::app()->cache->set('CiiMenuItems', $items, 3600);
        }
        
        return $items;
    }
}
