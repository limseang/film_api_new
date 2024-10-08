<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Episode;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Constant\RolePermissionConstant;

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
            ->editColumn('deleted_at', function ($table) {
                return dateTimeFormat($table->deleted_at);
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
            ->editColumn('mutile_subtitle', function ($table) {
                // 
                $ul = '<ul>';
                $subtitle = $table->subtitles ?? [];
                if (count($subtitle) == 0) {
                    return '';
                }
                foreach ($subtitle as $value) {
                    $name = $value->language->name ?? '';
                    $ul .= '<li class="'.config('setup.badge_info').' ms-2">'.$name.'</li><br>';
                }
                $ul .= '</ul>';
                return $ul;
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->editColumn('description', function ($table) {
                // html_entity_decode() is used to convert the HTML entities to their corresponding characters
                return html_entity_decode($table->description);
            })
            ->editColumn('poster_image', function ($table) {
                $pic = $table->poster_image ?? '';
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->rawColumns(['poster_image','season','episode','status','description','mutile_subtitle']) #allowed for using html code here
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
            'created_at',
            'deleted_at']);
        $model->with('subtitles');
        $model->where('film_id', $this->film_id);
        if (request('name')) {
            $model->where('title', 'like', '%' . request('name') . '%');
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
            ->orderBy([2, "ASC"])
        ;
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $soft_delete = request('soft_delete');
        if(authorize(RolePermissionConstant::PERMISSION_FILM_DELETE_EPISODE) || authorize(RolePermissionConstant::PERMISSION_FILM_EDIT_EPISODE)
        || authorize(RolePermissionConstant::PERMISSION_FILM_CHANGE_STATUS_EPISODE) || authorize(RolePermissionConstant::PERMISSION_FILM_ADD_EPISODE_SUBTITLE)
        || authorize(RolePermissionConstant::PERMISSION_FILM_EDIT_EPISODE_SUBTITLE))
        {
            $columns[] = Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center');
        }
        $columns[] = Column::make('poster_image')->title(trans('sma.poster'))->width(10)->addClass('text-center');
        $columns[] = Column::make('title', 'title')->title(trans('sma.title'))->addClass('text-center');
        $columns[] = Column::make('description')->title(trans('sma.description'))->width(10)->addClass('text-center');
        $columns[] = Column::make('season')->title(trans('sma.season'))->width(10)->addClass('text-center');
        $columns[] = Column::make('episode')->title(trans('sma.episode'))->width(10)->addClass('text-center');
        $columns[] = Column::make('mutile_subtitle')->title(trans('sma.subtitle'))->width(10)->addClass('text-center');
        $columns[] = Column::make('release_date')->title(trans('sma.release_date'))->width(10)->addClass('text-center');
        $columns[] = Column::make('status')->title(trans('sma.status'))->width(10)->addClass('text-center');
        
        if($soft_delete== 'deleted'){
            $columns[] = Column::make('deleted_at')->title(trans('global.deleted_at'))->width(10)->addClass('text-center');
        }else{
            $columns[]= Column::make('created_at','created_at')->title(trans('global.created_at'))->width(10)->addClass('text-center');
        }

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
