<?php

class DefaultController extends CiiDashboardController
{
    /**
     * Index action to render dashboard + cards
     */
	public function actionIndex()
	{
		$this->render('index', array('cards' => $this->getCards()));
	}

    /**
     * Retrieves all cards in a particular category
     * @param  string $id The category id
     */
    public function actionGetCardsByCategory($id=NULL)
    {
    	if ($id === NULL)
    		throw new CHttpException(400,  Yii::t('Dashboard.main', 'Missing category id'));

    	$categories = Yii::app()->cache->get('cards_in_category');

    	if ($categories === false)
    	{
    		$this->getCards();
    		$categories = Yii::app()->cache->get('cards_in_category');
    	}

    	$cards = $categories[$id];
    	$elements = $elementOptions = array();

        // TODO: Fix multiple select
    	//$elementOptions['multiple'] = 'multiple';
    	foreach ($cards as $k=>$card)
    	{
    		$asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias($card['path']), true, -1, YII_DEBUG);
    		$elements[] = $k;
			$elementOptions['options'][] = array(
				'value' => $k, 
				'data-img-src' => Yii::app()->getBaseUrl(true) . $asset .'/default.png'
			);
    			
    	}

    	$this->beginWidget('ext.cii.widgets.CiiActiveForm', array(
    		'htmlOptions' => array(
				'class' => 'pure-form pure-form-aligned item-selection-form'
			)
    	));
	    	echo CHtml::openTag('div', array('class' => 'pure-form-group', 'style' => 'padding-bottom: 20px'));
	    	echo CHtml::link( Yii::t('Dashboard.main', 'Add to Dashboard'), '#', array('id' => 'add-cards-button', 'class' => 'pure-button pure-button-link pure-button-primary pull-right pure-button-small', 'style' => 'position: absolute; top: 15px; right: 3%;'));
			echo CHtml::tag('legend', array(), $id);
				echo CHtml::dropDownList('card', NULL, $elements, $elementOptions);
			echo CHtml::closeTag('div');
		$this->endWidget();
    }

    /**
     * Retrieves the available system cards
     * @return array
     */
    private function getCards()
    {
    	$retCards = Yii::app()->cache->get('dashboard_cards_available');

    	if ($retCards === false)
    	{
	    	$cards = Yii::app()->db->createCommand("SELECT `key`, value FROM `configuration` WHERE `key` LIKE 'dashboard_card%';")->queryAll();
	    	$retCards = array();
	    	$cardsInCategory = array();

	    	foreach ($cards as $card)
	    	{
	    		$oldCard = $card;
	    		$card = json_decode($card['value'], true);
	    		$data = json_decode(file_get_contents(Yii::getPathOfAlias($card['path']) . DIRECTORY_SEPARATOR . 'card.json'), true);
	    		$cardsInCategory[$data['category']][$oldCard['key']] = CMap::mergeArray($card, $data);
	    		$retCards[$data['category']] = array('url' => '#' . $data['category'], 'label' => $data['category'], 'itemOptions' => array('class' => $this->getCategoryIcon($data['category'])));
	    	}
            
	    	Yii::app()->cache->set('dashboard_cards_available', $retCards);
	    	Yii::app()->cache->set('cards_in_category', $cardsInCategory);
	    }


    	return array('available_cards' => $retCards);
    }

    /**
     * Retrieves the appropriate display icon for a given category
     * @param  string  $category  The category name
     * @return string             The FontAwesome class name
     */
    private function getCategoryIcon($category)
    {
    	switch ($category)
    	{
    		case "Default":
                return "icon-gears";
            case "Social":
                return "icon-twitter";
    		default:
    			return "icon-gears";
    	}
    }

}
