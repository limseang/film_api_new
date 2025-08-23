<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Cast;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Constant\RolePermissionConstant;
use App\Traits\AlibabaStorage;

class CastDataTable extends DataTable
{
    private $tableName = 'casts';
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
                return view('cast.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('film_name',function($table){
                return '<span class="'.config('setup.badge_secondary').'">'.$table->film_name.'</span>';
            })
            ->editColumn('actor_name', function($table){
                return '<span class="'.config('setup.badge_warning').'">'.$table->actor_name.'</span>';
            })
            // ->editColumn('image_url', function ($table) {
            //     $pic = $table->image ?? '';
            //     if ($pic) {
            //         $pic = getSignedUrl($pic);
            //     }
            //     return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            // })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->rawColumns(['image_url','actor_name','film_name','status']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Cast $model): QueryBuilder
    {
        $model = $model->newQuery();
        // Eager load relationships to prevent N+1 queries
        $model->select([
            'casts.id','casts.character',
            'actor_id',
            'film_id',
            'position',
            'casts.image',
            'casts.status',
            'films.title as film_name',
            'artists.name as actor_name',
            'casts.created_at','casts.deleted_at','casts.updated_at' ]);
        $model->join('films', 'films.id', '=', 'casts.film_id');
        $model->join('artists', 'artists.id', '=', 'casts.actor_id');
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
        if(authorize(RolePermissionConstant::PERMISSION_CAST_EDIT) || authorize(RolePermissionConstant::PERMISSION_CAST_DELETE) || authorize(RolePermissionConstant::PERMISSION_CAST_CHANGE_STATUS)){
            $columns[] = Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center');
        }
            // Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
        // $columns[] = Column::make('image_url')->title(trans('sma.image'))->width(20)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('character', 'character')->title(trans('sma.character'))->addClass('text-center');
        $columns[] = Column::make('position')->title(trans('sma.position'))->width(10)->addClass('text-center');
        $columns[] = Column::make('actor_name')->title(trans('sma.actor_name'))->width(10)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('film_name')->title(trans('sma.film_name'))->width(10)->addClass('text-center')->orderable(false);
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
