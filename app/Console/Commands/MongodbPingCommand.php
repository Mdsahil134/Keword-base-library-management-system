<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\Laravel\Connection;
use Throwable;

class MongodbPingCommand extends Command
{
    protected $signature = 'mongodb:ping';

    protected $description = 'Verify the MongoDB connection is reachable';

    public function handle(): int
    {
        try {
            $connection = DB::connection('mongodb');

            if (! $connection instanceof Connection) {
                $this->error('The mongodb connection is not configured. Check config/database.php and your .env file.');

                return self::FAILURE;
            }

            $connection->getMongoDB()->command(['ping' => 1]);
            $database = $connection->getDatabaseName();

            $this->info("MongoDB is reachable (database: {$database}).");

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('MongoDB connection failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
