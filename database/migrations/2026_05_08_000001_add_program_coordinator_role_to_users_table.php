<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('afad', 'executive', 'pc', 'admin') NOT NULL DEFAULT 'afad'");
            return;
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
            DB::statement("CREATE TABLE users_new (
                id integer primary key autoincrement not null,
                name varchar not null,
                email varchar not null,
                email_verified_at datetime,
                password varchar not null,
                remember_token varchar,
                role varchar check (role in ('afad', 'executive', 'pc', 'admin')) not null default 'afad',
                is_active tinyint(1) not null default '1',
                created_at datetime,
                updated_at datetime
            )");
            DB::statement('CREATE UNIQUE INDEX users_new_email_unique ON users_new (email)');
            DB::statement("INSERT INTO users_new (id, name, email, email_verified_at, password, remember_token, role, is_active, created_at, updated_at)
                SELECT id, name, email, email_verified_at, password, remember_token, role, is_active, created_at, updated_at FROM users");
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');
            DB::statement('PRAGMA foreign_keys=on');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        DB::table('users')->where('role', 'pc')->update(['role' => 'executive']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('afad', 'executive', 'admin') NOT NULL DEFAULT 'afad'");
        }
    }
};
