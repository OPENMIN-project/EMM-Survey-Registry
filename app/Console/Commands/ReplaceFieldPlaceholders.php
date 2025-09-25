<?php

namespace App\Console\Commands;

use Illuminate\Http\File;
use SimpleCsv;
use Illuminate\Console\Command;

class ReplaceFieldPlaceholders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ethmig:replace-field-placeholders {map} {template} {--print} {export?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mapFilePath = $this->argument('map');
        $templateFilePath = $this->argument('template');
        if (!file_exists($mapFilePath)) {
            throw new \Exception("Map file not found.");
        }
        if (!file_exists($templateFilePath)) {
            throw new \Exception("Template file not found.");
        }


        $map = [];
        $mapFile = SimpleCsv::import($mapFilePath);
        $mapFile
            ->filter(function ($row) {
                return !!$row['key'];
            })
            ->each(function ($row) use (&$map) {
                if (!array_key_exists($row['key'], $map)) {
                    $map["[{$row['key']}]"] = $row['id'] ? "[id_{$row['id']}]" : "";
                }
            });

        $templateFileContents = file_get_contents($templateFilePath);

        $newTemplateContents = str_replace(array_keys($map), array_values($map), $templateFileContents);

        if ($this->option('print')) {
            echo $newTemplateContents;
            return 0;
        }

        file_put_contents($this->argument('export'), $newTemplateContents);

        return 0;
    }
}
