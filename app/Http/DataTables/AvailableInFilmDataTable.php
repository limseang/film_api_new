<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\FilmAvailable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AvailableInFilmDataTable extends DataTable
{
    private $tableName = 'available_in_films';
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
                return view('available_in.assign_film_action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('releas_date', function ($table) {
                $date = $table->films->release_date ?  strtotime($table->films->release_date) : '';
                return $date ? dateFormat($date) : '';
            })

            ->editColumn('poster_image', function ($table) {
                $pic = $table->films->poster_image ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->rawColumns(['poster_image']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(FilmAvailable $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select('*')->with('films')->where('available_id', $this->available_id);
        if (request('name')) {
            $model->where('title', 'like', '%' . request('name') . '%');
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
            Column::make('poster_image')->title(trans('sma.poster'))->width(10)->addClass('text-center')->orderable(false),
            Column::make('films.title', 'films.title')->title(trans('sma.title'))->addClass('text-center'),
            Column::make('films.release_date')->title(trans('sma.release_date'))->width(10)->addClass('text-center'),
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
