<?php 
Yii::import('zii.widgets.CListView');
class ContentListView extends CListView
{
	public $beforeAjaxUpdate;
	public $afterAjaxUpdate;
	public $ajaxUpdateError;
	public $preview = NULL;
	public $content = NULL;

	/**
	 * Registers necessary client scripts.
	 */
	public function registerClientScript()
	{
		$id=$this->getId();

		if($this->ajaxUpdate===false)
			$ajaxUpdate=array();
		else
			$ajaxUpdate=array_unique(preg_split('/\s*,\s*/',$this->ajaxUpdate.','.$id,-1,PREG_SPLIT_NO_EMPTY));

		$options=array(
			'ajaxUpdate'=>$ajaxUpdate,
			'ajaxVar'=>$this->ajaxVar,
			'pagerClass'=>$this->pagerCssClass,
			'loadingClass'=>$this->loadingCssClass,
			'sorterClass'=>$this->sorterCssClass,
			'enableHistory'=>$this->enableHistory
		);

		if($this->ajaxUrl!==null)
			$options['url']=CHtml::normalizeUrl($this->ajaxUrl);

		if($this->updateSelector!==null)
			$options['updateSelector']=$this->updateSelector;
		foreach(array('beforeAjaxUpdate', 'afterAjaxUpdate', 'ajaxUpdateError') as $event)

		{
			if($this->$event!==null)
			{
				if($this->$event instanceof CJavaScriptExpression)
					$options[$event]=$this->$event;
				else
					$options[$event]=new CJavaScriptExpression($this->$event);
			}
		}

		$options=CJavaScript::encode($options);
		$cs=Yii::app()->getClientScript();

		$cs->registerCoreScript('bbq', CClientScript::POS_HEAD);

		$cs->registerScriptFile($this->baseScriptUrl.'/jquery.yiilistview.js',CClientScript::POS_END);
		$cs->registerScript(__CLASS__.'#'.$id,"$(document).ready(function() { $('#$id').yiiListView($options); });");
	}

	/**
	 * Renders the data item list.
	 */
	public function renderItems()
	{
		echo CHtml::openTag('div', array('class' => 'sidebar'));
			echo CHtml::openTag($this->itemsTagName,array('class'=>$this->itemsCssClass, 'id' => 'main'))."\n";
				echo CHtml::openTag('div', array('class' => 'content'));

					echo CHtml::openTag('div', array('class' => 'post post-header'));
						echo CHtml::tag('h6', array('class' => 'pull-left'), 'Posts');
						if (Yii::app()->user->role !== 7)
							echo CHtml::link(NULL, Yii::app()->createUrl('/dashboard/content/save'), array('class' => 'fa fa-plus pull-right'));
						echo CHtml::tag('div', array('class' => 'clearfix'), NULL);
					echo CHtml::closeTag('div');
					
				$data=$this->dataProvider->getData();

				if(($n=count($data))>0)
				{
					$owner=$this->getOwner();
					$viewFile=$owner->getViewFile($this->itemView);
					$j=0;
					foreach($data as $i=>$item)
					{
						$data=$this->viewData;
						$data['index']=$i;
						$data['data']=$item;
						$data['widget']=$this;
						$owner->renderFile($viewFile,$data);
						if($j++ < $n-1)
							echo $this->separator;
					}
				}
				else
					$this->renderEmptyText();

				echo CHtml::closeTag('div');
			echo CHtml::closeTag($this->itemsTagName);
			

		$this->renderPager();
		echo CHtml::closeTag('div');

		echo CHtml::openTag('div', array('class' => 'body-content preview-container'));
			$this->renderSorter();

			echo CHtml::openTag('div', array('class' => 'preview nano', 'id' => 'preview'));
				echo CHtml::openTag('div', array('class' => 'content'));
					$this->render('preview', array('model' => $this->preview));
				echo CHtml::closeTag('div');
			echo CHtml::closeTag('div');

			echo CHtml::openTag('div', array('class' => 'content-sidebar'));

				// Header
				echo CHtml::openTag("div", array('class' => 'comments-header'));
					echo CHtml::tag('span', array('class' => 'title pull-left'), Yii::t('Dashboard.main', 'Comments'));
					echo CHtml::tag('div', array('class' => 'clearfix'), NULL);
				echo CHtml::closeTag('div');

				Yii::app()->controller->widget('ext.cii.widgets.comments.CiiCommentWidget');

			echo CHtml::closeTag('div');
		echo CHtml::closeTag('div');
	}

	// Disable pagination in favor of infinite scrolling
	public function renderPager() {
		return false;
	}

	/**
	 * Renders the sorter
	 */
	public function renderSorter()
	{
		echo CHtml::openTag('div',array('class'=>$this->sorterCssClass))."\n";

		echo CHtml::openTag('form', array('class' => 'pure-form pull-left header-form header-form-content'));
			echo CHtml::tag('span', array('class' => 'fa fa-search pull-right icon-legend'), NULL);
			echo CHtml::textField(
	    		'Content[title]', 
	    		Cii::get(Cii::get($_GET, 'Content', array()), 'title'), 
	    		array(
	    			'id' => 'Content_title', 
	    			'name' => 'Content[title]',
	    			'class' => 'pull-right pure-input pure-search',
	    			'placeholder' => Yii::t('Dashboard.views', 'Search by title')
				)
	    	); 
	    echo CHtml::closeTag('form');


		if($this->dataProvider->getItemCount()<=0 || !$this->enableSorting || empty($this->sortableAttributes)) {
			echo CHtml::closeTag('div');
			return;
		}

		echo $this->sorterHeader===null ? Yii::t('zii','Sort by: ') : $this->sorterHeader;
		echo "<ul>\n";
			$sort=$this->dataProvider->getSort();
			foreach($this->sortableAttributes as $name=>$label)
			{
				echo "<li>";
				if(is_integer($name))
					echo $sort->link($label);
				else
					echo $sort->link($name,$label);
				echo "</li>\n";
			}
		echo "</ul>";

		echo $this->sorterFooter;

		echo CHtml::closeTag('div');
	}
}