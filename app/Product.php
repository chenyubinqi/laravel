<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 1/11/17
 * Time: 09:23
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'pms_product';

    protected $guarded = array();

    public $timestamps = false;
}