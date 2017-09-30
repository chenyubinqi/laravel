<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 22/9/17
 * Time: 11:28
 */

namespace App\Search;

use App\Search\BaseSearch;

use DB;


class TestSearch extends BaseSearch
{

    const INDEX = 'pms_product';

    const TYPE = 'pms_product';

    private static $_mappings = [
        'pms_product' => [
            'properties' => [
                'product_name' => [
                    'analyzer' => 'ik_max_word',
                    'type' => 'text',
                    'fields' => [
                        'keyword' => [
                            'type' => 'keyword',
                            'ignore_above' => 256,
                        ]
                    ],
                ],
                'product_name_en' => [
                    'analyzer' => 'english',
                    'type' => 'text'
                ],
                'source' => [
                    'type' => 'long'
                ],
                'main_category' => [
                    'type' => 'long'
                ],
                'formula' => [
                    'type' => 'keyword'
                ],
                'status' => [
                    'type' => 'long'
                ],
                'cas_no' => [
                    'type' => 'keyword'
                ],
                'product_code' => [
                    'type' => 'keyword'
                ],
                'en_synonyms' => [
                    'type' => 'text',
                    'fields' => [
                        'keyword' => [
                            'type' => 'keyword',
                            'ignore_above' => 256,
                        ]
                    ],
                ],
                'product_category' => [
                    'type' => 'long'
                ],
                'product_parent_category' => [
                    'type' => 'long'
                ],
                'sort' => [
                    'type' => 'long'
                ],
                'create_time' => [
                    'type' => 'date',
                    "format" => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                ],
            ]
        ]
    ];

    private static $_settings = [
        'number_of_shards' => 5,
        'number_of_replicas' => 0,
    ];

    /**
     * mapping字段
     * @var string
     */
    private $id = [];
    private $productName = '';
    private $productNameEn = '';
    private $source = 0;
    private $mainCategory = 0;
    private $formula = '';
    private $status = 0;
    private $casNo = '';
    private $productCode = '';
    private $zhSynonyms = '';
    private $enSynonyms = '';
    private $productCategory = [];
    private $productParentCategory = [];
    private $sort = '';
    private $createTimeStart = '';
    private $createTimeEnd = '';


    public static function getMappings()
    {
        return self::$_mappings;
    }

