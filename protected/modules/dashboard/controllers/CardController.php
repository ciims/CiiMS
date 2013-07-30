<?php

class CardController extends CiiDashboardController
{
	/**
	 * Disable layout rendering for this controller
	 * @var string $layout
	 */
	public $layout = NULL;

	/**
	 * Creates a new Dashboard card with the $id dashboard_card_{ID_HERE}
	 * $id is a randomly generated hash based upon the unique data of the card.json + name, and contains 2 key pieces of information
	 *     1) The class name
	 *     2) The path in.dot.format
	 *
	 * Once created, the unique instance is added to the user's specific dashboard. This is an Ajax specific request
	 * @param  string  $id  The unique id of the card as defined on creation of said card type
	 * @return bool
	 */
	public function actionAdd($id)
	{
		if ($id == NULL)
			throw new CHttpException(400, 'An ID must be specified');

		$name = Yii::app()->db->createCommand("SELECT value FROM `configuration` WHERE `key` = :id")->bindParam(':id', $id)->queryScalar();

		if ($name == NULL)
			throw new CHttpException(400, 'No card type exists with that ID');

		$name = json_decode($name, true);

		Yii::import($name['path'].'.*');

		$card = new $name['class'];
		$card->create($id);

		$data = json_decode($card->getJSON(), true);
		$data['id'] = $card->id;

		// Update the user's card information
		$meta = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'dashboard'));

		if ($meta == NULL)
		{
			$meta = new UserMetadata();
			$meta->key = 'dashboard';
			$meta->user_id = Yii::app()->user->id;
			$meta->value = array();
		}

		if (!is_array($meta->value))
			$order = json_decode($meta->value);
		else
			$order = $meta->value;

		$order[] = $card->id;
		$meta->value = json_encode($order);
		$meta->save();

		return $card->render();
	}

	public function actionUpdate($id)
	{
		$card = $this->getCardById($id);

		return $this->submitPost($card);
	}

	/**
	 * Provides callback for deleting a given instance of a card
	 * @param  string $id The card UID
	 */
	public function actionDelete($id)
	{
		$card = $this->getCardById($id);

		if(!$card->delete())
			throw new CHttpException(400, 'There was an unknown error processing your request');
		
		// Delete the user metadata object if the delete was successful
		$meta = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'dashboard'));

		$uids = json_decode($meta->value, true);
		unset($uids[$id]);
		$meta->value = json_encode($uids);
		return $meta->save();
		
	}

	/**
	 * Provides functionality for resizing any widget based upon it's ID
	 * NOTE, that this widget DOES NOT perform any validation. Validation is done client side.
	 * @param  string $id  The card ID
	 */
	public function actionResize($id)
	{
		if (Cii::get($_POST, 'activeSize'))
		{
			$card = $this->getCardById($id);

			$data = json_decode($card->getJSON(), true);
			$data['activeSize'] = $_POST['activeSize'];

			if ($card->update($data))
				return true;
		}

		throw new CHttpException(400, 'Missing POST data');
	}

	/**
	 * Retrieves cards for the dashboard
	 * @return bool true
	 */
	public function actionGetCards()
	{
		$meta = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'dashboard'));
		if ($meta == NULL)
			return true;

		$uids = json_decode($meta->value, true);

		foreach ($uids as $id)
		{
			$name = Yii::app()->db->createCommand("SELECT value FROM `configuration` LEFT JOIN `cards` ON `cards`.`name` = `configuration`.`key` WHERE `cards`.`uid` = :uid")->bindParam(':uid', $id)->queryScalar();
			$name = json_decode($name, true);

			// This seems to happen more than often
			// If for some reason we get a path that is blank, delete the card reference from the user
			if ($name['path'] == '') 
			{
				unset($uids[$id]);
				$meta->value = json_encode($uids);
				$meta->save();
				continue;
			}

			Yii::import($name['path'].'.*');
			$card = new $name['class']($id);
			$card->render();
		}

		return true;
	}

	/**
	 * Retrieves a card given a particular $id
	 * @return string $id
	 */
	private function getCardById($id)
	{
		if ($id == NULL)
			throw new CHttpException(400, 'An ID must be specified');

		$name = Yii::app()->db->createCommand("SELECT name FROM `cards` WHERE `uid` = :id")->bindParam(':id', $id)->queryScalar();

		if ($name === false)
			throw new CHttpException(400, 'No card with that ID exists');
		
		$json = json_decode(Yii::app()->db->createCommand("SELECT value FROM `configuration` WHERE `key` = :name")->bindParam(':name', $name)->queryScalar(), true);

		Yii::import($json['path'].'.*');

		return new $json['class']($id);
	}

	/**
	 * Generic handler for sacing $model data since the model is completely generic.
	 * @param  CiiSettingsModel $model The model we are working with
	 */
	private function submitPost(&$model)
	{
		if (Cii::get($_POST, get_class($model)) !== NULL)
		{
			$model->populate($_POST);

			if ($model->save())
				return true;
		}

		return false;
	}
}