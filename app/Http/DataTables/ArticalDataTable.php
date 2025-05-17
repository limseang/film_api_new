<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\Artical;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Constant\RolePermissionConstant;

class ArticalDataTable extends DataTable
{
    private $tableName = 'articals';
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
                return view('artical.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            // ->editColumn('multiple_tag', function ($table) {
            //     //
            //     $ul = '<ul>';
            //     $tags = $table->tag ?? [];
            //     if (count($tags) == 0) {
            //         return '';
            //     }
            //     foreach ($tags as $value) {
            //         $ul .= '<li class="'.config('setup.badge_warning').' ms-2">'.$value->name.'</li><br>';
            //     }
            //     $ul .= '</ul>';
            //     return $ul;
            // })
            // ->editColumn('total_like', function ($table) {
            //     return '<span class="'.config('setup.badge_info').'">'.$table->likes->count().'</span>';
            // })
            // ->editColumn('total_comment', function($table){
            //     return '<span class="'.config('setup.badge_info').'">'.$table->comments->count().'</span>';
            // })
            ->editColumn('origin_name', function ($table) {
                return '<span class="'.config('setup.badge_info').'">'.$table->origin_name ?? ''.'</span>';
            })
            ->editColumn('category_name',function($table){
                return '<span class="'.config('setup.badge_secondary').'">'.$table->category_name ?? ''.'</span>';
            })
            ->editColumn('view', function ($table) {
                return  '<span class="'.config('setup.badge_success').'">'.$table->view.'</span>';
            })
            ->editColumn('type_name', function($table){
                return '<span class="'.config('setup.badge_info').'">'.$table->type_name.'</span>';
            })
             ->editColumn('image_url', function ($table) {
                $pic = $table->image ?? '';
                if ($pic) {
                    $pic = getSignedUrl($pic);
                }
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->rawColumns(['image_url','view','origin_name','category_name','type_name','multiple_tag','running_time','status']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Artical $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select([
            'articals.id','articals.title',
            'origin_id',
            'articals.image',
            'category_id',
            'categories.name as category_name',
            'types.name as type_name',
            'origins.name as origin_name',
            'articals.type_id',
            'articals.like',
            'articals.comment',
            'articals.share',
            'articals.tag_id',
            'articals.view',
            'articals.film_id',
            'articals.status',
            'articals.created_at',
            'articals.deleted_at',
            'articals.updated_at'
         ]);
         $model->join('categories', 'categories.id', '=', 'articals.category_id');
        $model->join('origins', 'origins.id', '=', 'articals.origin_id');
        $model->join('types', 'types.id', '=', 'articals.type_id');
        // $model->with(['category','type','origin','tag','likes','comments']);
        if (request('name')) {
            $model->where('title', 'like', '%' . request('name') . '%');
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

        if(authorize(RolePermissionConstant::PERMISSION_ARTICAL_EDIT) || authorize(RolePermissionConstant::PERMISSION_ARTICAL_DELETE)){
            $columns[] = Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center');
        }
            // Column::computed('DT_RowIndex', trans('global.n_o'))->width(50)->addClass('text-center'),
        $columns[] = Column::make('image_url')->title(trans('sma.image'))->width(10)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('title', 'title')->title(trans('sma.title'))->addClass('text-right');
        $columns[] = Column::make('origin_name')->title(trans('sma.origin'))->width(10)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('category_name')->title(trans('sma.category'))->width(10)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('type_name')->title(trans('sma.type_name'))->width(10)->addClass('text-center')->orderable(false);
        // $columns[] = Column::make('multiple_tag')->title(trans('sma.tag'))->width(10)->addClass('text-center')->orderable(false);
        $columns[] = Column::make('view')->title(trans('sma.total_view'))->width(10)->addClass('text-center');
        // $columns[] = Column::make('total_like')->title(trans('sma.total_like'))->width(10)->addClass('text-center')->orderable(false);
        // $columns[] = Column::make('total_comment')->title(trans('sma.total_comment'))->width(10)->addClass('text-center')->orderable(false);
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
