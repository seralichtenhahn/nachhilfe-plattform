<?php

namespace App\Http\Controllers\DataTable;
use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;

abstract class DataTableController extends Controller
{
    protected $builder;

    abstract public function builder();

    public function __construct() {
      $builder = $this->builder();

       if (!$builder instanceof Builder) {
         throw new Exception('Entity builder not instance of Builder');
      }
      $this->builder = $builder;

    }

    public function index() {
      return response()->json([
        'data' => [
          'table' => $this->builder->getModel()->getTable(),
          'displayable' => array_values($this->getDisplayableColumns()),
          'updatable' => array_values($this->getUpdatableColumns()),
          'records' => $this->getRecords(),
        ]
      ]);
    }

    public function update($id, Request $request) {
      $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }

    public function getDisplayableColumns() {
      return array_diff($this->getDatabaseColumnNames(), $this->builder->getModel()->getHidden());
    }

    public function getUpdatableColumns() {
      return $this->getDisplayableColumns();
    }

    protected function getDatabaseColumnNames() {
      return Schema::getColumnListing($this->builder->getModel()->getTable());
    }

    protected function getRecords() {
      return $this->builder->limit(request()->limit)->orderBy('id', 'asc')->get($this->getDisplayableColumns());
    }
}
