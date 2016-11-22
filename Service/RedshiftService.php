<?php
namespace AppBundle\Service;
use \Symfony\Component\HttpFoundation\JsonResponse;


class RedshiftService {

    public static $connections = array();
    private $connectionString = null;
    private $connection = null;
    protected $app;

    /**
     * RedshiftService constructor.
     * @param $credentials
     * @param string $destinationServer
     * @throws \Exception
     */
    public function __construct($credentials, $destinationServer = 'default'){
        //$this->logger = new LogController($app);

        if(isset($credentials[$destinationServer])) {
            $server = array(
                'host' => $credentials[$destinationServer]['host'],
                'port' => $credentials[$destinationServer]['port'],
                'dbname' => $credentials[$destinationServer]['database'],
                'user' => $credentials[$destinationServer]['user'],
                'password' => $credentials[$destinationServer]['password'],
                //'sslmode' => 'verify-ca',
                // https://s3.amazonaws.com/redshift-downloads/redshift-ssl-ca-cert.pem
                //'sslrootcert' => 'ssl/redshift-ssl-ca-cert.pem', //as provided by aws
                'options' => '--client_encoding=UTF8',
            );



            $connectionString = '';
            foreach ($server as $k => $v) {
                $connectionString .= $k . '=' . $v . ' ';
            }
            $this->connectionString = (strlen($connectionString) > 0) ? substr($connectionString, 0, -1) : '';
            $this->connect($this->connectionString);
        }else{
//            $this->logger->createLog('critical error',030002,'RedshiftConnectionService->__construct','Could not connect to Redshift',
//                array('connectionString' => $this->connectionString));
            throw new \Exception('Failed to connect');
        }
    }

    /**
     * Executes a push query
     * @param $query
     * @return bool
     * @throws \Exception
     */
    public function pushData($query){
        try {
            $status = $this->exec($query);
        }catch(\Exception $e){
            throw $e;
        }
        return true;
    }

    /**
     * Connects to the database
     * @param $connectionString
     * @throws \Exception
     */
    public function connect($connectionString){
        try{
            $this->connection = \pg_connect($connectionString);
        }catch(Excepction $e){
//            $this->logger->createLog('critical error',030002,'RedshiftConnectionService->connect','Could not connect to Redshift',
//                array('connectionString' => $connectionString));
            throw new \Exception('Failed to connect: '.\pg_last_error($this->connection));
        }
        //sometimes try catch doesn't work on this connect command
        if(!$this->connection){
//            $this->logger->createLog('critical error',030002,'RedshiftConnectionService->connect','Could not connect to Redshift',
//                array('connectionString' => $connectionString));
            throw new \Exception('Failed to connect: '.\pg_last_error($this->connection));
        }
    }



    /**
     * Executes a query
     * @param $query
     * @return resource
     * @throws \Exception
     */
    public function exec($query){
        $result = @\pg_query($this->connection, $query);
        $errorMessage = trim(pg_last_error($this->connection));
        if($errorMessage == ''){
            $result = true;
        }else{
//            $this->logger->createLog('error',230002,'RedshiftConnectionService->exec','Redshift query error',
//                array('query' => $query, 'errorMessage' => $errorMessage));
            throw new \Exception($errorMessage);
        }
        return $result;
    }

    /**
     * Executes query and returns query results
     * @param $query
     * @return resource
     * @throws \Exception
     */
    public function doQuery($query){
        $result = @\pg_query($this->connection, $query);
        if (!is_resource($result)) {
            $errorMessage = trim(@pg_last_error($this->connection));
            if ($errorMessage != '') {
//            $this->logger->createLog('error',230002,'RedshiftConnectionService->doQuery','Redshift query error',
//                array('query' => $query, 'errorMessage' => $errorMessage));
                throw new \Exception($errorMessage);
            }
        }else {
            $rows = pg_fetch_all($result);
            return $rows;
        }
        return false;
    }

    /**
     * Destructor
     */
    public function __destruct(){
        @\pg_close($this->connection);
    }

    /**
     * Resets connection to the database;
     * @throws \Exception
     */
    public function reset(){
        @\pg_close($this->connection);
        $this->connect($this->connectionString);
    }


}