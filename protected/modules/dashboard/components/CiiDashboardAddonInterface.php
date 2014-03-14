<?php

interface CiiDashboardAddonInterface
{
    public function actionIsUpdateAvailable($id=NULL);
    public function actionUpgrade($id=NULL);
    public function actionInstall($id=NULL);
    public function actionUninstall($id=NULL);
    public function actionInstalled();
    public function actionUninstalled();
    public function isInstalled($id=NULL);
}
