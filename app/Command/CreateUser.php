<?php

namespace App\Command;

use Commando\Command;
use League\CLImate\CLImate;

use function addslashes;
use function filter_var;
use function password_hash;
use function trim;

class CreateUser
{
    /**
     * @var CLImate
     */
    private CLImate $io;

    /**
     * @var Command
     */
    private Command $command;

    /**
     */
    public function __construct()
    {
        $this->io = new CLImate();

        $this->command = new Command();
        // Define a flag "-s" a.k.a. "--source"
        $this->command->setHelp('Create a user account.')
            ->option('n')
            ->aka('name')
            ->describedAs('The new user name')
            ->option('e')
            ->aka('email')
            ->describedAs('The new user email');
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $name = $this->command['name'] ?? '';
        do {
            if (!$name) {
                $input = $this->io->input("Enter the new user name:\n>");
                $name = $input->prompt();
            }
            if (($name = trim($name)) === '') {
                $this->io->red('Please enter a valid name.');
                $name = ''; // Reset the name, so it will be asked again.
            }
        } while (!$name);

        $email = $this->command['email'] ?? '';
        do {
            if (!$email) {
                $input = $this->io->input("Enter the new user email:\n>");
                $email = $input->prompt();
            }
            if (!filter_var(($email = trim($email)), FILTER_VALIDATE_EMAIL)) {
                $this->io->red('Please enter a valid email.');
                $email = ''; // Reset the email, so it will be asked again.
            }
        } while (!$email);

        $password = '';
        $passwordPattern = '/^(?=.*[A-Za-z]).{8,}$/';
        do {
            $input = $this->io->password("Enter the new user password:\n>");
            $password = $input->prompt();
            if (!preg_match($passwordPattern, $password)) {
                $this->io->red('Please enter a valid password (at least 8 chars and one letter).');
                $password = ''; // Reset the password, so it will be asked again.
            }
        } while (!$password);

        $passwordConfirm = '';
        do {
            $input = $this->io->password("Confirm the new user password:\n>");
            $passwordConfirm = $input->prompt();
            if ($passwordConfirm !== $password) {
                $this->io->red('Please confirm the user password.');
                $passwordConfirm = ''; // Reset the password, so it will be asked again.
            }
        } while (!$passwordConfirm);

        $name = addslashes($name);
        $password = password_hash($password, PASSWORD_DEFAULT);

        $this->io->green("The new user data are valid.");
        $this->io->green("Now add this entry to the 'users' array in the 'config/app.php' file.");
        $this->io->green("[
    'name' => '$name',
    'email' => '$email',
    'password' => '$password',
]");
    }
}
