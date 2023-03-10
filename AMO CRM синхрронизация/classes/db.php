<?php
namespace amo;

require_once("exceptions/dbException.php");
require_once("exceptions/fileException.php");
require_once(__DIR__ . "/../lang/ru/amo.php");

/**
 * db class
 * @author Suslov Igor <IUSuslov@1cbit.ru>
 */
class DB
{
    /**
     * host
     * @var string
     */
    private $server;

    /**
     * user name
     * @var string
     */
    private $user;

    /**
     * password
     * @var string
     */
    private $password;

    /**
     * db name
     * @var string
     */
    private $db_name;

    /**
     * object connecting to the MySQL
     * @var false|\mysqli
     */
    private $link;

    /**
     * @throws FileException
     * @throws DBException
     */
    public function __construct()
    {
        try {
            if (!($file = file_get_contents(__DIR__ . "/../config/db.json"))) throw new FileException($GLOBALS["lang"]["errorFile"], __DIR__ . "/../config/db.json");
        } catch (FileException $e) {
            http_response_code(404);
            print $e->getMessage();
            die();
        }

        $dbConfig = json_decode($file, true);

        $this->server = $dbConfig["server"];
        $this->user = $dbConfig["user"];
        $this->password = $dbConfig["password"];
        $this->db_name = $dbConfig["db_name"];

        try {
            if (!($this->link = mysqli_connect($this->server, $this->user, $this->password, $this->db_name))) throw new DBException($GLOBALS["lang"]["errorMySQL"], [$dbConfig, mysqli_connect_error()]);
        } catch (DBException $e) {
            http_response_code(403);
            print $e->getMessage();
            die();
        }
    }

    /**
     * query into MySql
     * @param string $sql
     * @return array
     * @throws DBException
     */
    public function query(string $sql)
    :array
    {
        try {
            if (($result = mysqli_query($this->link, $sql)) === false) throw new DBException($GLOBALS["lang"]["errorMySQL"], [mysqli_error($this->link), $sql]);
        } catch (DBException $e) {
            http_response_code(400);
            print $e->getMessage();
            die();
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}