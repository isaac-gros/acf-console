<?php

namespace App\Console\Command\Commons;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class LocationAttributes
{
    /**
     * Index for locations rules.
     * Each time an user add a 'OR' rule, $rule_index increase of 1.
     * @var int
     */
    private $index = 0;

    /**
     * The final locations rules array.
     * @var array
     */
    private $rules = [];

    /**
     * Write a single location rule.
     * @return array | rules
     */
    public function createLocationRule(InputInterface $input, OutputInterface $output, Command $display, array $locations, string $field_name)
    {
        // Retrieve instance of Helper and SymfonyStyle
        $helper = $display->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        // Create a rule index if it doesnt exist.
        if(!isset($this->rules[$this->index])) {
            $this->rules[$this->index] = [];
        }

        // The normal success message.
        $success_message = [
            'The locations rules has been added.'
        ];


        /**
         * Ask the user to choose a location parameter category.
         * As there is a lot of different location parameters, add this extra step
         * to improve the readability in the console.
         */
        $io->newLine();
        $q_field_location_param_category = new ChoiceQuestion(
            'Please choose first a field location category.',
            array_keys($locations),
            0
        );
        $q_field_location_param_category->setErrorMessage('The category %s doesn\'t exist or is invalid.');
        $field_location_param_category = $helper->ask($input, $output, $q_field_location_param_category);


        /**
         * Ask the user to choose the parameter for location rule.
         */
        $io->newLine();
        $q_field_location_param = new ChoiceQuestion(
            'Please choose now the field location parameter.',
            array_keys($locations[$field_location_param_category]),
            0
        );
        $q_field_location_param->setErrorMessage('The parameter %s doesn\'t exist or is invalid.');
        $field_location_param = $helper->ask($input, $output, $q_field_location_param);
        

        /**
         * Check if there is existings values according
         * to the parameters submitted by the user.
         */
        $possibles_location_values = $locations[$field_location_param_category][$field_location_param];
        
        // If no values are found, warn the user and change the final success message.
        if(!isset($possibles_location_values) || empty($possibles_location_values)) {
            $io->warning([
                'It looks like there is no value defined for this parameter.',
                'Make sure you defined values in the config.yaml file.'
            ]);

            $success_message = [
                'The field location rules has been created, but some values are missing.',
                'Make sure to complete the missing values later.'
            ];
        }


        /**
         * Ask the user to choose the operator for location rule.
         */
        $io->newLine();
        $q_field_location_operator = new ChoiceQuestion(
            'Define now the field location operator.',
            ['==', '!='],
            0
        );
        $q_field_location_operator->setErrorMessage('Please choose a valid operator.');
        $field_location_operator = $helper->ask($input, $output, $q_field_location_operator);


        /**
         * Ask the user to choose the parameter for location rule.
         * Set it as empty if there is no value available.
         */
        $io->newLine();
        $field_location_value = '(missing value)';
        if(!empty($possibles_location_values) && isset($possibles_location_values)) {
            
            $io->newLine();
            $q_field_location_value = new ChoiceQuestion(
                'Finally, choose the location value.',
                $possibles_location_values,
                reset($possibles_location_values)
            );
            $q_field_location_value->setErrorMessage('Please choose a valid value.');
            $field_location_value = $helper->ask($input, $output, $q_field_location_value);
        }


        /**
         * Add the created rule to the location rules array. 
         */
        array_push($this->rules[$this->index], [
            'param' => $field_location_param,
            'operator' => $field_location_operator,
            'value' => $field_location_value
        ]);


        /**
         * Sum up the field location rule created.
         */
        $io->newLine();
        $io->text("<info>Display <comment>{$field_name}</comment> when :</info>");
        
        /**
         * Display the array of the locations
         */
        $field_location_table = new Table($output);
        $field_location_table->setStyle('box');
        
        $field_location_table->setHeaders(['Parameter', 'Operator', 'Value']);
        foreach($this->rules as $location_rule_key => $location_rule) {
            foreach($location_rule as $location_condition) {
                $field_location_table->addRow([
                    $location_condition['param'],
                    $location_condition['operator'],
                    $location_condition['value']
                ]);
            }

            if(!empty($this->rules[$location_rule_key + 1])) {
                $field_location_table->addRow(new TableSeparator());
            }
        }

        $field_location_table->render();


        /**
         * Ask the user to add another condition to the current rule.
         */
        $io->newLine();
        $q_add_new_location_rule_condition = new ConfirmationQuestion('Add another condition to this location rule? [y/n] > ', false);
        $add_new_location_rule_condition = $helper->ask($input, $output, $q_add_new_location_rule_condition);
        
        if($add_new_location_rule_condition) {
            $io->newLine();
            $this->createLocationRule($input, $output, $display, $locations, $field_name);
        } else {

            /**
             * Ask the user to add another location rule.
             */
            $io->newLine();
            $q_add_new_location_condition = new ConfirmationQuestion('Add another location rule ? [y/n] > ', false);
            $add_new_location_condition = $helper->ask($input, $output, $q_add_new_location_condition);
            if($add_new_location_condition) {
                $io->newLine();
                
                $this->index++;
                $this->createLocationRule($input, $output, $display, $locations, $field_name);
            }
        }


        /**
         * To save the "state" of the defined rules, return it to the console.
         */
        return $this->rules;
    }
}