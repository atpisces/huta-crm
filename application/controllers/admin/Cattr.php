<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cattr extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('utilities_model');
    }

    public function index($error = ''){
        if(isset($_SESSION["cattr_is_admin"]) && $_SESSION["cattr_is_admin"] == "1"){
            unset($_SESSION["staff_id"]);
        }
        $id = get_staff_user_id();
        $data['staff_details']     = $this->staff_model->get($id);
        
        $data['Cattr'] = 'Cattr';
        $data['title'] = 'Cattr World';

        if($error != ''){
            $data['invalid_user'] = "Invalid Credentials. Please try again!";
        }
        
        if($this->input->post()){
            $cattr_account_password = $this->input->post("cattr_account_password");
            //print_r($data['staff_details']);
            // CURL API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/auth/login");
            curl_setopt($ch, CURLOPT_POST, 1);
            // curl_setopt($ch, CURLOPT_POSTFIELDS,
            //             "email=talha@botsolutions.tech&password=".$cattr_account_password);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "email=".$data['staff_details']->email."&password=".$cattr_account_password);

            // In real life you should use something like:
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 
            //          http_build_query(array('postvar1' => 'value1')));

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);
            //print_r($server_output);
            $decode_output = json_decode($server_output);
            $data['cattr_response'] = $decode_output;
            // print_r($decode_output);
            // exit;
            curl_close ($ch);
            // echo $decode_output->access_token."<br>";
            // echo $decode_output->token_type."<br>";
            // echo $decode_output->user->id."<br>";
            if (isset($decode_output->message)){
                redirect('/admin/cattr/index/0', 'refresh');
            } else {
                $_SESSION["cattr_access_token"] = $decode_output->access_token;
                $_SESSION["cattr_token_type"] = $decode_output->token_type;
                $_SESSION["cattr_user"] = $decode_output->user->id;
                $_SESSION["cattr_is_admin"] = $decode_output->user->is_admin;
                $data['projects_list'] = $this->api_get_projects_list($decode_output->user->id,$decode_output->access_token,$decode_output->token_type);
            }

        }
        if(isset($_SESSION["cattr_user"])){
            $data['projects_list'] = $this->api_get_projects_list($_SESSION["cattr_user"],$_SESSION["cattr_access_token"],$_SESSION["cattr_token_type"]);
        }   
        if(isset($_SESSION["cattr_user"]) && $_SESSION["cattr_is_admin"] == "1"){
            $data['all_users'] = $this->get_all_users();
        }
        $data['controller'] = $this;

        $this->load->view('admin/cattr/index', $data);
    }

    public function api_get_projects_list($user_id,$access_token, $token_type){
        // echo "I'm here bro:";
        // exit;

        $data['title'] = 'Projects | Cattr World';
        // CURL API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/projects/list");
        if($user_id != ""){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        'user_id='.$_SESSION["cattr_user"]);
        }
        $headers = array();
        $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // In real life you should use something like:
        // curl_setopt($ch, CURLOPT_POSTFIELDS, 
        //          http_build_query(array('postvar1' => 'value1')));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $decode_output = json_decode($server_output);

        curl_close ($ch);
        // echo "<br><br>Closing man";
        // exit;
        return $decode_output;
    }

    public function view_individual_user_projects($user_id = ''){
        if($user_id == ''){
            redirect('/admin/cattr/index', 'refresh');
        } else {
            $_SESSION["staff_id"] = $user_id;
            $data["user_details"] = $this->user_details($_SESSION["staff_id"]);

            $data['title'] = 'Projects | Cattr World';
            // CURL API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/projects/list");
            // if($user_id != ""){
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                            'uids='.$_SESSION["staff_id"]);
            // }
            $headers = array();
            $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // In real life you should use something like:
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 
            //          http_build_query(array('postvar1' => 'value1')));

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);
            $data['projects_list'] = json_decode($server_output);

            curl_close ($ch);
            $data['controller'] = $this;
            $this->load->view('admin/cattr/view_projects', $data);
        }
    }

    public function project_details($project_id){
        // CURL API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/projects/show");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                    'id='.$project_id);
        $headers = array();
        $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // In real life you should use something like:
        // curl_setopt($ch, CURLOPT_POSTFIELDS, 
        //          http_build_query(array('postvar1' => 'value1')));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $data["project_info"] = json_decode($server_output);

        curl_close ($ch);
        // $this->load->view('admin/cattr/details', $data);
        return $data["project_info"];
    }

    public function disconnect_cattr(){
        unset($_SESSION["cattr_user"]);
        unset($_SESSION["cattr_access_token"]);
        unset($_SESSION["cattr_token_type"]);
        unset($_SESSION["cattr_is_admin"]);
        redirect('/admin/cattr/index', 'refresh');
    }

    public function project_tasks($project_id = ''){

        $data['title'] = 'Tasks List | Cattr World';

        if(!isset($_SESSION["cattr_user"])){
            redirect('/admin/cattr/index', 'refresh');
        }

        if($project_id != ''){
            // CURL API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/tasks/list");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        'project_id='.$project_id);
            $headers = array();
            $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // In real life you should use something like:
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 
            //          http_build_query(array('postvar1' => 'value1')));

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);
            $data["tasks_list"] = json_decode($server_output);

            curl_close ($ch);

            $data['controller'] = $this;
            // $data['user_todays_total_time'] = $this->user_todays_total_time();
            // CURL API
            /*$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/time-intervals/list");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        'user_id=9&task_id=20');
            $headers = array();
            $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // In real life you should use something like:
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 
            //          http_build_query(array('postvar1' => 'value1')));

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);
            $data["users_activity"] = json_decode($server_output);

            curl_close ($ch);*/

            $this->load->view('admin/cattr/tasks', $data);

        } else {
            redirect('/admin/cattr/index', 'refresh');
        }
    }

    public function get_all_users(){

        // CURL API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/users/list");
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS,
        //             'id=9');
        $headers = array();
        $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // In real life you should use something like:
        // curl_setopt($ch, CURLOPT_POSTFIELDS, 
        //          http_build_query(array('postvar1' => 'value1')));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $decode_output = json_decode($server_output);
        return $decode_output;

        curl_close ($ch);
    }

    public function user_details(){

        // CURL API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/users/show");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                    'id=9');
        $headers = array();
        $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // In real life you should use something like:
        // curl_setopt($ch, CURLOPT_POSTFIELDS, 
        //          http_build_query(array('postvar1' => 'value1')));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $data["users_info"] = json_decode($server_output);

        curl_close ($ch);
        // $this->load->view('admin/cattr/details', $data);
        return $data["users_info"];
    }

    public function user_todays_total_time_by_task($task_id){

        if(!isset($_SESSION["cattr_user"]) || $task_id == ""){
            redirect('/admin/cattr/index', 'refresh');
        } else {
            // CURL API
            $user_id = "";
            if(isset($_SESSION["staff_id"])){
                $user_id = $_SESSION["staff_id"];
            } else {
                $user_id = $_SESSION["cattr_user"];
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/time/tasks");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        'user_id='.$user_id.'&task_id='.$task_id.'&start_at='.date("Y-m-d").' 00:00:00&end_at='.date("Y-m-d").' 23:59:00');
            $headers = array();
            $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // In real life you should use something like:
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 
            //          http_build_query(array('postvar1' => 'value1')));

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);
            $decode_output = json_decode($server_output);

            curl_close ($ch);

            return $decode_output;
        }
    }

    public function user_todays_total_time($user_id){

        if(!isset($_SESSION["cattr_user"]) || $user_id == ""){
            redirect('/admin/cattr/index', 'refresh');
        } else {
            // CURL API
            if(isset($_SESSION["staff_id"])){
                $user_id = $_SESSION["staff_id"];
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://54.226.186.12/api/time/total");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        'user_id='.$user_id.'&start_at='.date("Y-m-d").' 00:00:00&end_at='.date("Y-m-d").' 23:59:00');
            $headers = array();
            $headers[] = 'Authorization: '.$_SESSION["cattr_token_type"].' '.$_SESSION["cattr_access_token"];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // In real life you should use something like:
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 
            //          http_build_query(array('postvar1' => 'value1')));

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);
            $decode_output = json_decode($server_output);

            curl_close ($ch);

            return $decode_output;
        }
    }

    /* All perfex activity log */
    public function activity_log()
    {
        // Only full admin have permission to activity log
        if (!is_admin()) {
            access_denied('Activity Log');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('activity_log');
        }
        $data['title'] = _l('utility_activity_log');
        $this->load->view('admin/utilities/activity_log', $data);
    }

    /* All perfex activity log */
    public function pipe_log()
    {
        // Only full admin have permission to activity log
        if (!is_admin()) {
            access_denied('Ticket Pipe Log');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('ticket_pipe_log');
        }
        $data['title'] = _l('ticket_pipe_log');
        $this->load->view('admin/utilities/ticket_pipe_log', $data);
    }

    public function clear_activity_log()
    {
        if (!is_admin()) {
            access_denied('Clear activity log');
        }
        $this->db->empty_table(db_prefix() . 'activity_log');
        redirect(admin_url('utilities/activity_log'));
    }

    public function clear_pipe_log()
    {
        if (!is_admin()) {
            access_denied('Clear ticket pipe activity log');
        }
        $this->db->empty_table(db_prefix() . 'tickets_pipe_log');
        redirect(admin_url('utilities/pipe_log'));
    }

    /* Calendar functions */
    public function calendar()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $data = $this->input->post();

            $success = $this->utilities_model->event($data);
            $message = '';
            if ($success) {
                if (isset($data['eventid'])) {
                    $message = _l('event_updated');
                } else {
                    $message = _l('utility_calendar_event_added_successfully');
                }
            }
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die();
        }
        $data['google_ids_calendars'] = $this->misc_model->get_google_calendar_ids();
        $data['google_calendar_api']  = get_option('google_calendar_api_key');
        $data['title']                = _l('calendar');
        add_calendar_assets();

        $this->load->view('admin/utilities/calendar', $data);
    }

    public function get_calendar_data()
    {
        echo json_encode($this->utilities_model->get_calendar_data(
                date('Y-m-d', strtotime($this->input->get('start'))),
                date('Y-m-d', strtotime($this->input->get('end'))),
                '',
                '',
                $this->input->get()
            ));
        die();
    }

    public function view_event($id)
    {
        $data['event'] = $this->utilities_model->get_event($id);
        if ($data['event']->public == 1 && !is_staff_member()
            || $data['event']->public == 0 && $data['event']->userid != get_staff_user_id()) {
        } else {
            $this->load->view('admin/utilities/event', $data);
        }
    }

    public function delete_event($id)
    {
        if ($this->input->is_ajax_request()) {
            $event = $this->utilities_model->get_event_by_id($id);
            if ($event->userid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            $success = $this->utilities_model->delete_event($id);
            $message = '';
            if ($success) {
                $message = _l('utility_calendar_event_deleted_successfully');
            }
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die();
        }
    }

    // Moved here from version 1.0.5
    public function media()
    {
        $this->load->helper('url');
        $data['title']     = _l('media_files');
        $data['connector'] = admin_url() . '/utilities/media_connector';

        $mediaLocale = get_media_locale();

        $this->app_scripts->add('media-js', 'assets/plugins/elFinder/js/elfinder.min.js');

        if (file_exists(FCPATH . 'assets/plugins/elFinder/js/i18n/elfinder.' . $mediaLocale . '.js') && $mediaLocale != 'en') {
            $this->app_scripts->add('media-lang-js', 'assets/plugins/elFinder/js/i18n/elfinder.' . $mediaLocale . '.js');
        }

        $this->load->view('admin/utilities/media', $data);
    }

    public function media_connector()
    {
        $media_folder = $this->app->get_media_folder();
        $mediaPath    = FCPATH . $media_folder;

        if (!is_dir($mediaPath)) {
            mkdir($mediaPath, 0755);
        }

        if (!file_exists($mediaPath . '/index.html')) {
            $fp = fopen($mediaPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        $this->load->helper('path');

        $root_options = [
            'driver' => 'LocalFileSystem',
            'path'   => set_realpath($media_folder),
            'URL'    => site_url($media_folder) . '/',
            //'debug'=>true,
            'uploadMaxSize' => get_option('media_max_file_size_upload') . 'M',
            'accessControl' => 'access_control_media',
            'uploadDeny'    => [
                'application/x-httpd-php',
                'application/php',
                'application/x-php',
                'text/php',
                'text/x-php',
                'application/x-httpd-php-source',
                'application/perl',
                'application/x-perl',
                'application/x-python',
                'application/python',
                'application/x-bytecode.python',
                'application/x-python-bytecode',
                'application/x-python-code',
                'wwwserver/shellcgi', // CGI
            ],
            'uploadAllow' => !$this->input->get('editor') ? [] : ['image', 'video'],
            'uploadOrder' => [
                'deny',
                'allow',
            ],
            'attributes' => [
                [
                    'pattern' => '/.tmb/',
                    'hidden'  => true,
                ],
                [
                    'pattern' => '/.quarantine/',
                    'hidden'  => true,
                ],
                [
                    'pattern' => '/public/',
                    'hidden'  => true,
                ],
            ],
        ];

        if (!is_admin()) {
            $this->db->select('media_path_slug,staffid,firstname,lastname')
            ->from(db_prefix() . 'staff')
            ->where('staffid', get_staff_user_id());
            $user = $this->db->get()->row();
            $path = set_realpath($media_folder . '/' . $user->media_path_slug);
            if (empty($user->media_path_slug)) {
                $this->db->where('staffid', $user->staffid);
                $slug = slug_it($user->firstname . ' ' . $user->lastname);
                $this->db->update(db_prefix() . 'staff', [
                    'media_path_slug' => $slug,
                ]);
                $user->media_path_slug = $slug;
                $path                  = set_realpath($media_folder . '/' . $user->media_path_slug);
            }
            if (!is_dir($path)) {
                mkdir($path, 0755);
            }
            if (!file_exists($path . '/index.html')) {
                $fp = fopen($path . '/index.html', 'w');
                if ($fp) {
                    fclose($fp);
                }
            }
            array_push($root_options['attributes'], [
                'pattern' => '/.(' . $user->media_path_slug . '+)/', // Prevent deleting/renaming folder
                'read'    => true,
                'write'   => true,
                'locked'  => true,
            ]);
            $root_options['path'] = $path;
            $root_options['URL']  = site_url($media_folder . '/' . $user->media_path_slug) . '/';
        }

        $publicRootPath      = $media_folder . '/public';
        $public_root         = $root_options;
        $public_root['path'] = set_realpath($publicRootPath);

        $public_root['URL'] = site_url($media_folder) . '/public';
        unset($public_root['attributes'][3]);

        if (!is_dir($publicRootPath)) {
            mkdir($publicRootPath, 0755);
        }

        if (!file_exists($publicRootPath . '/index.html')) {
            $fp = fopen($publicRootPath . '/index.html', 'w');
            if ($fp) {
                fclose($fp);
            }
        }

        $opts = [
            'roots' => [
                $root_options,
                $public_root,
            ],
        ];

        $opts      = hooks()->apply_filters('before_init_media', $opts);
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }

    public function bulk_pdf_exporter()
    {
        if (!has_permission('bulk_pdf_exporter', '', 'view')) {
            access_denied('bulk_pdf_exporter');
        }

        if ($this->input->post()) {
            $export_type = $this->input->post('export_type');

            $this->load->library('app_bulk_pdf_export', [
                'export_type'       => $export_type,
                'status'            => $this->input->post($export_type . '_export_status'),
                'date_from'         => $this->input->post('date-from'),
                'date_to'           => $this->input->post('date-to'),
                'payment_mode'      => $this->input->post('paymentmode'),
                'tag'               => $this->input->post('tag'),
                'redirect_on_error' => admin_url('utilities/bulk_pdf_exporter'),
            ]);

            $this->app_bulk_pdf_export->export();
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get();

        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();

        $this->load->model('credit_notes_model');
        $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $features = [];

        if (has_permission('invoices', '', 'view')
        || has_permission('invoices', '', 'view_own')
        || get_option('allow_staff_view_invoices_assigned') == '1') {
            $features[] = [
                'feature' => 'invoices',
                'name'    => _l('bulk_export_pdf_invoices'),
            ];
        }

        if (has_permission('estimates', '', 'view')
            || has_permission('estimates', '', 'view_own')
            || get_option('allow_staff_view_estimates_assigned') == '1') {
            $features[] = [
                'feature' => 'estimates',
                'name'    => _l('bulk_export_pdf_estimates'),
            ];
        }

        if (has_permission('payments', '', 'view') || has_permission('invoices', '', 'view_own')) {
            $features[] = [
                'feature' => 'payments',
                'name'    => _l('bulk_export_pdf_payments'),
            ];
        }

        if (has_permission('credit_notes', '', 'view') || has_permission('credit_notes', '', 'view_own')) {
            $features[] = [
                'feature' => 'credit_notes',
                'name'    => _l('credit_notes'),
            ];
        }

        if (has_permission('proposals', '', 'view')
            || has_permission('proposals', '', 'view_own')
            || get_option('allow_staff_view_proposals_assigned') == '1') {
            $features[] = [
                'feature' => 'proposals',
                'name'    => _l('bulk_export_pdf_proposals'),
            ];
        }

        if (has_permission('expenses', '', 'view')
            || has_permission('expenses', '', 'view_own')) {
            $features[] = [
                'feature' => 'expenses',
                'name'    => _l('expenses'),
            ];
        }

        $data['bulk_pdf_export_available_features'] = hooks()->apply_filters(
            'bulk_pdf_export_available_features',
            $features
        );

        $data['title'] = _l('bulk_pdf_exporter');
        $this->load->view('admin/utilities/bulk_pdf_exporter', $data);
    }
}
