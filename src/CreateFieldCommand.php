<?php

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\Table;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Console\Command\FileWriter\FileWriter;

class CreateBasicFieldCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('acf:field:basic')
            ->setDescription('Create a basic field.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $filesystem = new Filesystem();
        $filewriter = new FileWriter();

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
        $output->writeln("<info>You selected the <comment>{$field_type}</comment> field type.</info>");
        
        /**
         * Define the field key identifier.
         */
        $q_field_id = new Question(
            'Please write the field key identifier. Eg: my_field >',
            'my_field'
        );
        $field_id = $helper->ask($input, $output, $q_field_id);
        $output->writeln("<info><comment>{$field_id}</comment> is now the field key.</info>");


        /**
         * Define the field label.
         */
        $q_field_label = new Question(
            'Please write the field label. Eg: My field > ',
            'My field'
        );
        $field_label = $helper->ask($input, $output, $q_field_label);
        $output->writeln("<info><comment>{$field_label}</comment> is now the field label.</info>");

        /**
         * Define the field intructions.
         * If the user doesnt provide instructions, the instructions will temporarily set as #.
         */
        $q_field_instructions = new Question(
            'Please write in the field instructions. Leave empty if not necessary. > ',
            '#'
        );
        $field_instructions = $helper->ask($input, $output, $q_field_instructions);
        $field_instructions_message = ($field_instructions == '#') ? "Ignore the instructions for now." : "Defined the field instructions.";
        $output->writeln("<info>$field_instructions_message</info>");

        /**
         * Define if the field is required or not.
         */
        $q_field_required = new ConfirmationQuestion('Is the field required? [y/n] > ', false);
        $field_required = $helper->ask($input, $output, $q_field_required);

        /**
         * Output the result of the field.
         */
        $field_result = new Table($output);
        $field_result
            ->setHeaders(['Parameter', 'Value'])
            ->setRows([
                ['Type', $field_type],
                ['Key', $field_id],
                ['Label', $field_label],
                ['Instructions', ($field_instructions == '#') ? '(blank)' : $field_instructions],
            ]);
        $field_result->render();
        $output->writeln('<info>Congratulations! Your field has been created.</info>');

        return 1;
    }
}