    public static function getSettings()
    {
        return self::$_settings;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    public function setProductNameEn($productNameEn)
    {
        $this->productNameEn = $productNameEn;

        return $this;
    }

    public function setSource($source)
    {
        $this->source = (int)$source;

        return $this;
    }

    public function setMainCategory($mainCategory)
    {
        $this->mainCategory = (int)$mainCategory;

        return $this;
    }

    public function setFormula($formula)
    {
        $this->formula = $formula;

        return $this;
    }

    public function setStatus($status)
    {
        $this->status = (int)$status;

        return $this;
    }

    public function setCasNo($casNo)
    {
        $this->casNo = $casNo;

        return $this;
    }

    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function setZhSynonyms($zhSynonyms)
    {
        $this->zhSynonyms = $zhSynonyms;

        return $this;
    }

    public function setEnSynonyms($enSynonyms)
    {
        $this->enSynonyms = $enSynonyms;

        return $this;
    }

    public function setProductCategory($productCategory)
    {
        if (!is_array($productCategory) && $productCategory == 0) {
            return $this;
        }

        $this->productCategory = is_array($productCategory) ? $productCategory : [(int)$productCategory];

        return $this;

    }

    public function setProductParentCategory($productParentCategory)
    {
        if (!is_array($productParentCategory) && $productParentCategory == 0) {
            return $this;
        }

        $this->productParentCategory = is_array($productParentCategory) ? $productParentCategory : [(int)$productParentCategory];

        return $this;
    }

    public function setSort($sort)
    {
        $this->sort = (int)$sort;

        return $this;
    }

    public function setCreateTimeRange($startTime, $endTime)
    {
        $startTime && $this->createTimeStart = $startTime;
        $endTime && $this->createTimeEnd = $endTime;

        return $this;
    }

    /**
     * 获取查询条件
     * @return array
     */
    protected function getCondition()
    {

        $query = $filter = [];

        //注意_id 是数组
        if (!empty($this->id)) {
            if (is_array($this->id)) {
                $query['bool']['must'][] = [
                    'terms' => [
                        '_id' => $this->id
                    ]
                ];
            } else {
                $query['bool']['must'][] = [
                    'term' => [
                        '_id' => $this->id
                    ]
                ];
            }
        }

        if (!empty($this->productName)) {
            $query['bool']['must'][] = [
                'match' => [
                    'product_name' => $this->productName
                ]
            ];
        }

        if (!empty($this->casNo)) {
            $query['bool']['must'][] = [
                'term' => [
                    'cas_no' => $this->casNo
                ]
            ];
        }

        if (!empty($this->productParentCategory)) {

            $query['bool']['must'][] = [
                'terms' => [
                    'product_parent_category' => $this->productParentCategory
                ],
            ];
        }

        if ($this->status > 0) {
            $query['bool']['filter'][] = [
                'term' => [
                    'status' => $this->status,
                ],
            ];
        }

        if (!empty($this->source)) {
            $query['bool']['filter'][] = [
                'term' => [
                    'source' => $this->source,
                ],
            ];
        }

        if ($this->createTimeStart > 0 && $this->createTimeEnd > 0) {
            $query['bool']['filter'][] = [
                'range' => [
                    'create_time' => [
                        'gte' => $this->createTimeStart,
                        'lt' => $this->createTimeEnd,
                    ],
                ],
            ];
        } elseif ($this->createTimeStart > 0) {
            $query['bool']['filter'][] = [
                'range' => [
                    'create_time' => [
                        'gte' => $this->createTimeStart,
                    ],
                ],
            ];
        } elseif ($this->createTimeEnd > 0) {
            $query['bool']['filter'][] = [
                'range' => [
                    'create_time' => [
                        'lt' => $this->createTimeEnd,
                    ],
                ],
            ];
        }

        $body = [];
        if (!empty($query)) {

            $body = [
                'query' => $query,
            ];
        }

        //dd(json_encode($body));

        return $body;
    }

    /**
     * 获取结果
     * @return array
     */
    public function getResult()
    {

        $body = $this->getCondition();

        foreach ($this->sortFields as $field => $sort) {
            $body['sort'][$field] = $sort == self::SORT_ASC ? 'asc' : 'desc';
        }

        list ($count, $list) = $this->query(
            self::INDEX, self::TYPE, $body);

        return [
            $count,
            $list,
        ];
    }

    /**
     * 从数据库写入数据到ES
     * @param array $ids
     * @return array
     */
    public static function getUpdateDataEs(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $idsStr = implode(',', $ids);

        $sql = "SELECT pms_product.*,
                        GROUP_CONCAT(DISTINCT `pms_relation_product_category`.`category_id` SEPARATOR ',') AS `product_category`                
                    FROM `pms_product`
                        LEFT JOIN `pms_relation_product_category` ON `pms_relation_product_category`.`product_id` = `pms_product`.`id`
                        LEFT JOIN `pms_category` ON `pms_category`.`id` = `pms_relation_product_category`.`category_id`
                        WHERE `pms_product`.`id` IN ($idsStr) GROUP BY `pms_product`.`id`;";
        $list = DB::select($sql);

        $result = [];

        foreach ($list as $data) {

            $category = array_values(
                array_unique(
                    $data->product_category ? array_map(
                        function ($item) {
                            return (int)$item;
                        }, explode(',', $data->product_category)) : []));

            //根据主分类提取信息
            !in_array($data->main_category, $category) && $category[] = $data->main_category;

            //提取主分类父级分类信息
            $maincateGory = DB::table('pms_category')->where('id', $data->main_category)->first();

            $parentCategory = [];
            if ($maincateGory) {
                $tmp = explode(',', $maincateGory->path);
                foreach ($tmp as $v) {
                    if (!empty($v)) {
                        $parentCategory[] = (int)$v;
                    }
                }

                $parentCategory = array_values(array_unique($parentCategory));
            }

            $result[$data->id] = [
                'product_name' => $data->product_name,
                'product_name_en' => $data->product_name_en,
                'source' => (int)$data->source,
                'main_category' => (int)$data->main_category,
                'formula' => (string)$data->formula,
                'status' => (int)$data->status,
                'cas_no' => (string)$data->cas_no,
                'product_code' => (string)$data->product_code,
                'zh_synonyms' => (string)$data->zh_synonyms,
                'en_synonyms' => (string)$data->en_synonyms,
                'product_category' => ($category),
                'product_parent_category' => $parentCategory,
                'sort' => (int)$data->sort,
                'create_time' => $data->create_time
            ];
        }

        return $result;
    }
}