<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Director;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DirectorDataTable extends DataTable
{
    private $tableName = 'directors';
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
                return view('director.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('birth_date', function ($table) {
                return dateFormat($table->birth_date);
            })
            ->editColumn('death_date', function ($table) {
                return dateFormat($table->death_date);
            })
            ->editColumn('biography', function ($table) {
                $biography = strlen($table->biography) > 50 ? substr($table->biography, 0, 50) . '...' : $table->biography;
                return '<blockquote class="blockquote text-center py-2 mb-0">'.$biography.'</blockquote>';
            })
            ->editColumn('icon', function ($table) {
                $pic = ($table->avatar_url) ? $table->avatar_url : 'default.png';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->rawColumns(['status', 'icon','biography']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Director $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select(['id','name', 'birth_date', 'death_date', 'biography', 'know_for', 'status', 'nationality' , 'avatar','created_at' ]);
        if (request('name')) {
            $model->where(function ($query) {
                $query->orWhere('name', 'like', '%' . request('name') . '%');
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
            ->orderBy([2, "ASC"])
        ;
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
            Column::make('icon')->title(trans('sma.avatar_director'))->width(10)->addClass('text-center')->orderable(false),
            Column::make('name', 'name')->title(trans('sma.director_name'))->width(30),
            Column::make('know_for')->title(trans('sma.know_for'))->width(10)->addClass('text-center'),	
            Column::make('nationality_name')->title(trans('sma.national_name'))->width(10)->addClass('text-center')->orderable(false),
            Column::make('birth_date')->title(trans('sma.birth_date'))->width(10)->addClass('text-center'),
            Column::make('death_date')->title(trans('sma.death_date'))->width(10)->addClass('text-center'),
            Column::make('status', 'status')->title(trans('sma.status'))->width(10)->addClass('text-center'),
            Column::make('biography')->title(trans('sma.biography'))->width(10)->addClass('text-center'),
            Column::make('created_at')->title(trans('sma.created_at'))->width(10)->addClass('text-center'),
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
