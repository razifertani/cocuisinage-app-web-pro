<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use App\Models\Establishment;
use App\Models\Planning;
use App\Models\Professional;
use App\Models\Task;
use Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        {
            // Companies
            $company1 = Company::firstOrCreate([
                'name' => 'Company1',
                'email' => 'company1@company.com',
            ]);

            // Establishments
            $establishment1 = Establishment::firstOrCreate([
                'company_id' => $company1->id,
                'name' => 'Establishment1',
                'city' => 'Paris',
            ]);
            $establishment2 = Establishment::firstOrCreate([
                'company_id' => $company1->id,
                'name' => 'Establishment2',
                'city' => 'Lyon',
            ]);
            $establishment3 = Establishment::firstOrCreate([
                'company_id' => $company1->id,
                'name' => 'Establishment3',
                'city' => 'Nantes',
            ]);
            $establishment4 = Establishment::firstOrCreate([
                'company_id' => $company1->id,
                'name' => 'Establishment4',
                'city' => 'Rennes',
            ]);
            $establishment5 = Establishment::firstOrCreate([
                'company_id' => $company1->id,
                'name' => 'Establishment5',
                'city' => 'Marseille',
            ]);

            // Roles & Permissions
            $role1 = Role::firstOrCreate(['name' => 'Patron']);

            $permission1 = Permission::firstOrCreate(['name' => 'Add collaborators']);
            $permission2 = Permission::firstOrCreate(['name' => 'Add tasks to the team']);
            $permission3 = Permission::firstOrCreate(['name' => 'Manage roles']);

            $role1->syncPermissions([$permission1, $permission2, $permission3]);

            // Professionals
            $professional1 = Professional::firstOrCreate(
                [
                    'email' => 'razifertani1@gmail.com',
                ],
                [
                    'first_name' => 'Razi',
                    'last_name' => 'Fertani',
                    'email' => 'razifertani1@gmail.com',
                    'password' => Hash::make('123456'),
                    'company_id' => $company1->id,
                ]
            );

            $professional2 = Professional::firstOrCreate(
                [
                    'email' => 'co-cuisinage@outlook.fr',
                ],
                [
                    'first_name' => 'Co',
                    'last_name' => 'Cuisinage',
                    'email' => 'co-cuisinage@outlook.fr',
                    'password' => Hash::make('123456'),
                    'company_id' => $company1->id,
                ]
            );

            $professional3 = Professional::firstOrCreate(
                [
                    'email' => 'foodeatup@outlook.fr',
                ],
                [
                    'first_name' => 'Food',
                    'last_name' => 'Eat Up',
                    'email' => 'foodeatup@outlook.fr',
                    'password' => Hash::make('123456'),
                    'company_id' => $company1->id,
                ]
            );

            $professional4 = Professional::firstOrCreate(
                [
                    'email' => 'fares.khiari@esen.tn',
                ],
                [
                    'first_name' => 'Fares',
                    'last_name' => 'Khiari',
                    'email' => 'fares.khiari@esen.tn',
                    'password' => Hash::make('123456'),
                    'company_id' => $company1->id,
                ]
            );

            $professional5 = Professional::firstOrCreate(
                [
                    'email' => 'HamedChamkhii@gmail.com',
                ],
                [
                    'first_name' => 'Hamed',
                    'last_name' => 'Chamkhii',
                    'email' => 'HamedChamkhii@gmail.com',
                    'password' => Hash::make('123456'),
                    'company_id' => $company1->id,
                ]
            );

            // Affect Professional to Establishments with roles & permissions
            $professional1->establishments_roles()->attach(
                $establishment1->id,
                [
                    'role_id' => $role1->id,
                ],
            );
            $professional1->permissions()->attach(
                Role::findOrFail($role1->id)->permissions,
                [
                    'establishment_id' => $establishment1->id,
                ],
            );

            $professional2->establishments_roles()->attach(
                $establishment2->id,
                [
                    'role_id' => $role1->id,
                ],
            );
            $professional2->permissions()->attach(
                Role::findOrFail($role1->id)->permissions,
                [
                    'establishment_id' => $establishment2->id,
                ],
            );

            $professional3->establishments_roles()->attach(
                $establishment3->id,
                [
                    'role_id' => $role1->id,
                ],
            );
            $professional3->permissions()->attach(
                Role::findOrFail($role1->id)->permissions,
                [
                    'establishment_id' => $establishment3->id,
                ],
            );

            $professional4->establishments_roles()->attach(
                $establishment4->id,
                [
                    'role_id' => $role1->id,
                ],
            );
            $professional4->permissions()->attach(
                Role::findOrFail($role1->id)->permissions,
                [
                    'establishment_id' => $establishment4->id,
                ],
            );

            $professional5->establishments_roles()->attach(
                $establishment5->id,
                [
                    'role_id' => $role1->id,
                ],
            );
            $professional5->permissions()->attach(
                Role::findOrFail($role1->id)->permissions,
                [
                    'establishment_id' => $establishment5->id,
                ],
            );

            // Plannings
            $planning1 = Planning::create([
                'professional_id' => $professional1->id,
                'establishment_id' => $establishment1->id,
                'day' => '2022-10-6',
                'should_start_at' => '09:00',
                'should_finish_at' => '13:00',
            ]);

            // Tasks
            $task1 = Task::create([
                'professional_id' => $professional1->id,
                'establishment_id' => $establishment1->id,
                'planning_id' => $planning1->id,
                'name' => 'Préparer les tomates',
                'status' => 0,
                'comment' => null,
                'image' => null,
            ]);
        }
    }
}
