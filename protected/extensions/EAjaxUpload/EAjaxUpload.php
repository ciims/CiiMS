<?php
/**
 * EAjaxUpload class file.
 * This extension is a wrapper of http://valums.com/ajax-upload/
 *
 * @author Vladimir Papaev <kosenka@gmail.com>
 * @version 0.1
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
        How to use:

        view:
		 $this->widget('ext.EAjaxUpload.EAjaxUpload',
                 array(
                       'id'=>'uploadFile',
                       'config'=>array(
                                       'action'=>'/controller/upload',
                                       'allowedExtensions'=>array("jpg"),//array("jpg","jpeg","gif","exe","mov" and etc...
                                       'sizeLimit'=>10*1024*1024,// maximum file size in bytes
                                       'minSizeLimit'=>10*1024*1024,// minimum file size in bytes
                                       //'onComplete'=>"js:function(id, fileName, responseJSON){ alert(fileName); }",
                                       //'messages'=>array(
                                       //                  'typeError'=>"{file} has invalid extension. Only {extensions} are allowed.",
                                       //                  'sizeError'=>"{file} is too large, maximum file size is {sizeLimit}.",
                                       //                  'minSizeError'=>"{file} is too small, minimum file size is {minSizeLimit}.",
                                       //                  'emptyError'=>"{file} is empty, please select files again without it.",
                                       //                  'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
                                       //                 ),
                                       //'showMessage'=>"js:function(message){ alert(message); }"
                                      )
                      ));

        controller:

	public function actionUpload()
	{
	        Yii::import("ext.EAjaxUpload.qqFileUploader");
	        
                $folder='upload/';// folder for uploaded files
                $allowedExtensions = array("jpg"),//array("jpg","jpeg","gif","exe","mov" and etc...
                $sizeLimit = 10 * 1024 * 1024;// maximum file size in bytes
                $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
                $result = $uploader->handleUpload($folder);
                $result=htmlspecialchars(json_encode($result), ENT_NOQUOTES);
                echo $result;// it's array
	}

*/
class EAjaxUpload extends CWidget
{
        public $id="fileUploader";
	public $postParams=array();
	public $config=array();
	public $css=null;
        
        public function run()
        {
		if(empty($this->config['action']))
		{
		      throw new CException('EAjaxUpload: param "action" cannot be empty.');
                }

		if(empty($this->config['allowedExtensions']))
		{
		      throw new CException('EAjaxUpload: param "allowedExtensions" cannot be empty.');
                }

		if(empty($this->config['sizeLimit']))
		{
		      throw new CException('EAjaxUpload: param "sizeLimit" cannot be empty.');
                }

                unset($this->config['element']);

                echo '<div id="'.$this->id.'"><noscript><p>Please enable JavaScript to use file uploader.</p></noscript></div>';
		$assets = dirname(__FILE__).'/assets';
                $baseUrl = Yii::app()->assetManager->publish($assets);

		Yii::app()->clientScript->registerScriptFile($baseUrl . '/fileuploader.js', CClientScript::POS_HEAD);

                $this->css=(!empty($this->css))?$this->css:$baseUrl.'/fileuploader.css';
                Yii::app()->clientScript->registerCssFile($this->css);

		$postParams = array('PHPSESSID'=>session_id(),'YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken);
		if(isset($this->postParams))
		{
				$postParams = array_merge($postParams, $this->postParams);
		}

		$config = array(
                                'element'=>'js:document.getElementById("'.$this->id.'")',
                                'debug'=>false,
                                'multiple'=>false
                               );
		$config = array_merge($config, $this->config);
		$config['params']=$postParams;
		$config = CJavaScript::encode($config);
                Yii::app()->getClientScript()->registerScript("FileUploader_".$this->id, "var FileUploader_".$this->id." = new qq.FileUploader($config); ",CClientScript::POS_LOAD);
	}


}