<?php
  class Response implements JsonSerializable
  {
    private $result;
    private $redirectLink;

    public function __construct ($result, $link)
    {
      $this->result = $result;
      $this->redirectLink = $link;
    }

    public function getResult ()
    {
      return $this->result;
    }

    public function setResult ($result)
    {
      $this->result = $result;
    }

    public function getRedirectLink ()
    {
      return $this->redirectLink;
    }

    public function setRedirectLink ($link)
    {
      $this->redirectLink = $link;
    }

    public function jsonSerialize()
    {
        return
        [
            'result'   => $this->getResult(),
            'redirectLink' => $this->getRedirectLink()
        ];
    }
  }
 ?>
