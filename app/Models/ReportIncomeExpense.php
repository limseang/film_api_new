<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use App\Traits\AlibabaStorage;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportIncomeExpense extends Model
{
    use HasFactory, LogsActivity, AlibabaStorage, SoftDeletes;

    protected $table = 'report_income_expense';

    protected $fillable = [
        'name',
        'reference',
        'amount',
        'type',
        'noted',
        'attachment',
        'date_at',
        'currency',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'attachment_url'
    ];

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? $this->getSignedUrl($this->attachment) : null;
    }

    public function createdby()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $dontSubmitEmptyLogs = true;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName($this->table)
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function tapActivity(Activity $activity)
    {
        $activity->default_field    = "{$this->name}- {$this->amount}";
        $activity->log_name         = $this->table;
        $activity->causer_id        = Auth::user()->id ?? null;
    }

}
