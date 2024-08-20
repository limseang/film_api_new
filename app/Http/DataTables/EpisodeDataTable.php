<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Episode;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EpisodeDataTable extends DataTable
{
    private $tableName = 'episodes';
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
                return view('episode.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('releas_date', function ($table) {
                $date = strtotime($table->release_date);
                return dateFormat($date);
            })
            ->editColumn('season', function ($table) {
                return '<span class="'.config('setup.badge_info').'">'.$table->season.'</span>';
            })
            ->editColumn('episode', function ($table) {
                return  '<span class="'.config('setup.badge_success').'">'.$table->episode.'</span>';
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->editColumn('poster_image', function ($table) {
                $pic = $table->poster_image ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->rawColumns(['poster_image','season','episode','status']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Episode $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select([
            'id',
            'title',
            'description',
            'episode',
            'season',
            'release_date',
            'poster',
            'status',
            'created_at' ]);
        $model->where('film_id', $this->film_id);
        if (request('name')) {
            $model->where('title', 'like', '%' . request('name') . '%');
        }
        if (request('publish')) {
            $model->where('status', request('publish'));
        }
        $model->orderBy('created_at', 'ASC')
                ->orderBy('episode', 'ASC');
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
            Column::make('title', 'title')->title(trans('sma.title'))->addClass('text-center'),
            Column::make('description')->title(trans('sma.description'))->width(10)->addClass('text-center'),
            Column::make('season')->title(trans('sma.season'))->width(10)->addClass('text-center'),
            Column::make('episode')->title(trans('sma.episode'))->width(10)->addClass('text-center'),
            Column::make('release_date')->title(trans('sma.release_date'))->width(10)->addClass('text-center'),
            Column::make('status')->title(trans('sma.status'))->width(10)->addClass('text-center'),
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
