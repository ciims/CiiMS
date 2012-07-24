<?php
/******************************************************************
   Projectname:   Automatic Keyword Generator Application Script
   Version:       0.3
   Author:        Ver Pangonilo <smp@limbofreak.com>
   Last modified: 26 July 2006
   Copyright (C): 2006 Ver Pangonilo, All Rights Reserved

   * GNU General Public License (Version 2, June 1991)
   *
   * This program is free software; you can redistribute
   * it and/or modify it under the terms of the GNU
   * General Public License as published by the Free
   * Software Foundation; either version 2 of the License,
   * or (at your option) any later version.
   *
   * This program is distributed in the hope that it will
   * be useful, but WITHOUT ANY WARRANTY; without even the
   * implied warranty of MERCHANTABILITY or FITNESS FOR A
   * PARTICULAR PURPOSE. See the GNU General Public License
   * for more details.

   Description:
   This class can generates automatically META Keywords for your 
   web pages based on the contents of your articles. This will 
   eliminate the tedious process of thinking what will be the best
   keywords that suits your article. The basis of the keyword
   generation is the number of iterations any word or phrase
   occured within an article. 
   
   This automatic keyword generator will create single words, 
   two word phrase and three word phrases. Single words will be
   filtered from a common words list.

Change Log:
===========
0.3 Ver Pangonilo 26 July 2006
==============================
Revised to show changes in class.

******************************************************************/


//assuming that your site contents is from a database.
//set the outbase of the database to $data.

$data =<<<EOF
Imagine being overseas and your identity being available for the taking - your nationality, your name, your passport number. Everything.

That's the fear of privacy and security specialists now that the State Department plans to issue "e-Passports" to American travelers beginning in late August.

They'll have radio frequency identification (RFID) tags and are meant to cut down on human error of immigration officials, speed the processing of visitors and safeguard against counterfeit passports.

Yet critics are concerned that the security benefit of RFID technology, which combines silicon chips with antennas to make data accessible via radio waves, could be vastly outweighed by security threats to the passport holder.

"Basically, you've given everybody a little radio-frequency doodad that silently declares 'Hey, I'm a foreigner,'" says author and futurist Bruce Sterling, who lectures on the future of RFID technology. "If nobody bothers to listen, great. If people figure out they can listen to passport IDs, there will be a lot of strange and inventive ways to exploit that for criminal purposes."

RFID chips are used in security passes many companies issue to employees. They don't have to be touched to a reader-machine, only waved near it. Following initial objections by security and privacy experts, the State Department added several security precautions.

But experts still fear the data could be "skimmed," or read remotely without the bearer's knowledge.

Kidnappers, identity thieves and terrorists could all conceivably commit "contactless" crimes against victims who wouldn't know they've been violated until after the fact.

"The basic problem with RFID is surreptitious access to ID," said Bruce Schneier security technologist, author and chief technology officer of Counterpane Internet Security, a technology security consultancy. "The odds are zero that RFID passport technology won't be hackable."

The State Department argues the concerns are overstated. "We wouldn't be issuing the passports to ourselves if we didn't think they're secure," said Deputy Assistant Secretary of State for Passport Services Frank Moss, who noted that RFID passports have already been issued to core State Department personnel, including himself. "We're our own test population.

EOF;

//this the actual application.
include('class.autokeyword.php');

echo "<H1>Input - text</H1>";
echo $data;

$params['content'] = $data; //page content
//set the length of keywords you like
$params['min_word_length'] = 5;  //minimum length of single words
$params['min_word_occur'] = 2;  //minimum occur of single words

$params['min_2words_length'] = 3;  //minimum length of words for 2 word phrases
$params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
$params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase

$params['min_3words_length'] = 3;  //minimum length of words for 3 word phrases
$params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
$params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase

$keyword = new autokeyword($params, "iso-8859-1");

echo "<H1>Output - keywords</H1>";

echo "<H2>words</H2>";
echo $keyword->parse_words();
echo "<H2>2 words phrase</H2>";
echo $keyword->parse_2words();
echo "<H2>2 words phrase</H2>";
echo $keyword->parse_3words();

echo "<H2>All together</H2>";
echo $keyword->get_keywords();
?>
