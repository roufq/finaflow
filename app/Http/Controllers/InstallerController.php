<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstallerController extends Controller
{
    public function index()
    {
        $requirements = [
            'php' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'pdo' => extension_loaded('pdo'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml'),
            'ctype' => extension_loaded('ctype'),
            'json' => extension_loaded('json'),
            'bcmath' => extension_loaded('bcmath'),
            'curl' => extension_loaded('curl'),
        ];
        
        $allPassed = !in_array(false, $requirements, true);

        return view('installer.requirements', compact('requirements', 'allPassed'));
    }

    public function permissions()
    {
        $permissions = [
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env' => file_exists(base_path('.env')) ? is_writable(base_path('.env')) : is_writable(base_path()),
        ];
        
        $allPassed = !in_array(false, $permissions, true);

        return view('installer.permissions', compact('permissions', 'allPassed'));
    }

    public function environment()
    {
        return view('installer.environment');
    }

    public function saveEnvironment(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:50',
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
        ]);

        try {
            $this->setEnv([
                'APP_NAME' => '"' . $request->app_name . '"',
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'APP_URL' => url('/'),
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_database,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password ?? '',
            ]);

            return redirect()->route('installer.database');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save .env configuration: ' . $e->getMessage());
        }
    }

    public function database()
    {
        return view('installer.database');
    }

    public function runDatabase()
    {
        try {
            // Test DB connection before migrating
            DB::connection()->getPdo();
            
            // Clear config cache to use newly saved env
            Artisan::call('config:clear');
            
            // Generate Key if not exists
            if (empty(env('APP_KEY'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }
            
            // Run Migrations
            Artisan::call('migrate', ['--force' => true]);
            
            // Run Seeders (which includes the default admin and demo data)
            Artisan::call('db:seed', ['--force' => true]);
            
            // Put storage link
            Artisan::call('storage:link', ['--force' => true]);
            
            // Create lock file
            file_put_contents(storage_path('installed'), 'installed');
            
            // Clear caches
            Artisan::call('optimize:clear');
            
            return redirect()->route('installer.finish');
        } catch (\Exception $e) {
            Log::error('Installation DB Error: ' . $e->getMessage());
            return back()->with('error', 'Database Error: ' . $e->getMessage() . '. Please ensure the database has been created on your server.');
        }
    }

    public function finish()
    {
        return view('installer.finish');
    }

    private function setEnv($data = [])
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=(.*)$/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
