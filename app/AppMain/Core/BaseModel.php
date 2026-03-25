<?php


namespace App\AppMain\Core;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
	protected $dbConnection = 'mysql';

    public function getQuery($databaseConnection = null)
    {
        if (empty($databaseConnection)) {
            $databaseConnection = $this->dbConnection;
        }
        return static::on($databaseConnection)->newQuery();
    }
}