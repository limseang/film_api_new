<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Artist;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Constant\RolePermissionConstant;

class ArtistDataTable extends DataTable
{
    private $tableName = 'artists';
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
                return view('artist.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('birth_date', function ($table) {
                return $table->birth_date;
            })
            ->editColumn('death_date', function ($table) {
                return $table->death_date;
            })
            ->editColumn('icon', function ($table) {
                $pic =  $table->avatar_url ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->rawColumns(['status', 'icon']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Artist $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select(['id','name', 'birth_date', 'death_date', 'biography', 'known_for', 'status', 'nationality' , 'profile','created_at', 'deleted_at' ]);
        if (request('name')) {
            $model->where(function ($query) {
                $query->orWhere('name', 'like', '%' . request('name') . '%');
            });
        }
        if (request('publish')) {
            $model->where('status', request('publish'));
        }
        if (request('origin')) {
            $model->where('origin_id', request('origin'));
        }
        if (request('film')) {
            $model->where('film_id', request('film'));
        }

        if (request('category')) {
            $model->where('category_id', request('category'));
        }

        if (request('type')) {
            $model->where('type_id', request('type'));
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
                            d.origin = $("#origin").val();
                            d.film = $("#film").val();
                            d.category = $("#category").val();
                            d.type = $("#type").val();
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
            ->orderBy([2, "ASC"])
        ;
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {

        if(authorize(RolePermissionConstant::PERMISSION_ARTIST_EDIT) || authorize(RolePermissionConstant::PERMISSION_ARTIST_DELETE) || authorize(RolePermissionConstant::PERMISSION_ARTIST_CHANGE_STATUS)){
            $columns [] = Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center');
        }
            // Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
        $columns [] =  Column::make('icon')->title(trans('sma.avatar_artist'))->width(10)->addClass('text-center')->orderable(false);
        $columns [] =  Column::make('name', 'name')->title(trans('sma.artist_name'))->width(30);
        $columns [] =  Column::make('known_for')->title(trans('sma.know_for'))->width(10)->addClass('text-center');
        $columns [] =  Column::make('nationality_name')->title(trans('sma.national_name'))->width(10)->addClass('text-center')->orderable(false);
        $columns [] =  Column::make('birth_date')->title(trans('sma.birth_date'))->width(10)->addClass('text-center');
        $columns [] =  Column::make('death_date')->title(trans('sma.death_date'))->width(10)->addClass('text-center');
        $columns [] =  Column::make('status', 'status')->title(trans('sma.status'))->width(10)->addClass('text-center');
        $columns [] =  Column::make('created_at')->title(trans('sma.created_at'))->width(10)->addClass('text-center');
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
