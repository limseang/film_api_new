<?php

namespace App\Http\DataTables;

use App\Models\RequestFilm;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RequestFilmDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($requestFilm) {
                return view('request_film.action', ['requestFilm' => $requestFilm]);
            })
            ->addColumn('user', function ($requestFilm) {
                $user = $requestFilm->user;
                return $user ? $user->name : 'Unknown';
            })
            ->addColumn('status', function ($requestFilm) {
                $status = '';
                if ($requestFilm->status == 1) {
                    $status = '<span class="badge bg-warning">Pending</span>';
                } elseif ($requestFilm->status == 2) {
                    $status = '<span class="badge bg-success">Completed</span>';
                } elseif ($requestFilm->status == 3) {
                    $status = '<span class="badge bg-danger">Rejected</span>';
                }
                return $status;
            })
            ->addColumn('film_image', function ($requestFilm) {
                $uploadController = new \App\Http\Controllers\UploadController();
                $image = $uploadController->getSignedUrl($requestFilm->film_image);
                return '<img src="' . $image . '" alt="Film Image" style="width: 50px; height: 50px;">';
            })
            ->addColumn('created_at', function ($requestFilm) {
                return $requestFilm->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['action', 'status', 'film_image'])
            ->setRowId('id');
    }

    public function query(RequestFilm $model): QueryBuilder
    {
        return $model->newQuery()->with('user');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('request-film-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" .
                "<'row'<'col-sm-12'tr>>" .
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>")
            ->orderBy(0, 'desc')
            ->buttons([
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('film_name')->title('Film Name'),
            Column::make('film_image')->title('Image'),
            Column::make('user')->title('Requested By'),
            Column::make('status')->title('Status'),
            Column::make('created_at')->title('Requested Date'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'RequestFilm_' . date('YmdHis');
    }
}