<?php

namespace App\Repositories;

use League\Flysystem\Exception;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{

    protected $model;
    protected $scopesQuery;

    function __construct()
    {
        $this->makeModel();
    }

    abstract public function model();

    function eagerLoad(){

    }

    function getModel(){
        return $this->model;
    }

    function makeModel(){
        $model = $this->model();

        if (!$model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;
    }

    function resetModel(){
        $this->makeModel();
    }

    /**
     * Method for insert multiple scopes
     * @param \Closure $scope
     * @return $this
     */
    function scopes($scopes){
        $this->scopesQuery = $scopes;

        return $this;
    }
    function resetScopes()
    {
        $this->scopesQuery = null;
        return $this;
    }

    function get(){
        $this->eagerLoad();
        $this->applyScopes();

        $result = $this->model->get();

        $this->resetModel();
        $this->resetScopes();
        return $result;
    }

    function update(array $params){
        return $this->model->update($params);
    }

    function findBy($arg, $type = 'id')
    {
        $this->eagerLoad();
        $this->applyScopes();
        if($type == 'id'){
            $result = $this->model->find($arg);
        }else{
            $result = $this->model->where($type, $arg)->first();
        }

        $this->resetScopes();
        $this->resetModel();
        return $result;
    }

    function whereCond(array $where)
    {
        $this->applyConditions($where);

        return $this;
    }

    protected function applyScopes()
    {
        if(empty($this->scopesQuery)) return $this;
        foreach ($this->scopesQuery as $scope){
            if(is_array($scope)){
                foreach ($scope as $s => $sVals){
                    $callback = $this->makeScope(function ($q) use ($s, $sVals){
                        return $q->$s(...$sVals);
                    });
                }
            }else{
                $callback = $this->makeScope(function ($q) use ($scope){
                    return $q->$scope();
                });

            }
            $this->model = $callback($this->model);
        }

        return $this;
    }

    public function makeScope(\Closure $scope){
        return $scope;
    }
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val, $type) = $value;
                if($type == null){
                    $this->model = $this->model->where($field, $condition, $val);
                }else{
                    switch ($type){
                        case 'Date':
                            $this->model = $this->model->whereDate($field, $condition, $val);
                            break;
                        case 'Null':
                            $this->model = $this->model->whereNull($field);
                            break;
                        case 'NotNull':
                            $this->model = $this->model->whereNotNull($field);
                            break;
                        case 'In':
                            $this->model = $this->model->whereIn($field, $val);
                            break;
                        default:
                            throw new Exception('false');

                    }
                }
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }
    /*
     * Overwrite Model
     */
    function limit($val){
        $this->model = $this->model->limit($val);
        return $this;
    }

    function latest(){
        $this->model = $this->model->latest();
        return $this;
    }
}