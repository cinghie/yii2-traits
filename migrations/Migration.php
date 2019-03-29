<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.2
 */

namespace cinghie\traits\migrations;

use Yii;

class Migration extends \yii\db\Migration
{

    /**
     * @var string
     */
    protected $tableOptions;
	
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

	    switch ($this->db->driverName)
	    {
		    case 'mysql':
			    $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
			    $this->dbType = 'mysql';
			    break;
		    case 'pgsql':
			    $this->tableOptions = null;
			    $this->dbType = 'pgsql';
			    break;
		    case 'dblib':
		    case 'mssql':
		    case 'sqlsrv':
			    $this->restrict = 'NO ACTION';
			    $this->tableOptions = null;
			    $this->dbType = 'sqlsrv';
			    break;
		    default:
			    throw new \RuntimeException(Yii::t('traits','Your database is not supported!'));
	    }
    }

}
