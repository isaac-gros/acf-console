<?php

namespace App\Console\Command;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Console\Command\FieldWriter;
use App\Console\Command\Commons\GeneralAttributes;
use App\Console\Command\Commons\LocationAttributes;

class CreateBasicFieldCommand extends Command
{

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setName('acf:create:basic')
            ->setDescription('Create a basic field.');
    }

    /**
     * Command execution
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Retrieve configuration settings.
        $config = Yaml::parseFile('./config.yaml');
        $destination = $config['fields'];
        $basic_field_destination = $destination['basic']['path'];
        $locations = $config['locations'];

        // Create new instance Helper and of DocumentWriter
        $helper = $this->getHelper('question');
        $writer = new FieldWriter();
        $io = new SymfonyStyle($input, $output);

        /**
         * Define the field type.
         * Choose text field by default.
         */
        $io->section('[1/3] Define the field type');
        $q_field_type = new ChoiceQuestion(
            'Please choose a basic field type.',
            ['text', 'textarea', 'number', 'email', 'url', 'password'],
            0
        );
        $q_field_type->setErrorMessage('The field %s is invalid.');
        $field_type = $helper->ask($input, $output, $q_field_type);
        $output->writeln("<info><comment>{$field_type}</comment> is now the field type.</info>");

        /**
         * Run and create general attributes.
         * This is the dirty way for the moment.
         */
        $io->newLine();
        $io->section('[2/3] Define the field general attributes');
        $q_field_general_attributes = new GeneralAttributes();
        $field_general_attributes = $q_field_general_attributes->execute($input, $output, $this);

        /**
         * Run and create locations attributes.
         * This is the dirty way too.
         */
        $io->newLine();
        $io->section('[3/3] Define the locations rules');
        $q_field_locations = new LocationAttributes();
        $field_locations = $q_field_locations
            ->createLocationRule($input, $output, $this, $locations, $field_general_attributes['name']);
        
        /**
         * Write the field.
         */
        $field_array = array_merge($field_general_attributes, 
            ['type' => $field_type], 
            ['location' => $field_locations]
        );
        $field_name = $field_array['name'];

        // Write the field to the global file if set or at defined file. 
        if(isset($destination['global'])) {
            $writer->writeFieldToFile($destination['global'], $field_name, $field_array);
        }
        $writer->writeFieldToFile($basic_field_destination, $field_name, $field_array);

        /**
         * Output the result of the field.
         */
        $io->newLine();
        $io->success('Congratulations! Your field has been created.');

        return 0;
    }
}