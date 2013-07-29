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

	/**
	 * Provides callback for deleting a given instance of a card
	 * @param  string $id The card UID
	 */
	public function actionDelete($id)
	{
		if ($id == NULL)
			throw new CHttpException(400, 'An ID must be specified');

		$name = Yii::app()->db->createCommand("SELECT name FROM `cards` WHERE `uid` = :id")->bindParam(':id', $id)->queryScalar();

		if ($name === false)
			throw new CHttpException(400, 'No card with that ID exists');
		
		$json = json_decode(Yii::app()->db->createCommand("SELECT value FROM `configuration` WHERE `key` = :name")->bindParam(':name', $name)->queryScalar(), true);

		Yii::import($json['path'].'.*');

		$card = new $json['class']($id);

		if(!$card->delete())
			throw new CHttpException(400, 'There was an unknown error processing your request');
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
			if ($id == NULL)
				throw new CHttpException(400, 'An ID must be specified');

			$name = Yii::app()->db->createCommand("SELECT value FROM `configuration` LEFT JOIN `cards` ON `cards`.`name` = `configuration`.`key` WHERE `cards`.`uid` = :uid")->bindParam(':uid', $id)->queryScalar();

			if ($name == NULL)
				throw new CHttpException(400, 'No card with that ID exists');

			$name = json_decode($name, true);
			Yii::import($name['path'].'.*');

			$card = new $name['class']($id);

			$data = json_decode($card->getJSON(), true);
			$data['activeSize'] = $_POST['activeSize'];

			if ($card->update($data))
				return true;
		}

		throw new CHttpException(400, 'Missing POST data');
	}

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
			Yii::import($name['path'].'.*');
			$card = new $name['class']($id);
			$card->render();
		}
	}
}