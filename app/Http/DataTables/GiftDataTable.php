<?php

namespace App\Http\DataTables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Gift;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Constant\RolePermissionConstant;

class GiftDataTable extends DataTable
{
    private $tableName = 'gifts';
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
                return view('gift.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('expired_date', function ($table) {
                // convert date to string format after using dateTimeFormat() function
                $dateExpired = date('d-m-Y h:i:s', strtotime($table->expired_date));
                return $dateExpired;
            })  
            ->editColumn('code', function($table){
                return '<span class="'.config('setup.badge_info').'">'.$table->code ?? ''.'</span>';
            })
            ->editColumn('point', function($table){
                return '<span class="'.config('setup.badge_primary').'">'.$table->point ?? ''.'</span>';
            })
            ->editColumn('image_url', function ($table) {
                $pic = $table->image_url ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->editColumn('description', function ($table) {
                return $table->description;
            })
            ->rawColumns(['image_url','code','status','point','description']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Gift $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select([
            'id','name','description','image','code','noted','point','quantity','status', 'expired_date','created_at','updated_at','deleted_at']);
        if (request('name')) {
            $model->where(function ($query) {
                $query->orWhere('character', 'like', '%' . request('name') . '%');
            });
        }
        if (request('publish')) {
            $model->where('status', request('publish'));
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
        if(authorize(RolePermissionConstant::PERMISSION_GIFT_EDIT) || authorize(RolePermissionConstant::PERMISSION_GIFT_DELETE) || authorize(RolePermissionConstant::PERMISSION_GIFT_CHANGE_STATUS) || authorize(RolePermissionConstant::PERMISSION_GIFT_DELETE)){
            $columns[] = Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center');
        }
            // Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
        $columns[] = Column::make('image_url')->title(trans('sma.image'))->width(20)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('name')->title(trans('sma.name'))->width(10)->addClass('text-center');
        $columns[] = Column::make('code', 'code')->title(trans('sma.code'))->addClass('text-center');
        $columns[] = Column::make('point', 'point')->title(trans('sma.point'))->addClass('text-center');
        $columns[] = Column::make('noted', 'noted')->title(trans('sma.noted'))->addClass('text-center');
        $columns[] = Column::make('quantity', 'quantity')->title(trans('sma.quantity'))->width(10)->addClass('text-center');
        $columns[] = Column::make('expired_date', 'expired_date')->title(trans('sma.expired_date'))->width(10)->addClass('text-center');
        $columns[] = Column::make('status')->title(trans('sma.status'))->width(10)->addClass('text-center');
        $columns[] = Column::make('description', 'description')->title(trans('sma.description'))->width(20);
        $columns[] = Column::make('created_at')->title(trans('global.created_at'))->width(10)->addClass('text-center');
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
