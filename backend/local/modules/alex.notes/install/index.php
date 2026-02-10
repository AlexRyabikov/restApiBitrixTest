<?php

use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\ModuleManager;

class alex_notes extends CModule
{
    public $MODULE_ID = 'alex.notes';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME = 'Alex Notes';
    public $MODULE_DESCRIPTION = 'REST notes module';
    public $PARTNER_NAME = 'Alex';
    public $PARTNER_URI = '';

    public function __construct()
    {
        include __DIR__ . '/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallDB();
        $this->InstallFiles();
    }

    public function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallDB()
    {
        $connection = Application::getConnection();

        $connection->queryExecute(
            'CREATE TABLE IF NOT EXISTS alex_notes (
                ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
                TITLE VARCHAR(255) NOT NULL,
                CONTENT TEXT NOT NULL,
                CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (ID)
            )'
        );
    }

    public function UnInstallDB()
    {
        $connection = Application::getConnection();
        $connection->queryExecute('DROP TABLE IF EXISTS alex_notes');
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . '/tools',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/alex.notes',
            true,
            true
        );
    }

    public function UnInstallFiles()
    {
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/alex.notes');
    }
}
