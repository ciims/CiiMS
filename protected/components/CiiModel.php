<?
class CiiModel extends CActiveRecord
{
			
	public function parseMeta($model)
	{
		$items = array();
		if (!empty($model))
		{
			foreach ($model as $v)
			{
				if (isset($items[$v->key]))
				{
					$v->key = $v->key;
				}
			
				$items[$v->key] = array(
					'value'=>$v->value
					);
			}
		}
		return $items;
	}
}
?>
