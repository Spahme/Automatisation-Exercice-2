<?php

namespace App\Console;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Illuminate\Support\Facades\Schema;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Faker\Factory;

class PopulateDatabaseCommand extends Command
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database');
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int
    {
        $output->writeln('Populate database...');

        $faker = Factory::create('fr_FR'); // Langue en français

        /** @var \Illuminate\Database\Capsule\Manager $db */
        $db = $this->app->getContainer()->get('db');

        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $db->getConnection()->statement("TRUNCATE `employees`");
        $db->getConnection()->statement("TRUNCATE `offices`");
        $db->getConnection()->statement("TRUNCATE `companies`");
        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");

        // Configuration : quantité d'éléments à générer
        $numCompanies = 5;
        $numOffices = 20;
        $numEmployees = 50;

        // Insertion des entreprises
        for ($i = 1; $i <= $numCompanies; $i++) {
            $phone = $faker->unique()->numerify('####-###-###'); // Exemple de serviceNumber formaté
            $email = "company$i@company.com";
            $website = "https://company$i.com";
            $image = 'https://picsum.photos/id/'.$faker->unique()->numberBetween(0, 20).'/1200/900';
            $phone = $faker->unique()->phoneNumber;

            $db->getConnection()->statement("
                INSERT INTO `companies` 
                (`id`, `name`, `phone`, `email`, `website`, `image`, `created_at`, `updated_at`, `head_office_id`)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), NULL)
            ", [
                $i,
                "Company $i",
                $phone,
                $email,
                $website,
                $image
            ]);
        }

        // Insertion des bureaux
        for ($i = 1; $i <= $numOffices; $i++) {
            $address = $faker->address;
            $city = $faker->city;
            $zip_code = $faker->postcode;
            $email = "office@company$i.com";
            $companyId = $faker->numberBetween(1, $numCompanies);

            $db->getConnection()->statement("
                INSERT INTO `offices` 
                (`id`, `name`, `address`, `city`, `zip_code`, `country`, `email`, `phone`, `company_id`, `created_at`, `updated_at`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $i,
                "Office $i",
                $address,
                $city,
                $zip_code,
                'France',
                $email,
                null,
                $companyId
            ]);
        }

        // Association entre les entreprises et leurs bureaux principaux
        for ($i = 1; $i <= $numCompanies; $i++) {
            $headOffice = $db->getConnection()->select("
                SELECT id FROM offices WHERE company_id = ? LIMIT 1
            ", [$i]);

            if (!empty($headOffice)) {
                $headOfficeId = $headOffice[0]->id;

                $db->getConnection()->statement("
                    UPDATE `companies`
                    SET `head_office_id` = ?
                    WHERE `id` = ?
                ", [
                    $headOfficeId,
                    $i
                ]);
            }
        }

        for ($i = 1; $i <= $numEmployees; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = $firstName . '.' . $lastName . '@company.com';
            $phone = $faker->unique()->mobileNumber;
            $jobTitle = $faker->jobTitle;
            $officeId = $faker->numberBetween(1, $numOffices); // Choisit un bureau existant

            $db->getConnection()->statement("
                INSERT INTO employees 
                (first_name, last_name, office_id, email, phone, job_title, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $firstName,
                $lastName,
                $officeId,
                $email,
                $phone,
                $jobTitle
            ]);
        }




        $output->writeln('Database created successfully!');
        return 0;
    }
}
