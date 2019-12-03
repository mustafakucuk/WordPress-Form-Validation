# WordPress-Form-Validation
WordPress Advanced Form Validation like CodeIgniter

### Rules

| Rule            | Paramater | Example                   |
|-----------------|-----------|---------------------------|
| required        | No        | required                  |
| is_email        | No        | is_email                  |
| min_word        | Yes       | min_word[2]               |
| max_word        | Yes       | max_word[4]               |
| is_numeric      | No        | is_numeric                |
| min_length      | Yes       | min_length[10]            |
| max_length      | Yes       | max_length[20]            |
| less_than       | Yes       | less_than[10]             |
| greater_than    | Yes       | greater_than[20]          |
| in_list         | Yes       | in_list[apple,watermelon] |
| regex_match     | Yes       | regex_match[/regex/]      |
| is_url          | No        | is_url                    |
| username_exists | No        | username_exists           |
| email_exists    | No        | email_exists              |

### Set Fields
````php
<?php
$fields = [
    'user_meta' => [
        [
           'field' => 'full_name',
           'label' => __('Full Name', ''),
            'rules' => 'required|min_word[2]'
        ],
	]
];

$validation = new Validation();
$validation->set_fields($fields);
?>
````

### Set Custom Data
If you can set custom data instead of $_POST
````php
$data = [
	'user_meta' => [
		'full_name' => 'Full name value'
	]
];
$validation->set_data($data);
````

### Set textdomain for translation
````php
$validation = new Validation();
$validation->set_textdomain('your_textdomain');
````

### Run Validation
````php
$fields = [];
$validation = new Validation();
$validation->set_fields($fields);
$validation->run_validation();
````

### Get Validation Errors
````php
$fields = [];
$validation = new Validation();
$validation->set_fields($fields);
$validation->run_validation();

## Errors
print_r($validation->validation_errors());
````

### Custom Error Messages
````php
$fields = [
    'user_meta' => [
        [
           'field' => 'full_name',
           'label' => __('Full Name', ''),
           'rules' => 'required|min_word[2]'
            'error_messages' => [
                'required' => __('%s is cannot be blank.', 'your_textdomain'),
		'min_word' => __('%s must be at least %d words.', 'your_textdomain')
            ]
        ],
    ]
];
````

### You can use as function
````php
function form_validation($fields = [], $data = [])
{
    $validation = new Validation();
    $validation->set_fields($fields);
    $validation->set_data($data);
    $validation->set_textdomain('your_textdomain');

    return [
        'status' => $validation->run_validation(),
        'errors' => $validation->validation_errors(),
    ];
}

// Use
$fields = [
    'user_meta' => [
        [
           'field' => 'full_name',
           'label' => __('Full Name', ''),
           'rules' => 'required|min_word[2]'
        ],
    ]
];

$validation = form_validation($fields);

## if validation is successful
if($validation['status']){
  // do something.
  // maybe you can save data to database (:
}else {
  // if validation is fail
  print_r($validation['errors']);
}
````
