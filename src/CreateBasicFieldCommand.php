<?php

namespace App\Console\Command;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\Table;

use App\Console\Command\FieldWriter;
use App\Console\Command\GeneralAttributes;

class CreateBasicFieldCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('acf:create:basic')
            ->setDescription('Create a basic field.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Retrieve basic fields settings.
        $services = Yaml::parseFile('./config/services.yaml');
        $global_destination = $services['fields'];
        $file_destination = $global_destination['basic']['path'];

        // Create new instance Helper and of DocumentWriter
        $helper = $this->getHelper('question');
        $writer = new FieldWriter();

        /**
         * Define the field type.
         * Choose text field by default.
         */
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
        $q_field_general_attributes = new GeneralAttributes();
        $field_general_attributes = $q_field_general_attributes->execute($input, $output, $this);

        /**
         * Write the field.
         */
        $field_array = array_merge($field_general_attributes, ['type' => $field_type]);
        $field_name = $field_array['name'];

        // Write the field to the global file if set or at defined file. 
        if(isset($global_destination['global'])) {
            $writer->writeFieldToFile($global_destination['global'], $field_name, $field_array);
        }
        $writer->writeFieldToFile($file_destination, $field_name, $field_array);

        /**
         * Output the result of the field.
         */
        $output->writeln('<info>Congratulations! Your field has been created.</info>');

        return 1;
    }
}