<?php
namespace App\Repositories\Utility;

use App\Models\Utility\Backup;
use Illuminate\Validation\ValidationException;

class BackupRepository
{
    protected $backup;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Backup $backup
    ) {
        $this->backup = $backup;
    }

    /**
     * Find backup with given name or throw an error.
     *
     * @param string $name
     * @return Backup
     */
    public function findOrFail($name)
    {
        if (! \Storage::exists('backup/'.$name)) {
            throw ValidationException::withMessages(['message' => trans('utility.could_not_find_backup')]);
        }
    }

    /**
     * Paginate all backups using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = gv($params, 'sort_by', 'created_at');
        $order       = gv($params, 'order', 'desc');
        $page_length = gv($params, 'page_length', config('config.page_length'));

        return $this->backup->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Generate a new backup.
     *
     * @param array $params
     * @return Backup
     */
    public function generate($params = array())
    {
        if (gbv($params, 'delete_previous')) {
            $this->deletePrevious();
        }

        $file = $this->export();
    }

    /**
     * Export database.
     *
     * @return filename of backup as string
     */
    public function export()
    {
        \Artisan::call('backup:run --only-db');
    }

    /**
     * Delete previous backup.
     *
     * @param array $params
     * @return null
     */
    public function deletePrevious()
    {
        $this->backup->truncate();
        \Storage::deleteDirectory('backup');
    }
}
