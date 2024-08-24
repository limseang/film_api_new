<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Origin;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class OriginDataTable extends DataTable
{
    private $tableName = 'origins';
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
                return view('origin.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('description', function ($table) {
                return $table->description;
            })
            ->editColumn('page_url', function ($table) {
                // replace last '/ with empty string not // with empty string
                $url = rtrim($table->url, '/');
                return '<a class="'.config('setup.badge_primary').'" href="'.$url.'/'.$table->page_id.'" target="_blank">'.$url.'/'.$table->page_id.'</a>';
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->editColumn('icon', function ($table) {
                $pic = $table->image_url ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->rawColumns(['status', 'icon','description','page_url']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Origin $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select(['id','name','url','page_id', 'description', 'status','logo','created_at' ]);
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
            Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center'),
            // Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
            Column::make('icon')->title(trans('sma.icon'))->width(10)->addClass('text-center')->orderable(false),
            Column::make('name', 'name')->title(trans('sma.name')),
            Column::make('description')->title(trans('global.description'))->width(10)->addClass('text-center'),
            Column::make('page_url')->title(trans('sma.page_url'))->width(10)->addClass('text-center'),
            Column::make('status')->title(trans('sma.status'))->width(10)->addClass('text-center'),
            Column::make('created_at')->title(trans('global.created_at'))->width(10)->addClass('text-center'),
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
