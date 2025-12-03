<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset 
                            {--force : Force the operation in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the database, recreate it, run migrations and create an admin user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('This command cannot run in production without the --force flag.');
            return Command::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm('This will delete all data in the database. Are you sure?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->info('Starting database reset...');

        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        // Step 1: Drop the database
        $this->dropDatabase($connection, $database);

        // Step 2: Create the database
        $this->createDatabase($connection, $database);

        // Step 3: Run migrations
        $this->info('Running migrations...');
        $this->call('migrate', ['--force' => true]);

        // Step 4: Create admin user
        $this->createAdminUser();

        $this->newLine();
        $this->info('✓ Database reset completed successfully!');

        return Command::SUCCESS;
    }

    /**
     * Drop the PostgreSQL database.
     */
    private function dropDatabase(string $connection, string $database): void
    {
        $this->info('Dropping database...');

        // Connect to 'postgres' default database to drop the target database
        config(["database.connections.{$connection}.database" => 'postgres']);
        DB::purge($connection);
        DB::reconnect($connection);

        // Terminate existing connections to the database
        DB::statement("SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = ?", [$database]);
        DB::statement("DROP DATABASE IF EXISTS \"{$database}\"");

        $this->info("✓ Database dropped: {$database}");
    }

    /**
     * Create the PostgreSQL database.
     */
    private function createDatabase(string $connection, string $database): void
    {
        $this->info('Creating database...');

        DB::statement("CREATE DATABASE \"{$database}\"");

        // Restore database config and reconnect
        config(["database.connections.{$connection}.database" => $database]);
        DB::purge($connection);
        DB::reconnect($connection);

        $this->info("✓ Database created: {$database}");
    }

    /**
     * Create the admin user.
     */
    private function createAdminUser(): void
    {
        $this->info('Creating admin user...');

        $name = env('ADMIN_NAME');
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (!$name || !$email || !$password) {
            $this->error('Missing admin credentials. Please set ADMIN_NAME, ADMIN_EMAIL and ADMIN_PASSWORD in your .env file.');
            return;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->info("✓ Admin user created:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Password', $password],
            ]
        );
    }
}
