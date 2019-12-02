<?php

## Validation Class
require_once("validation.class.php");

## Validation
function form_validation($fields = [], $data = [])
{
    $validation = new Validation();
    $validation->set_fields($fields);
    $validation->set_data($data);


    return [
        'status' => $validation->run_validation(),
        'errors' => $validation->validation_errors(),
    ];
}
