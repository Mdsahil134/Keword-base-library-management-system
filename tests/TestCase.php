<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->cleanMongoDatabase();
    }

    protected function cleanMongoDatabase(): void
    {
        if (! extension_loaded('mongodb')) {
            return;
        }

        try {
            $database = DB::connection('mongodb')->getMongoDB();

            foreach ($database->listCollectionNames() as $name) {
                $database->selectCollection($name)->drop();
            }
        } catch (Throwable) {
            // MongoDB may be unavailable in some environments.
        }
    }
}
