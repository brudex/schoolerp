<?php

namespace App\Http\Controllers\Utility;

use Illuminate\Http\Request;
use App\Repositories\Utility\BackupRepository;
use App\Http\Controllers\Controller;

class BackupController extends Controller
{
    protected $request;
    protected $repo;

    protected $module = 'backup';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, BackupRepository $repo)
    {
        $this->repo = $repo;
        $this->request = $request;

        $this->middleware('permission:access-configuration');
        $this->middleware('feature.available:backup');
        $this->middleware('prohibited.test.mode')->only('download');
    }

    /**
     * Used to get all Backups
     * @get ("/api/backup")
     * @return Response
     */
    public function index()
    {
        $files = \Storage::files('backup');

        $backups = array();
        foreach ($files as $file) {
            $backups[] = array('name' => basename($file), 'size' => formatSizeUnits(\Storage::size($file)));
        }

        return $this->ok($backups);
    }

    /**
     * Used to store Backup
     * @post ("/api/backup")
     * @param ({
     *      @Parameter("deletePrevious", type="checkbox", required="true", description="Delete or not to delete previous backup"),
     * })
     * @return Response
     */
    public function store()
    {
        $backup = $this->repo->generate($this->request->all());

        return $this->success(['message' => trans('utility.backup_generated')]);
    }

    /**
     * Used to download Backup
     * @get ("/backup/{name}/download")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of backup to be downloaded"),
     * })
     * @return Response download
     */
    public function download($name)
    {
        try {
            \Storage::exists('backup/'.$name);
        } catch (\Exception $e) {
        }

        $download_path = storage_path('app/backup/'.$name);

        return response()->download($download_path);
    }

    /**
     * Used to delete Backup
     * @delete ("/api/backup/{name}")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of backup to be deleted"),
     * })
     * @return Response
     */
    public function destroy($name)
    {
        $this->repo->findOrFail($name);

        if (\Storage::exists('backup/'.$name)) {
            \Storage::delete('backup/'.$name);
        }

        return $this->success(['message' => trans('utility.backup_deleted')]);
    }
}
