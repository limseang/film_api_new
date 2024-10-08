<?php

namespace App\Http\DataTables;

use App\Constant\RolePermissionConstant;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Film;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FilmDataTable extends DataTable
{
    private $tableName = 'films';
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
                return view('film.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('releas_date', function ($table) {
                $date = strtotime($table->release_date);
                return dateFormat($date);
            })
            ->editColumn('multiple_category', function ($table) {
                // 
                $ul = '<ul>';
                $categories = $table->multiple_category ?? [];
                if (count($categories) == 0) {
                    return '';
                }
                foreach ($categories as $category) {
                    $ul .= '<li class="'.config('setup.badge_info').' ms-2">'.$category.'</li><br>';
                }
                $ul .= '</ul>';
                return $ul;
            })
            ->editColumn('genre_name', function ($table) {
                return '<span class="'.config('setup.badge_info').'">'.$table->genre_name.'</span>';
            })
            ->editColumn('tag_name',function($table){
                return '<span class="'.config('setup.badge_secondary').'">'.$table->tag_name.'</span>';
            })
            ->editColumn('view', function ($table) {
                return  '<span class="'.config('setup.badge_success').'">'.$table->view.'</span>';
            })
            ->editColumn('running_time', function($table){
                return '<span class="'.config('setup.badge_warning').'">'.$table->running_time.'</span>';
            })
            ->editColumn('director_name', function($table){
                return '<span class="'.config('setup.badge_warning').'">'.$table->director_name.'</span>';
            })
            ->editColumn('poster_image', function ($table) {
                $pic = $table->poster_image ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->editColumn('cover_image', function ($table) {
                $pic = $table->cover_image ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->rawColumns(['poster_image','view','multiple_category','genre_name','tag_name','running_time','cover_image','director_name']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Film $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select([
            'id','title',
            'poster',
            'cover',
            'director',
            'category',
            'genre_id',
            'tag',
            'running_time',
            'view',
            'release_date',
            'created_at' ]);
        if (request('name')) {
            $model->where('title', 'like', '%' . request('name') . '%');
        }
        if (request('publish')) {
            $model->where('status', request('publish'));
        }
        if (request('language')) {
            $model->where('language', request('language'));
        }
        if (request('tag')) {
            $model->where('tag', request('tag'));
        }
        if (request('genre')) {
            $model->where('genre_id', request('genre'));
        }
        if (request('type')) {
            $model->where('type', request('type'));
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
                            d.language = $("#language").val();
                            d.tag = $("#tag").val();
                            d.genre = $("#genre").val();
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
            ->orderBy([0, "ASC"])
        ;
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        
        if(authorize(RolePermissionConstant::PERMISSION_FILM_EDIT) || authorize(RolePermissionConstant::PERMISSION_FILM_DELETE)
            || authorize(RolePermissionConstant::PERMISSION_FILM_ADD_EPISODE) || authorize(RolePermissionConstant::PERMISSION_FILM_ASSIGN_AVAILABLE_IN)){
            $columns[] = Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center');
        }
            // Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
        $columns[] = Column::make('poster_image')->title(trans('sma.poster'))->width(10)->orderable(false);
        $columns[] = Column::make('cover_image')->title(trans('sma.cover'))->width(10)->orderable(false);
        $columns[] = Column::make('title', 'title')->title(trans('sma.title'))->width(20);
        $columns[] = Column::make('multiple_category')->title(trans('sma.film_category_name'))->width(10)->orderable(false);
        $columns[] = Column::make('genre_name')->title(trans('sma.genre_name'))->width(10)->orderable(false);
        $columns[] = Column::make('tag_name')->title(trans('sma.tag_name'))->width(10)->orderable(false);
        $columns[] = Column::make('running_time')->title(trans('sma.running_time'))->width(10);
        $columns[] = Column::make('view')->title(trans('sma.total_view'))->width(10)->addClass('text-center');
        $columns[] = Column::make('director_name')->title(trans('sma.director_name'))->width(10)->addClass('text-center');
        $columns[] = Column::make('release_date')->title(trans('sma.release_date'))->width(10)->addClass('text-center');
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
