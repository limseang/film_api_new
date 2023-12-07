<?php

namespace App\Console\Commands;

use App\Http\Controllers\CommentController;
use http\Env\Request;
use Illuminate\Console\Command;
use App\Http\Controllers\ArticalController;
use Illuminate\Support\Facades\Log;

class PostArticle extends Command
{
    protected $signature = 'post:article';

    protected $description = 'Post an article';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $articleController = new CommentController();
        $articleController->createFromCommand();
        Log::info('Post an article');
    }
}
