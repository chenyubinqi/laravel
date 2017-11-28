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
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\ProductRepository;

class ProductController extends Controller
{
    protected $product;

    /**
     * ProductController constructor.
     * @param ProductRepository $product
     */
    public function __construct(ProductRepository $product)
    {
        $this->product = $product;

    }

    public function index(Request $request)
    {
        $params = array_filter($request->all());
        return $this->product->getList($params);
    }

}