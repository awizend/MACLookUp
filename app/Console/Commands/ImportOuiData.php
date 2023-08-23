<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportOuiData extends Command
{
    protected $signature = 'import:oui';
    protected $description = 'Import the latest version of IEEE OUI data into the database';

    public function handle()
    {
        if (!Schema::hasTable('oui_data')) {
            $this->createOuiDataTable();
        }

        $url = 'http://standards-oui.ieee.org/oui/oui.csv';
        $response = Http::get($url);
        $csvData = $response->body();

        $lines = explode("\n", $csvData);
        array_shift($lines); // Remove the CSV header

        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) === 6) {
                DB::table('oui_data')->insert([
                    'registry' => $data[1],
                    'assignment' => $data[2],
                    'organization_name' => $data[3],
                    'organization_address' => $data[4],
                    'timestamp' => $data[5],
                ]);
            }
        }

        $this->info('IEEE OUI data imported successfully.');
    }

    private function createOuiDataTable()
    {
        Schema::create('oui_data', function ($table) {
            $table->id('sn');
            $table->string('registry');
            $table->string('assignment');
            $table->string('organization_name');
            $table->string('organization_address');
            $table->string('timestamp');
            $table->timestamps();
        });
        $this->info('oui_data table created successfully.');
    }
}
