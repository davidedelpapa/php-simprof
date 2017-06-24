<?php
/*
 * PHP simple profiler
 * Ver 0.1 - (c) 2017, Davide Del Papa, Public Domain
 * Originally based on an answer from StackOverflow: http://stackoverflow.com/questions/21133/simplest-way-to-profile-a-php-script#answer-29022400
 */

/* ----------------------------------------------------- */
/*                   REPORT FORMATTING                   */
/* ----------------------------------------------------- */

// page formatting
$prn_page_setup = "\n<html>\n<body>\n";
$prn_page_ending = "</body>\n</html>";
$prn_page_title= "<h1>Simple PHP Profiler</h1>\n";

// each voice
$prn_separator = "<hr>\n";
$prn_timing_line_beginning = "<b>File:</b> %s\n<h4>Results:</h4>\n<table>\n";
$prn_timing_line_header = "<caption><em>Results of 1 run -- %s</em></caption>\n<tr><th rowspan='2'></th><th colspan = '2'>Total Time</th></tr>\n<tr><th>Milliseconds</th><th>Seconds</th></tr>\n";
$prn_timing_line_pattern = "<tr><th>%s</th><td>%f</td><td>%f</td></tr>\n";
$prn_timing_line_ending_flag = "<tr><th>%s</th><td colspan = '2'><center>(last mark)</center></td></tr>\n";
$prn_timing_line_ending_summary = "<tr><th>TOTAl TIME</th><th>%f</th><th>%f</th></tr>\n";
$prn_timing_line_end = "</table>\n";

/* ----------------------------------------------------- */
/*                        FLAGS                          */
/* ----------------------------------------------------- */

// General flags
$_SP_ALL = 0;

// Parameters flags
$_SP_REQUEST = 1;
$_SP_GET = 2;
$_SP_POST = 3;


/* ----------------------------------------------------- */
/*                  ANCILLARY FUNCTIONS                  */
/* ----------------------------------------------------- */

/*
 * setReqParam
 * Function to load parameters in the $_REQUEST array
 */
function setReqParam($parameters){
    // clean_up
    $_REQUEST = array();
    foreach( $parameters as $par_key => $par_value ) {
        // storing
        $_REQUEST[ $par_key ] = $par_value;
    }
}

/*
 * setGetParam
 * Function to load parameters in the $_REQUEST array
 */
function setGetParam($parameters){
    // clean_up
    $_GET = array();
    foreach( $parameters as $par_key => $par_value ) {
        // storing
        $_GET[ $par_key ] = $par_value;
    }
}

/*
 * setPostParam
 * Function to load parameters in the $_REQUEST array
 */
function setPostParam($parameters){
    // clean_up
    $_POST = array();
    foreach( $parameters as $par_key => $par_value ) {
        // storing
        $_POST[ $par_key ] = $par_value;
    }
}

/* ----------------------------------------------------- */
/*             PROFILER's PRIVATE FUNCTIONS              */
/* ----------------------------------------------------- */

/*
 * _sp_clean
 * Function to clean the counters manuallly
 */
function _sp_clean(){
    global $_sp_times, $_sp_msg;
    $_sp_times = array();
    $_sp_msg = array();
}

/* ----------------------------------------------------- */
/*             PROFILER's PUBLIC FUNCTIONS               */
/* ----------------------------------------------------- */

/*
 * sp_manual
 * Function to set manual profiler mode
 */
function sp_manual(){
    // Nothing to do, for now
}

/*
 * sp_set_param
 * Function to set manually the parameters to run the script
 */
function sp_set_param($parameters, $mode = 0){
    switch($mode){
        case 1:
            setReqParam($parameters);
            break;
        case 2:
            setGetParam($parameters);
            break;
        case 3:
            setPostParam($parameters);
            break;
        case 0:
        default:
            setReqParam($parameters);
            setGetParam($parameters);
            setPostParam($parameters);
            break;
    }
}

/*
 * sp_flag
 * Function to set a profiler's flag with an optional message
 */
function sp_flag($message = ''){
    global $_sp_times, $_sp_msg;
    $_sp_times[] = microtime(true);
    $_sp_msg[] =  sizeof($_sp_msg) . ': ' . ($message == '' ? '(nameless flag)' : $message);
}

/*
 * sp_start
 * Function to set a starting flag
 */
function sp_start(){
    sp_flag('Profiler::Start');
}

/*
 * sp_end
 * Function to set an ending flag
 */
function sp_end(){
    sp_flag('Profiler::End');
}

/*
 * sp_prepare_report
 * Function to prepare the report for the current script
 */
function sp_prepare_report($_sp_curr_file = ""){
    global $_sp_times, $_sp_msg, $_sp_prof_report;
    global $prn_separator, $prn_timing_line_beginning, $prn_timing_line_header, $prn_timing_line_pattern, $prn_timing_line_ending_flag, $prn_timing_line_ending_summary, $prn_timing_line_end;
    if ($_sp_curr_file == "")
        $_sp_curr_file = basename(__FILE__);
    $total_time_s = 0;
    $size = count($_sp_times);
    $_sp_prof_report[] =  $prn_separator;
    $_sp_prof_report[] =  sprintf($prn_timing_line_beginning, basename($_sp_curr_file));
    $_sp_prof_report[] =  sprintf($prn_timing_line_header, date("Y M d, h:i:sa"));
    for($i=0;$i<$size - 1; $i++)
    {
        $time_s = ($_sp_times[$i+1]-$_sp_times[$i]);
        $total_time_s = $total_time_s + $time_s;
        $_sp_prof_report[] =  sprintf($prn_timing_line_pattern, $_sp_msg[$i], $time_s * 1000, $time_s);
    }
    $_sp_prof_report[] =  sprintf($prn_timing_line_ending_flag, $_sp_msg[$size-1]);
    $_sp_prof_report[] =  sprintf($prn_timing_line_ending_summary, $total_time_s * 1000, $total_time_s);
    $_sp_prof_report[] =  $prn_timing_line_end;
}

/*
 * sp_print_report
 * Function to print the whole report
 */
function sp_print_report(){
    global $_sp_prof_report, $prn_page_setup, $prn_page_title, $prn_page_ending;
    echo $prn_page_setup;
    echo $prn_page_title;
    foreach($_sp_prof_report as $curr_line){
        echo $curr_line;
    }
    echo $prn_page_ending;
}
?>