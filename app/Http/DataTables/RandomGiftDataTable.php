<?php

namespace App\Http\DataTables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\RendomPoint;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RandomGiftDataTable extends DataTable
{
    private $tableName = 'random_gifts';
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
                return view('random_gift.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
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
                if ($table->status == 1) {
                    $statusName = trans('sma.publish_yes');
                    $bgColor = 'btn-flat-success';
                } elseif ($table->status == 2) {
                    $statusName = trans('sma.publish_no');
                    $bgColor = 'btn-flat-danger';
                } elseif ($table->status == 3) {
                    $statusName = trans('sma.publish_cancel');
                    $bgColor = 'btn-flat-warning';
                }
            
                return '
                    <div class="btn-group">
                        <a href="#" class="btn ' . $bgColor . ' rounded-pill btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            ' . $statusName . '
                        </a>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0)" class="dropdown-item bpo-status ' . ($table->status == 1 ? 'active' : '') . '" data-click="bpo-status' . $table->id . $table->status . '" data-action="' . route('random_gift.status', [$table->id, 1]) . '">
                                ' . trans('sma.publish_yes') . '
                            </a>
                            <a href="javascript:void(0)" class="dropdown-item bpo-status ' . ($table->status == 2 ? 'active' : '') . '" data-click="bpo-status' . $table->id . $table->status . '" data-action="' . route('random_gift.status', [$table->id, 2]) . '">
                                ' . trans('sma.publish_no') . '
                            </a>
                            <a href="javascript:void(0)" class="dropdown-item bpo-status ' . ($table->status == 3 ? 'active' : '') . '" data-click="bpo-status' . $table->id . $table->status . '" data-action="' . route('random_gift.status', [$table->id, 3]) . '">
                                ' . trans('sma.publish_cancel') . '
                            </a>
                        </div>
                    </div>';
            })
            
            ->editColumn('gift_name', function ($table) {
                return $table->gifts->name ?? '';
            })
            ->editColumn('user_name', function ($table) {
                return $table->users->name ?? '';
            })
            ->rawColumns(['image_url','code','status','point']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(RendomPoint $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select([
            'id','user_id','gift_id','point','phone_number','code','status','created_at','updated_at','deleted_at']);
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
        return [
            Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center'),
            Column::make('gift_name')->title(trans('sma.gift_name'))->orderable(false),
            Column::make('user_name')->title(trans('sma.user_name'))->orderable(false),
            Column::make('phone_number')->title(trans('sma.phone_number'))->orderable(false),
            Column::make('code', 'code')->title(trans('sma.code'))->addClass('text-center'),
            Column::make('point', 'point')->title(trans('sma.point'))->addClass('text-center'),
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
