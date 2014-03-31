<?php

interface CiiDashboardAddonInterface
{
    public function actionIsUpdateAvailable($id=NULL);
    public function actionUpgrade($id=NULL);
    public function actionInstall($id=NULL);

    /**
     * @return boolean
     */
    public function actionUninstall($id=NULL);
    public function actionInstalled();
    public function actionUninstalled();

    /**
     * @return boolean
     */
    public function isInstalled($id=NULL);
}
