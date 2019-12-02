<?php

/**
 * WordPress Form Validation
 *
 * @author Mustafa KÜÇÜK
 * @link https://github.com/mustafakucuk/WordPress-Form-Validation
 * @license https://opensource.org/licenses/MIT	MIT License
 */

class Validation
{
    /**
     * @var array
     */
    protected $fields = [];
    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var array|WP_Error
     */
    protected $errors = [];

    /**
     * @var string
     */
    public $textdomain = '';

    /**
     * Validation constructor.
     */
    public function __construct()
    {
        $this->errors = new WP_Error();
        $this->data = $_POST;
    }

    /**
     * @param string $textdomain
     */
    public function set_textdomain($textdomain = '')
    {
        $this->textdomain = $textdomain;
    }

    /**
     * @param array $data
     */
    public function set_data($data = [])
    {
        $this->data = $data ?: $_POST;
    }

    /**
     * @param array $fields
     */
    public function set_fields($fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * @param $element
     * @param $array
     * @param null $default
     * @return mixed|null
     */
    public function array_element($element, $array, $default = null)
    {
        return !empty($array[$element]) ? $array[$element] : $default;
    }

    /**
     * @return bool
     */
    public function run_validation()
    {
        $fields = $this->fields;

        if (!$this->data) {
            return false;
        }

        foreach ($fields as $parent_key => $field) {
            if (is_array($field) && !$this->array_element('field', $field)) {
                foreach ($field as $field_item) {
                    $this->run_validation_for_field($field_item, $parent_key);
                }
            } else {
                $this->run_validation_for_field($field);
            }
        }

        return !$this->errors->has_errors();
    }

    /**
     * @param $field
     * @param string $parent_key
     * @return bool|void
     */
    public function run_validation_for_field($field, $parent_key = '')
    {
        $field_key = $this->array_element('field', $field);
        $field_label = $this->array_element('label', $field, $field_key);
        $field_value = $this->array_element($field_key, $parent_key ? $this->data[$parent_key] : $this->data);
        $field_value = trim($field_value);

        $error_messages = $this->array_element('error_messages', $field);

        $rules = $this->array_element('rules', $field);

        if (!$rules) {
            return true;
        }

        $rules = explode('|', $rules);

        if (!in_array('required', $rules) && !$field_value) {
            return;
        }

        foreach ($rules as $rule) {
            $is_param_rule = preg_match('/(.*?)\[(.*)]/', $rule, $match);
            $param = '';
            if ($is_param_rule) {
                $rule = $match[1];
                $param = $match[2];
                $rule_function = $this->{$match[1]}($field_value, $match[2]);
            } else {
                $rule_function = $this->{$rule}($field_value);
            }

            if (!$rule_function) {
                $error_message = $this->get_error_message($rule, $field_label, $param);

                if ($this->array_element($rule, $error_messages)) {
                    $error_message = sprintf($this->array_element($rule, $error_messages), $field_label, $param);
                }

                $this->errors->add($field_key, $error_message);
            }
        }

    }

    /**
     * @param $rule
     * @param $field_label
     * @param string $val
     * @return string
     */
    public function get_error_message($rule, $field_label, $val = '')
    {
        $messages = [
            'required' => __('%s is cannot be blank.', $this->textdomain),
            'is_email' => __('%s is must be valid email.', $this->textdomain),
            'min_word' => __('%s must be at least %d words.', $this->textdomain),
            'max_word' => __('%s should be no more than %d words.', $this->textdomain),
            'is_numeric' => __('%s must be numeric.', $this->textdomain),
            'min_length' => __('%s must contain at least %d characters.', $this->textdomain),
            'max_length' => __('%s name must be up to %d characters long.', $this->textdomain),
            'less_than' => __('%s cannot be less than %d.', $this->textdomain),
            'greater_than' => __('%s cannot be greater than %d.', $this->textdomain),
            'in_list' => __('%s is not valid value, one of them must be. "%s"', $this->textdomain),
            'regex_match' => __('%s is not valid value.', $this->textdomain),
            'is_url' => __('%s is not valid url.', $this->textdomain),
            'username_exists' => __('%s is already exists.', $this->textdomain),
            'email_exists' => __('%s is already exists.', $this->textdomain),
        ];

        return sprintf($this->array_element($rule, $messages), $field_label, $val);
    }

    /**
     * @return array|bool
     */
    public function validation_errors()
    {
        return $this->errors->has_errors() ? $this->errors->get_error_messages() : false;
    }

    /**
     * Validation Functions
     * You can write own function...,
     */

    public function required($str)
    {
        return !empty($str) ? true : false;
    }

    /**
     * @param $str
     * @return bool
     */
    public function is_email($str)
    {
        return is_email($str) ? true : false;
    }

    /**
     * @param $str
     * @param $val
     * @return bool
     */
    public function min_word($str, $val)
    {
        return (str_word_count($str) >= $val);
    }

    /**
     * @param $str
     * @param $val
     * @return bool
     */
    public function max_word($str, $val)
    {
        return (str_word_count($str) <= $val);
    }

    /**
     * @param $str
     * @return bool
     */
    public function is_numeric($str)
    {
        return (is_numeric($str));
    }

    /**
     * @param $str
     * @param $val
     * @return bool
     */
    public function min_length($str, $val)
    {
        return (strlen($str) >= $val);
    }

    /**
     * @param $str
     * @param $val
     * @return bool
     */
    public function max_length($str, $val)
    {
        return (strlen($str) <= $val);
    }

    /**
     * @param $str
     * @param $val
     * @return bool
     */
    public function less_than($str, $val)
    {
        return is_numeric($str) ? ($str < $val) : false;
    }

    /**
     * @param $str
     * @param $val
     * @return bool
     */
    public function greater_than($str, $val)
    {
        return is_numeric($str) ? ($str > $val) : false;
    }

    /**
     * @param $str
     * @param $list
     * @return bool
     */
    public function in_list($str, $list)
    {
        return (in_array($str, explode(',', $list)));
    }

    /**
     * @param $str
     * @param $regex
     * @return bool
     */
    public function regex_match($str, $regex)
    {
        return (bool)preg_match($regex, $str);
    }

    /**
     * @param $str
     * @return mixed
     */
    public function is_url($str)
    {
        return filter_var($str, FILTER_VALIDATE_URL);
    }

    /**
     * @param $str
     * @return bool
     */
    public function username_exists($str)
    {
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_current_login = $current_user->user_login;
            if ($str == $user_current_login) {
                return true;
            }
        }

        return !username_exists($str);
    }

    /**
     * @param $str
     * @return bool
     */
    public function email_exists($str)
    {
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_current_email = $current_user->user_email;
            if ($str == $user_current_email) {
                return true;
            }
        }
        return !email_exists($str);
    }

    /**
     * @param $str - Post ID or post object
     * @return bool
     */
    public function post_exists($str)
    {
        return (bool)get_post($str);
    }

    /**
     * @param $str - Accepts term ID, slug, or name.
     * @return bool
     */
    public function term_exists($str)
    {
        return (bool)$this->term_exists($str);
    }
}