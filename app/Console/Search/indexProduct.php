<?php

namespace App\Console\Search;

use Illuminate\Console\Command;
use DB;
use App\Repositories\ProductRepository;

/**
 * 导入百科数据到es中
 * @package App\Console\Commands
 */
class indexProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:product {--F|full}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入化合物数据到es中';

    protected $product;

    /**
     * Create a new command instance.
     * indexMolData constructor.
     * @param ProductRepository $product
     */
    public function __construct(ProductRepository $product)
    {
        parent::__construct();

        $this->product = $product;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("start");
        $startTime = time();
        $lastId = $this->product->getLastId();//同一批次的最后记录ID
        $full = $this->option('full');
        $full && $lastId = 0;
        $count = DB::table('pms_product')->where('status', '=', 1)->where('id', '>', $lastId)->count();

        $pageSize = 200;
        $totalPage = ceil($count / $pageSize);

        $processCount = 5;//同时启动的进程数量
        $childArr = [];//子进程ID数组

        // 创建共享内存,创建信号量,定义共享key
        $shmStr = $full ? "f" : "i";
        $semStr = $full ? "u" : "c";
        $shm_id = ftok(__FILE__, $shmStr);
        $sem_id = ftok(__FILE__, $semStr);
        $shareMemory = shm_attach($shm_id);
        $signal = sem_get($sem_id);
        $shareKey = 1;

        for ($page = 1; $page <= $totalPage; $page++) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                $this->error('could not fork');
                exit;
            } else if ($pid == 0) {
                //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
                $num = $page % $processCount;
                $connection = 'process_' . $num;
                $resQuery = DB::connection($connection)->table('pms_product')
                    ->where('status', '=', 1)
                    ->where('id', '>', $lastId);

                if ($num != 1) { //第一批次不需要
                    if ($num > 1) {
                        $resQuery = $resQuery->skip(($num - 1) * $pageSize);
                    } else if ($num == 0) {
                        $resQuery = $resQuery->skip(($processCount - 1) * $pageSize);
                    }
                }

                $resQuery = $resQuery->orderBy('id', 'asc')->take($pageSize)->get()->toArray();

                $list = [];

                $ids = array_map(
                    function ($item) {
                        return $item->id;
                    }, $resQuery);

                $category = $this->product->getListById($ids, $connection);
                foreach ($resQuery as $query) {
                    $list[$query->id] = [
                        'product_name' => $query->product_name,
                        'product_name_en' => $query->product_name_en,
                        'source' => (int)$query->source,
                        'main_category' => (int)$query->main_category,
                        'formula' => (string)$query->formula,
                        'status' => (int)$query->status,
                        'cas_no' => (string)$query->cas_no,
                        'product_code' => (string)$query->product_code,
                        'zh_synonyms' => (string)$query->zh_synonyms,
                        'en_synonyms' => (string)$query->en_synonyms,
                        'product_category' => isset($category[$query->id]) ? array_map(function ($v) {
                            return intval($v);
                        },
                            $category[$query->id]) : [],
                        'sort' => (int)$query->sort,
                        'create_time' => $query->create_time
                    ];
                }

                // 获得信号量
                sem_acquire($signal);
                if (shm_has_var($shareMemory, $shareKey)) {
                    // 有值
                    $lastIdArr = shm_get_var($shareMemory, $shareKey);
                    $lastIdArr[] = end($ids);
                    shm_put_var($shareMemory, $shareKey, $lastIdArr);
                } else {
                    // 无值,初始化
                    shm_put_var($shareMemory, $shareKey, [end($ids)]);
                }
                // 用完释放
                sem_release($signal);

                if (!empty($list)) {
                    app('es')->type("pms_product")->bulk($list);
                }

                $this->info("$page/$totalPage");
                exit;
            }

            $childArr[] = $pid;
            if ($page % $processCount == 0 || $page == $totalPage) {
                while (count($childArr) > 0) {
                    foreach ($childArr as $key => $pid) {
                        $res = pcntl_waitpid($pid, $status, WNOHANG);//等待子进程中断，防止子进程成为僵尸进程。
                        if ($res == -1 || $res > 0)
                            unset($childArr[$key]);
                    }
                    usleep(10);
                }

                if (shm_has_var($shareMemory, $shareKey)) {
                    // 有值
                    $lastIdArr = shm_get_var($shareMemory, $shareKey);
                    sort($lastIdArr);
                    $lastId = end($lastIdArr);
                    // 初始化为空
                    shm_put_var($shareMemory, $shareKey, []);
                }
            }

        }

        // 释放共享内存与信号量
        shm_remove($shareMemory);
        sem_remove($signal);

        $totalTime = time() - $startTime;
        $this->info("For a total of {$totalTime}s");
        $this->info("end");
    }
}
