<?php

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GeneralAttributes
{
    /**
     * Falsely execute command questions and return written input.
     * @return array
     */
    public function execute(InputInterface $input, OutputInterface $output, $display)
    {
        // Create new instance of Helper 
        $helper = $display->getHelper('question');

        /**
         * Define the field name.
         */
        $q_field_name = new Question(
            'Please write the field name. Eg: my_field > ',
            'my_field'
        );
        $field_name = $helper->ask($input, $output, $q_field_name);
        $output->writeln("<info><comment>{$field_name}</comment> is now the field name.</info>");


        /**
         * Define the field title.
         */
        $q_field_title = new Question(
            'Please write the field title. Eg: My field > ',
            'My field'
        );
        $field_title = $helper->ask($input, $output, $q_field_title);
        $output->writeln("<info><comment>{$field_title}</comment> is now the field title.</info>");


        /**
         * Define the field intructions.
         * If the user doesnt provide instructions, set it as # to prevent errors.
         */
        $q_field_instructions = new Question(
            'Please write in the field instructions. Leave empty if not necessary. > ',
            '#'
        );
        $field_instructions = $helper->ask($input, $output, $q_field_instructions);
        $field_instructions_message = ($field_instructions == '#') ? "Ignore the instructions for now." : "Defined the field instructions.";
        $output->writeln("<info>$field_instructions_message</info>");


        /**
         * Define the field prepend.
         * If the user doesnt provide prepend, set it as # to prevent errors.
         */
        $q_field_prepend = new Question(
            'Please write a field prepend. Leave empty if not necessary. > ',
            '#'
        );
        $field_prepend = $helper->ask($input, $output, $q_field_prepend);
        $field_prepend_message = ($field_prepend == '#') ? "No prepend provided." : "Defined the field prepend as '$field_prepend'.";
        $output->writeln("<info>$field_prepend_message</info>");


        /**
         * Define the field append.
         * If the user doesnt provide append, set it as # to prevent errors.
         */
        $q_field_append = new Question(
            'Please write a field append. Leave empty if not necessary. > ',
            '#'
        );
        $field_append = $helper->ask($input, $output, $q_field_append);
        $field_append_message = ($field_append == '#') ? "No append provided." : "Defined the field append as '$field_append'.";
        $output->writeln("<info>$field_append_message</info>");

        /**
         * Define if the field is required.
         */
        $q_field_required = new ConfirmationQuestion('Is the field required? [y/n] > ', false);
        $field_required = $helper->ask($input, $output, $q_field_required);

        return [
            'key' => uniqid($field_name . '_'),
            'name' => $field_name,
	        'title' => $field_title,
            'instructions' => ($field_instructions == "#") ? '' : $field_instructions,
            'required' => $field_required
        ];
    }
}