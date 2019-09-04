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
use yii\db\Exception;
use yii\db\Migration as baseMigration;

class Migration extends baseMigration
{
    /**
     * @var string
     */
    protected $tableOptions;
	protected $restrict = 'RESTRICT';
	protected $cascade = 'CASCADE';
	protected $dbType;
	
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

	/**
	 * Drop Column Constraints
	 *
	 * @param string $table
	 * @param string $column
	 *
	 * @throws Exception
	 */
	public function dropColumnConstraints($table, $column)
	{
		$table = $this->db->schema->getRawTableName($table);
		$cmd = $this->db->createCommand('SELECT name FROM sys.default_constraints
                                WHERE parent_object_id = object_id(:table)
                                AND type = \'D\' AND parent_column_id = (
                                    SELECT column_id 
                                    FROM sys.columns 
                                    WHERE object_id = object_id(:table)
                                    and name = :column
                                )', [ ':table' => $table, ':column' => $column ]);

		$constraints = $cmd->queryAll();
		foreach ($constraints as $c) {
			$this->execute('ALTER TABLE '.$this->db->quoteTableName($table).' DROP CONSTRAINT '.$this->db->quoteColumnName($c['name']));
		}
	}
}
