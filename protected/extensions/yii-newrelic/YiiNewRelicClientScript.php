<?php

/**
 * YiiNewRelicClientScript
 *
 * @author    Paul Lowndes <github@gtcode.com>
 * @author    GTCode
 * @link      http://www.GTCode.com/
 * @package   YiiNewRelic
 * @version   0.01a
 * @category  ext*
 *
 * This class is designed for use with YiiNewRelic.  Please see that class for
 * more information.
 *
 * @see {@link http://newrelic.com/about About New Relic}
 * @see {@link https://newrelic.com/docs/php/the-php-api New Relic PHP API}
 */
class YiiNewRelicClientScript extends CClientScript
{
	/**
	 * Reimplementation of CClientScript::render() that adds a call to a new
	 * method renderNewRelicTimingHeader() and renderNewRelicTimingFooter().
	 */
	public function render(&$output)
	{
		if (!$this->hasScripts)
		{
			if ($this->enableJavaScript)
			{
				$this->renderNewRelicTimingHeader($output);
				$this->renderNewRelicTimingFooter($output);
			}

			return;
		}

		$this->renderCoreScripts();

		if (!empty($this->scriptMap))
			$this->remapScripts();

		$this->unifyScripts();
		$this->renderHead($output);

		if ($this->enableJavaScript)
		{
			$this->renderNewRelicTimingHeader($output);
			$this->renderBodyBegin($output);
			$this->renderBodyEnd($output);
			$this->renderNewRelicTimingFooter($output);
		}
	}

	/**
	 * This method first attempts to insert the New Relic timing header directly
	 * after the <meta http-equiv> tag.  If not found, it inserts it before the
	 * <title> or closing </head> tag.
	 */
	public function renderNewRelicTimingHeader(&$output)
	{
		$html = NULL;

		try 
		{
			$html = Yii::app()->newRelic->getBrowserTimingHeader();
		} 
		catch (Exception $e) 
		{
			return;
		}

		if (!$html) 
			return;
		
		$count = 0;

		$output = preg_replace('/(<\s*meta\s*http-equiv\s*=\s*("|\')content-type\b[^>]*>)/is', '$1<###newRelicMetaReplace###>', $output, 1, $count);
		if ($count)
		{
			$output = str_replace('<###newRelicMetaReplace###>', $html, $output);
			return;
		}

		$count = 0;
		$output = preg_replace('/(<title\b[^>]*>|<\\/head\s*>)/is', '<###head###>$1', $output, 1, $count);

		if ($count)
			$output = str_replace('<###head###>', $html, $output);
		else
			$output = $html . $output;
		
	}

	/**
	 * This method renders the New Relic timing footer directly before the page
	 * closing </body> tag.
	 */
	public function renderNewRelicTimingFooter(&$output)
	{
		$html = NULL;

		try
		{
			$html = Yii::app()->newRelic->getBrowserTimingFooter();
		}
		catch (Exception $e)
		{
			return;
		}

		if (!$html) 
			return;
		
		$fullPage = 0;
		$output = preg_replace('/(<\\/body\s*>)/is', '<###end###>$1', $output, 1, $fullPage);

		if ($fullPage)
			$output=str_replace('<###end###>', $html, $output);
		else
			$output = $output . $html;
	}
}