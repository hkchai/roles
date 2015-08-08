<?php

namespace Bican\Roles\Console;

use Illuminate\Console\Command;
use App\Exception;

// Used in copying.
define('DS', DIRECTORY_SEPARATOR);

class RolesMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration as per Bican Roles\' specs.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $src = __DIR__ . '/../../../migrations/';
        $tgt = base_path('/database/migrations/');
        
        self::copy_r($src, $tgt);
        echo "Done copying migration files\n";
    }

    public function copy_r($src_t, $tgt_t)
    {
        $src = $src_t;
        $tgt = $tgt_t;

        if(!file_exists($src)) throw new \Exception($src.' doesn\'t exist');

        // Check if is directory
        if (is_dir($src)) {            
            // src is a directory

            if (is_file($tgt)) {
                throw new \Exception('Error: Try to copy a directory('.$src.') to a file('.$tgt.')');
                return false;
            }

            if (!file_exists($tgt)) {
                @mkdir($tgt);
            }

            $objects = scandir($src);

            if ($objects > 0) {
                foreach ($objects as $file) {
                    if ($file == ".." || $file == ".") {
                        continue;
                    }
                    $srcfile = $src.DS.$file;

                    if (is_dir($srcfile)) {
                        copy_r($src.DS.$file, $tgt);
                    } 
                    else 
                    {
                        if (!copy($srcfile, $tgt.DS.$file)) {
                            throw new \Exception('Failed to copy '.$srcfile.' to '.$tgt);
                        }
                    }
                }

            }
        } else {
            // Not directory

            $file_components = explode(DS, $src);

            $cnt = count($file_components);

            $file = $file_components[$cnt-1];
            $tgtfile = $tgt.DS.$file;

            if (!is_dir($tgt)) {
                // File to file
                if(!copy($src, $tgt))
                {
                    throw new \Exception('Failed to copy '.$src.' to '.$tgt);
                }
            } else {
                if(!copy($src, $tgtfile))
                {
                    throw new \Exception('Failed to copy '.$src.' to '.$tgtfile);
                }
            }
            
        }
        
    }// public function copy_r      
}
