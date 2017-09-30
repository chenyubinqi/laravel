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

class TestController extends Controller
{
    public function index(Request $request)
    {
        return view('search.test.index');
    }

}