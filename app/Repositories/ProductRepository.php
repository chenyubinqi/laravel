<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 1/11/17
 * Time: 09:14
 */

namespace App\Repositories;

use App\Product;
use DB;

class ProductRepository
{
    use BaseRepository;

    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    /**
     * 获取化合物ID所属分类
     * @param array ...$id
     * @return mixed
     */
    public function getListById(...$id)
    {
        $connection = isset($id[1]) ? $id[1] : '';
        $ids = $id[0];
        $connection = DB::connection($connection);

        foreach ($ids as $id) {
            $arr[$id] = null;
        }

        $results = $connection->table('pms_product')
            ->selectRaw('pms_product.id,GROUP_CONCAT(DISTINCT c.`category_id` SEPARATOR \',\') AS `product_category`')
            ->leftJoin('pms_relation_product_category AS c', 'pms_product.id', '=', 'c.product_id')
            ->whereIn('pms_product.id', $ids)
            ->groupBy('pms_product.id')
            ->get();

        foreach ($results as $result) {
            $arr[$result->id] = explode(',', $result->product_category);
        }

        return $arr;
    }

    /**
     * 从ES中返回最后一条记录的ID
     * @return int
     */
    public function getLastId()
    {
        $last = app('es')->type('pms_product')->body([
            "sort" => [
                "_script" => [
                    "type" => "number",
                    "script" => [
                        "inline" => "Integer.parseInt(doc['_uid'].value.substring(12))" //pms_product#
                    ],
                    "order" => "desc"
                ]
            ]
        ])->first();

        return $last ? $last->_id : 0;
    }

    public function getList($params)
    {
        $es = app('es')->type('pms_product');
        $page = isset($params['page']) ? $params['page'] : 1;
        $pageSize = isset($params['page_size']) ? $params['page_size'] : 10;
        $this->resetEsPage($page, $pageSize);
        if (isset($params['id']) && $params['id']) {
            $es = $es->id($params['id']);
        }
        if (isset($params['product_name']) && $params['product_name']) {
            $es = $es->where("product_name", "like", $params['product_name']);
        }
        if (isset($params['cas_no']) && $params['cas_no']) {
            $es = $es->where("cas_no", $params['cas_no']);
        }
        if (isset($params['status']) && $params['status']) {
            $es = $es->where("status", $params['status']);
        }
        if (isset($params['source']) && $params['source']) {
            $es = $es->where("source", $params['source']);
        }
        if (isset($params['product_category']) && $params['product_category']) {
            $es = $es->where("product_category", $params['product_category']);
        }
        if (isset($params['start']) && $params['start']) {
            $es = $es->where("create_time", '>=', $params['start']);
        }
        if (isset($params['end']) && $params['end']) {
            $es = $es->where("create_time", '<', $params['end']);
        }

        return $this->resetEsResult($es->take($pageSize)->skip(($page - 1) * $pageSize)->get());

    }

}