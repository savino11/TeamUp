<?php
  class DbConnect
  {
    private const HOST = 'localhost';
    private const DBNAME = 'team_up';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private static $connection = NULL;

    public static function connect()
    {
      self::$connection = new mysqli (self::HOST, self::USERNAME, self::PASSWORD, self::DBNAME);

      if (self::$connection->connect_error)
      {
        die("Connessione fallita: " . self::$connection->connect_error);
      }

      return self::$connection;
    }

    public static function disconnect()
    {
      if (self::$connection == NULL)
      {
        die("Connessione inesistente.");
      }

      self::$connection->close();
      self::$connection = NULL;
    }
  }
?>
