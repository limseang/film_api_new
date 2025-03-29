<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\CinemBranch;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Constant\RolePermissionConstant;

class CinemaBranchDataTable extends DataTable
{
    private $tableName = 'cinem_branches';
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
                return view('cinema_branch.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('cinema_name', function($table){
                return '<span class="'.config('setup.badge_warning').'">'.$table->cinemas->name ?? ''.'</span>';
            })
            ->editColumn('ticket_price', function($table){
                return '<span class="'.config('setup.badge_info').'">'.$table->ticket_price ?? ''.'</span>';
            })
            ->editColumn('show_type', function($table){
                return '<span class="'.config('setup.badge_primary').'">'.$table->show_type ?? ''.'</span>';
            })
            ->editColumn('image_url', function ($table) {
                $pic = $table->image_url ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->editColumn('link', function ($table) {
                return '<a class="'.config('setup.badge_info').'" href="'.$table->link.'" target="_blank">'.$table->link.'</a>';
            })
            ->rawColumns(['image_url','cinema_name','status','ticket_price','show_type','link']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CinemBranch $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select([
            'id','cinema_id','name','address','phone','show_type','ticket_price','image','status',
            'created_at']);
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
        
        if(authorize(RolePermissionConstant::PERMISSION_CINEMA_BRANCH_DELETE) || authorize(RolePermissionConstant::PERMISSION_CINEMA_BRANCH_EDIT) || authorize(RolePermissionConstant::PERMISSION_CINEMA_BRANCH_VIEW_DETAIL) || authorize(RolePermissionConstant::PERMISSION_CINEMA_BRANCH_DELETE)){
            $columns[] = Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center');
        }
            // Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
        $columns[] =     Column::make('image_url')->title(trans('sma.image'))->width(20)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('name')->title(trans('sma.name'))->width(10)->addClass('text-center');
        $columns[] = Column::make('cinema_name', 'cinema_name')->title(trans('sma.cinema_name'))->addClass('text-center');
        $columns[] = Column::make('ticket_price', 'ticket_price')->title(trans('sma.ticket_price'))->addClass('text-center');
        $columns[] = Column::make('show_type', 'show_type')->title(trans('sma.show_type'))->addClass('text-center');
        $columns[] = Column::make('phone', 'phone')->title(trans('sma.phone'))->addClass('text-center');
        $columns[] = Column::make('address', 'address')->title(trans('sma.address'))->width(20);
        $columns[] = Column::make('status')->title(trans('sma.status'))->width(10)->addClass('text-center');
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
