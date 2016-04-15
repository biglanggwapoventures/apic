<?php

class PM_Controller extends CI_Controller
{

    public static function admin_only_status()
    {
        return array(
            M_Status::STATUS_APPROVED,
            M_Status::STATUS_DELIVERED,
            M_Status::STATUS_FINALIZED
        );
    }

    private $page_settings = array(
        'data_tab_title' => '',
        'data_nav' => '',
        'data_title' => '',
        'data_subtitle' => '',
        'data_css' => array(),
        'data_javascript' => array(),
        'main_view' => '',
        'user_info' => array()
    );

    public function setTabTitle($param = '')
    {
        $this->page_settings['data_tab_title'] = $param;
    }

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('page');
        if (!$this->session->userdata('user_id'))
        {
            redirect('login');
        }
        else
        {
            $this->page_settings['user_info'] = $this->session->all_userdata();
        }
    }

    function response($error_flag, $message, $data = array())
    {
        return array(
            'error_flag' => $error_flag,
            'message' => $message,
            'data' => $data
        );
    }

    private function set_title($data_nav = FALSE)
    {
        switch ($data_nav)
        {
            case NAV_HOME:
                return $this->lang->line('TITLE_HOME');
            case NAV_INVENTORY:
                return $this->lang->line('TITLE_INVENTORY');
            case NAV_SALES:
                return $this->lang->line('TITLE_SALES');
            case NAV_PRODUCTION:
                return $this->lang->line('TITLE_PRODUCTION');
        }
    }

    public function set_data($data_nav = FALSE, $data_subtitle = '', $data_css = array(), $data_js = array())
    {
        $this->page_settings['data_nav'] = $data_nav;
        $this->page_settings['data_title'] = $this->set_title($data_nav);
        $this->page_settings['data_subtitle'] = $data_subtitle;
        $this->page_settings['data_css'] = $data_css;
        $this->page_settings['data_javascript'] = $data_js;
    }

    public function load_page($main_view = FALSE)
    {
        $this->page_settings['main_view'] = $main_view;
        $this->load->view('template/html', $this->page_settings);
    }

    /* individual page settings */

    public function set_content_subtitle($subtitle = '')
    {
        $this->page_settings['data_subtitle'] = $subtitle;
    }

    public function add_javascript($js = array())
    {
        if (is_array($js))
        {
            foreach ($js as $j)
            {
                $this->page_settings['data_javascript'][] = $j;
            }
            return;
        }
        $this->page_settings['data_javascript'][] = $js;
    }

    public function add_css($css = array())
    {
        if (is_array($css))
        {
            foreach ($css as $c)
            {
                $this->page_settings['data_css'][] = $c;
            }
            return;
        }
        $this->page_settings['data_css'][] = $css;
    }

    public function set_active_nav($nav)
    {
        $this->page_settings['data_nav'] = $nav;
    }

    public function set_content_title($title)
    {
        $this->page_settings['data_title'] = $title;
    }

    public function set_content($view, $viewpage_settings = FALSE)
    {
        $this->page_settings['main_view'] = $this->load->view($view, $viewpage_settings, TRUE);
    }

    public function generate_page()
    {
        $this->load->view('template/html', $this->page_settings);
    }

    public function set_content_class($class = '')
    {
        $this->page_settings['main_view_extra_class'] = $class;
    }

    public function to_JSON()
    {
        $this->output->set_content_type('json')->set_output(json_encode($this->response));
    }

    public function generate_response($error_flag, $message = FALSE, $data = FALSE)
    {
        if (is_array($error_flag))
        {
            $this->response = $error_flag;
            return $this;
        }
        $this->response['error_flag'] = $error_flag;
        if ($message !== FALSE)
        {
            $this->response['message'] = is_string($message) ? [$message] : $message;
        }
        if ($data !== FALSE)
        {
            $this->response['data'] = $data;
        }
        return $this;
    }

    /* individual page settings */
}

class PM_Controller_v2 extends CI_Controller
{

    protected $response = [];
    protected $action;
    private $_page_settings = array(
        'data_tab_title' => '',
        'data_nav' => '',
        'data_title' => '',
        'data_subtitle' => '',
        'data_css' => array(),
        'data_javascript' => array(),
        'main_view' => '',
        'main_view_extra_class' => '',
        'user_info' => array()
    );

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('page');
        if (!$this->session->userdata('user_id'))
        {
            redirect('login');
        }
        else
        {
            $this->_page_settings['user_info'] = $this->session->all_userdata();
        }
    }

    function response($error_flag, $message = '', $data = array())
    {
        return array(
            'error_flag' => $error_flag,
            'message' => $message,
            'data' => $data
        );
    }

    /* individual page settings */

    public function set_content_subtitle($subtitle = '')
    {
        $this->_page_settings['data_subtitle'] = $subtitle;
    }

    public function add_javascript($js = array())
    {
        if (is_array($js))
        {
            foreach ($js as $j)
            {
                $this->_page_settings['data_javascript'][] = $j;
            }
            return;
        }
        $this->_page_settings['data_javascript'][] = $js;
    }

    public function add_css($css = array())
    {
        if (is_array($css))
        {
            foreach ($css as $c)
            {
                $this->_page_settings['data_css'][] = $c;
            }
            return;
        }
        $this->_page_settings['data_css'][] = $css;
    }

    public function set_content_class($class = '')
    {
        $this->_page_settings['main_view_extra_class'] = $class;
    }

    public function set_active_nav($nav)
    {
        $this->_page_settings['data_nav'] = $nav;
    }

    public function set_content_title($title)
    {
        $this->_page_settings['data_title'] = $title;
    }

    public function set_content($view, $viewpage_settings = FALSE)
    {
        $this->_page_settings['main_view'] = $this->load->view($view, $viewpage_settings, TRUE);
        return $this;
    }

    public function generate_page()
    {
        $this->load->view('template/html', $this->_page_settings);
    }

    public function setTabTitle($param = '')
    {
        $this->_page_settings['data_tab_title'] = $param;
    }

    public function view($filename)
    {
        return $this->view_url . $filename;
    }

    public function to_JSON()
    {
        $this->output->set_content_type('json')->set_output(json_encode($this->response));
    }

    public function generate_response($error_flag, $message = FALSE, $data = FALSE)
    {
        if (is_array($error_flag))
        {
            $this->response = $error_flag;
            return $this;
        }
        $this->response['error_flag'] = $error_flag;
        if ($message !== FALSE)
        {
            $this->response['message'] = is_string($message) ? [$message] : $message;
        }
        if ($data !== FALSE)
        {
            $this->response['data'] = $data;
        }
        return $this;
    }

    public function set_action($action)
    {
        $this->action = $action;
        return $this;
    }

    public function action($action = FALSE)
    {
        return $action !== FALSE ? $action === $this->action : $this->action;
    }

    public function flash_message($error_flag, $message)
    {
        $this->session->set_flashdata('FLASH_NOTIF', json_encode(['error_flag' => $error_flag, 'message' => $message]));
        return $this;
    }

    /* individual page settings */
}
