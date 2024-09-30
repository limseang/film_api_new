<?php

namespace App\Http\DataTables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\ReportIncomeExpense;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Constant\RolePermissionConstant;

class ReportIncomeExpenseDataTable extends DataTable
{
    private $tableName = 'report_income_expense';
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->setRowClass(function ($table) {
                return "row_reload_".$table->id;
            })
            ->addColumn('action', function ($table) {
                return view('report_income_expense.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->date_at);
            })
            ->editColumn('attachment_url', function ($table) {
                $pic = $table->attachment_url ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->editColumn('amount', function($table){
                return number_format($table->amount, 2);
            })
            ->editColumn('currency', function($table){
                return '<span class="'.config('setup.badge_primary').'">'.$table->currency ?? ''.'</span>';
            })
            ->editColumn('type', function ($table) {
                $publish_status = ($table->type == '1') ? '<span class="'.config('setup.badge_warning').'">'.trans('sma.expense').'</span>' : '<span class="'.config('setup.badge_success').'">'.trans('sma.income').'</span>';
                return $publish_status;
            })
            ->editColumn('noted', function ($table) {
                return $table->noted;
            })
            ->editColumn('created_by_name', function ($table) {
                return $table->createdby->name ?? '';
            })
            ->editColumn('updated_by_name', function ($table) {
                return $table->updatedby->name ?? '';
            })
            ->rawColumns(['attachment_url','type','noted','currency']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ReportIncomeExpense $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select([
            'id','name','reference','type','amount','attachment','noted','updated_by','created_by','currency','created_at','updated_at','deleted_at','date_at']);
        if (request('name')) {
            $model->where(function ($query) {
                $query->orWhere('name', 'like', '%' . request('name') . '%');
            });
        }
        if (request('publish')) {
            $model->where('type', request('publish'));
        }
        if (request('soft_delete')) {
            if (request('soft_delete') == 'deleted') {
                $model->withTrashed();
                $model->where($this->tableName . '.deleted_at', '!=', null);
            }
            elseif (request('soft_delete') == 'all_records') {
                $model->withTrashed();
            }
        }
        $model->orderBy('updated_at', 'DESC');
        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId($this->tableName . '-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                            d.name = $("#name").val();
                            d.publish = $("#publish").val();
                            d.soft_delete = $("#soft_delete").val();
                        }'
            ])
            ->parameters([
                'initComplete' => 'function() {
                            $("#filter").submit(function(event) {
                                event.preventDefault();
                                $("#' . $this->tableName . '-table").DataTable().ajax.reload();
                            });
                        }'
            ])
            ->orderBy([0, "ASC"])
        ;
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        if(authorize(RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_EDIT) || authorize(RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_DELETE)){
            $columns[] = Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center');
        }
        $columns[] = Column::make('created_at')->title(trans('global.created_at'))->width(10)->addClass('text-center');
        $columns[] = Column::make('attachment_url')->title(trans('sma.attachment_url'))->width(20)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('name')->title(trans('sma.name'))->width(10);
        $columns[] = Column::make('type', 'code')->title(trans('sma.type'))->addClass('text-center');
        $columns[] = Column::make('reference', 'reference')->title(trans('sma.reference'))->orderable(false);
        $columns[] = Column::make('amount', 'amount')->title(trans('sma.amount'))->addClass('text-center');
        $columns[] = Column::make('currency')->title(trans('sma.currency'))->addClass('text-center');
        $columns[] = Column::make('noted', 'noted')->title(trans('sma.noted'))->width(20)->orderable(false);
        $columns[] = Column::make('created_by_name', 'created_by_name')->title(trans('sma.created_by'))->width(10)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('updated_by_name', 'updated_by_name')->title(trans('sma.updated_by'))->width(10)->addClass('text-center')->orderable(false);
        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return $this->tableName.'_' . date('YmdHis');
    }
}
