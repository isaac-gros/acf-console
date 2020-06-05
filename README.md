# ACF Console
Generate your ACF through a terminal.

## Example
``` sh
php acf.php acf:create:basic

Please choose a basic field type.
  [0] text
  [0] text
  [3] email
  [4] url
  [5] password
 > 0
# text is now the field type.

Please write the field name. Eg: my_field > background_image
# background_image is now the field name.

Please write the field title. Eg: My field > Background image
# Background image is now the field title.

Please write the field instructions. Press enter to skip. >
# Ignore the instructions for now.

Please write a field prepend. Press enter to skip. > pre
# Defined the field prepend as 'pre'.

Please write a field append. Press enter to skip. > after
# Defined the field append as 'after'.

Is the field required? [y/n] > y

Congratulations! Your field has been created.
```

### Configuration
You can edit the `config/services.yaml` file to define the path to save the generated fields. In this repository the /public path is in the `.gitignore` for development purposes.
