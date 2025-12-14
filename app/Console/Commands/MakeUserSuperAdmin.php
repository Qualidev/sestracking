<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-super-admin 
                            {identifier : The user email or ID}
                            {--remove : Remove super admin status instead of adding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make an existing user a super admin (or remove super admin status)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('identifier');
        $remove = $this->option('remove');

        // Try to find user by ID first, then by email
        $user = User::where('id', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$user) {
            $this->error("User not found: {$identifier}");
            $this->info("Please provide a valid user ID or email address.");
            return Command::FAILURE;
        }

        // Check current status
        $wasSuperAdmin = $user->super_admin;

        if ($remove) {
            if (!$wasSuperAdmin) {
                $this->warn("User '{$user->email}' (ID: {$user->id}) is not a super admin.");
                return Command::FAILURE;
            }

            $user->super_admin = false;
            $user->save();

            $this->info("✓ Removed super admin status from user: {$user->email} (ID: {$user->id})");
            return Command::SUCCESS;
        } else {
            if ($wasSuperAdmin) {
                $this->warn("User '{$user->email}' (ID: {$user->id}) is already a super admin.");
                return Command::SUCCESS;
            }

            $user->super_admin = true;
            $user->save();

            $this->info("✓ Made user a super admin: {$user->email} (ID: {$user->id})");
            $this->info("  User: {$user->name}");
            $this->info("  Email: {$user->email}");
            return Command::SUCCESS;
        }
    }
}
