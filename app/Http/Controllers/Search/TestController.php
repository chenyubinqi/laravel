<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 25/9/17
 * Time: 16:49
 */

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Search\TestSearch;
use Illuminate\Pagination\LengthAwarePaginator;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $search = new TestSearch();
        $params = array_filter($request->all());
        $page = $params['page'] ?? 1;
        $pageSize = 10;
        $search->setLimit(10)->setOffset(($page - 1) * $pageSize)
            ->setSortField('sort', TestSearch::SORT_DESC);
        if (isset($params['id'])) {
            $search->setId($params['id']);
        }
        if (isset($params['product_name'])) {
            $search->setProductName($params['product_name']);
        }
        if (isset($params['cas_no'])) {
            $search->setCasNo($params['cas_no']);
        }
        if (isset($params['product_parent_category'])) {
            $search->setProductParentCategory(explode(',', $params['product_parent_category']));
        }
        if (isset($params['status'])) {
            $search->setStatus($params['status']);
        }
        if (isset($params['source'])) {
            $search->setSource($params['source']);
        }
        $params['start_create_time'] = $params['start_create_time'] ?? null;
        $params['end_create_time'] = $params['end_create_time'] ?? null;
        $search->setCreateTimeRange($params['start_create_time'], $params['end_create_time']);
        list($total, $items) = $search->getResult();
        $total = $total < 10000 ? $total : 10000;
        $paginator = new LengthAwarePaginator($items, $total, $pageSize);

        return view('search.test.index', compact('paginator', 'params'));
    }

    /**
     * 折叠分页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function collapse(Request $request)
    {
        $search = new TestSearch();
        $params = array_filter($request->all());
        $page = $params['page'] ?? 1;
        $pageSize = 10;
        $search->setLimit(10)->setOffset(($page - 1) * $pageSize)
            ->setSortField('sort', TestSearch::SORT_DESC);
        if (isset($params['id'])) {
            $search->setId($params['id']);
        }
        if (isset($params['product_name'])) {
            $search->setProductName($params['product_name']);
        }
        if (isset($params['cas_no'])) {
            $search->setCasNo($params['cas_no']);
        }
        if (isset($params['product_parent_category'])) {
            $search->setProductParentCategory(explode(',', $params['product_parent_category']));
        }

        $search->setCollapse(['field' => 'source']);
        $search->setCardinality(true);

        if (isset($params['status'])) {
            $search->setStatus($params['status']);
        }
        if (isset($params['source'])) {
            $search->setSource($params['source']);
        }
        $params['start_create_time'] = $params['start_create_time'] ?? null;
        $params['end_create_time'] = $params['end_create_time'] ?? null;
        $search->setCreateTimeRange($params['start_create_time'], $params['end_create_time']);
        list($total, $items) = $search->getResult();
        $total = $total < 10000 ? $total : 10000;
        $paginator = new LengthAwarePaginator($items, $total, $pageSize);

        return view('search.test.index', compact('paginator', 'params'));
    }

}