<?php
$I = new WebDev($scenario);
$I->wantTo('verify all default pages load');
$I->amOnPage('/');
$I->amOnPage('/register');
$I->amOnPage('/forgot');
$I->amOnPage('/login');
$I->amOnPage('/blog');
