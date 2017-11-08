<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 1/11/17
 * Time: 09:16
 */

namespace App\Repositories;

use Basemkhirat\Elasticsearch\Collection;
use Basemkhirat\Elasticsearch\Model;

trait BaseRepository
{
    public $esPage = 1;
    public $esPageSize = 10;
    public $esMaxResult = 10000;

    /**
     * Get number of records
     *
     * @return array
     */
    public function getNumber()
    {
        return $this->model->count();
    }

    /**
     * Update columns in the record by id.
     *
     * @param $id
     * @param $input
     * @return App\Model|User
     */
    public function updateColumn($id, $input)
    {
        $this->model = $this->getById($id);

        foreach ($input as $key => $value) {
            $this->model->{$key} = $value;
        }

        return $this->model->save();
    }

    /**
     * Destroy a model.
     *
     * @param  $id
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->getById($id)->delete();
    }

    /**
     * Get model by id.
     *
     * @return App\Model
     */
    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Get all the records
     *
     * @return array User
     */
    public function all()
    {
        return $this->model->get();
    }

    /**
     * Get number of the records
     *
     * @param  int $number
     * @param  string $sort
     * @param  string $sortColumn
     * @return Paginate
     */
    public function page($number = 10, $sort = 'desc', $sortColumn = 'created_at')
    {
        return $this->model->orderBy($sortColumn, $sort)->paginate($number);
    }

    /**
     * Store a new record.
     *
     * @param  $input
     * @return User
     */
    public function store($input)
    {
        return $this->save($this->model, $input);
    }

    /**
     * Update a record by id.
     *
     * @param  $id
     * @param  $input
     * @return User
     */
    public function update($id, $input)
    {
        $this->model = $this->getById($id);

        return $this->save($this->model, $input);
    }

    /**
     * Save the input's data.
     *
     * @param  $model
     * @param  $input
     * @return User
     */
    public function save($model, $input)
    {
        $model->fill($input);

        $model->save();

        return $model;
    }

    /**
     * 重置分页
     * @param $page
     * @param $pageSize
     */
    public function resetEsPage($page, $pageSize)
    {
        if ($page == 1) {
            $this->esPage = $page;
            if ($pageSize <= $this->esMaxResult) {
                $this->esPageSize = $pageSize;
            }
        } else if ((($page - 1) * $pageSize + $pageSize) > $this->esMaxResult) {
            $this->esPage = 1;
            if ($pageSize <= $this->esMaxResult) {
                $this->esPageSize = $pageSize;
            }
        } else {
            $this->esPage = $page;
            $this->esPageSize = $pageSize;
        }

        return;

    }

    /**
     * 处理ES返回的结果集
     * @param $result
     * @return array
     */
    public function resetEsResult($result)
    {
        if ($result instanceof Collection) {
            $data['total'] = $result->total;
            if ($result->total > $this->esMaxResult) {
                $data['total'] = $this->esMaxResult;
            }
            $data['data'] = $result->toArray();
            return $data;
        } elseif ($result instanceof Model) {
            $data = $result->toArray();
            return $data;
        }

        return $result;

    }

}