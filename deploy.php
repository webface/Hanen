<?php

// deploy script to fetch changes pushed to origin
// some code was borrowed from: https://bitbucket.org/lilliputten/automatic-bitbucket-deploy


date_default_timezone_set('America/Toronto');

class Deploy {

    /**
     * A callback function to call after the deploy has finished.
     * 
     * @var callback
     */
    public $post_deploy;

    /**
     * The name of the file that will be used for logging deployments. Set to 
     * FALSE to disable logging.
     * 
     * @var string
     */
    private $_log = 'deployments.log';

    /**
     * The timestamp format used for logging.
     * 
     * @link    http://www.php.net/manual/en/function.date.php
     * @var     string
     */
    private $_date_format = 'Y-m-d H:i:sP';

    /**
     * The name of the branch to pull from.
     * 
     * @var string
     */
    private $_branch = 'master';

    /**
     * The name of the remote to pull from.
     * 
     * @var string
     */
    private $_remote = 'origin';

    /**
     * The directory where your website and git repository are located, can be 
     * a relative or absolute path
     * 
     * @var string
     */
    private $_directory;

    /**
     * Sets up defaults.
     * 
     * @param  string  $directory  Directory where your website is located
     * @param  array   $data       Information about the deployment
     */
    public function __construct($directory, $options = array())
    {
        // Determine the directory path
        $this->_directory = realpath($directory).DIRECTORY_SEPARATOR;

        $available_options = array('log', 'date_format', 'branch', 'remote');

        foreach ($options as $option => $value)
        {
            if (in_array($option, $available_options))
            {
                $this->{'_'.$option} = $value;
            }
        }

        $this->log('Attempting deployment...');
    }

    /**
     * Writes a message to the log file.
     * 
     * @param  string  $message  The message to write
     * @param  string  $type     The type of log message (e.g. INFO, DEBUG, ERROR, etc.)
     */
    public function log($message, $type = 'INFO')
    {
        if ($this->_log)
        {
            // Set the name of the log file
            $filename = $this->_log;

            if ( ! file_exists($filename))
            {
                // Create the log file
                file_put_contents($filename, '');

                // Allow anyone to write to log files
                chmod($filename, 0666);
            }

            // Write the message into the log file
            // Format: time --- type: message
            file_put_contents($filename, date($this->_date_format).' --- '.$type.': '.$message.PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * Executes the necessary commands to deploy the website.
     */
    public function execute()
    {
        try
        {
            // Make sure we're in the right directory
            $last_line = system('cd '.$this->_directory, $output);
            $this->log('Changing working directory... '. $last_line . ' returned: ' . $output);
            echo 'Changing working directory... '. $last_line . ' returned: ' . $output . ' <br>\n';

            // Discard any changes to tracked files since our last deploy
            $last_line = system('git reset --hard HEAD', $output);
            $this->log('Reseting repository... '. $last_line . ' returned: ' . $output);
            echo 'Reseting repository... '. $last_line . ' returned: ' . $output . ' <br>\n';

            // Update the local repository
            $last_line = system('git pull '.$this->_remote.' '.$this->_branch, $output);
            $this->log('Pulling in changes... '. $last_line . ' returned: ' . $output);
            echo 'Pulling in changes... '. $last_line . ' returned: ' . $output . ' <br>\n';

            // Secure the .git directory
            $last_line = system('chmod -R og-rx .git');
            $this->log('Securing .git directory... ');
            echo 'Securing .git directory... '. $last_line . ' returned: ' . $output . ' <br>\n';

            if (is_callable($this->post_deploy))
            {
                call_user_func($this->post_deploy, $this->_data);
            }

            $this->log('Deployment successful.');
            echo 'Deployment successful.';
        }
        catch (Exception $e)
        {
            $this->log($e, 'ERROR');
            echo "ERROR: $e";
        }
    }

}

// make sure we got the right password
$cid = (isset($_REQUEST['cid'])) ? $_REQUEST['cid'] : 0;
if ($cid != 'zxasqw12~')
{
    echo "Wrong Password";
    exit;
}

// confirm were getting a request from bitbucket
if (isset($_SERVER['HTTP_X_EVENT_KEY'], $_SERVER['HTTP_X_HOOK_UUID'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'])) 
{
    $headers = ('*** ' . $_SERVER['HTTP_X_EVENT_KEY'] . ' #' . $_SERVER['HTTP_X_HOOK_UUID'] .
        ' (' . $_SERVER['HTTP_USER_AGENT'] . ')');
    $headers .= ('remote addr: ' . $_SERVER['REMOTE_ADDR']);
} 
else 
{
    echo '*** [unknown http event key] #[unknown http hook uuid] (unknown http user agent)';
    exit;
}

$options = array(
    'log' => 'deployments.log',
    'date_format' => 'Y-m-d H:i:sP',
    'branch' => 'development_premerge',
    'remote' => 'origin',
);

// This is just an example
$deploy = new Deploy('/opt/bitnami/apps/wordpress/htdocs', $options);
//$deploy = new Deploy('/Users/hagai/code/eot_v5.dev', $options);

$deploy->post_deploy = function() use ($deploy) {
    // hit the wp-admin page to update any db changes
    $last_line = system('curl http://expertonlinetraining.info/wp-admin/upgrade.php?step=upgrade_db');
    $deploy->log('Updating wordpress database... '. $last_line . ' returned: ' . $output);
    echo 'Updating wordpress database... '. $last_line . ' returned: ' . $output . ' <br>\n';
};

// log the headers
$deploy->log($headers);

$payload = json_decode(file_get_contents('php://input'));
// make sure payload is not empty
if (empty($payload))
{
    echo "No payload";
    $deploy->log("no payload");
    exit;
}

// make sure payload has the right data in it
if ( !isset($payload->repository->name, $payload->push->changes) ) 
{
    $deploy->log("Invalid payload data was received!");
    echo "Invalid payload data was received!";
    exit;
}

// check the payload to make sure were in the branch we require and only execute if its the right branch
foreach ( $payload->push->changes as $change ) 
{
    if ( is_object($change->new) && $change->new->type == "branch" && $change->new->name == $options['branch'] ) 
    {
        // the new pushed branch is the same as the one in the options so go ahead and pull it in.
        $deploy->execute();
    }
    else
    {
        $deploy->log("Branch is not what were looking for. It is: " . $change->new->name);
    }
}



?>