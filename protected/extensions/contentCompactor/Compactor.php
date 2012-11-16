<?php

	/**
	 * @author Oliver Lillie (aka buggedcom) <publicmail@buggedcom.co.uk>
	 *
	 * @license BSD
	 * @copyright Copyright (c) 2008 Oliver Lillie <http://www.buggedcom.co.uk>
	 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
	 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
	 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
	 * is furnished to do so, subject to the following conditions:  The above copyright notice and this permission notice shall be
	 * included in all copies or substantial portions of the Software.
	 *
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
	 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
	 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
	 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	 *
	 * @name Compactor
	 * @version 0.6.0
	 * @abstract This class can be used in speeding up delivery of webpages front the server to the client browser, by compacting
	 * the whitespace. There are multiple options for compacting, including both horizontal and vertical whitespace removal and
	 * css/javascript compacting also. The class can also compact the output of a php script using automatic output buffering. 
	 *
	 * @example compressor.example1.php Compacts HTML using the default options.
	 * @example compressor.example2.php Compacts remote HTML with custom javascript compression.
	 * @example
	 * <?php
	 * 		// this example will automatically compact any buffered output from the script
	 * 		$compactor = new Compactor(array(
	 * 			'use_buffer'			=> true,
	 * 			'buffer_echo'			=> true,
	 * 			'compact_on_shutdown'	=> true
	 * 		));
	 * ?>
	 *
	 * @note The functions to provide deflate functionality are partially lifted from 
	 * minify http://code.google.com/p/minify/
	 *
	 *
	 * @note This class has been modified by Martin Nilsson (martin.nilsson@haxtech.se) to integrate
	 * better with Yii framework thus minimize the impact on server by removing unnecessary functions.
	 */

	class Compactor
	{

		/**
		 * Holds the options array
		 * @access private
		 * @var array
		 */
		private $_options 			= array(
			// line_break; string; The type of line break used in the HTML that you are processing.
			// ie, \r, \r\n, \n or PHP_EOL
			'line_break' 						=> PHP_EOL,
			// preserved_tags; array; An array of html tags whose innerHTML contents format require preserving.
			'preserved_tags'					=> array('textarea', 'pre', 'script', 'style', 'code'),
			// preserved_boundry; string; The holding block that is used to replace the contents of the preserved tags
			// while the compacting is taking place.
			'preserved_boundry'					=> '@@PRESERVEDTAG@@',
			// strip_comments; boolean; This will strip html comments from the html. NOTE, if the below option 'keep_conditional_comments'
			// is not set to true then conditional Internet Explorer comments will also be stripped.
			'strip_comments' 					=> true,
			// keep_conditional_comments; boolean; Only applies if the baove option 'strip_comments' is set to true.
			// Only if the client browser is Internet Explorer then the conditional comments are kept.
			'keep_conditional_comments'			=> true,
			// conditional_boundries; array; The holding block boudries that are used to replace the opening and
			// closing tags of the conditional comments.
			'conditional_boundries'				=> array('@@IECOND-OPEN@@', '@@IECOND-CLOSE@@'),
			// compress_horizontal; boolean; Removes horizontal whitespace of the HTML, ie left to right whitespace (spaces and tabs).
			'compress_horizontal'				=> true,
			// compress_vertical; boolean; Removes vertical whitespace of the HTML, ie line breaks.		
			'compress_vertical'					=> true,
			// compress_scripts; boolean; Compresses content from script tags using a simple algorythm. Removes javascript comments,
			// and horizontal and vertical whitespace. Note as only a simple algorythm is used there are limitations to the script 
			// and you may want to use a more complex script like 'minify' http://code.google.com/p/minify/ or 'jsmin'
			// http://code.google.com/p/jsmin-php/ See test3.php for an example.
			'compress_scripts'					=> false,
			// script_compression_callback; boolean; The name of a callback for custom js compression. See test3.php for an example.	
			'script_compression_callback' 		=> false,
			// script_compression_callback_args; array; Any additional args for the callback. The javascript will be put to the
			// front of the array.
			'script_compression_callback_args' 	=> array(),
			// compress_css; boolean; Compresses CSS style tags.		
			'compress_css'						=> true,
		);
		
		/**
		 * Holds the preserved blocks so multiple scans of the html don't have to be made.
		 * @access private
		 * @var mixed 
		 */
		private $_preserved_blocks  = false;

		/**
		 * Constructor
		 */
		function __construct($options=array())
		{
			$this->setOption($options);
		}
		
		/**
		 * Sets an option in the option array();
		 * 
		 * @access public
		 * @param mixed $varname Can take the form of an array of options to set a string of an option name.
		 * @param mixed $varvalue The value of the option you are setting.
		 **/
		public function setOption($varname, $varvalue=null)
		{
			$keys = array_keys($this->_options);
			if(gettype($varname) == 'array')
			{
				foreach($varname as $name=>$value)
				{
					if(in_array($name, $keys))
					{
						$this->_options[$name] = $value;
					}
				}
			}
			else
			{
				if(in_array($varname, $keys))
				{
					$this->_options[$varname] = $varvalue;
				}
			}
		}
		
		/**
		 * Compresses the html, either that is supplied to the function or if the use_buffer
		 * option is enabled then the buffer is grabbed for compression.
		 * 
		 * @access public
		 * @param string $html HTML string required for compression, however if the use_buffer option
		 * 		is enabled the param can be left out because it will be ignored anyway.
		 * @return string
		 */
		public function squeeze($html=null)
		{
// 			unify the line breaks so we have clean html to work with
			$html = $this->_unifyLineBreaks($html);
// 			compress any script tags if required
			if($this->_options['compress_scripts'] || $this->_options['compress_css'])
			{
				$html = $this->_compressScriptAndStyleTags($html);
			}
// 			make the compressions
			if($this->_options['strip_comments'])
			{
				$html = $this->_stripHTMLComments($html);
			}
			if($this->_options['compress_horizontal'])
			{
				$html = $this->_compressHorizontally($html);
			}
			if($this->_options['compress_vertical'])
			{
				$html = $this->_compressVertically($html);
			}
// 			replace the preserved blocks with their original content
			$html = $this->_reinstatePreservedBlocks($html);

			return $html;
		}
		
		/**
		 * Strips HTML Comments from the buffer whilst making a check to see if
		 * Inernet Explorer conditional comments should be stripped or not.
		 *
		 * @access private
		 * @param string $html The HTML string for comment removal.
		 * @return string
		 */
		private function _stripHTMLComments($html)
		{
			$keep_conditionals = false;
// 			only process if the Internet Explorer conditional statements are to be kept
			if($this->_options['keep_conditional_comments'])
			{
// 				check that the opening browser is internet explorer
				$msie = '/msie\s(.*).*(win)/i';
			    $keep_conditionals = (isset($_SERVER['HTTP_USER_AGENT']) && preg_match($msie, $_SERVER['HTTP_USER_AGENT']));
// 			    $keep_doctype = false;
// 			    if(strpos($html, '<!DOCTYPE'))
// 			    {
// 					$html = str_replace('<!DOCTYPE', '--**@@DOCTYPE@@**--', $html);
// 			   	 	$keep_doctype = true;
// 			    }
// 			    ie conditionals are to be kept so substitute
				if($keep_conditionals)
				{
					$html = str_replace(array('<!--[if', '<![endif]-->'), $this->_options['conditional_boundries'], $html);
				}
			}			
// 		    remove comments
		    $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);
// 		    $html = preg_replace ('@<![\s\S]*?--[ \t\n\r]*>@', '', $html);
// 		   	re sub-in the conditionals if required.
			if($keep_conditionals)
			{
				$html = str_replace($this->_options['conditional_boundries'], array('<!--[if', '<![endif]-->'), $html);
			}
// 		    if($keep_doctype)
// 		    {
// 				$html = str_replace('--**@@DOCTYPE@@**--', '<!DOCTYPE', $html);
// 		    }
// 			return the buffer
			return $html;
		}
		
		/**
		 * Finds html blocks to preserve the formatting for.
		 * 
		 * @access private
		 * @param string $html
		 * @return string
		 */
		private function _extractPreservedBlocks($html)
		{
			if($this->_preserved_blocks !== false)
			{
				return $html;
			}
 			$tag_string = implode('|', $this->_options['preserved_tags']);
// 			get the textarea matches
			preg_match_all("!<(".$tag_string.")[^>]*>.*?</(".$tag_string.")>!is", $html, $preserved_area_match);
			$this->_preserved_blocks = $preserved_area_match[0];
// 			replace the textareas inerds with markers
			return preg_replace("!<(".$tag_string.")[^>]*>.*?</(".$tag_string.")>!is", $this->_options['preserved_boundry'], $html);
		}
		
		/**
		 * Replaces any preservations made with the original content.
		 * 
		 * @access private
		 * @param string $html
		 * @return string
		 */
		private function _reinstatePreservedBlocks($html)
		{
			if($this->_preserved_blocks === false)
			{
				return $html;
			}
			foreach($this->_preserved_blocks as $curr_block)
			{
				$html = preg_replace("!".$this->_options['preserved_boundry']."!", $curr_block, $html, 1);
			}
			return $html;
		}
		
		/**
		 * Compresses white space horizontally (ie spaces, tabs etc) whilst preserving
		 * textarea and pre content.
		 *
		 * @access private
		 * @param string $html
		 * @return string
		 */
		private function _compressHorizontally($html)
		{
			$html = $this->_extractPreservedBlocks($html);
// 			remove the white space
			$html = preg_replace('/((?<!\?>)'.$this->_options['line_break'].')[\s]+/m', '\1', $html);
// 			Remove extra spaces
			return preg_replace('/\t+/', '', $html);
		}

		/**
		 * Compresses white space vertically (ie line breaks) whilst preserving
		 * textarea and pre content.
		 *
		 * @access private
		 * @param string $html
		 * @param mixed $textarea_blocks false if no textarea blocks have already been taken out, otherwise an array.
		 * @return unknown
		 */
		private function _compressVertically($html)
		{
			$html = $this->_extractPreservedBlocks($html);
// 			remove the line breaks
			return str_replace($this->_options['line_break'], '', $html);
		}
		
		/**
		 * Converts line breaks from the different platforms onto the one type.
		 *
		 * @access private
		 * @param string $html HTML string
		 * @param string $break The format of the line break you want to unify to. ie \r\n or \n
		 * @return string
		 */
		private function _unifyLineBreaks($html)
		{
		    return preg_replace ("/\015\012|\015|\012/", $this->_options['line_break'], $html);
		}
		
		/**
		 * Compresses white space vertically (ie line breaks) whilst preserving
		 * textarea and pre content. This uses the classes '_simpleCodeCompress' to compress
		 * the javascript, however it would be advisable to use another library such as 
		 * 'minify' http://code.google.com/p/minify/ because this function has certain 
		 * limitations with comments and other regex expressions. You can set another function 
		 * callback using the 'compress_js_callback' option.
		 *
		 * @access private
		 * @param string $html
		 * @return string
		 */
		private function _compressScriptAndStyleTags($html)
		{
			$compress_scripts = $this->_options['compress_scripts'];
			$compress_css = $this->_options['compress_css'];
			$use_script_callback = $this->_options['script_compression_callback'] != false;
// 			pregmatch all the script tags
			$scripts = preg_match_all("!(<(style|script)[^>]*>(?:\\s*<\\!--)?)(.*?)((?://-->\\s*)?</(style|script)>)!is", $html, $scriptparts);
// 			collect and compress the parts
			$compressed = array();
			$parts = array();
			for($i=0; $i<count($scriptparts[0]); $i++)
			{
				$code = trim($scriptparts[3][$i]);
				$not_empty = !empty($code);
				$is_script = ($compress_scripts && $scriptparts[2][$i] == 'script');
				if($not_empty && ($is_script || ($compress_css && $scriptparts[2][$i] == 'style')))
				{
					if($is_script && $use_script_callback)
					{
						$callback_args = $this->_options['script_compression_callback_args'];
						if(gettype($callback_args) !== 'array')
						{
							$callback_args = array($callback_args);
						}
						array_unshift($callback_args, $code);
						$minified = call_user_func_array($this->_options['script_compression_callback'], $callback_args);
					}
					else
					{
						$minified = $this->_simpleCodeCompress($code);
					}
					array_push($parts, $scriptparts[0][$i]);
					array_push($compressed, trim($scriptparts[1][$i]).$minified.trim($scriptparts[4][$i]));
				}
			}
// 			do the replacements and return
			return str_replace($parts, $compressed, $html);
		}
		
		/**
		 * Use simple preg_replace to compresses code (ie javascript and css) whitespace.
		 * It would be advisable to use another library such as 'minify' http://code.google.com/p/minify/
		 * because this function has certain limitations with comments and other regex expressions.
		 * You can set another function callback using the 'compress_js_callback' option.
		 *
		 * @access private
		 * @param string $code Code string
		 * @return string
		 **/
		private function _simpleCodeCompress($code)
		{
// 			Remove multiline comment
			$code = preg_replace('/\/\*(?!-)[\x00-\xff]*?\*\//', '', $code);
// 			Remove single line comment
// 			$code = preg_replace('/[^:]\/\/.*/', '', $code);
			$code = preg_replace('/\\/\\/[^\\n\\r]*[\\n\\r]/', '', $code);
			$code = preg_replace('/\\/\\*[^*]*\\*+([^\\/][^*]*\\*+)*\\//', '', $code);
// 			Remove extra spaces
			$code = preg_replace('/\s+/', ' ', $code);
// 			Remove spaces that can be removed
			return preg_replace('/\s?([\{\};\=\(\)\/\+\*-])\s?/', "\\1", $code);
		}
		
	}
	
	