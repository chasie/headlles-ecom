<?php

namespace HeadlessEcom\Base\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    /**
     * Get the log options for the activity log.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('headless-ecom')
            ->logAll()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at']);
    }
}
