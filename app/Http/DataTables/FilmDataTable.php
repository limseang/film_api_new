<?php

namespace App\Http\DataTables;

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
            ->editColumn('film_category_name', function ($table) {
                return '<span class="'.config('setup.badge_primary').'">'.$table->film_category_name.'</span>';
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
            ->rawColumns(['poster_image','view','film_category_name','genre_name','tag_name','running_time','cover_image','director_name']) #allowed for using html code here
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
            $model->where(function ($query) {
                $query->orWhere('title', 'like', '%' . request('title') . '%');
            });
        }
        if (request('publish')) {
            $model->where('status', request('publish'));
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
        return [
            Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
            Column::make('poster_image')->title(trans('sma.poster'))->width(10)->addClass('text-center'),
            Column::make('cover_image')->title(trans('sma.cover'))->width(10)->addClass('text-center'),
            Column::make('title', 'title')->title(trans('sma.title')),
            Column::make('film_category_name')->title(trans('sma.film_category_name'))->width(10)->addClass('text-center'),
            Column::make('genre_name')->title(trans('sma.genre_name'))->width(10)->addClass('text-center'),
            Column::make('tag_name')->title(trans('sma.tag_name'))->width(10)->addClass('text-center'),
            Column::make('running_time')->title(trans('sma.running_time'))->width(10)->addClass('text-center'),
            Column::make('view')->title(trans('sma.total_view'))->width(10)->addClass('text-center'),
            Column::make('director_name')->title(trans('sma.director_name'))->width(10)->addClass('text-center'),
            Column::make('release_date')->title(trans('sma.release_date'))->width(10)->addClass('text-center'),
            Column::make('created_at')->title(trans('global.created_at'))->width(10)->addClass('text-center'),
            Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return $this->tableName.'_' . date('YmdHis');
    }
}
