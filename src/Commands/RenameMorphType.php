<?php

namespace GIS\TraitsHelpers\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameMorphType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rename:morph-type {table} {column} {from} {to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename model class in table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument("table");
        $column = $this->argument("column");
        $from = $this->argument("from");
        $to = $this->argument("to");

        if (! Schema::hasTable($table)) {
            $this->error("Table not found");
            return;
        }

        if (! Schema::hasColumn($table, $column)) {
            $this->error("Column not found");
            return;
        }

        DB::table($table)
            ->where($column, $from)
            ->update([$column => $to]);

        $this->info("Column $column in table $table was updated");
    }
}
