<?php

include_once 'job_handler.php';

//ob_start();
//var_dump($f);
//$result = ob_get_clean();
//file_put_contents('test.lg', $f);

class REST {
    private $method = '';
    private $args = Array();
    private $user = ''; // TODO: remove

    public function __construct() {
        // Check authentication
        // Note that we rely on the web server to do the password checking
        /* TODO: delete
        
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="studentupn"');
            $this->fail('You are not authorized.', 401);
        }
        if (array_search($_SERVER['PHP_AUTH_USER'], $GLOBALS['users']) === false) {
            header('WWW-Authenticate: Basic realm="studentupn"');
            $this->fail('You are not authorized.', 401);
        }
        $this->user = $_SERVER['PHP_AUTH_USER'];
        */
        // Get the request method

        $this->method = $_SERVER['REQUEST_METHOD'];

        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                $this->fail("Invalid Request Method in HTTP_X_HTTP_METHOD", 405);
            }
        }

        // Get the parameters
        // We are expecting something like /some/path/queue.php/arg1
        $fname = basename($_SERVER['SCRIPT_NAME']);
        $uriparts = explode($fname, $_SERVER['REQUEST_URI']);
        if ($uriparts === false || count($uriparts) < 2) {
            $this->fail("Invalid URI", 400);
        }

        // This now splits /arg1/arg2
        $args = explode('/', $uriparts[1]);
        array_shift($args);
        $this->args = $args;
    } //function __construct

    public function processAPI() {
        switch ($this->method) {
            case "GET":
                if (!isset($this->args[0])) { // TODO: check if args[0] is correct (I might be off by one)

                    $gotNew = false;
                    // get older jobs first
                    $allJobs = get_job_list();
                    sort($allJobs);

                    foreach($allJobs as $job){
                        // found a new job
                        if($job->is_new()){
                            $JSON = array(
                                'newJob' => true,
                                'jobID' => $job->id ,
                                'dependencies' => $job->get_dependency_files()
                            );
                            $gotNew = true;
                            break;
                        }
                    }

                    // if no new jobs
                    if(!$gotNew){
                        $JSON = array('newJob' => false);
                    }

                    // send JSON
                    header('Content-type: text/javascript');
                    echo $this->json_readable_encode($JSON);

                } else {
                    if (!preg_match('/^[0-9]{8,10}$/i', $this->args[0])) { // TODO: fix the regex to match the queue_id format
                        $this->fail("Invalid queue ID", 406);
                    }
                    // TODO: dump a JSON containing all info about a single queue item
                }
                break;
            case "PUT":
                if (!isset($this->args[0])) {
                    $this->fail("Invalid queue ID", 406);
                }

                // check type is json
                if ($_SERVER['CONTENT_TYPE'] != "application/json") {
                    $this->fail("Unsupported content type. Expecting: application/json.", 500);
                }

                // if job with this id does not exist, fail
                $jobID = $this->args[0];
                if(!job_exists($jobID)){
                    $this->fail("Invalid queue ID, no such job", 406);
                }
                $job = new Job($jobID);

                // parse incoming json
                $json = file_get_contents('php://input');
                $json = json_decode($json, true);
                if ($json == null) {
                    $this->fail("Incorrectly formatted json input.", 500);
                }

                // update job
                if($json['action'] == 'update'){
                    // status update
                    if(isset($json['status'])) {
                        if ($json['status'] == 'submitted') {
                            $job->set_to_submitted();
                        } elseif ($json['status'] == 'running') {
                            $job->set_to_running();
                        } elseif ($json['status'] == 'success') {
                            $job->set_to_success();
                        } elseif ($json['status'] == 'fail') {
                            $job->set_to_fail();
                        }
                    }

                    // tracking URL
                    if(isset($json['tracking'])) {
                        $job->set_hadoop_link($json['tracking']);
                    }

                    // progress
                    if(isset($json['progress'])) {
                        $job->update_progress($json['progress']);
                    }
                }

                try { // TODO: this is just an example of exception handling and reponse returning
                    if ($error) {
                        $this->fail("Some error.", 404);
                    } else {
                        // TODO: you can pass an associative array (dictionary) to the response function and it will automatically JSON encode it
                        $this->response("Oh great all is good", 200);
                    }
                } catch (Exception $e) {
                    $this->fail($e->getMessage(), 500);
                }

                break;
            default:
                $this->fail("Method not allowed.", 405);
        }
    } //function processAPI


    private function fail($msg, $status = 500) {
        $data = Array('error' => $msg);
        $this->response($data, $status);
    }

    private function response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json; charset=utf-8");

        echo json_encode($data);
        exit(0);
    } //function response

    private function requestStatus($code) {
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        if (array_key_exists($code, $status)) {
            return $status[$code];
        } else {
            return $status[500];
        }
    } // function requestStatus

    /*
        json readable encode
        basically, encode an array (or object) as a json string, but with indentation
        so that i can be easily edited and read by a human

        THIS REQUIRES PHP 5.3+

        Copyleft (C) 2008-2011 BohwaZ <http://bohwaz.net/>

        Licensed under the GNU AGPLv3

        This software is free software: you can redistribute it and/or modify
        it under the terms of the GNU Affero General Public License as published by
        the Free Software Foundation, either version 3 of the License, or
        (at your option) any later version.

        This software is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU Affero General Public License
        along with this software. If not, see <http://www.gnu.org/licenses/>.
    */

    private function json_readable_encode($in, $indent = 0, Closure $_escape = null)
    {
        if (__CLASS__ && isset($this))
        {
            $_myself = array($this, __FUNCTION__);
        }
        elseif (__CLASS__)
        {
            $_myself = array('self', __FUNCTION__);
        }
        else
        {
            $_myself = __FUNCTION__;
        }
        if (is_null($_escape))
        {
            $_escape = function ($str)
            {
                return str_replace(
                    array('\\', '"', "\n", "\r", "\b", "\f", "\t", '/', '\\\\u'),
                    array('\\\\', '\\"', "\\n", "\\r", "\\b", "\\f", "\\t", '\\/', '\\u'),
                    $str);
            };
        }

        $out = '';
        foreach ($in as $key=>$value)
        {
            $out .= str_repeat("\t", $indent + 1);
            $out .= "\"".$_escape((string)$key)."\": ";

            if (is_object($value) || is_array($value))
            {
                $out .= "\n";
                $out .= call_user_func($_myself, $value, $indent + 1, $_escape);
            }
            elseif (is_bool($value))
            {
                $out .= $value ? 'true' : 'false';
            }
            elseif (is_null($value))
            {
                $out .= 'null';
            }
            elseif (is_string($value))
            {
                $out .= "\"" . $_escape($value) ."\"";
            }
            else
            {
                $out .= $value;
            }
            $out .= ",\n";
        }
        if (!empty($out))
        {
            $out = substr($out, 0, -2);
        }
        $out = str_repeat("\t", $indent) . "{\n" . $out;
        $out .= "\n" . str_repeat("\t", $indent) . "}";
        return $out;
    }
    /** end json readable encode */
} //class REST
?>