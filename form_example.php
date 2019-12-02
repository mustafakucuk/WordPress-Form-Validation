<?php

$fields = [
    'user_meta' => [
        [
           'field' => 'full_name',
           'label' => __('Full Name', ''),
           'rules' => 'required|min_word[2]'
        ],
        [
           'field' => 'user_email',
           'label' => __('Email', ''),
           'rules' => 'required|is_email|email_exists'
        ],
        [
           'field' => 'billing_phone',
           'label' => __('Phone Number', ''),
           'rules' => 'required|regex_match[/^[0-9]{1} [0-9]{3} [0-9]{3} [0-9]{2} [0-9]{2}$/]'
        ],
        [
            'field' => 'user_url',
            'label' => __('Website', ''),
            'rules' => 'required|is_url'
        ],
        [
            'field' => 'linkedin',
            'label' => __('Linkedin', ''),
            'rules' => 'is_url'
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

?>

<!-- Example Form -->
<form action="" method="post" accept-charset="utf-8" class="form" id="" role="form">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group form-item">
                <label for="full_name"><?php _e('Full Name', ''); ?></label>
                <input type="text" class="form-control" id="full_name" name="user_meta[full_name]" placeholder="" required/>
            </div>
        </div>
    </div>
</form>
