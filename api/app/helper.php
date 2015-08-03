<?php
/**
     * Check if its valid request
     * 
     * @param array $data
     * @return boolean
     */
    function check_valid_request($data) {
        if ($data === NULL || !isset($data)) {
            $response['message'] = "Theres something wrong with this request";
            echo $this->dataSender($response);
            exit;
        }
        return true;
    }

    /**
     * Return data in output format
     * 
     * @param array $data
     * @param string $format
     * @return string
     */
    function dataSender($data, $format = 'json') {
        switch (strtolower($format)) {
            case 'json':
                $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode($data));
            default:
                $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode($data));
        }

        $outputStr = $this->output->get_output();
        return $outputStr;
    }

    /**
     * Check if session exist within API
     */
    public function session() {
        $response = array('uid' => '', 'email' => '', 'user_type' => '0', 'username' => '', 'user_photo' => '', 'validated' => false);

        if ($this->session->userdata('validated') === true) {
            $response['uid'] = $this->session->userdata('uid');
            $response['email'] = $this->session->userdata('email');
            $response['user_type'] = $this->session->userdata('user_type');
            $response['username'] = $this->session->userdata('username');
            $response['user_photo'] = $this->session->userdata('user_photo');
            $response['validated'] = $this->session->userdata('validated');
        }
        echo $this->dataSender($response);
        exit;
    }

    /**
     * Check if user is logged in or not
     * @return boolean
     */
    public function check_session() {
        if ($this->session->userdata('validated') !== true) {
            $response['message'] = "You are not authorized to access this page";
            echo $this->dataSender($response);
            exit;
        }
        return true;
    }
?>