<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
public function run()
{
// Create default administrator
User::create([
'name' => 'System Administrator',
'email' => 'admin@university.edu',
'email_verified_at' => now(),
'password' => Hash::make('admin123'),
'role' => 'administrator',
'employee_id' => 'ADM001',
'department' => 'IT Services',
'is_active' => true,
]);

// Create sample lecturer
User::create([
'name' => 'Dr. John Smith',
'email' => 'john.smith@university.edu',
'email_verified_at' => now(),
'password' => Hash::make('lecturer123'),
'role' => 'lecturer',
'employee_id' => 'LEC001',
'department' => 'Computer Science',
'is_active' => true,
]);

// Create sample support staff
User::create([
'name' => 'Support Staff',
'email' => 'support@university.edu',
'email_verified_at' => now(),
'password' => Hash::make('support123'),
'role' => 'support_staff',
'employee_id' => 'SUP001',
'department' => 'Facilities',
'is_active' => true,
]);
}
}