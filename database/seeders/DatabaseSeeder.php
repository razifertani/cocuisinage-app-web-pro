<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use App\Models\Establishment;
use App\Models\FCMNotification;
use App\Models\NotificationType;
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

            // Notifications Types
            $notification_type1 = NotificationType::create(['name' => 'Gestion de permissions', 'slug' => 'permission']);
            $notification_type2 = NotificationType::create(['name' => 'Gestion d\'horaire', 'slug' => 'planning']);
            $notification_type3 = NotificationType::create(['name' => 'Gestion de tâches', 'slug' => 'task']);

            // Companies
            $company1 = Company::firstOrCreate(['name' => 'Company1', 'email' => 'company1@company.com']);
            $company2 = Company::firstOrCreate(['name' => 'Food Eat Up', 'email' => 'foodeatup@outlook.fr']);
            $company3 = Company::firstOrCreate(['name' => 'Hamed Company', 'email' => 'hamedchamkhii@gmail.com']);

            // Establishments
            $establishment1 = Establishment::firstOrCreate([
                'company_id' => $company1->id,
                'name' => 'Establishment de Razi',
                'city' => 'Tunis',
                'longitude' => '48.858974',
                'latitude' => '2.293415',
            ]);
            $establishment2 = Establishment::firstOrCreate([
                'company_id' => $company2->id,
                'name' => 'Co Cuisinage',
                'city' => 'Aubervilliers',
                'longitude' => '48.858974',
                'latitude' => '2.293415',
            ]);
            $establishment3 = Establishment::firstOrCreate([
                'company_id' => $company2->id,
                'name' => 'Prends Ta Part',
                'city' => 'Tunis',
                'longitude' => '48.858974',
                'latitude' => '2.293415',
            ]);
            $establishment4 = Establishment::firstOrCreate([
                'company_id' => $company3->id,
                'name' => 'Hamed Est.',
                'city' => 'Tunis',
                'longitude' => '48.858974',
                'latitude' => '2.293415',
            ]);

            // Roles & Permissions
            $role1 = Role::create(['name' => 'Patron', 'establishment_id' => 1]);
            $role2 = Role::create(['name' => 'Dévelopeur', 'establishment_id' => 1]);
            $role3 = Role::create(['name' => 'Chef de projet', 'establishment_id' => 1]);

            $role4 = Role::create(['name' => 'Développeur', 'establishment_id' => 2]);
            $role5 = Role::create(['name' => 'Rédacteur', 'establishment_id' => 2]);
            $role6 = Role::create(['name' => 'Nutritionniste', 'establishment_id' => 2]);
            $role7 = Role::create(['name' => 'Sécurité', 'establishment_id' => 2]);
            $role8 = Role::create(['name' => 'Graphiste', 'establishment_id' => 2]);

            $role9 = Role::create(['name' => 'Chef', 'establishment_id' => 4]);
            // $role10 = Role::create(['name' => 'Graphiste', 'establishment_id' => 3]);

            $permission1 = Permission::firstOrCreate(['name' => 'Gestion des collaborateurs']);
            $permission2 = Permission::firstOrCreate(['name' => 'Gestion des tâches']);
            $permission3 = Permission::firstOrCreate(['name' => 'Gestion des permissions']);
            $permission4 = Permission::firstOrCreate(['name' => 'Travailler à distance']);
            $permission5 = Permission::firstOrCreate(['name' => 'Travailler librement']);

            $role1->syncPermissions([$permission1, $permission2, $permission3, $permission4, $permission5]);

            // Professionals - Owners
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
                    'is_owner' => 1,
                ]
            );
            $professional1->attach_role($establishment1->id, $role1->id);
            $professional1->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment1->id, 'active' => 1]);

            $professional2 = Professional::create([
                'first_name' => 'Razi',
                'last_name' => 'Employe',
                'email' => 'razi.fertani@esprit.tn',
                'password' => Hash::make('123456'),
                'company_id' => $company1->id,
                'is_owner' => 0,
            ]);
            $professional2->attach_role($establishment1->id, $role2->id);
            $professional2->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment1->id, 'active' => 1]);

            $professional3 = Professional::firstOrCreate(
                [
                    'email' => 'mika9394@hotmail.fr',
                ],
                [
                    'first_name' => 'Micheal',
                    'last_name' => 'Kebail-Ali',
                    'email' => 'mika9394@hotmail.fr',
                    'password' => Hash::make('123456'),
                    'company_id' => $company2->id,
                    'is_owner' => 1,
                ]
            );
            $professional3->attach_role($establishment2->id, $role1->id);
            $professional3->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment2->id, 'active' => 1]);

            $professional4 = Professional::firstOrCreate(
                [
                    'email' => 'benalisouhail1@gmail.com',
                ],
                [
                    'first_name' => 'Souhail',
                    'last_name' => 'Ben Ali',
                    'email' => 'benalisouhail1@gmail.com',
                    'password' => Hash::make('123456'),
                    'company_id' => $company2->id,
                    'is_owner' => 0,
                ]
            );
            $professional4->attach_role($establishment2->id, $role4->id);
            $professional4->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment2->id, 'active' => 1]);

            $professional5 = Professional::firstOrCreate(
                [
                    'email' => 'fares.khiari@esen.tn',
                ],
                [
                    'first_name' => 'Fares',
                    'last_name' => 'Khiari',
                    'email' => 'fares.khiari@esen.tn',
                    'password' => Hash::make('123453'),
                    'company_id' => $company2->id,
                    'is_owner' => 0,
                ]
            );
            $professional5->attach_role($establishment2->id, $role4->id);
            $professional5->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment2->id, 'active' => 1]);

            $professional6 = Professional::firstOrCreate(
                [
                    'email' => 'yohanclairic@gmail.com',
                ],
                [
                    'first_name' => 'Yohan',
                    'last_name' => 'Clairic',
                    'email' => 'yohanclairic@gmail.com',
                    'password' => Hash::make('123456'),
                    'company_id' => $company2->id,
                    'is_owner' => 0,
                ]
            );
            $professional6->attach_role($establishment2->id, $role7->id);
            $professional6->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment2->id, 'active' => 1]);

            $professional7 = Professional::firstOrCreate(
                [
                    'email' => 'hamedchamkhii@gmail.com',
                ],
                [
                    'first_name' => 'Hamed',
                    'last_name' => 'Chamkhii',
                    'email' => 'hamedchamkhii@gmail.com',
                    'password' => Hash::make('123456'),
                    'company_id' => $company3->id,
                    'is_owner' => 1,
                ]
            );
            $professional7->attach_role($establishment4->id, $role1->id);
            $professional7->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment4->id, 'active' => 1]);

            $professional8 = Professional::firstOrCreate(
                [
                    'email' => 'hcamkhi2002@gmail.com',
                ],
                [
                    'first_name' => 'Hamed',
                    'last_name' => 'Chamkhii',
                    'email' => 'hcamkhi2002@gmail.com',
                    'password' => Hash::make('123456'),
                    'company_id' => $company3->id,
                    'is_owner' => 0,
                ]
            );
            $professional8->attach_role($establishment4->id, $role9->id);
            $professional8->notifications_params()->attach(NotificationType::all(), ['establishment_id' => $establishment4->id, 'active' => 1]);

            // Plannings
            $planning1 = Planning::create([
                'professional_id' => $professional2->id,
                'establishment_id' => $establishment1->id,
                'day' => '2022-10-26',
                'should_start_at' => '09:00',
                'should_finish_at' => '13:00',
            ]);
            $planning2 = Planning::create([
                'professional_id' => $professional2->id,
                'establishment_id' => $establishment1->id,
                'day' => '2022-10-26',
                'should_start_at' => '14:00',
                'should_finish_at' => '18:00',
            ]);
            $planning3 = Planning::create([
                'professional_id' => $professional2->id,
                'establishment_id' => $establishment1->id,
                'day' => '2022-10-27',
                'should_start_at' => '16:00',
                'should_finish_at' => '18:00',
            ]);
            $planning4 = Planning::create([
                'professional_id' => $professional8->id,
                'establishment_id' => $establishment4->id,
                'day' => '2022-10-26',
                'should_start_at' => '16:00',
                'should_finish_at' => '18:00',
            ]);

            // Tasks
            $task1 = Task::create([
                'professional_id' => $professional2->id,
                'establishment_id' => $establishment1->id,
                'planning_id' => $planning1->id,
                'name' => 'Préparer les tomates',
                'status' => 0,
                'comment' => null,
                'image' => null,
            ]);
            $task2 = Task::create([
                'professional_id' => $professional2->id,
                'establishment_id' => $establishment1->id,
                'planning_id' => $planning2->id,
                'name' => 'Préparer les sauces',
                'status' => 0,
                'comment' => null,
                'image' => null,
            ]);

            $notification1 = FCMNotification::create([
                'sender_id' => $professional2->id,
                'receiver_id' => $professional1->id,
                'notification_type_id' => 1,
                'title' => "Tâche terminée",
                'establishment_id' => 1,
                'body' => "L'employé Ahmed a terminé une tâche",
            ]);
            $notification2 = FCMNotification::create([
                'sender_id' => $professional2->id,
                'receiver_id' => $professional1->id,
                'notification_type_id' => 1,
                'title' => "Pointage en retard",
                'establishment_id' => 1,
                'body' => "L'employé Ahmed a pointée son entrée avec 10 minutes de retard",
            ]);
        }
    }
}
