<?php

namespace App\Search;

use Elasticsearch;
use Log;

/**
 * 搜索基类
 * Class BaseSearch
 * @package App\Search
 */
abstract class BaseSearch
{

    const SORT_DESC = 1;

    const SORT_ASC = 2;

    const MAX_RECORD = 500000;

    /**
     * 分页大小
     * @var int
     */
    protected $limit = 20;

    protected $offset = 0;

    public $sortFields = [];

    /**
     * @param string $field
     * @param int $sort
     * @return $this
     */
    public function setSortField($field, $sort)
    {
        $this->sortFields[$field] = $sort;

        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = (int)$offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * 返回的数据格式为
     * array(
     * hits => array(
     *          total => int
     *          hits => array() 数据
     *      )
     * )
     *
     * @param string $index
     * @param string $type
     * @param array $body
     * @param array $fields 指定返回的字段
     * @return array [count, list]
     * @throws \Exception
     */
    protected function query($index, $type, $body, $fields = [])
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => $body,
            'from' => $this->offset > self::MAX_RECORD ? self::MAX_RECORD : $this->offset,
            'size' => $this->limit
        ];

        if (is_array($fields)) {
            $params['fields'] = $fields;
        }

        Log::info('es search params:' . json_encode($params));
        $result = Elasticsearch::search($params);

        config('app.debug') && Log::info('es search results:' . json_encode($result));

        $count = 0;
        $list = [];

        if (isset($result['hits'])) {
            $count = $result['hits']['total'];
            foreach ($result['hits']['hits'] as $item) {
                if (isset($item['_source'])) {
                    $list[$item['_id']] = $item['_source'];
                } elseif (isset($item['fields'])) {
                    $list[$item['_id']] = array_map(
                        function ($item) {
                            return current($item);
                        }, $item['fields']);
                } else {
                    $list[$item['_id']] = [];
                }
            }
        }

        return [
            $count,
            $list,
        ];
    }

    /**
     * 聚合搜索
     * @param $index
     * @param $type
     * @param $body
     * @return array
     */
    protected function agg($index, $type, $body)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => $body,
            'from' => $this->offset,
            'size' => 0
        ];

        Log::info('es search params:' . json_encode($params));

        $result = Elasticsearch::search($params);

        $count = 0;
        $list = [];

        if (isset($result['aggregations'])) {
            foreach ($result['aggregations'] as $groupName => $group) {
                foreach ($group['buckets'] as $bucket) {
                    $list[$groupName][$bucket['key']] = $bucket['doc_count'];
                }
                $count += 1;
            }
        }

        return [
            $count,
            $list,
        ];
    }

    /**
     * 返回结果
     * @param string $fields
     * @return mixed
     */
    abstract public function getResult();

    /**
     * 更新文档
     * @param $index
     * @param $type
     * @param $id
     * @param $doc
     * @return mixed
     */
    public static function updateOrInsertDoc($index, $type, $id, $doc)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id,
            'body' => [
                'upsert' => $doc,
                'doc' => $doc
            ],
        ];

        $ret = Elasticsearch::update($params);

        return $ret;
    }

    /**
     * 批量更新文档
     * @param $index
     * @param $type
     * @param $doc
     * @return mixed
     * @throws \Exception
     */
    final public static function bulkUpdateOrInsertDoc($index, $type, $doc)
    {
        $params = [
            'index' => $index,
            'type' => $type,
        ];

        foreach ($doc as $id => $item) {
            $action = [
                'update' => [
                    '_id' => (string)$id,
                ],
            ];

            $params['body'][] = json_encode($action) . "\n" . json_encode(
                    [
                        'doc' => $item,
                        'doc_as_upsert' => true,
                    ]);
        }

        if (!isset($params['body'])) {
            throw new \Exception('更新内容不能为空');
        }

        $params['body'] = implode("\n", $params['body']) . "\n";

        return Elasticsearch::bulk($params);
    }

    /**
     * 批量删除文档
     * @param $index
     * @param $type
     * @param $ids
     * @return mixed
     * @throws \Exception
     */
    final public static function bulkDeleteDoc($index, $type, $ids)
    {
        $params = [
            'index' => $index,
            'type' => $type,
        ];
        foreach ($ids as $id) {
            $action = [
                'delete' => [
                    '_id' => (string)$id,
                ],
            ];

            $params['body'][] = json_encode($action);
        }

        if (!isset($params['body'])) {
            throw new \Exception('要删除的内容不能为空');
        }

        $params['body'] = implode("\n", $params['body']) . "\n";

        return Elasticsearch::bulk($params);
    }

    /**
     * 删除文档
     * @param $index
     * @param $type
     * @param $key
     */
    public static function deleteDoc($index, $type, $key)
    {
        Elasticsearch::delete(
            [
                'index' => $index,
                'type' => $type,
                'id' => $key,
            ]);
    }

    /**
     * 按照条件删除索引数据
     * @param $index
     * @param $type
     * @param $query
     */
    public static function deleteDocByQuery($index, $type, $query)
    {
        Elasticsearch::deleteByQuery(
            [
                'index' => $index,
                'type' => $type,
                'body' => $query,
            ]);
    }

    /**
     * 创建索引
     * @param $index
     * @param $type
     * @param $mappings
     * @return mixed
     */
    public static function createIndex($index, $type, $mappings, $settings = [])
    {
        $params = [
            'index' => $index,
            'body' => [
                'mappings' => [
                    $type => $mappings,
                ],
            ]
        ];

        if ($settings) {
            $params['body']['settings'] = $settings;
        }

        return Elasticsearch::indices()->create($params);
    }

    /**
     * 删除索引
     * @param $index
     */
    public static function dropIndex($index)
    {
        try {
            Elasticsearch::indices()->delete(['index' => $index]);
        } catch (\Exception $e) {

        }
    }

    /**
     * 判断索引是否存在
     * @param $index
     * @return mixed
     */
    public static function existsIndex($index)
    {
        return Elasticsearch::indices()->exists(['index' => $index]);
    }

}