<?php

namespace App\Console\Search;

use App\Search\TestSearch;
use Illuminate\Console\Command;
use DB;

/**
 * 导入化合物到es中
 * @package App\Console\Commands
 */
class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入化合物到es中';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("start");

        $query = DB::table('pms_product')->where('status', '=', 1);

        $count = $query->count();

        $pageSize = 50;
        $totalPage = ceil($count / $pageSize);
        $lastId = 0;

        $isExist = TestSearch::existsIndex(TestSearch::INDEX);
        if (!$isExist) {
            TestSearch::createIndex(TestSearch::INDEX, TestSearch::TYPE, TestSearch::getMappings(), TestSearch::getSettings());
        }

        for ($page = 1; $page <= $totalPage; $page++) {
            $resQuery = DB::table('pms_product')->where('status', '=', 1)->select('id')->where('id', '>', $lastId)
                ->orderBy('id', 'asc')->take($pageSize)->get()->toArray();

            $ids = array_map(
                function ($item) {
                    return $item->id;
                }, $resQuery);

            $list = TestSearch::getUpdateDataEs($ids);

            if (!empty($list)) {
                TestSearch::bulkUpdateOrInsertDoc(TestSearch::INDEX, TestSearch::TYPE, $list);
            }

            $lastId = end($ids);

            $this->info("$page/$totalPage");
        }
        $this->info("end");
    }
}
